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

        <div class="wag-table-main">
            <div class="card-header d-none">
                @if(isset($module_name))
                    <h5 class="card-title mb-0 ">{{  $module_name }}</h5>
                @endif
            </div>
            <table class="table project-datatable" >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date Added</th>
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
                { data: 'name', name: 'name',className:'user-name-details'},
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
                        if( !o.pet_pro_count ){
                            btnStr += " <a href='javascript:void(0);' class='deleteRecord' val='" + o.id + "' title='Delete' ><i class='fa fa-trash-icon text-danger'></i></a>";
                        } else {
                            btnStr += " <a href='javascript:void(0);' class='disabled-delete' title='You can not delete' ><i class='fa fa-trash-icon text-danger'></i></a>";                            
                        }


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

    //delete blog
    jQuery(document).on('click', '.deleteRecord', function(event) {
        var id = $(this).attr('val');
        var eventURL = "{!!  $module_route  !!}/" + id;
        var title = "Delete Business Nature?";
        var text = "Once business nature is deleted, all pet pros under this business nature will be deleted as well.";
        var method = "DELETE";
        var successMessage = "Pet pro and business nature deleted.";
        changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/pet-pro-business');  !!}");
    });

});

</script>
@endpush
