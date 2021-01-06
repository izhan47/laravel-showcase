@push("styles")
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/timepicker/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
{{-- <style>
    .col-form-label {
        padding-left: 0;
        padding-right: 0;
    }
</style> --}}
@endpush
<div class="wag-inner-section-block-main">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Deal</label><br/>
                {{ Form::text('deal', null, ['id' => 'deal', 'class'=>"form-control"]) }}
                @if($errors->has('deal'))
                    <p class="text-danger">{{ $errors->first('deal') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Fine Print</label><br />
                {{ Form::textarea('fine_print', null, ['class'=>"form-control", "rows" => 5]) }}
                @if($errors->has('fine_print'))
                    <span class="help-block m-b-none">{{ $errors->first('fine_print') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Start Date</label><br/>
                {{ Form::text('start_date', null, ['id' => 'start_date', 'class'=>"form-control"]) }}
                @if($errors->has('start_date'))
                    <p class="text-danger">{{ $errors->first('start_date') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>End Date</label><br/>
                {{ Form::text('end_date', null, ['id' => 'end_date', 'class'=>"form-control"]) }}
                @if($errors->has('end_date'))
                    <p class="text-danger">{{ $errors->first('end_date') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $('#start_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        $('#end_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        $("#form_validate").validate({
            ignore: [],
            errorElement: 'p',
            errorClass: 'text-danger',
            normalizer: function( value ) {
                return $.trim( value );
            },
            rules: {
                deal: {
                    required: true,
                    maxlength: 255
                },

                fine_print: {
                    required: true
                },

                start_date: {
                    //required: true
                    required: function(element){
                        return $("#end_date").val()!="";
                    }
                },

                end_date: {
                    //required: true
                    required: function(element){
                        return $("#start_date").val()!="";
                    }
                },
            }
        });
    });
</script>

@endpush
