@extends('admin.layouts.admin')

@push("styles")
    <link href="{{ asset('vendor/content-builder/contentbuilder/contentbuilder.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/content-builder/assets/minimalist-basic/content-bootstrap.css') }}" rel="stylesheet" type="text/css" />

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
@endpush
@section('content')

<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            <a href="{{ $back_url_path }}" class="wag-go-back-btn-main">Go Back</a>
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
    <main class="wagdt-main-page">

        <section class="wagdt-watch-video-inner-page-main">
            <div class="container">
                {{-- <div class="row">
                    <div class="col-md-12 col-lg-10 offset-lg-1" data-aos="fade-up" data-aos-duration="1500">
                        <div class="wagdt-watch-video-block watch-thumb-div">
                            <span class="watch-video-block" id="watch-video-block-main">
                                <img src="{{ $result->thumbnail_thumb_full_path }}" alt=""/>
                                @if($result->formated_duration )
                                    <span class="duration-box">{{ $result->formated_duration  }}</span>
                                @endif
                                @if($result->embed_link )
                                <span class="play-icon"><img src="/images/Play-icon.svg" alt=""/></span>
                                @endif
                            </span>
                            <div class="wagdt-watch-video-block watch-video-div d-none">
                                <iframe title="{{ $result->title }}" height="500" width="100%" src="{{ $result->embed_link}}" > </iframe>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-10 offset-md-1 col-lg-10 offset-lg-1" data-aos="fade-up" data-aos-duration="1500">
                        <div class="wagdt-watch-details-block-main">
                            <div class="">
                                <h1 class="wagdt-inner-page-title">{{ $result->title }} <img src="/images/heart-icon.svg" alt="Save"  class=""saveIcon" /></h1>
                                <h6 class="watch-andlearn-details">{{ $result->category->name }}</h6>
                                <ul class="wagdt-social-icons">
                                    <span class="">SHARE</span>
                                    <li><a target="_blank" href="" class="wagdt-social"><img src="/images/facebook.svg" alt=""/></a></li>
                                    <li><a target="_blank" href="" class="wagdt-social"><img src="/images/twitter.svg" alt=""/></a></li>
                                    <li><a target="_blank" href="" class="wagdt-social"><img src="/images/linkedin.svg" alt=""/></a></li>
                                </ul>
                            </div>
                            <div class="wagdt-inner-paragraph">
                                <span class="content-builder-data">
                                    {!! $result->description !!}
                                </span>
                            </div>
                            @if( $result->author_id &&  $result->author )
                                <div class="wagdt-author-block-main">
                                    <div class="clearfix">
                                        <div class="wagdt-author-img-block">
                                            <img alt="{{ $result->author->name }}" src="{{ $result->author->image_thumb_full_path }}" />
                                        </div>
                                    </div>
                                    <div class="wagdt-author-details-block">
                                        <h5>About the Author</h5>
                                        <h6>{{ $result->author->name }}
                                            @if( $result->author->website_link )
                                                <a class="personal-website-btns" target="_blank" href="{{ $result->author->website_link }}">Personal Website <img src="/images/diagonal-arrow.svg" alt=""/></a></a>
                                           @endif
                                        </h6>
                                        <p>{{ $result->author->about }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="wagdt-social-block-main">
                                <div class="row">
                                    <div class="col-sm-4 col-md-6">
                                        <span class="watch-andlearn-details">{{ $result->formated_created_at }}</span>
                                    </div>
                                </div>
                            </div>
						</div>
						<div class="wagdt-comments-section-main">
							<h1 class="wagdt-inner-page-title text-center">Comments</h1>
							<p class="text-center mt-4 no-comment-text d-none">No comments yet</p>
							<div class="wagdt-comments-block-main">
								
							</div>
							<div class="text-center mb-4 mt-4 see-more-comment-main-div d-none">
								<button class="wag-admin-btns-main see-more-btn">See more</button>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</section>
<div class="wagdt-comments-list-container d-none" id="comment-div-clone">
	<div class="wagdt-comments-list">
		<div class="clearfix">
			<div class="wagdt-user-img">
				<img src="" class="comment-user-img" alt="user-image"/>
			</div>
		</div>
		<div class="wagdt-comments-details">
			<div class="wagdt-use-details">
				<div class="wagdt-comments-nema">
					<h5 class="comment-user-name">User name</h5>
					<p class="comment-date">25th february 2020</p>
				</div>
				<div class="wag-reply-and-delete-section">
					<span class="delete-comment d-none" id=""><i class='fa fa-trash-icon text-danger'></i></span>
				</div>
			</div>
			<div class="">
				<p class="comment-message">comment message here</p>
			</div>
		</div>
	</div>
	<div class="text-center mb-4 mt-4 see-more-comment-div d-none">
		<button class="wag-admin-btns-main see-more-btn">See more</button>
	</div>
</div>


@push("scripts")
    <script src="{{ asset('plugins/sweetalert2/es6-promise.auto.min.js') }} "></script> <!-- for IE support -->
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }} "></script>
    <script type="text/javascript">
		var lastId = 0;
		var parentId = 0;
		var comments = [];
		var childrenCount = [];
		var commentsCount = 0;

		getComments(lastId, parentId);

        $(document).on('click', '#watch-video-block-main', function(event) {
            if( "{!! $result->embed_link  !!}" ) {
                $('#watch-video-block-main').addClass('d-none');
                $('.watch-video-div').removeClass('d-none');
            }
        });

        //delete blog
        jQuery(document).on('click', '#delete-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id  !!}";
            var title = "Delete post?";
            var text = "Once you delete this blog post, you will no longer be able to view, edit or recover.";
            var method = "DELETE";
            var successMessage = "Watch and learn deleted.";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/watch-and-learn');  !!}");
        });

        //update-and-publish blog
        jQuery(document).on('click', '.update-publish-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/published'  !!}";
            var title = "Update post?";
            var text = "";
            var method = "GET";
            var successMessage = "Watch and Learn is now update and published";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/watch-and-learn');  !!}");
        });

        //unpublish-blog
        jQuery(document).on('click', '.unpublish-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/draft'  !!}";
            var title = "Unpublish post?";
            var text = "Once you unpublish this blog post, it will no longer be visible to the public.";
            var method = "GET";
            var successMessage = "Watch and Learn is now unpublished";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/watch-and-learn');  !!}");
        });

        //publish-blog blog
        jQuery(document).on('click', '.publish-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/published'  !!}";
            var title = "Publish post?";
            var text = "Once published, this post will be visible to the public.";
            var method = "GET";
            var successMessage = "Watch and Learn is now published";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/watch-and-learn');  !!}");
        });

        //draft-blog
        jQuery(document).on('click', '.draft-blog', function(event) {
            var eventURL = "{!!  $module_route.'/'.$result->id.'/change-status/draft'  !!}";
            var title = "Save as draft?";
            var text = "This post will not be visible to the public.";
            var method = "GET";
            var successMessage = "Watch and Learn is now draft";
            changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, false, "{!! url('admin/watch-and-learn');  !!}");
        });

		$(document).on('click', '.see-more-btn', function(){
			lastId = $(this).attr('data-last-id');
			parentId = $(this).attr('data-parent-id');
			getComments(lastId, parentId);
		});

		jQuery(document).on('click', '.delete-comment', function(event) {
			var id = $(this).attr('id');

			swal({
				title: "Delete comment?",
				text: "Once deleted, you will not be able to recover it.",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Confirm",
				cancelButtonText: "Cancel",
				showLoaderOnConfirm: true,
				allowOutsideClick:false,
				allowEscapeKey:false,
				preConfirm: function (email) {
					return new Promise(function (resolve, reject) {
						setTimeout(function() {
							jQuery.ajax({
								url: "{!!  $module_route.'/delete-comment/'.$result->slug  !!}/"+ id,
								type: "DELETE",
								dataType: 'json',
								data: {
									"_token": window.Laravel.csrfToken,
								},
								success: function (result) {   
									swal("success!", "Comment Deleted successfully.", "success");                                     
									fnToastSuccess("Watch and learn comment deleted.");
									lastId = 0;
									parentId = 0;
									comments = [];
									childrenCount = [];
									$(".wagdt-comments-block-main").html('');
									getComments(lastId, parentId);
								},
								error: function (xhr, status, error) {
									if(xhr.responseJSON && xhr.responseJSON.message!=""){
										swal("ohh snap!", xhr.responseJSON.message, "error");
									} else {
										swal("ohh snap!", "Something went wrong", "error");
									}
									ajaxError(xhr, status, error);
								}
							});
						}, 0)
					})
				},
			});
		});

		function getComments(lastId, parentId)
		{
			jQuery.ajax({
				url: "{!!  $module_route.'/get-comments/'.$result->slug  !!}/"+lastId+"/"+parentId+"",
				type: 'GET',
				dataType: 'json',
				data: {
					"_token": window.Laravel.csrfToken,
				},
				success: function (result) {
					if(result.data.comments.length > 0){	
						commentsCount = result.data.comment_count;					
						let newComments = comments;
						let newChildrenCount = childrenCount;
						if(parentId == 0 && lastId == 0) {
							newComments = result.data.comments;
							newChildrenCount = result.data.children_count;
						} else if(parentId == 0) {
							newComments = [...comments, ...result.data.comments];
							newChildrenCount = {...childrenCount, ...result.data.children_count};
						} else {
							newComments = comments.map((tempComment) => {
								if(tempComment.id == parentId) {
									tempComment.children.push(...result.data.comments);
								} else if(tempComment.children) {
									tempComment = getNewComments(result.data.comments, tempComment, parentId);
								}
								return tempComment;
							});
							newChildrenCount = {...childrenCount, ...result.data.children_count};
						}

						comments = newComments;
						childrenCount = newChildrenCount;

						$(".wagdt-comments-block-main").html('');
						comments.forEach(comment => {
							addCommentToList(comment);
						});

						if(comments.length < commentsCount){
							$(".wagdt-comments-section-main div.see-more-comment-main-div").removeClass('d-none');
							$(".wagdt-comments-section-main button.see-more-btn").attr({'data-parent-id': 0, 'data-last-id': comments.slice(-1)[0].id});
						} else {
							$(".wagdt-comments-section-main div.see-more-comment-main-div").addClass('d-none');
						}
						$('.no-comment-text').addClass('d-none');
					} else {
						$('.no-comment-text').removeClass('d-none');
					}
				},
				error: function (xhr, status, error) {
					$(location).attr("{!! url('admin/watch-and-learn');  !!}");
					if(xhr.responseJSON && xhr.responseJSON.message!=""){
						swal("ohh snap!", xhr.responseJSON.message, "error");
					} else {
						swal("ohh snap!", "Something went wrong", "error");
					}
					ajaxError(xhr, status, error);
				}
			});
		}

		function getNewComments(comments, watchAndLearnComments, parentId){
			let children = watchAndLearnComments.children;
			let newChildren = children.map((child) => {
				if(child.id == parentId) {
					if(!child.children) {
						child.children = [];
					}
					child.children.push(...comments);
				} else if(child.children) {
					child = getNewComments(comments, child, parentId);
				}
				return child;
			});
			watchAndLearnComments.children = newChildren;

			return watchAndLearnComments;
		};

		function addCommentToList(comment, appendToId = "") {
			var childLength = comment.children ? comment.children.length : 0;
			lastId = comment.children ? comment.children.slice(-1)[0].id : 0;

			var commentTemplate = $("#comment-div-clone").clone();
			commentTemplate.removeClass('d-none');
			commentTemplate.attr('id', 'comment-'+comment.id);
			commentTemplate.find('.comment-message').html(comment.message);
			commentTemplate.find('.comment-user-name').html(comment.name);
			commentTemplate.find('.comment-user-img').attr('src', comment.user.profile_image_thumb_full_path);
			commentTemplate.find('.delete-comment').removeClass('d-none').attr('id', comment.id);
			commentTemplate.find('.comment-date').html(comment.formated_created_at);
			if(appendToId == "" && comment.parent_comment_id != 0){
				appendToId = "comment-"+comment.parent_comment_id;
			}
			if(appendToId){
				commentTemplate.addClass('ml-3 ml-lg-5');
				$(commentTemplate).insertBefore("#"+appendToId+" .see-more-comment-div:last");
			} else {
				$(".wagdt-comments-block-main").append(commentTemplate);
			}

			if(childrenCount[comment.id] > childLength){
				commentTemplate.find(".see-more-comment-div").removeClass('d-none');
				commentTemplate.find(".see-more-btn").attr({'data-parent-id': comment.id, 'data-last-id': lastId});
			} else {
				commentTemplate.find(".see-more-comment-div").addClass('d-none');
			}

			if(comment.children && comment.children.length > 0){
				comment.children.forEach(child => {
					addCommentToList(child, "comment-"+comment.id);
				});
			}
		}
    </script>

@endpush

@endsection