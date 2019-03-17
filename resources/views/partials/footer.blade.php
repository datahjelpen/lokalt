<footer id="footer-main">
    <div id="footer-main-wrapper">
        <h4>{{ config('app.name') }} — en tjeneste levert av <a href="https://datahjelpen.no" rel="noopener" class="datahjelpen">Datahjelpen</a></h4>
        <p>
            <a href="https://datahjelpen.no/personvern" rel="noopener" >Personvern</a>
            <span>|</span>
            <a href="{{ route('login') }}">{{ __('Login') }}</a>
            @if (Route::has('register'))
                <span>|</span>
                <a href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
            <span>|</span>
            <a href="{{ route('dashboard.index') }}">{{ __('My places') }}</a>
            <span>|</span>
            <a href="{{ route('user.show') }}">{{ __('My profile') }}</a>
        </p>
        <p class="copyright">&copy; {{ __('Copyright') }} {{ date('Y') }} — Datahjelpen AS</p>
    </div>
</footer>
