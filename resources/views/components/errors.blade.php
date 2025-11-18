@if($errors->any())
    @foreach($errors->all() as $error)
        <toast message="{{$error}}"
               type="error"></toast>
    @endforeach
@endif
