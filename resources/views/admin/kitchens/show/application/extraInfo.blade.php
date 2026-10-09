<hr>
<p class="title is-5">@lang('kitchen/extraInfo.dimensionsTab')</p>
@include('kitchen.extraInfo.dimensions', ['readonly' => true])
<hr>
<p class="title is-5">@lang('kitchen/extraInfo.equipmentTab')</p>
@include('kitchen.extraInfo.equipment', ['readonly' => true])
<hr>
<p class="title is-5">@lang('kitchen/extraInfo.menuTab')</p>
@include('kitchen.extraInfo.menu', ['readonly' => true])
<hr>
<p class="title is-5">@lang('kitchen/extraInfo.otherTab')</p>
@include('kitchen.extraInfo.other', ['readonly' => true])
