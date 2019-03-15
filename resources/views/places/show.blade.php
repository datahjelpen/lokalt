<h1>{{ $place->name }}</h1>
<p>{{ $place->description }}</p>
<p>
    <span>{{ $place->address->street_name_number }},</span>
    <span>{{ $place->address->postal_code }} {{ $place->address->postal_city }}</span>
</p>
<ul>
    <li>{{ $place->website }}</li>
    <li>{{ $place->phone }}</li>
    <li>{{ $place->email }}</li>
</ul>
<h2>{{ __('Opening hours') }}</h2>
@php
    $i = 0;
@endphp
@foreach ($weekdays as $weekday)
@php
    $i++;
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
@endforeach
@auth
    @if ($place->userHasAccess($user))
        <a href="{{ route('places.edit', $place) }}">Rediger</a>
        <a href="{{ route('places.delete', $place) }}">Slett</a>
    @endif
@endauth
