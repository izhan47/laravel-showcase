@push("styles")
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
</style>
@endpush
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Categories</label>
            {{ Form::select('category_id[]',$categories, ( isset($selectedCategories) && count($selectedCategories))?$selectedCategories:null,  ['id'=> 'category_id', 'class' => 'form-control','multiple' => 'multiple']) }}
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
        
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>

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
        $('#category_id').select2({
			tags: false,
			placeholder: 'Select categories'
		});
        $("#form_validate").validate({
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                'category_id[]': {
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
                }                
            }
        });

        $("#form_validate").submit(function(){
            isContentUpdated=false;
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

</script>

@endpush
