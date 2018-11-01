<aside class="menu">
    @foreach(config('navbar.items') as $key => $value)
        <p class="menu-label">
            {{$key}}
        </p>
        <ul class="menu-list">
            @foreach($value as $text => $link )
                <li><a href="{{$link}}">{{$text}}</a></li>
            @endforeach
        </ul>
    @endforeach
</aside>