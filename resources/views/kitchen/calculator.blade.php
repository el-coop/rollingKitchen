<div class="hero">
    <div class="hero-body">
        {{app('settings')->get('application_calculator_model_text_' . App::getLocale())}}
    </div>
</div>
<fee-calculator ref="calculator" :init-services="{{json_encode($kitchen->servicesCalculationTable)}}">
</fee-calculator>
