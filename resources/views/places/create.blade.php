@extends('partials.master')

@section('content')
<h1>{{ __('Add a new place') }}</h1>

<form action="{{ route('places.store') }}" method="post">
    @csrf

    @include('places.form-fields')

    <div class="form-group">
        <button type="submit">{{ __('Save') }}</button>
    </div>
</form>
@endsection
