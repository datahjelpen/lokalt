@extends('partials.master')

@section('content')

<h1>{{ __('Register') }}</h1>
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    @include('auth.register-fields')

    <div class="form-group">
        <button type="submit">
            {{ __('Register') }}
        </button>
    </div>
</form>

@endsection
