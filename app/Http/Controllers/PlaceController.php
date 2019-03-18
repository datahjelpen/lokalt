<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Country;
use App\Address;
use App\Place;
use App\PlaceType;
use App\PlaceRole;
use App\PlaceUser;
use App\PlaceOpenHour;
use Carbon\Carbon;

class PlaceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('show', 'opening_hours');
        $this->middleware('verified')->except('show', 'opening_hours');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $place_types = PlaceType::all();
        $weekdays = PlaceOpenHour::getWeekdays();
        $time_now = Carbon::now()->format('H:i');
        $available_hours = PlaceOpenHour::getAvailableHours();

        return view('places.create', compact('place_types', 'weekdays', 'time_now', 'available_hours'));
    }

    protected function place_validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1024'],
            'address_id' => ['required', Rule::in(Address::pluck('id')->toArray())],
            'place_type_id' => ['required', Rule::in(PlaceType::pluck('id')->toArray())],
            'website' => ['nullable', 'URL'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'founded_at' => ['nullable', 'date'],
            'special_hours_text' => ['nullable', 'string', 'max:512']
        ]);
    }

    protected function address_validator(array $data)
    {
        return Validator::make($data, [
            'street_name_number' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'postal_city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $session = $request->session();

        // Validate address inputs
        $this->address_validator($request->all())->validate();

        // Check if address already exist
        $address = Address::findByInfo($request->street_name_number, $request->postal_code, $request->postal_city, $request->province);

        // If address doesn't exist, create it
        if ($address === null) {
            $address = new Address;
            $address->street_name_number = $request->street_name_number;
            $address->postal_code = $request->postal_code;
            $address->postal_city = $request->postal_city;
            $address->province = $request->province;
            $address->country_id = Country::first()->id;
            $address->save();
        }

        // Add address_id and slug to the request
        $request->merge(['address_id' => $address->id]);
        $request->merge(['slug' => str_slug($request->name)]);

        // Slugs are supposed to be unique. Is there already a place with this slug?
        // If there is, prepend some random string to the slug
        $place = Place::findBySlug($request->slug);
        if ($place !== null) {
            $random_string = bin2hex(random_bytes(8));
            $request->merge(['slug' => str_slug($request->name . '-' . $random_string)]);
        }

        // Validate the place inputs
        $this->place_validator($request->all())->validate();

        // Create the place
        $place = new Place;
        $place->place_type_id = $request->place_type_id;
        $place->name = $request->name;
        $place->slug = $request->slug;
        $place->description = strip_tags($request->description);
        $place->address_id = $request->address_id;
        $place->place_type_id = $request->place_type_id;
        $place->website = $request->website;
        $place->phone = $request->phone;
        $place->email = $request->email;
        $place->founded_at = $request->founded_at;
        $place->save();

        // Find the place owner role
        $place_role = PlaceRole::where([
            'slug' => 'owner',
            'place_type_id' => $place->place_type_id
        ])->first();

        // Give the place and role to the user
        $place_user = new PlaceUser;
        $place_user->user_id = $user->id;
        $place_user->place_id = $place->id;
        $place_user->place_role_id = $place_role->id;
        $place_user->save();

        // Set opening hours
        $weekdays = PlaceOpenHour::getWeekdays();
        foreach ($weekdays as $weekday_name) {
            $weekday_name_slug = str_slug($weekday_name);
            $input_name_open_closed = 'open_hours_open_closed-' . $weekday_name_slug;
            $input_name_open_from = 'open_hours_from-'  . $weekday_name_slug;
            $input_name_open_to = 'open_hours_to-'  . $weekday_name_slug;

            $day_is_open = $request->has($input_name_open_closed) && $request->{$input_name_open_closed} == 'on';

            if ($day_is_open && $request->has($input_name_open_from) && $request->has($input_name_open_to)) {

                // Validate the opening and closing times
                $place_open_hours_validated = PlaceOpenHour::validateTimes($request->{$input_name_open_from}, $request->{$input_name_open_to});
                if ($place_open_hours_validated === true) {
                    $place_open_hour = new PlaceOpenHour;
                    $place_open_hour->place_id = $place->id;
                    $place_open_hour->weekday = PlaceOpenHour::getWeekdayNumber($weekday_name);
                    $place_open_hour->time_from = $request->{$input_name_open_from};
                    $place_open_hour->time_to = $request->{$input_name_open_to};
                    $place_open_hour->save();
                } else {
                    $session->flash('error', __($place_open_hours_validated, ['weekday' => lcfirst($weekday_name)]));
                    return redirect()->back()->withInput();
                }
            }
        }

        $session->flash('success', 'Stedet ble opprettet');
        return redirect()->route('dashboard.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  String  $place_slug
     * @return \Illuminate\Http\Response
     */
    public function show(String $place_slug)
    {
        $place = Place::findBySlug($place_slug);

        if ($place == null) {
            abort(404, __('Place not found'));
        }

        $weekdays = PlaceOpenHour::getWeekdays();

        return view('places.show', compact('place', 'weekdays'));
    }

    public function opening_hours(Place $place)
    {
        return response()->json($place->openingHours(false));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Place $place)
    {
        if (!$place->userHasAccess($request->user())) {
            $request->session()->flash('error', 'Du har ikke tilgang til å redigere dette stedet.');
            return redirect()->route('dashboard.index');
        }

        $place_types = PlaceType::all();
        $weekdays = PlaceOpenHour::getWeekdays();
        $time_now = Carbon::now()->format('H:i');
        $available_hours = PlaceOpenHour::getAvailableHours();

        return view('places.edit', compact('place', 'place_types', 'weekdays', 'time_now', 'available_hours'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Place $place)
    {
        if (!$place->userHasAccess($request->user())) {
            $request->session()->flash('error', 'Du har ikke tilgang til å redigere dette stedet.');
            return redirect()->route('dashboard.index');
        }

        $user = $request->user();
        $session = $request->session();

        // Validate address inputs
        $this->address_validator($request->all())->validate();

        // Check if address already exist
        $address = Address::findByInfo($request->street_name_number, $request->postal_code, $request->postal_city, $request->province);

        // If address doesn't exist, create it
        if ($address === null) {
            $address = new Address;
            $address->street_name_number = $request->street_name_number;
            $address->postal_code = $request->postal_code;
            $address->postal_city = $request->postal_city;
            $address->province = $request->province;
            $address->country_id = Country::first()->id;
            $address->save();
        }

        // Add address_id and slug to the request
        $request->merge(['address_id' => $address->id]);
        $request->merge(['slug' => str_slug($request->name)]);

        // Validate the place inputs
        $this->place_validator($request->all())->validate();

        // Create the place
        $place->place_type_id = $request->place_type_id;
        $place->name = $request->name;
        $place->description = strip_tags($request->description);
        $place->address_id = $request->address_id;
        $place->place_type_id = $request->place_type_id;
        $place->website = $request->website;
        $place->phone = $request->phone;
        $place->email = $request->email;
        $place->founded_at = $request->founded_at;
        $place->special_hours_text = strip_tags($request->special_hours_text);
        $place->save();

        // Set opening hours
        $weekdays = PlaceOpenHour::getWeekdays();
        foreach ($weekdays as $weekday_name) {
            $weekday_name_slug = str_slug($weekday_name);
            $input_name_open_closed = 'open_hours_open_closed_' . $weekday_name_slug;
            $input_name_open_from = 'open_hours_from_'  . $weekday_name_slug;
            $input_name_open_to = 'open_hours_to_'  . $weekday_name_slug;

            // Try to get existing opening hours
            $weekday_number = PlaceOpenHour::getWeekdayNumber($weekday_name);
            $place_open_hour = $place->opening_hours_regular->where('weekday', $weekday_number)->first();

            $day_is_open = $request->has($input_name_open_closed) && $request->{$input_name_open_closed} == 'on';

            if ($day_is_open && $request->has($input_name_open_from) && $request->has($input_name_open_to)) {

                // Validate the opening and closing times
                $place_open_hours_validated = PlaceOpenHour::validateTimes($request->{$input_name_open_from}, $request->{$input_name_open_to});
                if ($place_open_hours_validated === true) {
                    if ($place_open_hour === null) {
                        $place_open_hour = new PlaceOpenHour;
                    }

                    $place_open_hour->place_id = $place->id;
                    $place_open_hour->weekday = PlaceOpenHour::getWeekdayNumber($weekday_name);
                    $place_open_hour->time_from = $request->{$input_name_open_from};
                    $place_open_hour->time_to = $request->{$input_name_open_to};
                    $place_open_hour->save();
                } else {
                    $session->flash('error', __($place_open_hours_validated, ['weekday' => lcfirst($weekday_name)]));
                    return redirect()->back()->withInput();
                }
            } else if ($place_open_hour !== null) {
                // Opening hours exist for this day, but the checkbox was unchecked, so let's delete it
                $place_open_hour->delete();
            }
        }

        // Handle special hours
        $special_fields = [
            'open_hours_from_special_dates',
            'open_hours_from_special_opens',
            'open_hours_from_special_closes',
            'open_hours_from_special_info',
            'open_hours_from_special_id'
        ];

        // If we have one field,. we must have the rest
        if ($request->has($special_fields[0])) {
            foreach ($special_fields as $special_field) {
                if (!$request->has($special_field)) {
                    $session->flash('error', 'Noe gikk galt, vi kunne ikke legge til de spesielle åpningstidene' );
                    return redirect()->back()->withInput();
                }
            }

            foreach ($request->open_hours_from_special_dates as $special_date_key => $special_date_value) {
                $info = $request->open_hours_from_special_info[$special_date_key];
                $id = $request->open_hours_from_special_id[$special_date_key];

                if ($info === "add") {
                    $place_open_hour = new PlaceOpenHour;
                    $place_open_hour->place_id = $place->id;
                } else if ($info === "edit") {
                    $place_open_hour = PlaceOpenHour::find($id);

                    if ($place_open_hour === null) {
                        continue;
                    }
                }

                if ($info === "delete") {
                    $place_open_hour = PlaceOpenHour::find($id);

                    if ($place_open_hour === null) {
                        continue;
                    }

                    $place_open_hour->delete();
                } else {
                    $place_open_hour->special_hours_date = $special_date_value;
                    $place_open_hour->time_from = $request->open_hours_from_special_opens[$special_date_key];
                    $place_open_hour->time_to = $request->open_hours_from_special_closes[$special_date_key];
                    $place_open_hour->save();
                }
            }
        }

        $session->flash('success', 'Stedet ble oppdatert');
        return redirect()->back();
    }

    /**
     * Show the form for destroying the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, Place $place)
    {
        if (!$place->userHasAccess($request->user())) {
            $request->session()->flash('error', 'Du har ikke tilgang til å slette dette stedet.');
            return redirect()->route('dashboard.index');
        }

        return view('places.delete', compact('place'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Place $place)
    {
        if ($place->userHasAccess($request->user())) {
            $place->delete();
            $request->session()->flash('success', 'Stedet ble slettet.');
        } else {
            $request->session()->flash('error', 'Du har ikke tilgang til å slette dette stedet.');
        }

        return redirect()->route('dashboard.index');
    }
}
