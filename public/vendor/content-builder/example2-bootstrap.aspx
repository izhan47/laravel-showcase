<%@ Page Language="VB"%>
<%@ OutputCache Location="None" VaryByParam="none"%>

<script runat="server">

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        
        If Not Page.IsPostBack Then
            If Not IsNothing(Session("content-bootstrap")) Then
                 litContent.Text = Session("content-bootstrap") 
            Else
                'Optional initial content
                litContent.Text = "<div class=""row""><div class=""col-md-12"">" & _
                    "<div class=""display""><h1>Lorem Ipsum is simply dummy text of the printing and typesetting industry</h1></div>" & _
                    "</div></div>" & _
                    "<div class=""row"">" & _
                    "<div class=""col-md-12"">" & _
                    "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus leo ante, consectetur sit amet vulputate vel, dapibus sit amet lectus.</p>" & _
                    "</div></div>"
            End If

        End If

    End Sub
    
    Protected Sub btnPost_Click(sender As Object, e As System.EventArgs) Handles btnPost.Click

        'Get Submitted Content
        Dim sContent As String = System.Uri.UnescapeDataString(hidContent.Value)
        
        'You can save the content into a database. in this example we just display the content back.
        Session("content-bootstrap") = sContent
        
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

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/minimalist-basic/content-bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="contentbuilder/contentbuilder.css" rel="stylesheet" type="text/css" />
        <style>
        body { background-color: #efefef; }
        .is-container {  margin: 90px auto; max-width: 1050px; width:100%; padding:55px 50px; box-sizing:border-box; background-color: #f7f7f7;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);}
        @media all and (max-width: 1080px) {
            .is-container { margin:0; padding:55px 35px; }
        }       
        
        body {margin:0 0 57px} /* give space 70px on the bottom for panel */
        #panelCms {width:100%;height:57px;border-top: #eee 1px solid;background:rgba(255,255,255,0.95);position:fixed;left:0;bottom:0;padding:10px;box-sizing:border-box;text-align:center;white-space:nowrap;z-index:10001;}
        #panelCms button {border-radius:4px;padding: 10px 15px;text-transform:uppercase;font-size: 11px;letter-spacing: 1px;line-height: 1;}
    </style>
</head>
<body>

<div id="contentarea" class="is-container container-fluid">
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
</div>

<script src="contentbuilder/jquery.min.js" type="text/javascript"></script>
<script src="contentbuilder/jquery-ui.min.js" type="text/javascript"></script>
<script src="contentbuilder/contentbuilder.js" type="text/javascript"></script>
<script src="contentbuilder/saveimages.js" type="text/javascript"></script>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        $("#contentarea").contentbuilder({
            snippetFile: 'assets/minimalist-basic/snippets-bootstrap.html',
            snippetOpen: true,
            toolbar: 'left',
            iconselect: 'assets/ionicons/selecticon.html'
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
