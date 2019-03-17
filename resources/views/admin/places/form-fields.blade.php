@php
    $random = 'form-place-' . bin2hex(random_bytes(4));
@endphp
<fieldset>
    <legend>Give user access</legend>
    <div class="form-group">
        <label for="{{ $random }}-email">{{ __('Email') }}</label>
        <input id="{{ $random }}-email" type="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="{{ $random }}-place_role">{{ __('Role') }}</label>
        <select id="{{ $random }}-place_role" name="place_role_id" required>
            @php
                $old_value = old('place_role', isset($place->place_role_id) ? $place->place_role_id : null);
            @endphp
            @if ($old_value == null)
                <option value="" hidden selected disabled>{{ __('Pick one') }}</option>
            @endif
            @foreach ($place_roles as $place_role)
                @if ($old_value == $place_role->id)
                    <option value="{{ $place_role->id }}" selected>{{ $place_role->name }}</option>
                @else
                    <option value="{{ $place_role->id }}">{{ $place_role->name }}</option>
                @endif
            @endforeach
        </select>
    </div>
</fieldset>
