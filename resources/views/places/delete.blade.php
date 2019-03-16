@extends('partials.master')

@section('content')
<h1>{{ __('Delete place') }}</h1>

<form class="form-place" action="{{ route('places.destroy', $place) }}" method="post">
    @csrf
    @method('delete')

    <fieldset>
        <legend>{{ __('Are you sure?') }}</legend>
        <p>{{ __('Do you really want to delete :place?', ['place' => $place->name]) }}</p>

        <div class="form-group">
            <a href="{{ URL::previous() }}">{{ __('No, cancel') }}</a>
        </div>

        <div class="form-group">
            <button type="submit">{{ __('Yes, delete') }}</button>
        </div>
    </fieldset>
</form>
@endsection
