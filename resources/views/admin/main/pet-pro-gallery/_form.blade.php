@push("styles")

{{-- <style>
    .col-form-label {
        padding-left: 0;
        padding-right: 0;
    }
</style> --}}

@endpush
<div class="row">
    <div class="col-md-6">
        <div class="form-group hs-user">
            <label>Upload Gallery Image</label>
            <input type="hidden" name="cropped_image" id="cropped_image" value="" style="display:none" />
            <div class="">
                {{ Form::checkbox('is_cover_image', 1, (isset($result) && isset($result->is_cover_image) && $result->is_cover_image == 1) ?  true : false, ['id' => 'is_cover_image']) }}              
                <label for="is_cover_image">&nbsp;Cover Image</label>
			</div>
			<label for="image" class="btn upload-img-btn">Upload</label>			
			<label class="img-name-lbl ml-3 d-none"></label><br />
            {{ Form::file('image', ['id' => 'image', 'class'=>"form-control d-none", "accept" => ".png, .jpg, .jpeg"]) }}
            @if($errors->has('image'))
                <p class="text-danger">{{ $errors->first('testimonial') }}</p>
			@endif
        </div>
    </div>
</div>
<div class="form-group row hs-user ">
    <div class="col-md-5">
        @if(isset($result) && $result->gallery_image)
            <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px;">
                <img src="{{ $result->image_thumb_full_path }}" class="img-thumbnail">
            </div>
        @else
            <div id="preview-crop-image" class="upvideoblk hs-blog-img-preview" style="width: 200px; height: 200px; display: none;">
                <img src="" class="img-thumbnail" style="display: none;">
            </div>
        @endif
    </div>
    <div class="col-sm-7 tc-crop-img-section" style="display: none; text-align:center;">
        <div id="upload-demo"></div>
        <button class="wag-admin-btns-main upload-image">Crop Image</button>
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<link href="{{ asset('plugins/croppie/croppie.min.css') }}" rel="stylesheet">
<script src="{{ asset('plugins/croppie/croppie.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#form_validate").validate({
            ignore: [],
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                image: {
                    required: {{ (isset($result)) ? "false" : "true" }},

                },
            }
        });

        var resize = $('#upload-demo').croppie({
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
