
<style>
    .cookie-btn {
        color: #fff!important;
        background: #ee2e24;
    }
</style>

<div class="container fixed-bottom">
    <div class="alert bg-black js-cookie-consent cookie-consent text-center">

        <span class="text-white cookie-consent__message">
            {!! trans('cookieConsent::texts.message') !!}
        </span>

        <button class="btn cookie-btn js-cookie-consent-agree cookie-consent__agree ">
            {{ trans('cookieConsent::texts.agree') }}
        </button>
    </div>
</div>
