<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use App\Place;
use App\PlaceType;
use App\PlaceOpenHour;
use App\PlaceRole;
use App\PlaceUser;
use App\Address;
use App\User;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin|admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $places = [];
        return view('admin.index', compact('places'));
    }

    protected function search_place_validator(array $data)
    {
        return Validator::make($data, [
            'search' => ['required', 'string', 'max:255']
        ]);
    }

    public function search_place(Request $request)
    {
        $this->search_place_validator($request->all())->validate();

        $places = Place::where('name', 'LIKE', "%{$request->search}%")->get();

        if ($places->count() == 0) {
            $place = new Place;
            $place->name = "Ingen resultater";
            $place->slug = str_slug($place->name);
            $places->add($place);
        }

        return view('admin.index', compact('places'));
    }

    public function edit_place(Place $place)
    {
        $place_types = PlaceType::all();
        $weekdays = PlaceOpenHour::getWeekdays();
        $time_now = Carbon::now()->format('H:i');
        $available_hours = PlaceOpenHour::getAvailableHours();

        $place_roles = PlaceRole::where('place_type_id', $place->place_type_id)->get();

        return view('admin.places.edit', compact('place', 'place_types', 'weekdays', 'time_now', 'available_hours', 'place_roles'));
    }

    protected function update_place_validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', Rule::in(User::pluck('email')->toArray())],
            'place_role_id' => ['required', Rule::in(PlaceRole::pluck('id')->toArray())]
        ]);
    }

    public function update_place(Request $request, Place $place)
    {
        $this->update_place_validator($request->all())->validate();

        $user = User::where('email', $request->email)->firstOrFail();
        $place_role = PlaceRole::findOrFail($request->place_role_id);

        $place_user = PlaceUser::where([
            'user_id' => $user->id,
            'place_id' => $place->id
        ])->first();

        if ($place_user === null) {
            $place_user = new PlaceUser;
            $place_user->user_id = $user->id;
            $place_user->place_id = $place->id;
            $place_user->place_role_id = $place_role->id;
            $place_user->save();

            $request->session()->flash('success', $user->name . ' ble lagt til som ' . $place_role->name . ' for ' . $place->name);
        } else {
            $request->session()->flash('error', $user->name . ' har allerede tilgang til ' . $place->name);
        }

        return redirect()->back();
    }

    public function create_user(Request $request)
    {
        return view('admin.users.create');
    }

    protected function store_user_validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function store_user(Request $request)
    {
        $this->store_user_validator($request->all())->validate();

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $request->session()->flash('success', 'Brukeren ' . $user->name . ' ble opprettet');

        return redirect()->back();
    }
}
