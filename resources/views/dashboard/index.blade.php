@extends('partials.master')

@section('content')
<h1>{{ __('Dashboard') }}</h1>
<h2>{{ __('My places') }}</h2>
<a href="{{ route('places.create') }}">{{ __('Add a new place') }}</a>
@foreach ($places as $place)
    <fieldset>
        <legend>{{ $place->name }}</legend>
        <p>
            <span>{{ $place->address->street_name_number }},</span>
            <span>{{ $place->address->postal_code }} {{ $place->address->postal_city }}</span>
        </p>
        <a href="{{ route('places.show', $place->slug) }}" target="_blank">{{ __('Show') }}</a>
        <a href="{{ route('places.edit', $place) }}">{{ __('Edit') }}</a>
        <a href="{{ route('places.delete', $place) }}">{{ __('Delete') }}</a>
    </fieldset>
@endforeach
@endsection
