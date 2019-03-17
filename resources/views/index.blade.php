@extends('partials.master')

@section('content')
<h1>{{ __('Places') }}</h1>
@foreach ($places as $place)
    <article itemscope itemtype="http://schema.org/LocalBusiness">
        <a href="{{ route('places.show', $place->slug) }}" class="place-link">
            <fieldset>
                <legend itemprop="name">
                    {{ $place->name }}
                </legend>
                <p class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <span itemprop="streetAddress">{{ $place->address->street_name_number }}</span>,
                    <span itemprop="postalCode">{{ $place->address->postal_code }}</span>
                    <span itemprop="addressLocality">{{ $place->address->postal_city }}</span>
                </p>
                <p class="about" itemprop="disambiguatingDescription">{{ $place->description_short }}</p>
            </fieldset>
        </a>
    </article>
@endforeach
@endsection
