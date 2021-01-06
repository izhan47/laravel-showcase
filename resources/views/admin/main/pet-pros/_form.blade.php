@push("styles")
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/timepicker/jquery-clockpicker.min.css') }}">

<style>
    .col-form-label {
        padding-left: 0;
        padding-right: 0;
    }

    .add-service-offered-btn { 
        color: #6161FF;
        cursor: pointer;
    }
    .delete-service-div {
        margin: auto;
    }
    .delete-service-btn, .delete-old-service-btn{
        cursor: pointer;        
    }

</style>
@endpush
<input type="hidden" name="deletedServices" id="deletedServices">
<input type="hidden" name="deletedGallery" id="deletedGallery">

<div class="row">
    <div class="col-md-6">
        <div class="wag-inner-section-block-main">
            <h2 class="wag-admin-page-title-main">General Info</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Store Name *</label>
                        {{ Form::text('store_name', null, ['id' => 'store_name', 'class'=>"form-control"]) }}
                        @if($errors->has('store_name'))
                            <p class="text-danger">{{ $errors->first('store_name') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Website URL</label>
                        {{ Form::text('website_url', null, ['id' => 'website_url', 'class'=>"form-control"]) }}
                        @if($errors->has('website_url'))
                            <p class="text-danger">{{ $errors->first('website_url') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Email</label>
                        {{ Form::email('email', null, ['id' => 'email', 'class'=>"form-control"]) }}
                        @if($errors->has('email'))
                            <p class="text-danger">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        {{ Form::text('phone_number', null, ['id' => 'phone_number', 'class'=>"form-control"]) }}
                        @if($errors->has('phone_number'))
                            <p class="text-danger">{{ $errors->first('phone_number') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Address Line 1</label>
                        {{ Form::text('address_line_1', null, ['id' => 'address_line_1', 'class'=>"form-control"]) }}
                        @if($errors->has('address_line_1'))
                            <p class="text-danger">{{ $errors->first('address_line_1') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Address Line 2</label>
                        {{ Form::text('address_line_2', null, ['id' => 'address_line_2', 'class'=>"form-control"]) }}
                        @if($errors->has('address_line_2'))
                            <p class="text-danger">{{ $errors->first('address_line_2') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Postal Code</label>
                        {{ Form::text('postal_code', null, ['id' => 'postal_code', 'class'=>"form-control"]) }}
                        @if($errors->has('postal_code'))
                            <p class="text-danger">{{ $errors->first('postal_code') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>State</label>
                        {{ Form::select('state_id', [ "" => "Select"] + $states, null, ['id'=> 'state_id', 'class' => 'form-control']) }}
                        @if($errors->has('state_id'))
                            <p class="text-danger">{{ $errors->first('state_id') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>City</label>
                        {{ Form::select('city_id', [ "" => "Select"] + $cities, null, ['id'=> 'city_id', 'class' => 'form-control']) }}
                        @if($errors->has('city_id'))
                            <p class="text-danger">{{ $errors->first('city_id') }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Description</label>
                        {{ Form::textarea('description', null, ['class'=>"form-control", "rows" => 7]) }}
                        @if($errors->has('description'))
                            <span class="help-block m-b-none">{{ $errors->first('description') }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group wag-categories-box-main">
                        <label>Categories *</label>
                        {{ Form::select('category_id[]', $categories, ( isset($selectedCategories) && count($selectedCategories))?$selectedCategories:null, ['id'=> 'category_id', 'class' => 'form-control', 'multiple' => 'multiple']) }}
                        @if($errors->has('category_id'))
                            <p class="text-danger">{{ $errors->first('category_id') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="wag-inner-section-block-main">
            <h2 class="wag-admin-page-title-main">Hours of Operation</h2>
            <div class="wag-hours-block">
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="monday" id="monday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Monday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('monday_open',  isset($result) && isset($result->formatted_timetable["monday_open"]) ? $result->formatted_timetable["monday_open"] : null , ['id' => 'monday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('monday_open'))
                                    <p class="text-danger">{{ $errors->first('monday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Monday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('monday_close',  isset($result) && isset($result->formatted_timetable["monday_close"]) ? $result->formatted_timetable["monday_close"] : null , ['id' => 'monday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('monday_close'))
                                    <p class="text-danger">{{ $errors->first('monday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="tuesday" id="tuesday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tuesday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('tuesday_open',  isset($result) && isset($result->formatted_timetable["tuesday_open"])  ? $result->formatted_timetable["tuesday_open"] : null , ['id' => 'tuesday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('tuesday_open'))
                                    <p class="text-danger">{{ $errors->first('tuesday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tuesday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('tuesday_close',  isset($result) && isset($result->formatted_timetable["tuesday_close"]) ? $result->formatted_timetable["tuesday_close"] : null , ['id' => 'tuesday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('tuesday_close'))
                                    <p class="text-danger">{{ $errors->first('tuesday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="wednesday" id="wednesday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Wednesday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('wednesday_open',  isset($result) && isset($result->formatted_timetable["wednesday_open"]) ? $result->formatted_timetable["wednesday_open"] : null , ['id' => 'wednesday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('wednesday_open'))
                                    <p class="text-danger">{{ $errors->first('wednesday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Wednesday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('wednesday_close',  isset($result) && isset($result->formatted_timetable["wednesday_close"]) ? $result->formatted_timetable["wednesday_close"] : null , ['id' => 'wednesday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('wednesday_close'))
                                    <p class="text-danger">{{ $errors->first('wednesday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="thursday" id="thursday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Thursday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('thursday_open',  isset($result) && isset($result->formatted_timetable["thursday_open"])  ? $result->formatted_timetable["thursday_open"] : null , ['id' => 'thursday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('thursday_open'))
                                    <p class="text-danger">{{ $errors->first('thursday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Thursday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('thursday_close',  isset($result) && isset($result->formatted_timetable["thursday_close"]) ? $result->formatted_timetable["thursday_close"] : null , ['id' => 'thursday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('thursday_close'))
                                    <p class="text-danger">{{ $errors->first('thursday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="friday" id="friday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Friday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('friday_open',  isset($result) && isset($result->formatted_timetable["friday_open"]) ? $result->formatted_timetable["friday_open"] : null , ['id' => 'friday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('friday_open'))
                                    <p class="text-danger">{{ $errors->first('friday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Friday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('friday_close',  isset($result) && isset($result->formatted_timetable["friday_close"]) ? $result->formatted_timetable["friday_close"] : null , ['id' => 'friday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('friday_close'))
                                    <p class="text-danger">{{ $errors->first('friday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="saturday" id="saturday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Saturday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('saturday_open',  isset($result) && isset($result->formatted_timetable["saturday_open"]) ? $result->formatted_timetable["saturday_open"] : null , ['id' => 'saturday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('saturday_open'))
                                    <p class="text-danger">{{ $errors->first('saturday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Saturday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('saturday_close',  isset($result) && isset($result->formatted_timetable["saturday_close"]) ? $result->formatted_timetable["saturday_close"] : null , ['id' => 'saturday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('saturday_close'))
                                    <p class="text-danger">{{ $errors->first('saturday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <input type="checkbox" class="hoursOfOperation" name="sunday" id="sunday_checked">
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Sunday Open</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('sunday_open',  isset($result) && isset($result->formatted_timetable["sunday_open"]) ? $result->formatted_timetable["sunday_open"] : null , ['id' => 'sunday_open', 'class'=>"form-control"]) }}
                                @if($errors->has('sunday_open'))
                                    <p class="text-danger">{{ $errors->first('sunday_open') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Sunday Close</label>
                            <div class="clockpicker" data-autoclose="true">
                                {{ Form::text('sunday_close',  isset($result) && isset($result->formatted_timetable["sunday_close"]) ? $result->formatted_timetable["sunday_close"] : null , ['id' => 'sunday_close', 'class'=>"form-control"]) }}
                                @if($errors->has('sunday_close'))
                                    <p class="text-danger">{{ $errors->first('sunday_close') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wag-inner-section-block-main">
            <div class="wag-donation-link-header-bar">
                <h2 class="wag-admin-page-title-main">Donation Link</h2>
                <div class="">
                    <button class="wag-admin-btns-main" type="button" id="add_donation_link">{{ isset($result) && isset($result->donation_link) ? 'Edit Link' : 'Add New +' }}</button>
                    <button class="wag-admin-btns-main d-none" type="button" id="edit_donation_link">Edit</button>
                </div>
            </div>
            <div class="row d-none" id="donation_link_div">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::text('donation_link', null, ['id' => 'donation_link', 'class'=>"form-control"]) }}
                        @if($errors->has('donation_link'))
                            <p class="text-danger">{{ $errors->first('donation_link') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="wag-inner-section-block-main">
            <div class="wag-donation-link-header-bar">
                <h2 class="wag-admin-page-title-main">Featured Pet Pros</h2>
                <div class="">
                    {{ Form::checkbox('is_featured_pet_pro', 1, (isset($result) && isset($result->is_featured_pet_pro) && $result->is_featured_pet_pro == 1) ?  true : false, ['id' => 'is_featured_pet_pro']) }}
                </div>
            </div>
            <div class="row {{ (isset($result) && isset($result->is_featured_pet_pro) && $result->is_featured_pet_pro == 1) ? '' : 'd-none'}}" id="featured_section">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Featured Title</label>
                        {{ Form::text('featured_title', null, ['id' => 'featured_title', 'class'=>"form-control"]) }}
                        @if($errors->has('featured_title'))
                            <p class="text-danger">{{ $errors->first('featured_title') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-sm-12 control-wrapper">
                    <div class="form-group">
                        <label>Featured Description</label>
                        {{ Form::textarea('featured_description', null, ['id' => 'featured_description', "data-limit" => 200, 'class'=>"form-control", "maxlength" => 200, 'rows' => 3]) }}
                        <p class="text-info text-right remaining-text-countdown"></p>
                        @if($errors->has('featured_description'))
                            <p class="text-danger">{{ $errors->first('featured_description') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="wag-inner-section-block-main">
            <div class="wag-donation-link-header-bar">
                <h2 class="wag-admin-page-title-main">Services Offered</h2>                
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="row clone-service-div mb-1 d-none" id="clone-service-div">
                            <div class="col-10">
                                {{ Form::text('services', null, ['class'=>"form-control services"]) }}
                            </div>
                            <div class="col-2 delete-service-div">
                                <div class="text-danger delete-service-btn">Delete</div>                                
                            </div>
                        </div>
                        @if( isset($result) )
                            @foreach( $result->servicesOffered as $old_service )
                                <div class="row clone-service-div mb-1" data-serviceid="{{ $old_service->id}}">
                                    <div class="col-10">
                                        {{ Form::text('old_services['. $old_service->id .']', $old_service->service, ['class'=>"form-control services", "required" => "required"]) }}
                                    </div>
                                    <div class="col-2 delete-service-div">
                                        <div class="text-danger delete-old-service-btn">Delete</div>                                
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div id="append-service-div"></div>

                        <div class="add-service-offered-btn mt-2" id="add-service-offered">+ Add Services Offered</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wag-inner-page-main-section">
    <div class="wag-inner-section-block-main">
        <div class="wag-donation-link-header-bar">
            <h2 class="wag-admin-page-title-main">Gallery</h2>           
        </div>
        <div class="wag-gallery-add-images-block-main">
            @if( isset($result) )
                @foreach($result->images as $image)
                    <div class="main-gallery-image-div" data-galleryid="{{ $image->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group hs-user upload-image-div">                          
                                    <input type="hidden" name="{{ "old_row[".$image->id."][cropped_image]" }}" class="cropped_image" value="" style="display:none" />

                                    <label for="{{ "old_image_".$image->id }}"  class="btn upload-img-btn">Edit</label>
                                    <label class="btn delete-img-btn delete-old-gallery-image">Delete</label>

                                    <label class="img-name-lbl ml-3 d-none"></label><br />
                                    {{ Form::file("old_row[".$image->id."][image]", ['id' => "old_image_".$image->id, 'class'=>"form-control upload-old-image-input d-none", "accept" => ".png, .jpg, .jpeg"]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row hs-user ">
                            <div class="col-md-5">
                                <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px;">
                                    <img src="{{ $image->image_thumb_full_path }}" class="img-thumbnail">
                                </div>                           
                                <div class="mt-1">
                                    {{ Form::radio('is_cover_image', 'old_'.$image->id, (isset($image) && isset($image->is_cover_image) && $image->is_cover_image == 1) ?  true : false, ['id' => 'is_cover_image_'.$image->id]) }}              
                                    <label for={{ "is_cover_image_".$image->id }}>&nbsp;Cover Image</label>
                                </div>
                            </div>
                            <div class="col-sm-7 tc-crop-img-section" style="display: none; text-align:center;">
                                <div id="upload-demo" class="upload-demo"></div>
                                <button class="wag-admin-btns-main upload-image">Crop Image</button>
                            </div>
                        </div>
                    </div>
                    
                @endforeach
            @endif

        </div>
        <div id="clon-add-gallery-image-div" class="main-gallery-image-div d-none">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group hs-user upload-image-div">                        
                        <input type="hidden" name="row[0][cropped_image]" class="cropped_image" value="" style="display:none" />
                        <label for="image_0" class="btn upload-img-btn">Upload</label>
                        <label class="btn delete-img-btn delete-gallery-image">Delete</label>
                        <label class="img-name-lbl ml-3 d-none"></label><br />
                        {{ Form::file("row[0][image]", ['id' => 'image_0', 'class'=>"form-control upload-image-input d-none", "accept" => ".png, .jpg, .jpeg"]) }}
                        @if($errors->has('image'))
                            <p class="text-danger">{{ $errors->first('testimonial') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group row hs-user ">
                <div class="col-md-5">
                    <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px; display: none;">
                        <img src="" class="img-thumbnail" style="display: none;">
                    </div>
                    <div class="">
                        {{ Form::radio('is_cover_image', 0, false, ['class' => 'is_cover_image']) }}
                        <label for="is_cover_image" class="is_cover_image_label">&nbsp;Cover Image</label>
                    </div>
                </div>
                <div class="col-sm-7 tc-crop-img-section" style="display: none; text-align:center;">
                    <div id="upload-demo-0" class="upload-demo"></div>
                    <button type="button" class="wag-admin-btns-main upload-image">Crop Image</button>
                </div>
            </div>
        </div>
        <div id="add-new-gallery-image-div"></div>
        <div class="wag-gallery-btns-block text-left">
            <button class="wag-admin-btns-main " type="button" id="add-gallery-image">Add +</button>
        </div>
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src="{{ asset('plugins/timepicker/bootstrap-clockpicker.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var selectedStateId = '';
        var selectedCityId = '';        
        var monday_checked , tuesday_checked , wednesday_checked , thursday_checked ,friday_checked , saturday_checked , sunday_checked ;

        monday_checked = "{!! isset($result->formatted_timetable["monday_open"]) ? $result->formatted_timetable["monday_open"] : '' !!}" ? true : false;
        tuesday_checked = "{!! isset($result->formatted_timetable["tuesday_open"]) ? $result->formatted_timetable["tuesday_open"] : '' !!}" ? true : false;
        wednesday_checked = "{!! isset($result->formatted_timetable["wednesday_open"]) ? $result->formatted_timetable["wednesday_open"] : '' !!}" ? true : false;
        thursday_checked = "{!! isset($result->formatted_timetable["thursday_open"]) ? $result->formatted_timetable["thursday_open"] : '' !!}" ? true : false;
        friday_checked = "{!! isset($result->formatted_timetable["friday_open"]) ? $result->formatted_timetable["friday_open"] : '' !!}" ? true : false;
        saturday_checked = "{!! isset($result->formatted_timetable["saturday_open"]) ? $result->formatted_timetable["saturday_open"] : '' !!}" ? true : false;
        sunday_checked = "{!! isset($result->formatted_timetable["sunday_open"]) ? $result->formatted_timetable["sunday_open"] : '' !!}" ? true : false;

        var days_checked_name = [monday_checked, tuesday_checked, wednesday_checked, thursday_checked, friday_checked, saturday_checked, sunday_checked];
        var days_name = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

        for (var i = 0; i < 7; i++) {
            if( days_checked_name[i]) {
                $("#"+days_name[i]+"_checked").attr('checked', true);
            } else {
                $("#"+days_name[i]+"_open").prop('disabled', true);
                $("#"+days_name[i]+"_close").prop('disabled', true);
            }
        }

        $(".hoursOfOperation").change(function() {
            if(this.checked) {
                $("#"+this.name+"_open").prop('disabled', false);
                $("#"+this.name+"_close").prop('disabled', false);
            } else {
                $("#"+this.name+"_open").prop('disabled', true).val('');
                $("#"+this.name+"_close").prop('disabled', true).val('');
            }
        });

        $('.clockpicker').clockpicker({
			twelvehour: true,
			donetext: 'Done'
		});

		$('#state_id').select2({
			tags: false,
		});

		$('#city_id').select2({
			tags: false,
		});

		$('#category_id').select2({
			tags: false,
			placeholder: 'Select categories'
		});

        $("#postal_code").change(function(){
            var data = $(this).val();   
            if( data ) {            
                $.ajax({
                    url:  "{{ url('admin/pet-pros/get-geocode-data') }}",
                    type: "get",
                    data: {                    
                        'postal_code': $.trim($('#postal_code').val()),
                    },
                    success: function(data){      
                        if( data.data ) {                       
                            selectedStateId = data.data.state_id;
                            selectedCityId = data.data.city_id;
                            getStateList();                                            
                        }
                    },
                    error:function (error) {
                        console.log(error);
                    }
                });
            }
        });

        $("#state_id").change(function () {            
            if( $(this).val() ) {
                getCityList($(this).val());
            }
        });

        function getStateList() {
            $('#state_id').empty().trigger("change");
            var newStateOption = new Option('Loading..', '', false, false);
            $('#state_id').append(newStateOption).trigger('change');
            $.ajax({
                url:  "{{ url('admin/pet-pros/get-states') }}",
                type: "get",
                data: {},
                success: function(data){
                    $('#state_id').empty().trigger("change");
                    var newStateOption = new Option('Select', '', false, false);
                    $('#state_id').append(newStateOption).trigger('change');
                    for (var j = 0; j < data.data.length; j++) {
                        $("<option/>").attr("value", data.data[j].id).text(data.data[j].name).appendTo($("#state_id"));
                    }
                    if( selectedStateId ) {
                        //$('#state_id').val(selectedStateId).trigger('change');
                    }
                }
            });

        }

        function getCityList(state_id) {
            $('#city_id').empty().trigger("change");
            var newOption = new Option('Loading..', '', false, false);
            $('#city_id').append(newOption).trigger('change');            
            $.ajax({
                url:  "{{ url('admin/pet-pros/get-cities') }}/"+state_id,
                type: "get",
                data: {},
                success: function(data){
                    $('#city_id').empty().trigger("change");
                    var newOption = new Option('Select', '', false, false);
                    $('#city_id').append(newOption).trigger('change');
                    for (var i = 0; i < data.data.length; i++) {
                        $("<option/>").attr("value", data.data[i].id).text(data.data[i].name).appendTo($("#city_id"));
                    }
                    if( selectedCityId ) {                                         
                        //$('#city_id').val($.trim(parseInt(selectedCityId))).trigger('change');
                    }
                }
            });
        }
    
		jQuery.validator.addMethod("greaterThan", function(value, element, param) {
			var stt = new Date("July 01, 2020 " + param.val());
			stt = stt.getTime();

			var endt = new Date("July 01, 2020 " + value);
			endt = endt.getTime();

			if(stt > endt){
				return false;
			} else {
				return true;
			}
        }, 'Please select greater time than opening time.');

        $("#form_validate").validate({
            ignore: [],
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                store_name: {
                    required: true,
                    maxlength: 255
                },
                email: {
                    //required: true,
                    email: true
                },
                website_url: {
                    url: true
                },
                phone_number: {
                    //required: true
                },
                address_line_1: {
                    maxlength: 255
                },
                address_line_2: {
                    maxlength: 255
                },
                'category_id[]': {
                    required: true
                },
                /*state_id: {
                    required: true
                },
                city_id: {
                    required: true
                },
                postal_code: {
                    required: true
                },*/
                donation_link: {
                    url: true
                },
                featured_title: {
                    required: {
                        depends: function() {
                            return ($('input[name=is_featured_pet_pro]:checked').val() == '1')  ? true : false;
                        }
                    }
                },
                featured_description: {
                    required: {
                        depends: function() {
                            return ($('input[name=is_featured_pet_pro]:checked').val() == '1')  ? true : false;
                        }
                    }
                },
                monday_open: {
                    required: {
                        depends: function() {
                            return ($('#monday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                monday_close: {
                    required: {
                        depends: function() {
                            return ($('#monday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#monday_open")
                },

                tuesday_open: {
                    required: {
                        depends: function() {
                            return ($('#tuesday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                tuesday_close: {
                    required: {
                        depends: function() {
                            return ($('#tuesday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#tuesday_open")
                },

                wednesday_open: {
                    required: {
                        depends: function() {
                            return ($('#wednesday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                wednesday_close: {
                    required: {
                        depends: function() {
                            return ($('#wednesday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#wednesday_open")
                },

                thursday_open: {
                    required: {
                        depends: function() {
                            return ($('#thursday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                thursday_close: {
                    required: {
                        depends: function() {
                            return ($('#thursday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#thursday_open")
                },

                friday_open: {
                    required: {
                        depends: function() {
                            return ($('#friday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                friday_close: {
                    required: {
                        depends: function() {
                            return ($('#friday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#friday_open")
                },

                saturday_open: {
                    required: {
                        depends: function() {
                            return ($('#saturday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                saturday_close: {
                    required: {
                        depends: function() {
                            return ($('#saturday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#saturday_open")
                },

                sunday_open: {
                    required: {
                        depends: function() {
                            return ($('#sunday_checked').prop("checked"))  ? true : false;
                        }
                    }
                },
                sunday_close: {
                    required: {
                        depends: function() {
                            return ($('#sunday_checked').prop("checked"))  ? true : false;
                        }
                    },
					greaterThan: $("#sunday_open")
                },

            },
			errorPlacement: function(error, element) {
				if (element.attr("id") == "city_id" || element.attr("id") == "state_id" || element.attr("id") == "category_id" ){
					error.appendTo(element.closest('.form-group'));
				}
				else {
					error.insertAfter(element);
				}
			}            
        });

        $("#submitFormBtn").click(function(e){ 
            e.preventDefault();
            if( $("#form_validate").valid() ){ 
                  $( "#form_validate" ).submit();
            }
        });

        $("#add_donation_link").click(function() {
            $("#donation_link_div").removeClass('d-none');
            $("#add_donation_link").addClass('d-none');

        });

        $('#is_featured_pet_pro').click (function () {
            if ($(this).is (':checked')) {
                $("#featured_section").removeClass('d-none');
            } else {
                $("#featured_section").addClass('d-none');
            }
        });

        $(document).on('keyup', '#featured_description', function(event) {
            event.preventDefault();
            var maxLimit = $(this).data("limit") || 0;
            var charCount = $(this).val().length || 0;
            var remainingChars = maxLimit - charCount;

            if(remainingChars < 0) {
                $(this).val(($(this).val()).substring(0, maxLimit));
            }
            else {
                var countdownElement = $(this).parents(".control-wrapper").find('.remaining-text-countdown');
                if(countdownElement.length > 0) {
                    countdownElement.html(remainingChars + "/" + maxLimit);
                }
            }
        });

        var servicesRowId = 1;
        var deletedServices = []; 
        $("#add-service-offered").click(function () {
            var cloneDiv = $( "#clone-service-div" ).clone().removeClass('d-none').removeAttr('id');
            cloneDiv.find('.services ').attr('name', 'services['+servicesRowId+']').attr('required', 'required');        
            servicesRowId++;
            cloneDiv.appendTo( "#append-service-div" );
        });

        $("body").delegate(".delete-service-btn", "click", function(){                    
            $(this).closest('.clone-service-div').remove();
        });

        $("body").delegate(".delete-old-service-btn", "click", function(){ 
            var serviceid = $(this).closest('.clone-service-div').attr('data-serviceid'); 
            deletedServices.push(serviceid); 
            $("#deletedServices").val(deletedServices); 
            $(this).closest('.clone-service-div').remove();
        });
        

    });
</script>

<link href="{{ asset('plugins/croppie/croppie.min.css') }}" rel="stylesheet">
<script src="{{ asset('plugins/croppie/croppie.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var rowID = 1;

       /*
        var cloneDiv = $( "#clon-add-gallery-image-div" ).clone().removeClass('d-none').removeAttr('id');
        cloneDiv.find('.upload-image-input ').attr('name', 'row['+rowID+'][image]');
        cloneDiv.find('.cropped_image ').attr('name', 'row['+rowID+'][cropped_image]');
        cloneDiv.find('.upload-img-btn').attr('for', 'image_'+rowID);
        cloneDiv.find('.upload-image-input').attr('id', 'image_'+rowID);
        cloneDiv.find('.upload-demo').attr('id', 'upload-demo_'+rowID);
        cloneDiv.find('.is_cover_image').val(rowID);
        rowID++;
        cloneDiv.appendTo( "#add-new-gallery-image-div" );
        */

        $("#add-gallery-image").click(function () {
            var cloneDiv = $( "#clon-add-gallery-image-div" ).clone().removeClass('d-none').removeAttr('id');
            cloneDiv.find('.upload-image-input ').attr('name', 'row['+rowID+'][image]');
            cloneDiv.find('.cropped_image ').attr('name', 'row['+rowID+'][cropped_image]');
            cloneDiv.find('.upload-img-btn').attr('for', 'image_'+rowID);
            cloneDiv.find('.upload-image-input').attr('id', 'image_'+rowID);
            cloneDiv.find('.upload-demo').attr('id', 'upload-demo_'+rowID);
            cloneDiv.find('.is_cover_image').attr('id', 'is_cover_image_'+rowID).val(rowID);
            cloneDiv.find('.is_cover_image_label').attr('for', 'is_cover_image_'+rowID);
            rowID++;
            cloneDiv.appendTo( "#add-new-gallery-image-div" );
        });

        $("body").delegate(".upload-image-input", "change", function(){
            var fileName = event.target.files[0].name;
            var mainDiv = $(this).closest('.main-gallery-image-div');
            $(this).closest('.upload-image-div').find(".img-name-lbl").html(fileName).removeClass('d-none');
            ;
            mainDiv.find('.hs-blog-img-preview').css("display", 'block');
            displayImageOnFileSelect(this,  mainDiv.find('.img-thumbnail'));

            if(! mainDiv.find('.upload-demo').data('croppie') ) {
                var resize = mainDiv.find('.upload-demo').croppie({
                    enableExif: true,
                    enableOrientation: true,
                    viewport: {
                        width: 100,
                        height: 100,
                        type: 'square'
                    },
                    boundary: {
                        width: 150,
                        height: 150
                    }
                });
            } else {
                var resize = mainDiv.find('.upload-demo');
            }


            var reader = new FileReader();
            reader.onload = function (e) {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(blob){
                    //console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
            mainDiv.find('.tc-crop-img-section').show();
        });

        $("body").delegate(".upload-old-image-input", "change", function(){
            var fileName = event.target.files[0].name;
            var mainDiv = $(this).closest('.main-gallery-image-div');           
            $(this).closest('.upload-image-div').find(".img-name-lbl").html(fileName).removeClass('d-none');
            ;
            mainDiv.find('.hs-blog-img-preview').css("display", 'block');
            displayImageOnFileSelect(this,  mainDiv.find('.img-thumbnail'));

            if(! mainDiv.find('.upload-demo').data('croppie') ) {
                var resize = mainDiv.find('.upload-demo').croppie({
                    enableExif: true,
                    enableOrientation: true,
                    viewport: {
                        width: 100,
                        height: 100,
                        type: 'square'
                    },
                    boundary: {
                        width: 150,
                        height: 150
                    }
                });
            } else {
                var resize = mainDiv.find('.upload-demo');
            }


            var reader = new FileReader();
            reader.onload = function (e) {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(blob){
                    //console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
            mainDiv.find('.tc-crop-img-section').show();
        });

        $("body").delegate(".upload-image", "click", function(ev){
            var mainDiv = $(this).closest('.main-gallery-image-div');
            mainDiv.find('.upload-demo').croppie('result',{circle: false, size: "original", type:"rawcanvas"}).then(function (rawcanv) {
                var img = rawcanv.toDataURL();
                mainDiv.find('.cropped_image').val(img.split(',')[1]);
                html = '<img src="' + img + '" class="img-thumbnail" />';
                mainDiv.find(".hs-blog-img-preview").html(html);
            });
            return false;
        });

        function displayImageOnFileSelect(input, thumbElement) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(thumbElement).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#form_validate").validate({
            ignore: [],
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                'row[1][image]': {
                    required: {{ (isset($result)) ? "false" : "true" }},

                },
            }
        });
        
        var deletedGallery = []; 
        $("body").delegate(".delete-old-gallery-image", "click", function(){ 
            var serviceid = $(this).closest('.main-gallery-image-div').attr('data-galleryid'); 
            deletedGallery.push(serviceid); 
            $("#deletedGallery").val(deletedGallery); 
            $(this).closest('.main-gallery-image-div').remove();            
        });

        $("body").delegate(".delete-gallery-image", "click", function(){            
            $(this).closest('.main-gallery-image-div').remove();           
        });




    });
</script>
@endpush
