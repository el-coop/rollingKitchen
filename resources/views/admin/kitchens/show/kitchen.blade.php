<div class="tile is-ancestor">
    <div class="tile is-parent">
        <div class="tile is-child">
            <dynamic-form :init-fields="{{ $kitchen->fulldata }}"
                          url="{{ action('Admin\KitchenController@update', $kitchen) }}"></dynamic-form>
        </div>
    </div>
    <div class="tile is-parent is-vertical">
        <div class="tile is-child">
            <p class="title">
                @lang('global.photos')
            </p>
            <carousel ref="carousel" :photos="{{$kitchen->photos}}"></carousel>
            <image-manager @image-deleted="(e) => this.$refs.carousel.removeImage(e)"
                           @image-uploaded="(e) => this.$refs.carousel.addImage(e)"
                           url="{{ action('Kitchen\KitchenController@storePhoto', $kitchen) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $kitchen->photos }}" delete-url="/kitchen/{{ $kitchen->id }}/photo">
            </image-manager>
        </div>
    </div>
</div>
