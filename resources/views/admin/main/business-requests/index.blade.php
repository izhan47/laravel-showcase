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
            <div class="card-header d-none">
                @if(isset($module_name))
                    <h1 class="wag-admin-page-title-main">{{  $module_name }}</h1>
                @endif
            </div>
        </div>
    </div>
    <div class="wag-table-main">
        <table class="table project-datatable" >
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Business Name</th>
                    <th>Contact Email</th>
                    <th>Message</th>
                    <th>Date</th>
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
                { data: 'first_name', name: 'first_name',className:'user-name-details'},
                { data: 'last_name', name: 'last_name',className:'user-name-details'},
                { data: 'business_name', name: 'business_name',className:'user-name-details'},
                { data: 'contact_email', name: 'contact_email'},
                { data: 'message', name: 'message'},
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
                        btnStr += "<a href='{!!  $module_route  !!}/"+  o.id +"' title='Show'><i class='fa fa-file-icon'></i></a>";
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
