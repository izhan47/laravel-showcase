@extends('admin.layouts.admin')

@push("styles")
    <style type="text/css" media="screen">
        .wag-table-main table{
            padding: 10px;
        }
        div.dataTables_wrapper div.dataTables_processing {
          top: 300px;
        }
    </style>


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
        <div class="wag-title-and-nemu-block-main">
            <a class="wag-admin-btns-main" href="{{"$module_route/create"}}" title="Add">Create New +</a>
        </div>
    </div>

    <div class="wag-table-main">
		<div class="wag-select-main">
			{!! Form::select('category' ,(['' => 'All categories']+$categories), null, ['class' => 'form-control', 'id' => 'category']) !!}
		</div>
       <!-- <div class="wag-published-and-draft-section-main">
           <button val='published' class="wag-published-and-draft-btns active">Published</button>
           <button val='draft' class="wag-published-and-draft-btns ">Draft</button>
       </div> -->
        <table class="table project-datatable wag-watch-and-learn">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th class='text-left'>Action</th>
                </tr>
            </thead>
        </table>
    </div>

</section>


@endsection

@push("scripts")

<script>
$(document).ready(function(){

	$('#category').select2({
		tags: false,
	});

	$('#category').on('change', function(e) {
		oTable.draw();
		e.preventDefault();
	});

    var blogMode = "published";

    var oTable = $('.project-datatable').DataTable({
			"dom": '<"wag-header-main"<"row" <"col-sm-12 col-md-4"l> <"col-sm-4"r> <"col-sm-12 col-md-4 responsive-search-box"f>>> <"table-block table-main home-user-table"t> <"table-footer"<"row" <"col-sm-5"i> <"col-sm-7"p>>>',
            // "dom": '<"row" <"col-sm-4"l> <"col-sm-4"r> <"col-sm-4"f>> <"row"  <"col-sm-12"t>> <"row" <"col-sm-5"i> <"col-sm-7"p>>',
            processing: true,
            serverSide: true,
            responsive: true,
            "ordering": false,
            //"searching": false,
            pagingType: "numbers",
            lengthChange: false,
            bInfo : false,
            stateSave: false,
            /*"fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            },*/
            dom: 'frBtp',
            buttons: [
                {
                    text: 'Published',
                    attr:  {
                        id: "wag-published-btns",
                    },
                    className: 'wag-published-and-draft-btns dtb-active',
                    action: function ( e, dt, node, config ) {
                        $("#wag-draft-btns").removeClass('dtb-active');
                        $("#wag-published-btns").addClass('dtb-active');
                        blogMode="published";
                        oTable.draw();
                    }
                },
                {
                    text: 'Draft',
                    attr:  {
                        id: "wag-draft-btns",
                    },
                    className: 'wag-published-and-draft-btns',
                    action: function ( e, dt, node, config ) {
                        $("#wag-published-btns").removeClass('dtb-active');
                        $("#wag-draft-btns").addClass('dtb-active');
                        blogMode="draft";
                        oTable.draw();
                    }
                }
            ],
            "ajax": {
                "url": "{!! $module_route.'/datatable' !!}",
                "data": function ( d ) {
					d.blogMode = blogMode ? blogMode : 'published' ;
					d.category_id = $('#category').val();
                }
            },
            columns: [
                { data: 'title', name: 'title',className:'user-name-details'},
                { data: 'formated_category', name: 'category.name', width: 200},
                { data: 'formated_author', name: 'author.name', width: 200},
                { data: 'formated_created_at', name: 'created_at', width: 110},
                {
                    data:  null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    targets: 0,
                    width: 70,
                    className: 'text-left',
                    render:function(o){
                        var btnStr = "";

                            btnStr += "<a href='{!!  $module_route  !!}/"+  o.id +"/edit' title='Edit'><i class='fa fa-edit-icon'></i></a>";
                            btnStr += " <a href='{!!  $module_route  !!}/"+  o.id +"' title='Preview'><i class='fa fa-eye'></i></a>";
                            /*btnStr += " <a href='{!!  $module_route  !!}/"+  o.id +"/edit/buildwithcontentbuilder' title='Edit With Content Builder'><i class='fa fa-sitemap'></i></a>";*/
                            btnStr += " <a href='javascript:void(0);' class='deleteRecord' val='" + o.id + "' title='Delete' ><i class='fa fa-trash-icon text-danger'></i></a>";


                        return btnStr;
                    }
                }
            ],
            order: [[1, "ASC"]]
    });

    //delete blog
    jQuery(document).on('click', '.deleteRecord', function(event) {
        var id = $(this).attr('val');
        var eventURL = "{!!  $module_route  !!}/" + id;
        var title = "Delete post?";
        var text = "Once you delete this blog post, you will no longer be able to view, edit or recover.";
        var method = "DELETE";
        var successMessage = "Product review deleted.";
        changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/product-reviews');  !!}");
    });

});

</script>
@endpush
