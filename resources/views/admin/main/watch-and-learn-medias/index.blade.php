@extends('admin.layouts.admin')

@push("styles")
    <link rel="stylesheet" href="{{ asset('plugins/media/media.css') }}">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
                <h1 class="wag-admin-page-title-main">{{ $module_name }} List</h1>
        </div>
    </div>
    <div class="wag-medias-list-block-main">
        <form method="post" action="{{ $module_route }}" enctype="multipart/form-data" class="dropzone"
            id="li-media-dropzone">
            {{ csrf_field() }}
            <div class="dz-message">
                <div class="message text-center">Drop files here or Click to upload</div>
            </div>
            <div class="fallback">
                <input type="file" name="file" multiple>
            </div>
        </form>
        <div id="image-list" class="row wag-images-medias-list-page">
            {!! $html !!}
        </div>
    </div>

    {{-- <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <form method="post" action="{{ $module_route }}" enctype="multipart/form-data" class="dropzone mb-3"
                            id="li-media-dropzone">
                            {{ csrf_field() }}
                            <div class="dz-message">
                                <div class="col-xs-8">
                                    <div class="message text-center">Drop files here or Click to upload</div>
                                </div>
                            </div>
                            <div class="fallback">
                                <input type="file" name="file" multiple>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div id="image-list">{!! $html !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

</section>


@endsection

@push("scripts")
<script src="{{ asset('plugins/media/media.js') }}"></script>
<script>
        var nxtPage = 2;
        $(document).ready(function(){

            $("#li-media-dropzone").dropzone({
                uploadMultiple: true,
                parallelUploads: 2,
                maxFilesize: 16,
                dictFileTooBig: 'Image is larger than 16MB',
                timeout: 10000,
                acceptedFiles: 'image/*',
                init: function () {
                    this.on("complete", function(file) {
                        this.removeFile(file);
                        reset_media();
                        toastr.success("Upload media file successfully!");
                    });
                },
            });

            $(document).on('click', '#loadMore', function() {
                var $this=$(this);
                load_more($this);
            });

            $(document).on('click', '.delete-media', function() {
                swal({
                    title: "Are you sure?",
                    text: `You will not be able to recover this {{$module_name }}!`,
                    icon: "warning",
                    buttons: [true, "Yes, delete it!"],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var id = $(this).data('id');
                        var $this=$(this);
                        $.ajax({
                            type: "DELETE",
                            url: '{{ $module_route }}/'+ id,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if(response.status) {
                                    reset_media();
                                    toastr.success(response.message);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error:function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }
                })
            });

            copyImgUrl = (element) => {
                let url = $(element).data('url');
                copyToClipboard(url);
            }

        });

        function load_more($this) {
            $.ajax({
                type: "POST",
                url: '{{ $module_route }}/load-more',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    page: nxtPage
                },
                beforeSend: function () {
                    if($this) {
                        $this.remove();
                    }
                },
                success: function (response) {
                    if(response.status) {
                        nxtPage++;
                        $('#image-list').append(response.html);
                    }
                },
                error:function (error) {
                    toastr.error(error.responseJSON.message);
                }
            });
        }

        function reset_media() {
            $('#image-list').html('');
            nxtPage = 1;
            load_more(false);
        }
</script>
@endpush
