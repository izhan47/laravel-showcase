@extends('admin.layouts.admin')

@push("styles")
<link href="{{ asset('plugins/iCheck/custom.css') }}" rel="stylesheet">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $singular_module_name }}</title>
@endpush


@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    {!! Form::open(['url' => $module_route, 'method' => 'POST', "enctype"=>"multipart/form-data",'class'=>'form-horizontal','id'=>'form_validate', 'autocomplete'=>'off']) !!}

    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            <a href="{{ url('admin/pet-pros/'.$pet_pro_id.'/edit') }}" class="wag-go-back-btn-main">Go Back</a>
        </div>
        <div class="wag-title-and-nemu-block-main">
            <button id="submitFormBtn" class="wag-admin-btns-main" type="submit">Save</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="wag-inner-page-main-section">
                @if(isset($singular_module_name))
                    <h2 class="wag-admin-page-title-main">Add {{  $singular_module_name }}</h2>
                @endif                
                <div class="wag-inner-section-block-main">
                    <div id="clon-add-gallery-image-div" class="main-gallery-image-div d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group hs-user upload-image-div">
                                    <label>Upload Gallery Image</label>
                                    <input type="hidden" name="row[0][cropped_image]" class="cropped_image" value="" style="display:none" />
                                    <div class="">
                                        {{ Form::radio('is_cover_image', 0, false, ['class' => 'is_cover_image']) }}              
                                        <label for="is_cover_image">&nbsp;Cover Image</label>
                                    </div>
                                    <label for="image_0" class="btn upload-img-btn">Upload</label>            
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
                            </div>
                            <div class="col-sm-7 tc-crop-img-section" style="display: none; text-align:center;">
                                <div id="upload-demo-0" class="upload-demo"></div>
                                <button type="button" class="wag-admin-btns-main upload-image">Crop Image</button>
                            </div>
                        </div>
                    </div>
                    <div id="add-new-gallery-image-div"></div>
                    <button class="btn btn-info" type="button" id="add-gallery-image">ADD +</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.form.js') }} "></script>
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>

<link href="{{ asset('plugins/croppie/croppie.min.css') }}" rel="stylesheet">
<script src="{{ asset('plugins/croppie/croppie.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var rowID = 1;

        var cloneDiv = $( "#clon-add-gallery-image-div" ).clone().removeClass('d-none').removeAttr('id');
        cloneDiv.find('.upload-image-input ').attr('name', 'row['+rowID+'][image]');
        cloneDiv.find('.cropped_image ').attr('name', 'row['+rowID+'][cropped_image]');
        cloneDiv.find('.upload-img-btn').attr('for', 'image_'+rowID);              
        cloneDiv.find('.upload-image-input').attr('id', 'image_'+rowID);         
        cloneDiv.find('.upload-demo').attr('id', 'upload-demo_'+rowID);         
        cloneDiv.find('.is_cover_image').val(rowID);
        rowID++;         
        cloneDiv.appendTo( "#add-new-gallery-image-div" );  

        $("#add-gallery-image").click(function () {                       
            var cloneDiv = $( "#clon-add-gallery-image-div" ).clone().removeClass('d-none').removeAttr('id');
            cloneDiv.find('.upload-image-input ').attr('name', 'row['+rowID+'][image]');
            cloneDiv.find('.cropped_image ').attr('name', 'row['+rowID+'][cropped_image]');
            cloneDiv.find('.upload-img-btn').attr('for', 'image_'+rowID);              
            cloneDiv.find('.upload-image-input').attr('id', 'image_'+rowID);         
            cloneDiv.find('.upload-demo').attr('id', 'upload-demo_'+rowID);         
            cloneDiv.find('.is_cover_image').val(rowID);
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
        $('form').ajaxForm({
            beforeSend: function() {
                var percentVal = 'Uploading (0%)';                    
                $("#submitFormBtn").html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                if( percentComplete < 99.99 ) {
                    var percentVal = "Uploading ("+ percentComplete + '%)';
                    $("#submitFormBtn").html(percentVal);                
                }
            },
            complete: function(xhr) {
                $("#submitFormBtn").html("Uploading (100%)");                
                if(xhr.status === 200  ) {                
                    $("#submitFormBtn").html('Save'); 
                    fnToastSuccess(xhr.responseJSON["message"]);
                } else {
                    fnToastError(xhr.responseJSON["message"]);
                }
                setTimeout(() => {                                            
                    window.location.href = "{!! url("admin/pet-pros/".$pet_pro_id.'/edit') !!}";         
                }, 1000);
            },
            error:  function(xhr, desc, err) {
                fnToastError(err);
                console.debug(xhr);
                console.log("Desc: " + desc + "\nErr:" + err);
            }
        });    
    });
</script>
@endpush
