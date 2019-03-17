@extends('partials.master')

@section('content')
<h1>{{ __('Admin dashboard') }}</h1>
<nav>
    <a href="{{ route('admin.users.create') }}">Create a user</a>
</nav>

<form action="{{ route('admin.places.search') }}" method="post">
    @csrf

    <fieldset>
        <legend>Search for place</legend>
        <div class="form-group">
            <label for="search">Name of place</label>
            <input type="search" name="search" id="search" autocomplete="organization">
        </div>
        <div class="form-group form-group-actions">
            <span></span>
            <button>Search</button>
        </div>
    </fieldset>
</form>

@foreach ($places as $place)
    <article itemscope itemtype="http://schema.org/LocalBusiness">
        <a href="{{ route('admin.places.edit', $place) }}" class="place-link">
            <fieldset>
                <legend itemprop="name">
                    {{ $place->name }}
                </legend>
                <p class="about" itemprop="disambiguatingDescription">{{ $place->description_short }}</p>
            </fieldset>
        </a>
    </article>
@endforeach
@endsection
