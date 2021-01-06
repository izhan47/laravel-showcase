@extends('admin.layouts.admin')

@push("styles")
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
@endpush

@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">

    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            @if(isset($module_name))
                <h1 class="wag-admin-page-title-main">{{  $module_name }}</h1>
            @endif
        </div>
        <div class="wag-title-and-nemu-block-main">
            <a class="wag-admin-btns-main" href="{{"$module_route/create"}}" title="Add">Create New +</a>
        </div>
    </div>

    <div class="card-header d-none">
        @if(isset($module_name))
            <h5 class="card-title mb-0 ">{{  $module_name }}</h5>
        @endif
    </div>

    <div class="wag-table-main">
        <table class="table project-datatable" >
            <thead>
                <tr>
                    <th>Pet Pro Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City, State</th>
                    <th>Date Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>



</section>

@endsection

@push("scripts")

<script>
$(document).ready(function(){

    var oTable = $('.project-datatable').DataTable({
            "dom": '<"row" <"col-sm-4"l> <"col-sm-4"r> <"col-sm-4"f>> <"row"  <"col-sm-12"t>> <"row" <"col-sm-5"i> <"col-sm-7"p>>',
            processing: true,
            serverSide: true,
            responsive: true,
            "ordering": false,
            pagingType: "numbers",
            lengthChange: false,
            bInfo : false,
            stateSave: false,
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            },
            "ajax": {
                "url": "{!! $module_route.'/datatable' !!}",
                "data": function ( d ) {
                }
            },
            columns: [
                { data: 'store_name', name: 'store_name'},
                { data: 'email', name: 'email'},
                { data: 'phone_number', name: 'phone_number'},
                { data: 'city_state', name: 'city.name'},
                { data: 'formated_created_at', name: 'created_at'},
                {
                    data:  null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    targets: 0,
                    width: 70,
                    className: 'text-right',
                    render:function(o){
                        var btnStr = "";

                            btnStr += "<a href='{!!  $module_route  !!}/"+  o.id +"/edit' title='Edit'><i class='fa fa-edit-icon'></i></a>";
                            btnStr += " <a href='javascript:void(0);' class='deleteRecord' val='" + o.id + "' title='Delete' ><i class='fa fa-trash-icon text-danger'></i></a>";


                        return btnStr;
                    }
                }
            ],
            order: [[1, "ASC"]]
    });

    //delete Record
    jQuery(document).on('click', '.deleteRecord', function(event) {
        var id = $(this).attr('val');
        var deleteUrl = "{!!  $module_route  !!}/" + id;
        var isDelete = deleteRecordByAjax(deleteUrl, "{{$singular_module_name}}", oTable);
    });

});

</script>
@endpush
