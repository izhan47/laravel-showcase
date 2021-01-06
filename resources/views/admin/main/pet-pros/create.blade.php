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
            <a href="{{ $module_route }}" class="wag-go-back-btn-main">Go Back</a>
        </div>
        <div class="wag-title-and-nemu-block-main">
            <button id="submitFormBtn" class="wag-admin-btns-main" type="button">Save</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="wag-inner-page-main-section">
                @if(isset($singular_module_name))
                    <h2 class="wag-admin-page-title-main">Add {{  $singular_module_name }} </h2>
                @endif
                <div class="">
                    @include("$moduleView._form")
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@push("scripts")
<script src="{{ asset('plugins/jquery-validate/jquery.form.js') }} "></script>
<script src="{{ asset("plugins/iCheck/icheck.min.js") }}"></script>

<script>
    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });

    

    $('form').ajaxForm({
        beforeSend: function() {
            var percentVal = 'Saving... (0%)';                    
            $("#submitFormBtn").html(percentVal);
            $("#submitFormBtn").attr("disabled", true);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            if( percentComplete < 99.99 ) {
                var percentVal = "Saving... ("+ percentComplete + '%)';
                $("#submitFormBtn").html(percentVal);                
            }
        },
        complete: function(xhr) {
            $("#submitFormBtn").html("Saving... (100%)");    
            $("#submitFormBtn").attr("disabled", false);            
            if(xhr.status === 200  ) {                
                $("#submitFormBtn").html('Save'); 
                fnToastSuccess(xhr.responseJSON["message"]);
            } else {
                fnToastError(xhr.responseJSON["message"]);
            }
            setTimeout(() => {                                            
                window.location.href = "{!! url("admin/pet-pros") !!}";         
            }, 1000);
        },
        error:  function(xhr, desc, err) {
            $("#submitFormBtn").attr("disabled", false);
            fnToastError(err);
            console.debug(xhr);
            console.log("Desc: " + desc + "\nErr:" + err);
        }
    });
</script>
@endpush
