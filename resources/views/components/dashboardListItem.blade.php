<list-section label="{{$label}}">
    @foreach($items as $text => $link)
        <li><a href="{{$link}}">{{$text}}</a></li>
    @endforeach
</list-section>
