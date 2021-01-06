@push("styles")
@endpush

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Title</label>
            {{ Form::text('title', null, ['id' => 'title', 'class'=>"form-control"]) }}
            @if($errors->has('title'))
                <p class="text-danger">{{ $errors->first('title') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label>Description</label>
            {{ Form::textarea('description', null, ['id' => 'description', 'class'=>"form-control"]) }}
            @if($errors->has('description'))
                <p class="text-danger">{{ $errors->first('description') }}</p>
            @endif
		</div>
		<div class="form-group">
            <label>Client Name</label>
            {{ Form::text('client_name', null, ['id' => 'client_name', 'class'=>"form-control"]) }}
            @if($errors->has('client_name'))
                <p class="text-danger">{{ $errors->first('client_name') }}</p>
            @endif
		</div>
		<div class="form-group">
            <label>Client Title</label>
            {{ Form::text('client_title', null, ['id' => 'client_title', 'class'=>"form-control"]) }}
            @if($errors->has('client_title'))
                <p class="text-danger">{{ $errors->first('client_title') }}</p>
            @endif
		</div>
		<div class="form-group">
			<label>Image</label><br />
			<label for="image" class="btn upload-img-btn">Upload</label>
			<label class="img-name-lbl ml-3 d-none"></label><br />
            <input type="hidden" name="cropped_image" id="cropped_image" value="" style="display:none" />
            {{ Form::file('image', ['id' => 'image', 'class'=>"form-control d-none", "accept" => ".png, .jpg, .jpeg"]) }}
            @if($errors->has('image'))
                <p class="text-danger">{{ $errors->first('testimonial') }}</p>
			@endif
        </div>
        <div class="row">
            <div class="col-md-6 mt-3">
                @if(isset($result) && $result->testimonial_image_thumb_full_path)
                <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px;">
                    <img src="{{ $result->testimonial_image_thumb_full_path }}" class="img-thumbnail">
                </div>
                @else
                <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px; display: none;">
                    <img src="" class="img-thumbnail" style="display: none;">
                </div>
                @endif
            </div>
            <div class="col-md-6 tc-crop-img-section" style="display: none;">
                <div id="upload-demo"></div>
                <button class="wag-admin-btns-main upload-image">Crop Image</button>
            </div>
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

		var resize = $('#upload-demo').croppie({
            enableExif: true,
            enableOrientation: true,
            viewport: {
                width: 100,
                height: 130,
                type: 'square'
            },
            boundary: {
                width: 150,
                height: 150
            }
        });

        $("#form_validate").validate({
            ignore: [],
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                title: {
                    required: true,
                    maxlength: 255
                },
                description: {
                    required: true
                },
                client_name: {
                    required: true
                },
				client_title: {
                    required: true
                },
                image: {
                    required: {{ (isset($result)) ? "false" : "true" }},

                },
            }
        });

		if( "{{  isset($result)  }}" ) {
            $("#preview-profile_image-block").show();
            $('#preview-profile_image').attr('src', "{{ isset($result) ?  $result->testimonial_image_thumb_full_path : null }}");

        } else {
            $("#preview-profile_image-block").hide();
        }

        $('#image').on('change', function () {
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
</script>

@endpush
