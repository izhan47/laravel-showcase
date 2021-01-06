<%@ Page Language="VB"%>
<%@ OutputCache Location="None" VaryByParam="none"%>

<script runat="server">

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        
        If Not Page.IsPostBack Then
            If Not IsNothing(Session("content-example-lightbox")) Then
                litContent.Text = Session("content-example-lightbox")
            Else
                'Optional initial content
                litContent.Text = "<div class=""row clearfix"">" & _
                    "<div class=""column full"">" & _
                        "<h1 class=""size-48 is-title1-48 is-title-bold is-upper"">Image Lightbox Example</h1><p class=""size-21""><i>Click image to enlarge and embed image with option to open larger image.</i></p>" & _
                    "</div></div>" & _
                    "<div class=""row clearfix"">" & _
                        "<div class=""column third"">" & _
                            "<p>Lorem Ipsum is simply dummy text. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.&nbsp;Vivamus leo ante, consectetur sit amet vulputate vel, dapibus sit amet lectus.</p>" & _
                        "</div>" & _
                        "<div class=""column two-third"">" & _
                            "<a href=""assets/minimalist-basic/o01-1.jpg"" title="""" class=""is-lightbox"" target=""_blank""><img src=""assets/minimalist-basic/o01-1.jpg"" alt=""""></a>" & _
                        "</div>" & _
                    "</div>"
            End If

        End If

    End Sub
    
    Protected Sub btnPost_Click(sender As Object, e As System.EventArgs) Handles btnPost.Click

        'Get Submitted Content
        Dim sContent As String = System.Uri.UnescapeDataString(hidContent.Value)
        
        'You can save the content into a database. in this example we just display the content back.
        Session("content-example-lightbox") = sContent
        
        Response.Redirect(Request.RawUrl)
       
    End Sub
  
</script>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <link href="assets/minimalist-basic/content.css" rel="stylesheet" type="text/css" />
    <link href="contentbuilder/contentbuilder.css" rel="stylesheet" type="text/css" />
    <link href="assets/scripts/simplelightbox/simplelightbox.css" rel="stylesheet" type="text/css" />
    <style>
        body { background-color: #efefef; }
        .is-container {  margin: 90px auto; max-width: 1050px; width:100%; padding:55px 35px; box-sizing:border-box; background-color: #f7f7f7;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);}
        @media all and (max-width: 1080px) {
            .is-container { margin:0; }
        }

        body {margin:0 0 57px} /* give space 70px on the bottom for panel */
        #panelCms {width:100%;height:57px;border-top: #eee 1px solid;background:rgba(255,255,255,0.95);position:fixed;left:0;bottom:0;padding:10px;box-sizing:border-box;text-align:center;white-space:nowrap;z-index:10001;}
        #panelCms button {border-radius:4px;padding: 10px 15px;text-transform:uppercase;font-size: 11px;letter-spacing: 1px;line-height: 1;}
    </style>
</head>
<body>

<div id="contentarea" class="is-container container">
    <asp:Literal ID="litContent" runat="server"></asp:Literal>
</div>

<!-- Hidden Form Fields to post content -->
<form id="form1" runat="server" style="display:none">
    <asp:HiddenField ID="hidContent" ClientIDMode="Static" runat="server" />
    <asp:Button ID="btnPost" runat="server" Text="Button" />
</form>

<!-- CUSTOM PANEL (can be used for "save" button or your own custom buttons) -->
<div id="panelCms">
    <button onclick="save()" class="btn btn-primary"> Save </button>
    <button onclick="saveForUndo()" class="btn btn-primary"> Save for Undo </button>
    <button onclick="doUndo()" class="btn btn-primary"> Undo </button>
    <button onclick="doRedo()" class="btn btn-primary"> Redo </button>
</div>

<script src="contentbuilder/jquery.min.js" type="text/javascript"></script>
<script src="contentbuilder/jquery-ui.min.js" type="text/javascript"></script>
<script src="contentbuilder/contentbuilder.js" type="text/javascript"></script>
<script src="contentbuilder/saveimages.js" type="text/javascript"></script>
<script src="assets/scripts/simplelightbox/simple-lightbox.min.js" type="text/javascript"></script>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        $("#contentarea").contentbuilder({
            snippetFile: 'assets/minimalist-basic/snippets.html',
            snippetOpen: true,
            toolbar: 'left',
            iconselect: 'assets/ionicons/selecticon.html',
            largerImageHandler: 'saveimage-large.ashx',
            onRender: function () {
                $('a.is-lightbox').simpleLightbox({ closeText: '<i style="font-size:35px" class="icon ion-ios-close-empty"></i>', navText: ['<i class="icon ion-ios-arrow-left"></i>', '<i class="icon ion-ios-arrow-right"></i>'], disableScroll: false });
            }
        });

    });


    function save() {
        
        //Save all images first
        $("#contentarea").saveimages({
            handler: 'saveimage.ashx',
            onComplete: function () {

                //Then save the content
                var sContent = $('#contentarea').data('contentbuilder').html(); //Get content

                $('#hidContent').val(encodeURIComponent(sContent));
                $('#btnPost').click();

            }
        });
        $("#contentarea").data('saveimages').save();

        $("html").fadeOut(1000);
    }

</script>

</body>
</html>
