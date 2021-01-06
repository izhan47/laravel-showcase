@extends('admin.layouts.admin')

@push("styles")
<link href="{{ asset('plugins/iCheck/custom.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
@endpush

@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    {!! Form::model($result, array('url' => $module_route.'/'.$result->id, 'method' => 'PUT', "enctype"=>"multipart/form-data",'class'=>'form form-horizontal','id'=>'form_validate', 'autocomplete'=>'off')) !!}

    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            <a href="{{ $module_route }}" class="wag-go-back-btn-main">Go Back</a>
        </div>
        <div class="wag-title-and-nemu-block-main">
            <div class="dropdown">
                <button class="wag-admin-btns-main dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
                    Done
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($result->status == 'draft' )                    
                        <a class="dropdown-item draft-blog" href="javascript:void(0);">Save as draft</a>
                        <a class="dropdown-item publish-blog" href="javascript:void(0);" >Publish</a>
                    @else
                        <a class="dropdown-item update-publish-blog" href="javascript:void(0);">Update & Publish</a>
                        <a class="dropdown-item unpublish-blog" href="javascript:void(0);">Unpublish</a>
                    @endif
                        <a class="dropdown-item" id="delete-blog" href="javascript:void(0);">Delete</a>
                </div>
            </div>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="wag-inner-page-main-section">
                <div class="wag-inner-section-block-main">
                    @if(isset($singular_module_name))
                        <h2 class="wag-admin-page-title-main">Edit {{  $singular_module_name }} </h2>
                    @endif
                    @include("$moduleView._form")

                    <div class="wag-title-and-nemu-block-main">
                        {{Form::hidden('blogMode',null,['id' => 'blogMode'])}}
                        {{ Form::button('Next step <img src="'. asset('images/Next-step-arrow.svg') .'" />', ['class'=>'wag-admin-btns-main', 'id' => 'btn-next', 'type' => 'submit']) }}                      
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
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
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@push("scripts")
<script src="{{ asset("plugins/iCheck/icheck.min.js") }}"></script>
<script src="{{ asset('plugins/sweetalert2/es6-promise.auto.min.js') }} "></script> <!-- for IE support -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }} "></script>

<script>
    $(document).ready(function () {
        
        $('#blogMode').val('draft');
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

        //delete blog
        jQuery(document).on('click', '#delete-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id  !!}";
            var title = "Delete post?";
            var text = "Once you delete this blog post, you will no longer be able to view, edit or recover.";
            var method = "DELETE";
            var successMessage = "Product review deleted.";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/product-reviews');  !!}");            
        });

        //update-and-publish blog
        jQuery(document).on('click', '.update-publish-blog', function(event) {
            $('#blogMode').val('publish');
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/published'  !!}";          
            var title = "Update post?";
            var text = "";
            var method = "GET";
            var successMessage = "Product review is now updated and published";
            $('#form_validate').validate();
            if( $('#form_validate').valid()) {
                changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, true, "{!! url('admin/product-reviews');  !!}");            
            }

        });

        //unpublish-blog
        jQuery(document).on('click', '.unpublish-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/draft'  !!}";
            var title = "Unpublish post?";
            var text = "Once you unpublish this blog post, it will no longer be visible to the public.";
            var method = "GET";
            var successMessage = "Product review is now unpublished";
            $('#form_validate').validate();
            if( $('#form_validate').valid()) {
                changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, true, "{!! url('admin/product-reviews');  !!}");            
            }          
        });

        //publish-blog blog
        jQuery(document).on('click', '.publish-blog', function(event) {
            $('#blogMode').val('publish');
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/published'  !!}";
            console.log('eventURL', eventURL);
            var title = "Publish post?";
            var text = "Once published, this post will be visible to the public.";
            var method = "GET";
            var successMessage = "Product review is now published";
            $('#form_validate').validate();
            if( $('#form_validate').valid()) {
                changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, true, "{!! url('admin/product-reviews');  !!}");            
            }          
        });

        //draft-blog
        jQuery(document).on('click', '.draft-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/draft'  !!}";
            var title = "Save as draft?";
            var text = "This post will not be visible to the public.";
            var method = "GET";
            var successMessage = "Product review is now draft";
            $('#form_validate').validate();
            if( $('#form_validate').valid()) {
                changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, true, "{!! url('admin/product-reviews');  !!}");            
            }         
        });

        // deals table
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

        //Change Deal Staus Record
        jQuery(document).on('click', '.changeDealStatus', function(event) {
            var id = $(this).attr('val');

            var changeStatusUrl = "{!!  $module_route  !!}/{{ $result->id }}/deals/change-deal-status/"+id;
            var isDelete = toggleStatusRecordByAjax(changeStatusUrl, oDealTable);
        });
        
        //delete deal Record
        jQuery(document).on('click', '.deleteDealRecord', function(event) {
            var id = $(this).attr('val');
            var deleteUrl = "{!!  $module_route  !!}/{{ $result->id }}/deals/" + id;
            var isDelete = deleteRecordByAjax(deleteUrl, "Deal", oDealTable);
        });

    });
</script>
@endpush
