<services-form url="{{ action('Admin\ApplicationController@updateServices', $application) }}">
    @foreach($application->services as $service)
        @include('admin.kitchens.show.application.partials.service', [
            'service' => $service,
            'quantity' => $service->pivot->quantity ?? 0,
            'checkedBasePrice' => $service->pivot->quantity > 0
                && $service->pivot->equivalent_price == $service->price,
            'checkedCondition' => fn($price) =>
                $service->pivot->quantity > 0
                && $service->pivot->equivalent_price == $price,
            'decimalPoint' => $decimalPoint,
            'thousandSeparator' => $thousandSeparator
        ])
    @endforeach


    @foreach($services->whereNotIn('id',$application->services->pluck('id')) as $service)
        @include('admin.kitchens.show.application.partials.service', [
            'service' => $service,
            'quantity' => 0,
            'checkedBasePrice' => false,
            'checkedCondition' => fn($price) => false,
            'decimalPoint' => $decimalPoint,
            'thousandSeparator' => $thousandSeparator
        ])
    @endforeach
</services-form>
