<div class="notification">
    <div class="level">
        <div class="level-left flex-shrink-fix">
            <div class="level-item flex-shrink-fix">
                {!!  str_replace(PHP_EOL,'<br>',$message) !!}
            </div>
        </div>
        <div class="level-right flex-shrink-fix is-hidden-touch">
            <div class="level-item flex-shrink-fix">
                <figure class="image is-64x64 is-right">
                    <img src="{{ asset('storage/images/logo.png') }}">
                </figure>
            </div>
        </div>
    </div>
    @if(count($pdfs))
        <hr>
        <b>@lang('kitchen/kitchen.pdfsTitle'):</b>
        <ul>
            @foreach($pdfs as $pdf)
                <li><a href="{{ action('Band\BandController@showPdf', $pdf) }}">{{$pdf->name}}</a></li>
            @endforeach
        </ul>
    @endif
</div>
