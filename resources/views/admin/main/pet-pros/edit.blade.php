@extends('admin.layouts.admin')

@push("styles")
<link href="{{ asset('plugins/iCheck/custom.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $singular_module_name }}</title>
@endpush

@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    {!! Form::model($result, array('url' => $module_route.'/'.$result->id, 'method' => 'PUT', "enctype"=>"multipart/form-data",'class'=>'form form-horizontal','id'=>'form_validate', 'autocomplete'=>'off')) !!}

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
                    <h2 class="wag-admin-page-title-main">Edit {{  $singular_module_name }} </h2>
                @endif
                <div class="">
                    @include("$moduleView._form")
                </div>
            </div>

            <div class="wag-inner-section-block-main">
                <div class="wag-donation-link-header-bar">
                    <h2 class="wag-admin-page-title-main">Deals</h2>
                    <a class="wag-admin-btns-main" href='{{"$module_route/$result->id/deals/create"}}' id="add_deal">Add New +</a>
                </div>
                <div class="wag-table-main wag-inner-page-table-main">
                    <table class="table project-datatable deal-datatable" >
                        <thead>
                            <tr>
                                <th>Deal Text</th>
                                <th>Claimed</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="wag-inner-section-block-main">
                <div class="wag-donation-link-header-bar">
                    <h2 class="wag-admin-page-title-main">Events</h2>
                    <a class="wag-admin-btns-main" href='{{"$module_route/$result->id/events/create"}}' id="add_event">Add New +</a>
                </div>
                <div class="wag-table-main wag-inner-page-table-main">
                    <table class="table project-datatable event-datatable" >
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>URL</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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
<script src="{{ asset('plugins/sweetalert2/es6-promise.auto.min.js') }} "></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }} "></script>

<script>
    $(document).ready(function () {

        var oDealTable = $('.deal-datatable').DataTable({
                "dom": '<"row" <"col-sm-4"l> <"col-sm-4"r> <"col-sm-4"f>> <"row"  <"col-sm-12"t>> <"row" <"col-sm-5"i> <"col-sm-7"p>>',
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "numbers",
                lengthChange: false,
                bInfo : false,
                "fnDrawCallback": function(oSettings) {
                    if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                        $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                    }
                },
                "ajax": {
                    "url": "{!! $module_route.'/'.$result->id.'/deals/datatable' !!}",
                    "data": function ( d ) {
                    }
                },
                columns: [

                    { data: 'deal', name: 'deal',className:'user-name-details'},
                    { data: 'claimed', name: 'claimed'},
                    { data: 'formated_start_date', name: 'start_date'},
                    { data: 'formated_end_date', name: 'end_date'},
                    {
                        data:  null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        targets: 0,
                        width: 70,
                        className: 'text-right',
                        render:function(o){
                            var str = "";

                            if( o.status == 'active' ) {
                                str += "<button type='button' class='btn btn-success'>Active</button>";
                            } else {
                                str += "<button type='button' class='btn btn-danger'>Pause</button>";
                            }

                            return str;
                        }
                    },
                    {
                        data:  null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        targets: 0,
                        width: 70,
                        className: 'text-right',
                        render:function(o){
                            var str = "";

                            str += "<a href='javascript:void(0);' title='Status' class='changeDealStatus' val='" + o.id + "' >";
                            if( o.status == 'active' ) {
                                str += "<i class='fa fa-pause-icon'></i>";
                            } else {
                                str += "<i class='fa fa-play'></i>";
                            }
                            str +="</a>";

                            str += " <a href='{!!  $module_route  !!}/{{ $result->id }}/deals/"+o.id +"/edit' title='Edit'><i class='fa fa-edit-icon'></i></a>";

                            str += " <a href='javascript:void(0);' class='deleteDealRecord' val='" + o.id + "' title='Delete' ><i class='fa fa-trash-icon'></i></a> ";

                            return str;
                        }
                    }
                ]
        });

        var oEventTable = $('.event-datatable').DataTable({
                "dom": '<"row" <"col-sm-4"l> <"col-sm-4"r> <"col-sm-4"f>> <"row"  <"col-sm-12"t>> <"row" <"col-sm-5"i> <"col-sm-7"p>>',
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "numbers",
                lengthChange: false,
                bInfo : false,
                "fnDrawCallback": function(oSettings) {
                    if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                        $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                    }
                },
                "ajax": {
                    "url": "{!! $module_route.'/'.$result->id.'/events/datatable' !!}",
                    "data": function ( d ) {
                    }
                },
                columns: [

                    { data: 'name', name: 'name',className:'user-name-details'},
                    { data: 'formated_event_date', name: 'event_date'},
                    { data: 'formated_event_start_time', name: 'start_time'},
                    { data: 'address', name: 'address'},

                    {
                        data:  null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        targets: 0,
                        width: 70,
                        render:function(o){

                            var str = "<a href='"+ o.url +"' target='_blank' title='url'><i class='fa fa-link'></i></a>";

                            return str;
                        }
                    },
                    {
                        data:  null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        targets: 0,
                        width: 70,
                        className: 'text-right',
                        render:function(o){
                            var str = "";

                            str += "<a href='javascript:void(0);' class='changeEventStatus' val='" + o.id + "'>";
                            if( o.status == 'active' ) {
                                str += "<i class='fa fa-pause-icon'></i>";
                            } else {
                                str += "<i class='fa fa-play'></i>";
                            }
                            str +="</a>";

                            str += " <a href='{!!  $module_route  !!}/{{ $result->id }}/events/"+o.id +"/edit' title='Edit'><i class='fa fa-edit-icon'></i></a>";

                            str += " </a><a href='javascript:void(0);' class='deleteEventRecord' val='" + o.id + "' title='Delete' ><i class='fa fa-trash-icon'></i></a> ";

                            return str;
                        }
                    }
                ]
        });

        //delete deal Record
        jQuery(document).on('click', '.deleteDealRecord', function(event) {
            var id = $(this).attr('val');
            var deleteUrl = "{!!  $module_route  !!}/{{ $result->id }}/deals/" + id;
            var isDelete = deleteRecordByAjax(deleteUrl, "Deal", oDealTable);
        });

        //delete event Record
        jQuery(document).on('click', '.deleteEventRecord', function(event) {
            var id = $(this).attr('val');
            var deleteUrl = "{!!  $module_route  !!}/{{ $result->id }}/events/" + id;
            var isDelete = deleteRecordByAjax(deleteUrl, "Event", oEventTable);
        });

        //delete gallery image Record
        jQuery(document).on('click', '.deleteGalleryImageRecord', function(event) {
            var id = $(this).attr('val');
            var deleteUrl = "{!!  $module_route  !!}/{{ $result->id }}/gallery/" + id;
            var isDelete = deleteRecordByAjax(deleteUrl, "Gallery Image", '');

        });

        //Change Deal Staus Record
        jQuery(document).on('click', '.changeDealStatus', function(event) {
            var id = $(this).attr('val');

            var changeStatusUrl = "{!!  $module_route  !!}/{{ $result->id }}/deals/change-deal-status/"+id;
            var isDelete = toggleStatusRecordByAjax(changeStatusUrl, oDealTable);
        });

        //Change Event Staus Record
        jQuery(document).on('click', '.changeEventStatus', function(event) {
            var id = $(this).attr('val');

            var changeStatusUrl = "{!!  $module_route  !!}/{{ $result->id }}/events/change-events-status/"+id;
            var isDelete = toggleStatusRecordByAjax(changeStatusUrl, oEventTable);
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
