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
        <input id="{{ $random }}-name" type="text" name="name" value="{{ old('name', isset($place->name) ? $place->name : null) }}" required>
    </div>
    <div class="form-group">
        <label for="{{ $random }}-description">{{ __('Description') }}</label>
        <textarea id="{{ $random }}-description" name="description">{{ old('description', isset($place->description) ? $place->description : null) }}</textarea>
    </div>
    <div class="form-group">
        <label for="{{ $random }}-place_type">{{ __('Place type') }}</label>
        <select id="{{ $random }}-place_type" name="place_type" required>
            @php
                $old_value = old('place_type', isset($place->place_type_id) ? $place->place_type_id : null);
            @endphp
            @if ($old_value == null)
                <option value="null" selected disabled>{{ __('Pick one') }}</option>
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
    <legend>{{ __('Links and contact info') }}</legend>
    <div class="form-group">
        <label for="{{ $random }}-website">{{ __('Website') }}</label>
        <input id="{{ $random }}-website" type="url" name="website" value="{{ old('website', isset($place->website) ? $place->website : null) }}">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-phone">{{ __('Phone') }}</label>
        <input id="{{ $random }}-phone" type="TEL" name="phone" value="{{ old('phone', isset($place->phone) ? $place->phone : null) }}">
    </div>
    <div class="form-group">
        <label for="{{ $random }}-email">{{ __('Email') }}</label>
        <input id="{{ $random }}-email" type="email" name="email" value="{{ old('email', isset($place->email) ? $place->email : null) }}">
    </div>
</fieldset>
<fieldset>
    <legend>{{ __('Opening hours') }}</legend>
    <p>{{ __('Fill out hours on the HH:MM format. For example: 13:40') }}</p>
    @foreach ($weekdays as $weekday)
        @php
            $weekday_slug = str_slug($weekday);
        @endphp
        <div class="form-group">
            <fieldset>
                <legend>{{ $weekday }}</legend>
                <label for="{{ $random }}-{{ $weekday_slug }}-open_closed">
                    <span>{{ __('Open') }}</span>
                    <span>{{ __('Closed') }}</span>
                </label>
                <input id="{{ $random }}-{{ $weekday_slug }}-open_closed" type="checkbox" name="open_hours_open_closed-{{ $weekday_slug }}">
                <input type="text" placeholder="{{ __('Opens at') }}" name="open_hours_from[]">
                <span>—</span>
                <input type="text" placeholder="{{ __('Closes at') }}" name="open_hours_to[]">
            </fieldset>
        </div>
    @endforeach
</fieldset>
