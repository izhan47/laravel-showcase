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
                <label>Name</label><br/>
                {{ Form::text('name', null, ['id' => 'name', 'class'=>"form-control"]) }}
                @if($errors->has('name'))
                    <p class="text-danger">{{ $errors->first('name') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Description</label><br />
                {{ Form::textarea('description', null, ['class'=>"form-control", "rows" => 5]) }}
                @if($errors->has('description'))
                    <span class="help-block m-b-none">{{ $errors->first('description') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Start Date</label><br/>
                {{ Form::text('event_date', null, ['id' => 'event_date', 'class'=>"form-control date-input"]) }}
                @if($errors->has('event_date'))
                    <p class="text-danger">{{ $errors->first('event_date') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>End Date</label><br/>
                {{ Form::text('event_end_date', null, ['id' => 'event_end_date', 'class'=>"form-control date-input"]) }}
                @if($errors->has('event_end_date'))
                    <p class="text-danger">{{ $errors->first('event_end_date') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Start Time</label><br/>
                <div class="clockpicker" data-autoclose="true">
                    {{ Form::text('start_time',  isset($result) && isset($result->formated_event_start_time) ? $result->formated_event_start_time : null , ['id' => 'start_time', 'class'=>"form-control"]) }}
                    @if($errors->has('start_time'))
                        <p class="text-danger">{{ $errors->first('start_time') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>End Time</label><br/>
                <div class="clockpicker" data-autoclose="true">
                    {{ Form::text('end_time', isset($result) && isset($result->formated_event_end_time) ? $result->formated_event_end_time : null, ['id' => 'end_time', 'class'=>"form-control"]) }}
                    @if($errors->has('end_time'))
                        <p class="text-danger">{{ $errors->first('end_time') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Address</label><br />
                {{ Form::textarea('address', null, ['class'=>"form-control", "rows" => 5]) }}
                @if($errors->has('address'))
                    <span class="help-block m-b-none">{{ $errors->first('address') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="col-form-label">URL</label><br/>
                {{ Form::text('url', null, ['id' => 'url', 'class'=>"form-control"]) }}
                @if($errors->has('url'))
                    <p class="text-danger">{{ $errors->first('url') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push("scripts")

{{-- jQuery Validate --}}
<script src="{{ asset('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('plugins/timepicker/bootstrap-clockpicker.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {

         $('.clockpicker').clockpicker({
            twelvehour: true,
            donetext: 'Done'
        });

        $('.date-input').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        jQuery.validator.addMethod("greaterThan", 
            function(value, element, params) {

                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) >= new Date($(params).val());
                } else {
                    return true;
                }               
            },
            'Must be greater than start event date.'
        );

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
                event_date: {  
                    required: function(element){
                        return $("#event_end_date").val()!="";
                    }                  
                },
                event_end_date: {  
                    required: function(element){
                        return $("#event_date").val()!="";
                    },
                   greaterThan: "#event_date"
                                   
                },                
                start_time: {
                    //required: true
                    required: function(element){
                        return $("#end_time").val()!="";
                    }
                },
                end_time: {
                    //required: true
                    required: function(element){
                        return $("#start_time").val()!="";
                    }
                },
                url: {
                    required: true
                },
            }
        });
    });
</script>

@endpush
