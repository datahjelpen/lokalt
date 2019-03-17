@extends('partials.master')

@section('content')
<article itemscope itemtype="http://schema.org/LocalBusiness">
    <h1 itemprop="name">{{ $place->name }}</h1>
    <ul>
        @if ($place->website)
            <li class="url" itemprop="sameAs"><a href="{{ $place->website }}" target="_blank" ref="noopener nofollow noreferrer">{{ $place->website }}</a></li>
        @endif
        @if ($place->phone)
            <li class="phone" itemprop="telephone"><a href="tel:{{ $place->phone }}">{{ $place->phone }}</a></li>
        @endif
        @if ($place->email)
            <li class="email" itemprop="email"><a href="mailto:{{ $place->email }}">{{ $place->email }}</a></li>
        @endif
        <li class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <a href="{{ url('https://www.google.com/maps/search/' .  $place->address->street_name_number . ' ' . $place->address->postal_code . ' ' . $place->address->postal_city) }}"
                target="_blank"
                ref="noopener nofollow noreferrer"
            >
                <span itemprop="streetAddress">{{ $place->address->street_name_number }}</span>,
                <span itemprop="postalCode">{{ $place->address->postal_code }}</span>
                <span itemprop="addressLocality">{{ $place->address->postal_city }}</span>
            </a>
        </li>
    </ul>
    <p class="about">{!! ($place->description_formatted) !!}</p>

    @if (count($place->opening_hours_regular) > 0)
        <h2>{{ __('Opening hours') }}</h2>
        @php
            $i = 0;
        @endphp
        @foreach ($weekdays as $weekday)
            <p><strong>{{ $weekday }}:</strong> {{ $place->opening_hours->regular[++$i]->timeFromTo() }}</p>
            <meta itemprop="openingHours" content="{{ $weekday }} {{ $place->opening_hours->regular[$i]->timeFromTo(false) }}"/>
        @endforeach
    @endif

    @if (count($place->opening_hours->special) > 0)
        <hr>
        <h3>{{ __('Special hours') }}</h3>
        @foreach ($place->opening_hours->special as $special_hour)
            <p><strong>{{ $weekdays[$special_hour->weekday] }} {{ $special_hour->date }}:</strong> {{ $special_hour->timeFromTo(false) }}</p>
            <meta itemprop="openingHours" content="{{ $special_hour->date }} {{ $special_hour->timeFromTo(false) }}"/>
        @endforeach
    @endif
</article>
@endsection
