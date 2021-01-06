ContentBuilder.js ver. 2.4.8


*** USAGE ***

1. Includes:

	<link href="contentbuilder/contentbuilder.css" rel="stylesheet" type="text/css" />

	<script src="contentbuilder/contentbuilder.js" type="text/javascript"></script>

2. Run:

	$("#contentarea").contentbuilder({
		  snippetFile: 'snippets.html'
		  });

	The snippetFile parameter refers to a html file containing snippets collection. You can add your own snippets in this file (snippets.html).

3. To get HTML:

    var sHTML = $('#contentarea').data('contentbuilder').html();



*** OPTIONAL ***

To have left editor toolbar:

    $("#contentarea,#headerarea").contentbuilder({
        toolbar: 'left',
		.....
    });

To enable & specify Icon Selection dialog:

	$("#contentarea").contentbuilder({		
		  ...,
		  iconselect: 'assets/ionicons/selecticon.html'
		  });

To open snippet panel on first load:

	$("#contentarea").contentbuilder({
            snippetOpen: true,
            .....
            });

To view HTML:

	$('#contentarea').data('contentbuilder').viewHtml();



*** OTHERS ***

To enable custom image or file select dialog:

	$("#contentarea").contentbuilder({
            imageselect: 'images.html',
            fileselect: 'files.html',
            .....
            });

	- imageselect specifies custom page to open from the image dialog.
	- fileselect specifies custom page to open from the link dialog.
	
	Please see images.html (included in this package) as a simple example. 
	Use selectAsset() function as shown in the images.html to return a value to the dialog.

To load HTML at runtime:

	$("#contentarea").data('contentbuilder').loadHTML('<h1>Heading text</h1>');

To disable/destroy the plugin at runtime:

    if ($('#contentarea').data('contentbuilder')) $('#contentarea').data('contentbuilder').destroy();

To run custom script when a block is dropped (added) to the content:

    $("#contentarea").contentbuilder({
        onDrop: function (event, ui) {
            alert(ui.item.html());  //custom script here
        },
        .....
    });

To run custom script when content renders/updated:

    $("#contentarea").contentbuilder({
        onRender: function () {
            //custom script here
        },
        .....
    });

To make the snippet tool slide from left, use 'snippetTool' property, for example:

	$("#contentarea").contentbuilder({
            snippetTool: 'left',
            .....
            });

To disable Direct Image Embed:

    $("#contentarea").contentbuilder({
        imageEmbed: false,
        .....
    });

To disable HTML source editing:

    $("#contentarea").contentbuilder({
        sourceEditor: false,
        .....
    });


Now it's possible to make an image not replaceable. Just add data-fixed="1" to the <img> element on the snippet file (snippets.html), for example:

	<img data-fixed="1" src=".." />

To make a snippet not editable, add data-mode="readonly" on the snippet's DIV, for example:

	<div data-thumb="..../01.png">
		<div class="row clearfix" data-mode="readonly"> 
			......
		</div>
	</div>

To make a snippet not editable and cannot be deleted or duplicated, add data-mode="readonly-protected" on the snippet's DIV, for example:

	<div data-thumb="..../01.png">
		<div class="row clearfix" data-mode="readonly-protected"> 
			......
		</div>
	</div>

To have the editing toolbar always displayed (after cursor is placed on text):

    $("#contentarea").contentbuilder({
        toolbarDisplay: 'always',
        .....
    });

Now you can put assets folder not on its default location. Path adjustment will be needed using snippetPathReplace parameter, for example:

    $("#contentarea").contentbuilder({
        snippetPathReplace: ['assets/minimalist-basic/', 'mycustomfolder/assets/minimalist-basic/'],
        .....
    });

To show scroll up/down helper, use "scrollHelper" parameter  (default: false)

    $("#contentarea").contentbuilder({
        scrollHelper: true,   
        .....
    });

To implement different sliding effect for snippets, use "snippetPageSliding" parameter (default: false).
If set true, when snippets opens, the entire page will also slide.

        $("#contentarea").contentbuilder({
            snippetPageSliding: true,   
            .....
        });

To implement custom actions on image embed process, use the following events:
	- onImageBrowseClick: Triggered when image browse icon is clicked 
	- onImageSettingClick: Triggered when image settings icon is clicked
	(Image browse icon and image settings icon are displayed when you hover an image)

    $("#contentarea").contentbuilder({
        onImageBrowseClick: function () { },
        onImageSettingClick: function () { },    
        .....
    });

	- onImageSelectClick: Triggered when custom image select button is clicked 
	- onFileSelectClick: Triggered when custom file select button is clicked
	(If the events are used, custom image select button and custom file select button will be displayed on the Image Settings dialog)

    $("#contentarea").contentbuilder({
        onImageSelectClick: function () { },
        onFileSelectClick: function () { },    
        .....
    });

To activate custom tags insert button, specify "customTags" parameter with your own custom tags. Custom tags is commonly used in a CMS for adding dynamic content (ex. slider, form, etc) within the content (by replacing the tags).
Example:
	$("#contentarea").contentbuilder({
		customTags: [["Contact Form", "{%CONTACT_FORM%}"],
			["Slider", "{%SLIDER%}"],
			["My Plugin", "{%MY_PLUGIN%}"]],         
        ...
    });
Or if used in an email building application:
	$("#contentarea").contentbuilder({
        customTags: [["First Name", "{%first_name%}"],
            ["Last Name", "{%last_name%}"],
            ["Email", "{%email%}"]],       
        ...
    });

To make all path absolute (for used in an email building application), set "absolutePath" parameter to true.
	$("#contentarea").contentbuilder({
        absolutePath: true,       
        ...
    });

To enable "Click to Enlarge" image option, specify "largerImageHandler" with upload handler. We provide a working example in PHP and ASP.NET for the upload handler.
	
	In PHP:

	$("#contentarea").contentbuilder({            
            largerImageHandler: 'saveimage-large.php',
			...
        });

	In ASP.NET:

	$("#contentarea").contentbuilder({            
            largerImageHandler: 'saveimage-large.aspx',
			...
        });

	With this, when embedding image, users have an option to make the image clickable to open larger image. 

	Note:

	-	By default, larger image is saved in "uploads" folder. You can change the upload folder by editing the saveimage-large.php or saveimage-large.aspx. 
		Open the file and see commented line where you can change the upload folder.

	-	The "Click to Enlarge" option will be shown when you embed an image and click the '...' (more) icon near the 'Ok' button.
		When you click 'Ok', larger version of the image will be automatically uploaded and a link to the larger image version will be added to the embedded image 
		with additional "is-lightbox" class. This class can be used to apply lightbox JQuery plugin.
		There are many lightbox JQuery plugins that you can find and use. We provide an example in example6-lightbox.php or example6-lightbox.aspx.
		Here is the steps to use the plugin as you can see in the example:
		1) Include the required js & css:
			<link href="assets/scripts/simplelightbox/simplelightbox.css" rel="stylesheet" type="text/css" />
			<script src="assets/scripts/simplelightbox/simple-lightbox.min.js" type="text/javascript"></script>
		2) Run the plugin:
			$('a.is-lightbox').simpleLightbox(..)
		
			Optionally, this can also be run on ContentBuilder's "onRender" event to make sure that it is also clickable during editing.

To enable/disable Custom Code snippet/block, set "snippetCustomCode" parameter (default: false):

	$("#contentarea").contentbuilder({            
            snippetCustomCode: true,
			...
        });

	Then, make sure you have applied the latest snippet (folder: assets/minimalist-basic/) from the package.
	Or if you use your own custom snippet, add the following block into your snippets.html (Just make sure that it has data-cat="100". You can change the data-num.):

		<div data-num="1000" data-thumb="assets/minimalist-basic/thumbnails/code.png" data-cat="100">
			<div class="row clearfix" data-mode="code" data-html="%3Cdiv%20class%3D%22column%20full%22%3E%0A%0A%3Cp%20id%3D%22{id}%22%3E%0ALorem%20ipsum%0A%3C%2Fp%3E%0A%3Cp%3E%0AThis%20is%20a%20code%20block.%20You%20can%20edit%20this%20block%20using%20the%20source%20dialog.%0A%3C%2Fp%3E%0A%0A%3Cscript%3E%0A%2F*%20Example%20of%20script%20block%20*%2F%0A%24('%23{id}').html('%3Cb%3EHello%20World!%3C%2Fb%3E')%3B%0A%3C%2Fscript%3E%0A%0A%3C%2Fdiv%3E">
				<div class="column full">

				</div>
			</div>
		</div>

	If you're using Bootstrap snippets (snippets-bootstrap.html):

		<div data-num="301" data-thumb="assets/minimalist-basic/thumbnails/code.png" data-cat="100">
			<div class="row" data-mode="code" data-html="%3Cdiv%20class%3D%22column%20full%22%3E%0A%0A%3Cp%20id%3D%22{id}%22%3E%0ALorem%20ipsum%0A%3C%2Fp%3E%0A%3Cp%3E%0AThis%20is%20a%20code%20block.%20You%20can%20edit%20this%20block%20using%20the%20source%20dialog.%0A%3C%2Fp%3E%0A%0A%3Cscript%3E%0A%2F*%20Example%20of%20script%20block%20*%2F%0A%24('%23{id}').html('%3Cb%3EHello%20World!%3C%2Fb%3E')%3B%0A%3C%2Fscript%3E%0A%0A%3C%2Fdiv%3E">
				<div class="col-md-12">

				</div>
			</div>
		</div>

To change Code Block message (displayed on source dialog for Custom Code snippet/block), use "snippetCustomCodeMessage" parameter:

	$("#contentarea").contentbuilder({            
            snippetCustomCodeMessage: '<b>IMPORTANT</b>: This is a code block. Custom javascript code (&lt;script&gt; block) is allowed here but may not always work or compatible with the content builder, so proceed at your own risk. We do not support problems with custom code.',
			...
        });

To configure editor toolbar buttons, use "buttons" parameter:

	$("#contentarea").contentbuilder({  
			buttons: ["bold", "italic", "formatting", "textsettings", "color", "font", "formatPara", "align", "list", "table", "image", "createLink", "unlink", "icon", "tags", "removeFormat", "html"],      
			...
        });

To enable slider snippet, add new snippet category using "addSnippetCategories" parameter and specify "moduleConfig" parameter as follows:

	$("#contentarea").contentbuilder({  
            addSnippetCategories: [[35, "Slider/Slideshow"]],
            moduleConfig: [{
                "moduleSaveImageHandler": "saveimage-module.ashx" /* for module purpose image saving (ex. slider) */
            }],
			...
        });

	Then include the following js & css:

    <link href="assets/scripts/slick/slick.css" rel="stylesheet" type="text/css" />
    <link href="assets/scripts/slick/slick-theme.css" rel="stylesheet" type="text/css" />
	<script src="assets/scripts/slick/slick.min.js" type="text/javascript"></script>
	
	Note:
	The above new category "Slider/Slideshow" will be shown in category dropdown. In the snippets.html file the slider snippet is already defined there with category id=35, that's why we specify:
	
		addSnippetCategories: [[35, "Slider/Slideshow"]]

	The "moduleSaveImageHandler" is a parameter to specify image upload handler for the slider. We prepared 2 handlers that you can choose (and customize if needed):

		- saveimage-module.php (for PHP)
		- saveimage-module.ashx (for ASP.NET)

	Try drag & drop slider block into the content and click the slider block. You will see configuration icon on the left. When clicked, a dialog containing the slider settings will be opened.
	This dialog opens silder module file located in 'modules' folder: assets/modules/slider.html. 
	If you need to change the location the 'modules' folder, you need to use the modulePath parameter:

	$("#contentarea").contentbuilder({  
            modulePath: 'assets/modules/',
			...
        });
	
	
	
*** EXAMPLES ***


- example1.html (basic)


- example2.php and example2.aspx (with saving example)

	This example saves all images first and then save the html content. To save all embedded images:

	Step 1: Include SaveImages.js plugin:

		<script src="contentbuilder/saveimages.js" type="text/javascript"></script>

	Step 2: Implement Saving as follows:

		function save() {
        
			//Save all images
			$("#contentarea").saveimages({
				handler: 'saveimage.php',
				onComplete: function () {

					//Get content
					var sHTML = $('#contentarea').data('contentbuilder').html();

					//Save content
					.....

				}
			});
			$("#contentarea").data('saveimages').save();

		}

	Step 3: Specify folder on the server for storing images on saveimage.php (or saveimage.ashx if you're using .NET).

- example3.php and example3.aspx (multiple instance example)

- example4.html (enable custom image or file select dialog)

	To try, hover an image, click the link icon to open the link dialog. Here you will see additional button to open your custom image/file select dialog)

- example5.html (simple custom snippets)

- example6-lightbox.php and example6-lightbox.aspx (shows how to enable "Click to Enlarge" image option and how to use a simple lightbox JQuery plugin)

- example7.html (Example of Code Block & Slider Module/Snippet)


*** SPECIAL HOLIDAY GIFT (Dec 2016) ***

New Extra Blocks for email building using Foundation for Emails framework (assets/emailsnippets/snippets.html).

Checkout the example: example-email.html 

Usage: 

    $("#contentarea").contentbuilder({
        snippetFile: 'assets/emailsnippets/snippets.html',
        absolutePath:true,
        snippetOpen: true,
        toolbar: 'left',   
        customTags: [["First Name", "{%first_name%}"],
            ["Last Name", "{%last_name%}"],
            ["Email", "{%email%}"]],         
        snippetCategories: [
            [0,"Default"],
            [-1, "All"],
            [1, "Logo"],
            [2,"Title"],
            [3,"Title, Subtitle"],
            [4,"Info, Title"],
            [5,"Info, Title, Subtitle"],
            [6,"Heading, Paragraph"],
            [7,"Paragraph"],
            [8, "Buttons"],
            [9, "Callouts"],
            [10,"Images + Caption"],
            [11,"Images + Long Caption"],
            [12, "Images"],
            [13, "List"],
            [14,"Call to Action"],
            [15, "Pricing"],
            [16, "Quotes"],
            [17, "Profile"],
            [18, "Contact Info"],
            [19, "Footer"],
            [20,"Separator"]
            ]
    });

More about Foundation for Email framework: http://foundation.zurb.com/emails/getting-started.html


*** SNIPPETS ***


All examples use a snippets collection named "minimalist-basic", which is available at:
	
		- assets/minimalist-basic/snippets.html

	This collection is a basic version of our large snippets collection which is available at:

	http://innovastudio.com/content-builder/never-write-boring-content-again.aspx

	Here are the default snippets' categories:

		$("#contentarea").contentbuilder({
				 ...
				 snippetCategories: [
					[0,"Default"],
					[-1,"All"],
					[1,"Title"],
					[2,"Title, Subtitle"],
					[3,"Info, Title"],
					[4,"Info, Title, Subtitle"],
					[5,"Heading, Paragraph"],
					[6,"Paragraph"],
					[7,"Paragraph, Images + Caption"],
					[8,"Heading, Paragraph, Images + Caption"],
					[33,"Buttons"],
					[34,"Cards"],
					[9,"Images + Caption"],
					[10,"Images + Long Caption"],
					[11,"Images"],
					[12,"Single Image"],
					[13,"Call to Action"],
					[14,"List"],
					[15,"Quotes"],
					[16,"Profile"],
					[17,"Map"],
					[20,"Video"],
					[18,"Social"],
					[21,"Services"],
					[22,"Contact Info"],
					[23,"Pricing"],
					[24,"Team Profile"],
					[25,"Products/Portfolio"],
					[26,"How It Works"],
					[27,"Partners/Clients"],
					[28,"As Featured On"],
					[29,"Achievements"],
					[32,"Skills"],
					[30,"Coming Soon"],
					[31,"Page Not Found"],
					[19,"Separator"],
					[100,"Custom Code"] /* INFO: Category 100 cannot be changed. It is used for Custom Code block */
				]
			});

	On the snippets file, you can add, for example, data-cat="0,6" means it will be displayed on "Default" and "Paragraph" category.

	For example:
 
		<div data-thumb="assets/minimalist/thumbnails/g01.png" data-cat="0,6">
			.....HTML snippet here....
		</div>

	With this format, you can add your own snippets.
	

*** SUPPORT ***

Email us at: support@innovastudio.com



---- IMPORTANT NOTE : ---- 
1. Custom Development is beyond of our support scope.
 
Once you get the HTML content, then it is more of to user's custom application (eg. posting it to the server for saving into a file, database, etc).
PHP programming, ASP.NET programming or server side implementation is beyond of our support scope. 
We also do not provide free custom development of extra features or functionalities.

2. Our support doesn't cover custom integration into users' applications. It is users' responsibility.
------------------------------------------
