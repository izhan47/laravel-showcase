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
        <div class="form-group">
            <label>Name</label>
            {{ Form::text('name', null, ['id' => 'name', 'class'=>"form-control"]) }}
            @if($errors->has('name'))
                <p class="text-danger">{{ $errors->first('name') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label>Email</label>
            {{ Form::email('email', null, ['id' => 'email', 'class'=>"form-control"]) }}
            @if($errors->has('email'))
                <p class="text-danger">{{ $errors->first('email') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label>Password</label>
            {{ Form::password('password', ['id' => 'password', 'class'=>"form-control"]) }}
            @if($errors->has('password'))
                <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif
        </div>
        <!-- <div class="form-group">
            <label>Phone Number</label>
            {{ Form::text('phone_number', null, ['id' => 'phone_number', 'class'=>"form-control"]) }}
            @if($errors->has('phone_number'))
                <p class="text-danger">{{ $errors->first('phone_number') }}</p>
            @endif
        </div> -->
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
                email: {
                    required: true,
                    email: true
                },
                /*phone_number: {
                    required: true
                },*/
                password: {
                    required:  function() {
                        @if(isset($result))
                            return false;
                        @endif
                        return true;
                    },
                    minlength: 6
                },
            }
        });
    });
</script>

@endpush
