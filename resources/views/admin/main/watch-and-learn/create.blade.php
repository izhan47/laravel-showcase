@extends('admin.layouts.admin')

@push("styles")
<link href="{{ asset('plugins/iCheck/custom.css') }}" rel="stylesheet">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
@endpush


@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    {!! Form::open(['url' => $module_route, 'method' => 'POST', "enctype"=>"multipart/form-data",'class'=>'form-horizontal','id'=>'form_validate', 'autocomplete'=>'off']) !!}

    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            <a href="{{ $module_route }}" class="wag-go-back-btn-main">Go Back</a>
        </div>
       <div class="wag-title-and-nemu-block-main">
            <button id="btn-save-as-draft" class="wag-admin-btns-main" type="submit">Save as draft</button>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="wag-inner-page-main-section">
                <div class="wag-inner-section-block-main">
                    @if(isset($singular_module_name))
                        <h2 class="wag-admin-page-title-main">Add {{  $singular_module_name }}</h2>
                    @endif
                    @include("$moduleView._form")

                    <div class="wag-title-and-nemu-block-main">
                        {{Form::hidden('blogMode',null,['id' => 'blogMode'])}}                       
                        {{ Form::button('Next step <img src="'. asset('images/Next-step-arrow.svg') .'" />', ['class'=>'wag-admin-btns-main', 'id' => 'btn-next', 'type' => 'submit']) }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@endsection

@push("scripts")
<script src="{{ asset("plugins/iCheck/icheck.min.js") }}"></script>

<script>
    $(document).ready(function () {
        $('#btn-save-as-draft').on('click', function() {
            $('#blogMode').val('draft');
        });

        $('#btn-next').on('click', function() {
            $('#blogMode').val('next');
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
@endpush
