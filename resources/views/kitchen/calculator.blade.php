<div class="hero">
    <div class="hero-body">
        {{app('settings')->get('application_calculator_model_text_' . App::getLocale())}}
    </div>
</div>
<fee-calculator ref="calculator" :init-services="{{json_encode($kitchen->servicesCalculationTable)}}"
                :init-sells-drinks="{{ json_encode((bool) old('sells_drinks', $application->sells_drinks)) }}"
                :drinks-fee="{{ json_encode((float) app('settings')->get('application_drinks_fee', 0)) }}">
</fee-calculator>
