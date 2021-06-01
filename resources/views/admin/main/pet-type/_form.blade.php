@push("styles")
<style>
    .col-form-label {
        padding-left: 0;
        padding-right: 0;
    }
</style>
@endpush
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Name</label>
            {{ Form::text('name', null, ['id' => 'name', 'class'=>"form-control"]) }}
            @if($errors->has('name'))
                <p class="text-danger">{{ $errors->first('name') }}</p>
            @endif
        </div>
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>

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
                name: {
                    required: true,
                    maxlength: 255
                },
            }
        });
    });
</script>

@endpush
