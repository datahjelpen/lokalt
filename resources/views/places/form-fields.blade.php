@php
    // Random enough for our needs.
    // We just want to avoid collision in case the fields
    // are included multiple times on a single page
    $random = 'form-place-' . bin2hex(random_bytes(4));
@endphp
<fieldset>
    <legend>{{ __('About place') }}</legend>
    <div class="form-group">
        <label for="{{ $random }}-name">{{ __('Name') }}</label>
        <input id="{{ $random }}-name" type="text" name="name" value="{{ old('name', isset($place->name) ? $place->name : null) }}" required autocomplete="organization">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-description">{{ __('Description') }}</label>
        <textarea id="{{ $random }}-description" name="description">{{ old('description', isset($place->description) ? $place->description : null) }}</textarea>
    </div>
    <div class="form-group">
        <label for="{{ $random }}-place_type">{{ __('Place type') }}</label>
        <select id="{{ $random }}-place_type" name="place_type_id" required>
            @php
                $old_value = old('place_type', isset($place->place_type_id) ? $place->place_type_id : null);
            @endphp
            @if ($old_value == null)
                <option value="" hidden selected disabled>{{ __('Pick one') }}</option>
            @endif
            @foreach ($place_types as $place_type)
                @if ($old_value == $place_type->id)
                    <option value="{{ $place_type->id }}" selected>{{ $place_type->name }}</option>
                @else
                    <option value="{{ $place_type->id }}">{{ $place_type->name }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="{{ $random }}-founded_at">{{ __('Founded at') }}</label>
        <input id="{{ $random }}-founded_at" type="text" name="founded_at" value="{{ old('founded_at', isset($place->founded_at) ? $place->founded_at : null) }}">
    </div>
</fieldset>
<fieldset>
    <legend>{{ __('Address') }}</legend>
    <div class="form-group">
        <label for="{{ $random }}-street_name_number">{{ __('Street name and number') }}</label>
        <input id="{{ $random }}-street_name_number" type="text" name="street_name_number" value="{{ old('street_name_number', isset($place->address->street_name_number) ? $place->address->street_name_number : null) }}" required autocomplete="street-address">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-postal_code">{{ __('Postal code') }}</label>
        <input id="{{ $random }}-postal_code" type="text" name="postal_code" value="{{ old('postal_code', isset($place->address->postal_code) ? $place->address->postal_code : null) }}" required autocomplete="postal-code">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-postal_city">{{ __('City') }}</label>
        <input id="{{ $random }}-postal_city" type="text" name="postal_city" value="{{ old('postal_city', isset($place->address->postal_city) ? $place->address->postal_city : null) }}" required autocomplete="address-level2">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-province">{{ __('Province') }}</label>
        <input id="{{ $random }}-province" type="text" name="province" value="{{ old('province', isset($place->address->province) ? $place->address->province : null) }}" required autocomplete="address-level1">
    </div>
</fieldset>
<fieldset>
    <legend>{{ __('Links and contact info') }}</legend>
    <div class="form-group">
        <label for="{{ $random }}-website">{{ __('Website') }}</label>
        <input id="{{ $random }}-website" type="url" name="website" value="{{ old('website', isset($place->website) ? $place->website : null) }}" autocomplete="url">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-phone">{{ __('Phone') }}</label>
        <input id="{{ $random }}-phone" type="tel" name="phone" value="{{ old('phone', isset($place->phone) ? $place->phone : null) }}" autocomplete="tel-local">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-email">{{ __('Email') }}</label>
        <input id="{{ $random }}-email" type="email" name="email" value="{{ old('email', isset($place->email) ? $place->email : null) }}" autocomplete="email">
    </div>
</fieldset>
<fieldset>
    <datalist id="available-hours">
        @foreach ($available_hours as $available_hour)
            <option value="{{$available_hour}}"/>
        @endforeach
    </datalist>
    <legend>{{ __('Opening hours') }}</legend>
    <p>{{ __('Fill out hours on the 24-hour format (hh:mm).') . ' ' . __('For example:') . ' ' . $time_now }}</p>
    @php
        $i = 0;
    @endphp
    @foreach ($weekdays as $weekday)
        @php
            $i++;
            $weekday_slug = str_slug($weekday);
            $open_from = null;
            $open_to = null;
            $day_is_open = false;

            if (isset($place->opening_hours_regular)) {
                $open_hours = $place->opening_hours_regular->where('weekday', $i)->first();

                if ($open_hours != null) {
                    $open_from = $open_hours->time_from;
                    $open_to = $open_hours->time_to;

                    if ($open_from != null || $open_to != null) {
                        $day_is_open = true;
                    }
                }
            }
        @endphp
        <fieldset class="form-group form-group-opening-hours">
            <legend>{{ $weekday }}</legend>
            <input
                id="{{ $random }}-{{ $weekday_slug }}-open_closed"
                type="checkbox"
                class="input-toggle"
                name="open_hours_open_closed_{{ $weekday_slug }}"
                {{ old('open_hours_open_closed_' . $weekday_slug, $day_is_open) ? 'checked' : null }}
            >
            <label class="input-toggle-label" for="{{ $random }}-{{ $weekday_slug }}-open_closed">
                <span class="input-toggle-off">{{ __('Closed') }}</span>
                <span class="input-toggle-on">{{ __('Open') }}</span>
            </label>
            <div class="input-toggle-target">
                <div class="input-toggle-on">
                    <input
                        type="text"
                        placeholder="{{ __('Opens at') }}"
                        aria-label="{{ __(':weekday opens at', ['weekday' => $weekday]) }}"
                        name="open_hours_from_{{ $weekday_slug }}"
                        value="{{ old('open_hours_from_' . $weekday_slug, $open_from) }}"
                        pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                        oninvalid="this.setCustomValidity('{{ __('Fill out hours on the 24-hour format (hh:mm).') }}')"
                        oninput="this.setCustomValidity('')"
                        list="available-hours"
                        autocomplete="off"
                    ><span>—</span><input
                        type="text"
                        placeholder="{{ __('Closes at') }}"
                        aria-label="{{ __(':weekday closes at', ['weekday' => $weekday]) }}"
                        name="open_hours_to_{{ $weekday_slug }}"
                        value="{{ old('open_hours_to_' . $weekday_slug, $open_to) }}"
                        pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                        oninvalid="this.setCustomValidity('{{ __('Fill out hours on the 24-hour format (hh:mm).') }}')"
                        oninput="this.setCustomValidity('')"
                        list="available-hours"
                        autocomplete="off"
                    >
                </div>
            </div>
        </fieldset>
    @endforeach
</fieldset>

@if (isset($place))
    <fieldset>
        <legend>{{ __('Special hours') }}</legend>
        <p>
            {{ __('Got any holidays or vacations coming up? You can add the opening hours for those days here.') }}
        </p>
        <div class="form-group">
            <label for="{{ $random }}-special_hours_text">{{ __('Reason for special hours') }}</label>
            <textarea id="{{ $random }}-special_hours_text" name="special_hours_text">{{ old('special_hours_text', isset($place->special_hours_text) ? $place->special_hours_text : null) }}</textarea>
        </div>
        <p>
            <button class="form-place-special-hours-add" type="button">{{ __('Add new special hour') }}</button>
        </p>

        <input type="hidden" class="form-place-id" value="{{ $place->id }}">
        <div class="form-place-special-hours-wrapper"></div>
        <button class="form-place-special-hours-add" type="button">{{ __('Add new special hour') }}</button>
    </fieldset>
@endif
