@extends('partials.master')

@section('content')
<h1>{{ $place->name }}</h1>
<ul>
    @if ($place->website)
        <li class="url"><a href="{{ $place->website }}" target="_blank" ref="noopener nofollow noreferrer">{{ $place->website }}</a></li>
    @endif
    @if ($place->phone)
        <li class="phone"><a href="tel:{{ $place->phone }}">{{ $place->phone }}</a></li>
    @endif
    @if ($place->email)
        <li class="email"><a href="mailto:{{ $place->email }}">{{ $place->email }}</a></li>
    @endif
    <li class="address">
        <a href="{{ url('https://www.google.com/maps/search/' .  $place->address->street_name_number . ' ' . $place->address->postal_code . ' ' . $place->address->postal_city) }}"
            target="_blank"
            ref="noopener nofollow noreferrer"
        >
            <span>{{ $place->address->street_name_number }},</span>
            <span>{{ $place->address->postal_code }} {{ $place->address->postal_city }}</span>
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
    @endforeach
@endif

@if (count($place->opening_hours->special) > 0)
    <hr>
    <h3>{{ __('Special hours') }}</h3>
    @foreach ($place->opening_hours->special as $special_hour)
        <p><strong>{{ $weekdays[$special_hour->weekday] }} {{ $special_hour->date }}:</strong> {{ $special_hour->timeFromTo(false) }}</p>
    @endforeach
@endif


{{--
@foreach ($weekdays as $weekday)
@php
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
<p>
    <strong>{{ $weekday }}</strong>
    @if ($day_is_open)
        <span>{{ $open_from }}</span>
        <span>—</span>
        <span>{{ $open_to }}</span>
    @else
        <span>Stengt</span>
    @endif
</p>
@endforeach --}}
@endsection
