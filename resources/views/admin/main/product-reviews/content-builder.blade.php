<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Wag Enabled">
        <meta name="author" content="Wag Enabled">

        <title>{{ config('app.name', 'Wag Enabled') }} | Content Builder</title>

        <!-- favicon icon -->
        <link rel="apple-touch-icon" sizes="57x57" href="{{asset('images/favicons/apple-icon-57x57.png')}}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{asset('images/favicons/apple-icon-60x60.png')}}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{asset('images/favicons/apple-icon-72x72.png')}}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{asset('images/favicons/apple-icon-76x76.png')}}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{asset('images/favicons/apple-icon-114x114.png')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{asset('images/favicons/apple-icon-120x120.png')}}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{asset('images/favicons/apple-icon-144x144.png')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{asset('images/favicons/apple-icon-152x152.png')}}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicons/apple-icon-180x180.png')}}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{asset('images/favicons/android-icon-192x192.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicons/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{asset('images/favicons/favicon-96x96.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicons/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('images/favicons/manifest.json')}}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{asset('images/favicons/ms-icon-144x144.png')}}">
        <meta name="theme-color" content="#ffffff">
        <link href="{{ asset('admin-theme/fonts/stylesheet.css') }}" rel="stylesheet">

        <!-- end favicon icon -->
        <link href="{{ asset('vendor/content-builder/contentbuilder/contentbuilder.css')."?version=". env("APP_CSS_VERSION", 1) }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/content-builder/assets/minimalist-basic/content-bootstrap.css')."?version=". env("APP_CSS_VERSION", 1) }}" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />


        <!-- <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css" /> -->

        <style>
            body { background-color: #6161FF; }
            .is-container {  border-radius: 4px; margin: 60px auto 150px; max-width: 1050px; width:100%; padding: 35px; box-sizing:border-box; background-color: #ffffff;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);}
            @media all and (max-width: 1080px) {
                .is-container { /* margin:0 */; }
            }

            body::-webkit-scrollbar {
                width: 12px;
            }
            body::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.49);
                border-radius: 10px;
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            }
            body::-webkit-scrollbar-thumb {
                border-radius: 20px;
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
            }

            body {margin:0 0 57px} /* give space 70px on the bottom for panel */
            #panelCms { display: flex; justify-content: center; align-items: center; width:100%;height:57px;/* border-top: #f5f5f5 1px solid; background:#ffffff; */position:fixed;left:0;bottom:0;padding:10px;box-sizing:border-box;text-align:center;white-space:nowrap;z-index:10001;}
            #panelCms button {background: #8A288F;
                padding: 8px 15px;
                height: 45px;
                min-width: 160px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                border-radius: 100px;
                font-size: 14px;
                font-weight: 500;
                color: #fff !important;
                border: none;
                cursor: pointer;
                text-transform: uppercase;}
            .back-arrow {
                color: #fff;
                position: absolute;
                top: 30px;
                left: 50px;
                font-size: 16px;
            }
            .back-arrow:hover {
                color: #fff;
                text-decoration: none;
            }

        </style>

    </head>
    <body>
        <?php $currentPageUrl =env('APP_URL');  ?>

            <a href="{{ $module_route.'/'.$watchAndLearn['id'].'/edit' }}" class="back-arrow"><i class="icon ion-arrow-left-a"></i> Back</a>
            <div id="contentarea" class="is-container container content-builder-data">
                @if(isset($watchAndLearn['description']))
                    {!! $watchAndLearn['description'] !!}
                @endif

            </div>

            <!-- CUSTOM PANEL (can be used for "save" button or your own custom buttons) -->
            <div id="panelCms">
                <button onclick="save(this)" class="wag-admin-btns-main">Save and preview</button>
            </div>

        <form id='description-form' method="POST" action="{{ $module_route.'/'.$watchAndLearn['id'].'/save-description' }}" style="display:none">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <textarea name="description" id="description"></textarea>
        </form>

        <script type="text/javascript" src="{{ asset('vendor/content-builder/contentbuilder/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/content-builder/contentbuilder/jquery-ui.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/content-builder/contentbuilder/contentbuilder.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/content-builder/contentbuilder/saveimages.js') }}"></script>

        <link href="{{ asset('plugins/new-croppie/cropper.css') }}" rel="stylesheet">
        <script src="{{ asset('plugins/new-croppie/cropper.js') }}"></script>
        <script src="{{ asset('plugins/new-croppie/main.js') }}"></script>

        <script type="text/javascript">
            var isContentUpdated = false;
            $(window).bind('beforeunload', function(e){
                if(isContentUpdated) {
                    return true;
                } else {
                    return undefined;
                }
            });

            jQuery(document).ready(() => {
                var imagePath = "{{ env('APP_URL') }}";

                var builder = $("#contentarea").contentbuilder({
                                snippetFile: "/vendor/content-builder/assets/minimalist-basic/snippets-bootstrap.php",
                                snippetOpen: true,
                                toolbar: "left",
                                container: '.container',
			row: 'row',
			cols: ['col-md-1', 'col-md-2', 'col-md-3', 'col-md-4', 'col-md-5', 'col-md-6', 'col-md-7', 'col-md-8', 'col-md-9', 'col-md-10', 'col-md-11', 'col-md-12'] 
                                iconselect: "/vendor/content-builder/assets/ionicons/selecticon.html",
                                snippetPathReplace: ['assets/minimalist-basic/', 'assets/minimalist-basic/'],
                                snippetCategories: [
                                    /*[0,"Default"],*/
                                    /*[-1,"All"],*/
                                    [36,"Color Background"],
                                    [1,"Title"],
                                    /*[2,"Title, Subtitle"],*/
                                    /*[3,"Info, Title"],*/
                                    [4,"Info, Title, Subtitle"],
                                    [5,"Heading, Paragraph"],
                                    [6,"Paragraph"],
                                    [7,"Paragraph, Images + Caption"],
                                    [8,"Heading, Paragraph, Images + Caption"],
                                    [33,"Buttons"],
                                    /*[34,"Cards"],*/
                                    [9,"Images + Caption"],
                                    [10,"Images + Long Caption"],
                                    [11,"Images"],
                                    [12,"Single Image"],
                                    /*[13,"Call to Action"],
                                    [14,"List"],*/
                                    [15,"Quotes"],
                                    /*[16,"Profile"],
                                    [17,"Map"],*/
                                    [20,"Video"],
                                    /*[18,"Social"],
                                    [21,"Services"],
                                    [22,"Contact Info"],
                                    [23,"Pricing"],*/
                                    [24,"Team Profile"],
                                    [25,"Products/Portfolio"],
                                    /*[26,"How It Works"],
                                    [27,"Partners/Clients"],
                                    [28,"As Featured On"],
                                    [29,"Achievements"],
                                    [32,"Skills"],
                                    [30,"Coming Soon"],
                                    [31,"Page Not Found"],*/
                                    [19,"Separator"],

                                    [100,"Custom Code"] /* INFO: Category 100 cannot be changed. It is used for Custom Code block */
                                ],
                                onChange: function () {
                                    isContentUpdated = true;
                                }
                            });

            });

            function save(ele) {
                $(ele).prop('disabled', true);
                $(ele).html(`<img src="/vendor/content-builder/assets/loader.gif" style="margin-right: 10px;" /> Saving...`);

                // Save all images
                $("#contentarea").saveimages({
                    handler: "{{ url( 'admin/watch-and-learn/store-media') }}",
                    _token: "{{ csrf_token() }}",
                    onComplete: function () {

                        //Get content
                        var sHTML = $('#contentarea').data('contentbuilder').html();
                        isContentUpdated = false;
                        $("#description-form").find("#description").val(sHTML);
                        $( "#description-form" ).submit();

                        $(ele).prop('disabled', false);
                        $(ele).html(`Save`);
                    }
                });
                $("#contentarea").data('saveimages').save();

            }

            function view() {
                /* This is how to get the HTML (for saving into a database) */
                var sHTML = $('#contentarea').data('contentbuilder').viewHtml();

            }
        </script>
    </body>
</html>