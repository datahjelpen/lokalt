@extends('partials.master')

@section('content')
<h1>{{ __('Places') }}</h1>
@foreach ($places as $place)
    <a href="{{ route('places.show', $place->slug) }}" class="place-link">
        <fieldset>
            <legend>
                {{ $place->name }}
            </legend>
            <p class="address">
                {{ $place->address->street_name_number }}, {{ $place->address->postal_code }} {{ $place->address->postal_city }}
            </p>
            <p class="about">{{ $place->description_short }}</p>
        </fieldset>
    </a>
@endforeach
@endsection
