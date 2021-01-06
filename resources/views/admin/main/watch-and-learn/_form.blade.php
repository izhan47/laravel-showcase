@push("styles")
<!-- summernote -->
<link href="{{ asset('plugins/summernote/summernote.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/summernote/summernote-bs3.css') }}" rel="stylesheet">
 <style>   
    
    .core_edittable .referral_ftrbtn *{
      width: 149px!important;
      margin-right: 9px;
    }
    .core_edittable .referral_ftrbtn *:last-child {
      float: unset;
    }
    .editorbox .col-sm-10{
        width: 70% !important;
    }
    .editorbox .col-sm-10 .note-editor{
        border: 1px solid #dddfe1;
        border-radius: 5px;
        overflow: hidden;
    }

    @media screen and (max-width:768px){
        .editorbox .col-sm-10{width: 100% !important;}
    }
    
    .close {
      order: 2;
    }
    textarea{
        overflow: hidden !important;    
    }


    /* 
    .hs-blog-img-preview {
        width: 100%;
    }
    
    #preview-company_logo {
        height: 100px;
        width: 100px;
    }
    
    .img-thumbnail {
        height: 150px;
        width: 150px;
    }     
    */
</style>
@endpush
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Categories</label>
            {{ Form::select('category_id', [ "" => "Select"] + $categories, null, ['id'=> 'category_id', 'class' => 'form-control']) }}
            @if($errors->has('category_id'))
                <p class="text-danger">{{ $errors->first('category_id') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label>Title</label>
            {{ Form::text('title', null, ['id' => 'title', 'class'=>"form-control"]) }}
            @if($errors->has('title'))
                <p class="text-danger">{{ $errors->first('title') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label class="col-form-label">Author</label>
            {{ Form::select('author_id', [ "" => "Select"] + $authors, null, ['id'=> 'author_id', 'class' => 'form-control']) }}
            @if($errors->has('author_id'))
                <p class="text-danger">{{ $errors->first('author_id') }}</p>
            @endif
        </div>
    </div>
</div>
@if(isset($result['id']))
    <!-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Description (HTML)</label>
                
                    <a class="btn-sm wag-admin-btns-main h-25" href="{{ $module_route .'/'. $result['id'] .'/edit/buildwithcontentbuilder' }}" target="_blank">Edit with content builder</a>
    
                {{ Form::textarea('description', null, ['class'=>"form-control"]) }}
                <small id="description_help" class="form-text text-muted">Please note that any HTML (including any JS code) that is entered here will be echoed (without escaping)</small>
                @if($errors->has('description'))
                    <span class="help-block m-b-none text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div>
        </div>
    </div> -->
@endif
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Blog Meta Description</label>
            {{ Form::textarea('blog_meta_description', null, ['id' => 'blog_meta_description', 'class'=>"form-control", 'rows' => 1]) }}
            @if($errors->has('blog_meta_description'))
                <p class="text-danger">{{ $errors->first('blog_meta_description') }}</p>
            @endif
        </div>
        <div class="form-group">
            <div class="hs-user">
                <label>Thumbnail</label><br />
				<label for="image" class="btn upload-img-btn">Upload</label>
				<label class="img-name-lbl ml-3 d-none"></label><br />
                <input type="hidden" name="cropped_image" id="cropped_image" value="" style="display:none" />
                {{ Form::file('image', ['id' => 'image', 'class'=>"form-control d-none", "accept" => ".png, .jpg, .jpeg"]) }}
                @if($errors->has('image'))
                    <p class="text-danger">{{ $errors->first('testimonial') }}</p>
                @endif
            </div>
        </div>
        <div class="form-group">
            <div class="hs-user">
                @if(isset($result) && $result->thumbnail)
                    <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 100px; height: 100px;">
                        <img src="{{ $result->thumbnail_thumb_full_path }}" class="img-thumbnail">
                    </div>
                @else
                    <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 100px; height: 100px; display: none;">
                        <img src="" class="img-thumbnail" style="display: none;">
                    </div>
                @endif
            </div>
            <div class="tc-crop-img-section" style="display: none;">
                <div id="upload-demo"></div>
                <button class="wag-admin-btns-main upload-image">Crop Image</button>
            </div>
        </div>
        <div class="form-group">
            <label>Image Alt Text</label>
            {{ Form::text('alt_image_text', null, ['id' => 'alt_image_text', 'class'=>"form-control"]) }}
            @if($errors->has('alt_image_text'))
                <p class="text-danger">{{ $errors->first('alt_image_text') }}</p>
            @endif
        </div>
        {{-- <div class="form-group d-none">
            <label>Video Type</label>
            {{ Form::radio('video_type', "embed_link",  ((!isset($result)) ? true : null), ["class" => "video_type", 'id' => 'embed_link']) }}<label for="embed_link">&nbsp;Youtube Embed</label>
            {{ Form::radio('video_type', "video_upload", null, ["class" => "video_type", 'id' => 'video_upload']) }} <label for="video_upload">&nbsp;Upload Video</label>&nbsp;
            @if($errors->has('video_type'))
                <span class="help-block m-b-none">{{ $errors->first('video_type') }}</span>
            @endif
        </div> --}}
        {{-- <div class="form-group add_embed_link_section {{ (isset($result) && $result->video_type == 'video_upload') ? 'd-none' : '' }}">
            <label>Youtube Link</label>
            {{ Form::text('embed_link', null, ['id' => 'embed_link', 'class'=>"form-control"]) }}
            <small id="description_help" class="form-text text-muted">Ex.  https://www.youtube.com/embed/QKQc8z0_GmU</small>
            @if($errors->has('embed_link'))
                <p class="text-danger">{{ $errors->first('embed_link') }}</p>
            @endif
        </div>
        <div class="form-group upload_video_section {{ (isset($result) && $result->video_type == 'video_upload') ? '' : 'd-none' }}">
            <label>Upload Video</label>
            {{ Form::file('video_file', ['id' => 'video_file', 'class'=>"form-control file_multi_video", "accept" => ".mp4"]) }}
            @if($errors->has('video_file'))
                <span class="help-block m-b-none">{{ $errors->first('video_file') }}</span>
            @endif
        </div>   --}}
        
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
 {{-- summernote --}}
<script src="{{ asset('plugins/summernote/summernote.min.js') }}"></script>

<link href="{{ asset('plugins/croppie/croppie.min.css') }}" rel="stylesheet">
<script src="{{ asset('plugins/croppie/croppie.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $('#blog_meta_description').keyup();
        var isContentUpdated = false;
        $(window).bind('beforeunload', function(e){
            if(isContentUpdated) {
                return true;
            } else {
                return undefined;
            }
        });

        $('form').on('keyup change paste', 'input, select, textarea', function(){
            isContentUpdated = true;
        });

        jQuery.validator.addMethod("youtube_embaded_url_validate", function(value, element) {
          // allow any non-whitespace characters as the host part
          return this.optional( element ) || /^https:\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+/.test( value );
        }, 'Please enter the Youtube embed link');

        $("#form_validate").validate({
            ignore: ":hidden:not('.summernote'),.note-editable,.panel-body",
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                category_id: {
                    required: true
                },
                title: {
                    required: true,
                    maxlength: 255
                },
                author_id: {
                },
                image: {
                    required: {{ (isset($result)) ? "false" : "true" }},

                },
                blog_meta_description: {
                    required: true,
                    maxlength: 255
                },
                alt_image_text: {
                    required: true,
                    maxlength: 255
                },

                video_type: {
                    required: true
                },

                // video_file: {
                //     required: {
                //         depends: function() {
                //             @if(isset($result))
                //                 return false;
                //             @else
                //                 return ($('input[name=video_type]:checked').val() == 'video_upload') ? true : false;
                //             @endif
                //         }
                //     }
                // },
                // embed_link: {
                //     /*required: {
                //         depends: function() {
                //             return ($('input[name=video_type]:checked').val() == 'embed_link') ? true : false;
                //         }
                //     },*/
                //     youtube_embaded_url_validate: true
                // },
            }
        });

        $("#form_validate").submit(function(){
            isContentUpdated=false;
           $('input[name="description"]').val($('.summernote').code());
        });

        var resize = $('#upload-demo').croppie({
            enableExif: true,
            enableOrientation: true,
            viewport: {
                width: 150,
                height: 100,
                type: 'square'
            },
            boundary: {
                width: 150,
                height: 150
            }
        });

        $('#image').on('change', function (event) {
			var fileName = event.target.files[0].name;
			$(".img-name-lbl").html(fileName).removeClass('d-none');

			$('#preview-crop-image').css("display", 'block');
			displayImageOnFileSelect(this, $('.img-thumbnail'));
			
            var reader = new FileReader();
            reader.onload = function (e) {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(blob){
                    //console.log('jQuery bind complete');
                });
            }

            reader.readAsDataURL(this.files[0]);
            $('.tc-crop-img-section').show();
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

       $(document).on("change", ".file_multi_video", function(evt) {
            $('#preview-video').show();
            var $source = $('#video_here');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
        });

        $('.upload-image').on('click', function (ev) {
           resize.croppie('result',{circle: false, size: "original", type:"rawcanvas"}).then(function (rawcanv) {
             // resample_single(rawcanv, 340, 180, true);
             var img = rawcanv.toDataURL();

               $('#cropped_image').val(img.split(',')[1]);

               html = '<img src="' + img + '" class="img-thumbnail" />';
               $("#preview-crop-image").html(html);
           });
           return false;
       });

    });

    $("textarea").keyup(function(e) {
        while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
            $(this).height($(this).height()+1);
        };
    });

    // $(".video_type").change(function(){
    //     var selectedRadio = $(this).val();
    //     if(selectedRadio == "video_upload") {
    //         $(".add_embed_link_section").addClass("d-none");
    //         $(".upload_video_section").removeClass("d-none");
    //     }
    //     else {
    //         $(".upload_video_section").addClass("d-none");
    //         $(".add_embed_link_section").removeClass("d-none");
    //     }
    // });
</script>

@endpush
