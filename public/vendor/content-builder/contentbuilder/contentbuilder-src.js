/*
ContentBuilder.js ver.2.4.8
*/

var cb_list='';
var cb_edit = true;
var cb_snippetList = '#divSnippetList';
var cb_snippetPageSliding = false;

var oScripts=document.getElementsByTagName("script");
var sScriptPath;
for(var i=0;i<oScripts.length;i++) {
    var sSrc=oScripts[i].src.toLowerCase();
    if(sSrc.indexOf("contentbuilder-src.js")!=-1) sScriptPath=oScripts[i].src.replace(/contentbuilder-src.js/,"");
    if(sSrc.indexOf("contentbuilder.js")!=-1) sScriptPath=oScripts[i].src.replace(/contentbuilder.js/,"");
}

var sScriptPathArray = sScriptPath.split("?");
sScriptPath = sScriptPathArray[0];

var sc = document.createElement('script');
sc.src = sScriptPath + 'load-image.all.min.js';
document.getElementsByTagName('head')[0].appendChild(sc);

var sc2 = document.createElement('link');
sc2.rel = 'stylesheet';
sc2.type = 'text/css';
sc2.href = sScriptPath + 'codemirror/lib/codemirror.css';
document.getElementsByTagName('head')[0].appendChild(sc2);

(function (jQuery) {

    var $activeRow;

    jQuery.contentbuilder = function (element, options) {

        var defaults = {
            selectable: "h1,h2,h3,h4,h5,h6,p,blockquote,ul,ol,small,.edit,td,i",
            editMode: 'default',
            onChange: function () { },
            onRender: function () { },
            onDrop: function () { },
            onImageBrowseClick: function () { },
            onImageSettingClick: function () { },
            snippetFile: 'assets/default/snippets.html',
            modulePath: 'assets/modules/',
            snippetPathReplace: ['',''],
            hiquality: false,
            snippetTool: 'right',
            snippetOpen: false,
            snippetPageSliding: false,
            scrollHelper: false,
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
                ],
            addSnippetCategories: [],
            snippetCustomCode: false,
            snippetCustomCodeMessage: '<b>IMPORTANT</b>: This is a code block. Custom javascript code (&lt;script&gt; block) is allowed here but may not always work or compatible with the content builder, so proceed at your own risk. We do not support problems with custom code.',
            imageselect: '',
            fileselect: '',
            onImageSelectClick: function () { },
            onFileSelectClick: function () { },
            iconselect: '',
            imageEmbed: true,
            sourceEditor: true,
            buttons: ["bold", "italic", "formatting", "textsettings", "color", "font", "formatPara", "align", "list", "table", "image", "createLink", "unlink", "icon", "tags", "removeFormat", "html"],
            colors: ["#ffffc5","#e9d4a7","#ffd5d5","#ffd4df","#c5efff","#b4fdff","#c6f5c6","#fcd1fe","#ececec",
                "#f7e97a","#d09f5e","#ff8d8d","#ff80aa","#63d3ff","#7eeaed","#94dd95","#ef97f3","#d4d4d4",
                "#fed229","#cc7f18","#ff0e0e","#fa4273","#00b8ff","#0edce2","#35d037","#d24fd7","#888888",
                "#ff9c26","#955705","#c31313","#f51f58","#1b83df","#0bbfc5","#1aa71b","#ae19b4","#333333"],
            snippetList: '#divSnippetList',
            snippetCategoryList: '#divTool',
            toolbar: 'top',
            toolbarDisplay: 'auto',
            axis: '',
            hideDragPreview: false,
            customval: '',
            largerImageHandler: '',
            absolutePath: false,
            customTags: [],
            moduleConfig: []
        };

        this.settings = {};

        var $element = jQuery(element),
                    element = element;

        this.undoList = [];
        this.redoList = [];



        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

            var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
            if(is_firefox) this.settings.hideDragPreview = true; //prevent drag preview positioning problem

            //$element.css('zoom', 1);
            //$element.css('-moz-transform', 'scale(1)');
            $element.addClass('connectSortable');

            /**** Get list of editable area ****/
            if(cb_list==''){
                cb_list = '#'+$element.attr('id');
            }
            else {
                cb_list = cb_list + ',#'+$element.attr('id');
            }

            /**** Set global vars (used by all instances of ContentBuilder) ****/
            cb_snippetList = this.settings.snippetList;
            cb_snippetPageSliding = this.settings.snippetPageSliding;

            /**** Enlarge droppable area ****/
            //$element.css({ 'min-height': '50px' });

            /**** Localize All ****/
            if (jQuery('#divCb').length == 0) {
                jQuery('body').append('<div id="divCb"></div>');
            }

            /**** Load snippets library ****/

            for(var i=0;i<this.settings.addSnippetCategories.length;i++){
                this.settings.snippetCategories.push(this.settings.addSnippetCategories[i]);
            }

            if (jQuery('.is-snippet-list').length == 0) {

                //Prepare snippet categories
                var html_catselect = '';
                if( this.settings.snippetCustomCode == false ) {
                    for(var i=0;i<this.settings.snippetCategories.length;i++){
                        if(this.settings.snippetCategories[i][0]==100){
                            this.settings.snippetCategories.splice(i, 1); //Hide custom code snippet if parameter "snippetCustomCode" is set false.
                        }
                    }
                }

                var html_catselect = '<div id="divSnippetCat" style="display:none">Default <span class="caret"></span></div>' +
                    '<div id="divSnippetCatOptions">';
                for(var i=0;i<this.settings.snippetCategories.length;i++){
                    html_catselect += '<div data-value="' + this.settings.snippetCategories[i][0] + '">' + this.settings.snippetCategories[i][1] + '</div>';
                }
                html_catselect += '</div>';

                //Prepare snippet toolbar
                if(cb_snippetList=='#divSnippetList'){

                    var html_tool = '<div id="divTool"></div>';

                    jQuery('#divCb').append(html_tool);
                }

                //Add snippet categories
                jQuery(this.settings.snippetCategoryList).append(html_catselect); //this needs to be separated because of possibility for custom placement via parameter "snippetCategoryList"

                //Prepare snippet toolbar (continued)
                if(cb_snippetList=='#divSnippetList'){
                    var html_toolcontent = '<div id="divToolWait" style="position:absolute;top:0;left:0;width:100%;height:100%;display:table;background:rgba(255,255,255,0.2);z-index:1;">' +
                            '<div style="display:table-cell;vertical-align:middle;text-align:center;background:rgb(217, 217, 217);"><div class="loading">' +
                                        '<div class="dot"></div>' +
                                        '<div class="dot"></div>' +
                                        '<div class="dot"></div>' +
                                    '</div></div>' +
                        '</div>';
                    html_toolcontent += '<div id="divSnippetList"></div>';
                    html_toolcontent += '<a id="lnkToolOpen" href="#"><i class="cb-icon-left-open-big" style="font-size: 15px;"></i></a>';

                    var html_scrollhelper = '<div id="divSnippetScrollUp" style="display:none;background:rgba(0,0,0,0.3);width:45px;height:45px;line-height:45px;color:#eee;position:fixed;z-index:100000;border-radius:8px;text-align:center;font-size:12px;cursor:pointer;font-family:sans-serif;">&#9650;</div>' +
                         '<div id="divSnippetScrollDown" style="display:none;background:rgba(0,0,0,0.3);width:45px;height:45px;line-height:45px;color:#eee;position:fixed;z-index:100000;border-radius:8px;text-align:center;font-size:12px;cursor:pointer;font-family:sans-serif;">&#9660;</div>';

                    jQuery('#divTool').append(html_toolcontent);
                    jQuery('#divCb').append(html_scrollhelper);

                    //Interaction: Scroll Helper for Touch Devices
                    var maxScroll=100000000;
                    jQuery('#divSnippetScrollUp').css('display','none');
                    jQuery('#divSnippetScrollUp').on("click touchup", function(e) {
                        jQuery("#divSnippetList").animate({ scrollTop: (jQuery("#divSnippetList").scrollTop() - (jQuery("#divSnippetList").height()-150) ) + "px" },300, function(){
                            if(jQuery("#divSnippetList").scrollTop()!=0){
                                jQuery('#divSnippetScrollUp').fadeIn(300);
                            } else {
                                 jQuery('#divSnippetScrollUp').fadeOut(300);
                            }
                            if(jQuery("#divSnippetList").scrollTop() != maxScroll){
                                jQuery('#divSnippetScrollDown').fadeIn(300);
                            } else {
                                 jQuery('#divSnippetScrollDown').fadeOut(300);
                            }
                        });

                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    });
                    jQuery('#divSnippetScrollDown').on("click touchup", function(e) {
                        jQuery("#divSnippetList").animate({ scrollTop: (jQuery("#divSnippetList").scrollTop() + (jQuery("#divSnippetList").height()-150) ) + "px" }, 300, function() {
                            if(jQuery("#divSnippetList").scrollTop()!=0){
                                jQuery('#divSnippetScrollUp').fadeIn(300);
                            } else {
                                jQuery('#divSnippetScrollUp').fadeOut(300);

                            }
                            if(maxScroll==100000000){
                                maxScroll = jQuery('#divSnippetList').prop('scrollHeight') - jQuery('#divSnippetList').height() - 10;
                            }

                            if(jQuery("#divSnippetList").scrollTop() != maxScroll){
                                jQuery('#divSnippetScrollDown').fadeIn(300);
                            } else {
                                jQuery('#divSnippetScrollDown').fadeOut(300);
                            }
                        });

                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    });


                    /* Snippet Tool (Left pr Right) */
                    var $window = jQuery(window);
                    var windowsize = $window.width();
                    var toolwidth = 255;
                    if (windowsize < 600) {
                        toolwidth = 150;
                    }

                    var bUseScrollHelper=this.settings.scrollHelper;
                    if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
                        bUseScrollHelper=true;
                    }

                    if (this.settings.snippetTool == 'right') {

                        // Sliding from Right
                        jQuery('#divSnippetScrollUp').css('right','10px');
                        jQuery('#divSnippetScrollDown').css('right','10px');

                        //alert(jQuery('#divTool').css('right'))
                        if(jQuery('#divTool').css('right')!='0px'){ //if right=0px means snippets already exist and opened, so on new init (new instance), don't close by setting it =-toolwidth.
                            jQuery('#divTool').css('width', toolwidth + 'px');
                            jQuery('#divTool').css('right', '-' + toolwidth + 'px');
                        }
                        jQuery("#lnkToolOpen").off('click');
                        jQuery("#lnkToolOpen").click(function (e) {

                            //Clear Controls (clearControls)
                            jQuery('.row-tool').stop(true, true).fadeOut(0);
                            //jQuery(".ui-draggable").removeClass('code');
                            jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                            jQuery('#rte-toolbar').css('display', 'none');
                            jQuery('.rte-pop').css('display', 'none');

                            if(cb_snippetPageSliding ||
                                ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)))
                                ) {
                                if (parseInt(jQuery('#divTool').css('right')) == 0) {
                                    //Close
                                    jQuery('#divTool').animate({
                                        right: '-=' + toolwidth + 'px'
                                    }, 200);
                                    jQuery('body').animate({
                                    marginRight: '-=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#rte-toolbar').animate({ // Slide the editor toolbar
                                    paddingRight: '-=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#lnkToolOpen i').attr('class','cb-icon-left-open-big');

                                    jQuery('#divSnippetScrollUp').fadeOut(300);
                                    jQuery('#divSnippetScrollDown').fadeOut(300);
                                } else {
                                    //Open
                                    jQuery('#divTool').animate({
                                        right: '+=' + toolwidth + 'px'
                                    }, 200);
                                    jQuery('body').animate({
                                    marginRight: '+=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#rte-toolbar').animate({ // Slide the editor toolbar
                                    paddingRight: '+=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#lnkToolOpen i').attr('class','cb-icon-right-open-big');

                                    if(bUseScrollHelper){
                                        var ypos = jQuery('#divSnippetList').height()/2 - 60;
                                        jQuery('#divSnippetScrollUp').css('top',ypos);
                                        jQuery('#divSnippetScrollDown').css('top',ypos + 60);
                                        if(jQuery("#divSnippetList").scrollTop()!=0){
                                            jQuery('#divSnippetScrollUp').fadeIn(300);
                                        } else {
                                            jQuery('#divSnippetScrollUp').fadeOut(300);
                                        }
                                        jQuery('#divSnippetScrollDown').fadeIn(300);
                                    }
                                }
                                jQuery('#rte-toolbar').css('display', 'none');
                            } else {
                                if (parseInt(jQuery('#divTool').css('right')) == 0) {
                                    //Close
                                    jQuery('#divTool').animate({
                                        right: '-=' + toolwidth + 'px'
                                    }, 200);
                                    jQuery('#lnkToolOpen i').attr('class','cb-icon-left-open-big');

                                    jQuery('#divSnippetScrollUp').css('display','none');
                                    jQuery('#divSnippetScrollDown').css('display','none');
                                } else {
                                    //Open
                                    jQuery('#divTool').animate({
                                        right: '+=' + toolwidth + 'px'
                                    }, 200);
                                    jQuery('#lnkToolOpen i').attr('class','cb-icon-right-open-big');

                                    if(bUseScrollHelper){
                                        var ypos = jQuery('#divSnippetList').height()/2 - 60;
                                        jQuery('#divSnippetScrollUp').css('top',ypos);
                                        jQuery('#divSnippetScrollDown').css('top',ypos + 60);
                                        if(jQuery("#divSnippetList").scrollTop()!=0){
                                            jQuery('#divSnippetScrollUp').fadeIn(300);
                                        } else {
                                            jQuery('#divSnippetScrollUp').fadeOut(300);
                                        }
                                        jQuery('#divSnippetScrollDown').fadeIn(300);
                                    }
                                }
                            }

                            e.preventDefault();
                        });

                        //Adjust the row tool
                        jQuery('.row-tool').css('right', 'auto');
                        if (windowsize < 600) {
                            jQuery('.row-tool').css('left', '-30px'); //for small screen
                        } else {
                            jQuery('.row-tool').css('left', '-37px');
                        }

                        if(this.settings.snippetOpen){
                            if(jQuery('#divTool').attr('data-snip-open') != 1){
                                jQuery('#divTool').attr('data-snip-open',1);
                                jQuery('#divTool').animate({
                                    right: '+=' + toolwidth + 'px'
                                }, 900);
                                jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');
                            }
                        }

                    } else {

                        // Sliding from Left
                        jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');
                        jQuery('#divSnippetScrollUp').css('left','10px');
                        jQuery('#divSnippetScrollDown').css('left','10px');

                        jQuery('#divTool').css('width', toolwidth + 'px');
                        jQuery('#divTool').css('left', '-' + toolwidth + 'px');

                        jQuery('#lnkToolOpen').addClass('leftside');

                        jQuery("#lnkToolOpen").off('click');
                        jQuery("#lnkToolOpen").click(function (e) {

                            //Clear Controls (clearControls)
                            jQuery('.row-tool').stop(true, true).fadeOut(0);
                            //jQuery(".ui-draggable").removeClass('code');
                            jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                            jQuery('#rte-toolbar').css('display', 'none');
                            jQuery('.rte-pop').css('display', 'none');

                            if(cb_snippetPageSliding ||
                                ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)))
                                ) {
                                if (parseInt(jQuery('#divTool').css('left')) == 0) {
                                    //Close
                                    jQuery('#divTool').animate({
                                        left: '-=' + (toolwidth + 0) + 'px'
                                    }, 200);
                                    jQuery('body').animate({
                                    marginLeft: '-=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#rte-toolbar').animate({
                                    paddingLeft: '-=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');

                                    jQuery('#divSnippetScrollUp').fadeOut(300);
                                    jQuery('#divSnippetScrollDown').fadeOut(300);
                                } else {
                                    //Open
                                    jQuery('#divTool').animate({
                                        left: '+=' + (toolwidth + 0) + 'px'
                                    }, 200);
                                    jQuery('body').animate({
                                    marginLeft: '+=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery('#rte-toolbar').animate({ // CUSTOM
                                    paddingLeft: '+=' + toolwidth + 'px'
                                    }, 250);
                                    jQuery("#lnkToolOpen i").attr('class','cb-icon-left-open-big');

                                    if(bUseScrollHelper){
                                        var ypos = jQuery('#divSnippetList').height()/2 - 60;
                                        jQuery('#divSnippetScrollUp').css('top',ypos);
                                        jQuery('#divSnippetScrollDown').css('top',ypos + 60);
                                        if(jQuery("#divSnippetList").scrollTop()!=0){
                                            jQuery('#divSnippetScrollUp').fadeIn(300);
                                        } else {
                                            jQuery('#divSnippetScrollUp').fadeOut(300);
                                        }
                                        jQuery('#divSnippetScrollDown').fadeIn(300);
                                    }
                                }
                                jQuery('#rte-toolbar').css('display', 'none');
                                jQuery('.rte-pop').css('display', 'none');
                            } else {
                                if (parseInt(jQuery('#divTool').css('left')) == 0) {
                                    //Close
                                    jQuery('#divTool').animate({
                                        left: '-=' + (toolwidth + 0) + 'px'
                                    }, 200);
                                    jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');

                                    jQuery('#divSnippetScrollUp').css('display','none');
                                    jQuery('#divSnippetScrollDown').css('display','none');
                                } else {
                                    //Open
                                    jQuery('#divTool').animate({
                                        left: '+=' + (toolwidth + 0) + 'px'
                                    }, 200);
                                    jQuery("#lnkToolOpen i").attr('class','cb-icon-left-open-big');

                                    if(bUseScrollHelper){
                                        var ypos = jQuery('#divSnippetList').height()/2 - 60;
                                        jQuery('#divSnippetScrollUp').css('top',ypos);
                                        jQuery('#divSnippetScrollDown').css('top',ypos + 60);
                                        if(jQuery("#divSnippetList").scrollTop()!=0){
                                            jQuery('#divSnippetScrollUp').fadeIn(300);
                                        } else {
                                            jQuery('#divSnippetScrollUp').fadeOut(300);
                                        }
                                        jQuery('#divSnippetScrollDown').fadeIn(300);
                                    }
                                }
                            }

                            e.preventDefault();
                        });

                        //Adjust the row tool
                        jQuery('.row-tool').css('left', 'auto');
                        if (windowsize < 600) {
                            jQuery('.row-tool').css('right', '-30px'); //for small screen
                        } else {
                            jQuery('.row-tool').css('right', '-37px');
                        }

                        if(this.settings.snippetOpen){
                            if(jQuery('#divTool').attr('data-snip-open') != 1){
                                jQuery('#divTool').attr('data-snip-open',1);
                                jQuery('#divTool').animate({
                                    left: '+=' + toolwidth + 'px'
                                }, 900);
                                jQuery("#lnkToolOpen i").attr('class','cb-icon-left-open-big');
                            }
                        }
                    }


                }

                //Styling the snippet list area (#divSnippetList or custom area as specified in snippetList parameter)
                jQuery(cb_snippetList).addClass('is-snippet-list');

                //Interaction: Snippet category dropdown
                jQuery('#divSnippetCat').click(function(e){
                    if(jQuery('#divSnippetCatOptions').hasClass('active')) {
                        jQuery('#divSnippetCatOptions').removeClass('active');
                    } else {
                        jQuery('#divSnippetCatOptions').css('width', jQuery(this).css('width'));
                        jQuery('#divSnippetCatOptions').addClass('active');
                    }
					e.preventDefault();
					e.stopImmediatePropagation();
                });
                jQuery('#divSnippetCatOptions > div').click(function(){
                    var valueSelected = jQuery(this).attr('data-value');
                    jQuery('#divSnippetCat').html(jQuery(this).html() + ' <span class="caret"></span>');
                    jQuery('#divSnippetCatOptions').removeClass('active');

	                var $cbSnippetList = jQuery(cb_snippetList + ' > div');
                    if(valueSelected=='-1'){
                        $cbSnippetList.fadeIn(200);
                    } else {
		                $cbSnippetList.fadeOut(200, function () {
                            var $this = jQuery(this);
                            var $catSplit = $this.attr('data-cat').split(',');
                            for (var j = 0; j < $catSplit.length; j++) {
                                if (valueSelected == $catSplit[j]) {
                                    $this.fadeIn(400);
                                }
                            }
                        });
                    }
                });
                $('html').click(function (e) {
	                if($(e.target).parents('#divSnippetCatOptions').length > 0) return false;
	                if($(e.target).attr('id')=='divSnippetCatOptions') return false;
	                if($(e.target).parents('#divSnippetCat').length > 0) return false;
	                if($(e.target).attr('id')=='divSnippetCat') return false;
                    jQuery('#divSnippetCatOptions').removeClass('active');
                });

                //Load snippet file (assets/.../snippets.html)
                jQuery('#divCb').append('<div id="divSnippets" style="display:none"></div>');
                jQuery.get(this.settings.snippetFile, function (data) {
                    var htmlData = '';
                    var htmlThumbs = '';
                    var i = 1;
                    var bUseSnippetsFilter = false;

                    try{
                        if($element.data('contentbuilder').settings.snippetPathReplace[0]!='') {
					        var regex = new RegExp($element.data('contentbuilder').settings.snippetPathReplace[0], 'g');
                            data = data.replace(regex,$element.data('contentbuilder').settings.snippetPathReplace[1]);

                            var string1 = $element.data('contentbuilder').settings.snippetPathReplace[0].replace(/\//g, '%2F');
                            var string2 = $element.data('contentbuilder').settings.snippetPathReplace[1].replace(/\//g, '%2F');

					        var regex2 = new RegExp(string1, 'g');
					        data = data.replace(regex2,string2);
                        }
                    } catch(e){}

                    var $currentDataChildren = jQuery('<div/>').html(data).children('div');

					for (var i = 1; $currentDataChildren.length >= i; i++) {
						var $this = jQuery($currentDataChildren[i-1]);
                        var block = $this.html();
                        //Enclode each block. Source: http://stackoverflow.com/questions/1219860/html-encoding-in-javascript-jquery
                        var blockEncoded = jQuery('<div/>').text(block).html();
                        htmlData += '<div id="snip' + i + '">' + blockEncoded + '</div>'; //Encoded html prevents loading many images

                        if ($this.data("cat") != null) bUseSnippetsFilter = true;

                        var thumb = $this.data("thumb");
                        if(bUseSnippetsFilter){
                            htmlThumbs += '<div style="display:none" title="Snippet ' + i + '" data-snip="' + i + '" data-cat="' + $this.data("cat") + '"><img src="' + thumb + '" /></div>';
                        } else {
                            htmlThumbs += '<div title="Snippet ' + i + '" data-snip="' + i + '" data-cat="' + $this.data("cat") + '"><img src="' + thumb + '" /></div>';
                        }
                    }

                    jQuery('#divSnippets').html(htmlData);

                    jQuery(cb_snippetList).html(htmlThumbs);

                    //Snippets category dropdown
                    if(bUseSnippetsFilter){
                        var cats = [];

                        var defaultExists = false;
						var $cbSnippetListDivs = jQuery(cb_snippetList + ' > div');

                        for (var cbs = 0; $cbSnippetListDivs.length > cbs; cbs++) {
                            var $this = jQuery($cbSnippetListDivs[cbs]);
                            var catSplit = $this.attr('data-cat').split(',');
                            for (var j = 0; j < catSplit.length; j++) {
                                var catid = $this.attr('data-cat').split(',')[j];
                                if (catid == 0) {
                                    $this.fadeIn(400);
                                    defaultExists = true;
                                }
                                if (jQuery.inArray(catid, cats) == -1) {
                                    cats.push(catid);

                                    if( jQuery('#divSnippetCatOptions').find("[data-value='" + catid + "']").length == 0){//if category is specified in snippets.html, but not defined in "snippetCategories" param, hide it!
                                        //$this.css('display','none');
                                        $this.remove();
                                    }
                                }
                            }
                        }

                        //Remove empty categories
                        var $selSnips = jQuery('#divSnippetCatOptions');
                        var $selSnipsOption = jQuery('#divSnippetCatOptions > div');
                        for (var sso=0; $selSnipsOption.length > sso;sso++) {
                            var catid = jQuery($selSnipsOption[sso]).attr('data-value');

                            if(jQuery.inArray(catid, cats)==-1){
                                if(catid!=0 && catid!=-1){
                                    $selSnips.find("[data-value='" + catid + "']").remove();
                                }
                            }
                        }

                        if(!defaultExists){//old version: default not exists, show all (backward compatibility)
                            jQuery(cb_snippetList + ' > div').css('display','block');
                            jQuery('#divSnippetCatOptions').find("[data-value='0']").remove();
                            jQuery('#divSnippetCat').html('All <span class="caret"></span>');
                        }

                        jQuery('#divSnippetCat').css('display', 'block');
                    }

                    if(cb_snippetList=='#divSnippetList'){
                        if(bUseSnippetsFilter) {
                            //Give space for snippets filter dropdown
                            jQuery('#divSnippetList').css('border-top', 'rgba(0,0,0,0) 52px solid');
                        }
                    }

                    // Draggable
                    $element.data('contentbuilder').applyDraggable();

                    jQuery('#divToolWait').remove();
                });

            } else {

                //Snippets already added here. This section is executed if a new instance is dynamically added.

                // Draggable
                this.applyDraggable();

            }


            /**** Apply builder elements ****/
            $element.children("*").wrap("<div class='ui-draggable'></div>"); //$element.children("*").not('link').wrap("<div class='ui-draggable'></div>");
            $element.children("*").append('<div class="row-tool">' +
                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                '</div>');

            if (jQuery('#temp-contentbuilder').length == 0) {
                jQuery('#divCb').append('<div id="temp-contentbuilder" style="display: none"></div>');
            }

            /**** Apply builder behaviors ****/
            this.applyBehavior();

            // Function to run when column/grid changed
            this.blockChanged();

            /**** Trigger Render event ****/
            this.settings.onRender();

            /**** DRAG & DROP behavior ****/
            $element.sortable({
                helper: function(event, ui){
                    /*
                    Fix incorrect helper position while sorting
                    http://stackoverflow.com/questions/5791886/jquery-draggable-shows-helper-in-wrong-place-after-page-scrolled
                    */
                    var $clone =  jQuery(ui).clone();
                    $clone .css('position','absolute');

                    $clone.addClass('cloned-handler');
                    if($element.data('contentbuilder').settings.axis ==''){
                        if (!$clone.parent().is('body')) {
                            $clone.appendTo(jQuery('body'));
                        }
                    }

                    return $clone.get(0);

                  },
                sort: function(event, ui) {
                    if($element.data('contentbuilder').settings.hideDragPreview){
                        ui.helper.css({'display' : 'none'});
                    }
                },
                items: '.ui-draggable',
                connectWith: '.connectSortable', 'distance': 5,
                tolerance: 'pointer',
                handle: '.row-handle',
                delay: 200,
                cursor: 'move',
                placeholder: 'block-placeholder',

                start: function (e, ui){
                    jQuery(ui.placeholder).hide();

                    jQuery(ui.placeholder).slideUp(80);
                    cb_edit = false;
                },

                change: function (e, ui){
                    jQuery(ui.placeholder).hide().slideDown(80);
                },
                beforeStop: function (e, ui) {
                    jQuery(ui.placeholder).hide();
                },

                deactivate: function (event, ui) {

                    //When sorting, sometimes helper not removed after sorted
                    jQuery(".cloned-handler").remove();

                    /* Check if an instance exists. It can be not exist in case of destroyed during onRender */
                    if(!$element.data('contentbuilder'))return ;

                    cb_edit = true;

                    var bDrop = false;
                    if (ui.item.find('.row-tool').length==0) {
                        bDrop = true;
                    }

                    if (ui.item.parent().attr('id') == $element.attr('id')) {

                        ui.item.find("[data-html]").each(function () {//Mode: code
                            var html = (decodeURIComponent(jQuery(this).attr("data-html")));
                            //Fill the block with original code (when drag & drop code block or sorting code block)

                            //ui.item.children(0).html( html );
                            jQuery(this).html( html );
                        });

                        /*
                        var html = ui.item.html();
                        html = html.replace(/{id}/g, makeid());
                        ui.item.replaceWith(html);
                        */
                        ui.item.replaceWith(ui.item.html());


                        /*
                        var sItm = jQuery(ui.item.html());
                        ui.item.replaceWith(sItm);
                        ui.item = sItm;
                        */

                        /*
                        if(ui.item.children(0).attr('src').indexOf('thumbnails/')==-1){
                            ui.item.replaceWith(ui.item.html());
                        } else {
                             var snip = jQuery(ui.item).data('snip');
                            var snipHtml = jQuery('#snip' + snip).text();
                            ui.item.replaceWith(snipHtml);
                        }*/
                   //     alert(ui.item.html());


                        $element.children("*").each(function () {

                            if (!jQuery(this).hasClass('ui-draggable')) {
                                jQuery(this).wrap("<div class='ui-draggable'></div>");
                            }
                        });

                        $element.children('.ui-draggable').each(function () {
                            if (jQuery(this).find('.row-tool').length == 0) {
                                jQuery(this).append('<div class="row-tool">' +
                                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                                '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                                '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                                '</div>');
                            }
                        });

                        $element.children('.ui-draggable').each(function () {
                            if (jQuery(this).children('*').length == 1) {//empty (only <div class="row-tool">)
                                jQuery(this).remove();
                            }
                            //For some reason, the thumbnail is dropped, not the content (when dragging mouse released not on the drop area)
                            if (jQuery(this).children('*').length == 2) {//only 1 element
                                if(jQuery(this).children(0).prop("tagName").toLowerCase()=='img' &&
                                    jQuery(this).children(0).attr('src').indexOf('thumbnails/') != -1 ) {//check if the element is image thumbnail
                                    jQuery(this).remove();//remove it.
                                }
                            }
                        });

                        /*
                        //dropped on root
                        if (ui.item.find('.row-tool').length == 0) {
                        ui.item.append('<div class="row-tool">' +
                        '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                        '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                        '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                        '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                        '</div>');
                        }*/

                        $element.data('contentbuilder').settings.onDrop(event, ui);

                    } else {
                        //For some reason ui.item.parent().attr('id') can be undefined
                        //The thumbnail is dropped, not the content
                        //alert(ui.item.html())
                        //alert(ui.item.parent().attr('id') + ' = ' + $element.attr('id'))
                        /*
                        $element.children('.ui-draggable').each(function () {
                            jQuery(this).find('.ui-draggable-handle').each(function(){
                                jQuery(this).remove();
                            });
                        });
                        */
                        return;
                    }

                    //Apply builder behaviors
                    $element.data('contentbuilder').applyBehavior();

                    // Function to run when column/grid changed
                    $element.data('contentbuilder').blockChanged();

                    //Trigger Render event
                    $element.data('contentbuilder').settings.onRender();

                    //Trigger Change event
                    $element.data('contentbuilder').settings.onChange();

                    //Trigger Drop event
                    //if(bDrop) $element.data('contentbuilder').settings.onDrop(event, ui);
                    //$element.data('contentbuilder').settings.onDrop(event, ui);

                    //Save for Undo
                    saveForUndo();
                }
            });

            if(cb_list.indexOf(',')!=-1){
                jQuery(cb_list).sortable('option','axis',false);
            }
            if(this.settings.axis!=''){
                jQuery(cb_list).sortable('option','axis',this.settings.axis);
            }

            /* http://stackoverflow.com/questions/6285758/cannot-drop-a-draggable-where-two-droppables-touch-each-other */
            jQuery.ui.isOverAxis2 = function( x, reference, size ) {
                return ( x >= reference ) && ( x < ( reference + size ) );
            };
            jQuery.ui.isOver = function( y, x, top, left, height, width ) {
                return jQuery.ui.isOverAxis2( y, top, height ) && jQuery.ui.isOverAxis( x, left, width );
            };

            $element.droppable({
                drop: function (event, ui) {
                    if (jQuery(ui.draggable).data('snip')) {
                        var snip = jQuery(ui.draggable).data('snip');
                        var snipHtml = jQuery('#snip' + snip).text();

                        snipHtml = snipHtml.replace(/{id}/g, makeid());

                        jQuery(ui.draggable).data('snip', null); //clear
                        return ui.draggable.html(snipHtml);
                        event.preventDefault();
                    }
                },
                tolerance: 'pointer',
                greedy: true,
                hoverClass: 'drop-zone',
                activeClass: 'drop-zone',
                deactivate: function (event, ui) {
                    //If focus is still on an instance, but snippet is dropped on another instance, sortable deactivate not fully run (because of id checking). So droppable deactivate is used (to add row-tool on newly added content block)
                    jQuery(cb_list).each(function(){
                        var $cb = jQuery(this);

                        $cb.children('.ui-draggable').each(function () {
                            if (jQuery(this).find('.row-tool').length == 0) {
                                jQuery(this).append('<div class="row-tool">' +
                                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                                '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                                '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                                '</div>');
                            }
                        });

                        //Apply builder behaviors
                        $cb.data('contentbuilder').applyBehavior();

                    });
                }
            });

            jQuery(document).on('mousedown', function (event) {

                var $active_element;
                if(jQuery(event.target).parents(".ui-draggable").length>0){
                    if( jQuery(event.target).parents(".ui-draggable").parent().data('contentbuilder') ) {
                        $active_element = jQuery(event.target).parents(".ui-draggable").parent(); //Get current Builder element
                    }
                }
                //console.log(jQuery(event.target).prop("tagName").toLowerCase());

                //Remove Overlay on embedded object to enable the object.
                if (jQuery(event.target).attr("class") == 'ovl') {
                    jQuery(event.target).css('z-index', '-1');
                }

                if (jQuery(event.target).parents('.ui-draggable').length > 0 && jQuery(event.target).parents(cb_list).length > 0) {

                    var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

                    /****** Row Controls ******/
                    //jQuery(event.target).parents(".ui-draggable").removeClass('code');
                    if( jQuery(event.target).parents("[data-html]").length > 0 ) { //Mode: code
                        jQuery(event.target).parents(".ui-draggable").addClass('code');

                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-html').addClass('row-module');
                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .cb-icon-code').removeClass('cb-icon-code').addClass('cb-icon-cog');//Mode: code
                    }

                    if( jQuery(event.target).parents("[data-mode='readonly']").length > 0 ) { //Mode: readonly
                        jQuery(event.target).parents(".ui-draggable").addClass('code'); //to give different outline color whwn focused

                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-html').css('display','none'); //hide html source editor
                    }

                    if( jQuery(event.target).parents("[data-mode='readonly-protected']").length > 0 ) {
                        jQuery(event.target).parents(".ui-draggable").addClass('code'); //to give different outline color whwn focused

                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-html').css('display','none'); //hide html source editor
                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-remove').css('display','none'); //hide delete icon
                        jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-copy').css('display','none'); //hide duplicate icon
                    }

                    jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                    jQuery(event.target).parents(".ui-draggable").addClass('ui-dragbox-outlined');
                    if(is_firefox) jQuery(event.target).parents(".ui-draggable").addClass('firefox');

                    jQuery('.row-tool').stop(true, true).fadeOut(0);
                    if($active_element) {
                        if( jQuery(event.target).parents(".ui-draggable").find("[data-html-edit='off']").length > 0 || !$active_element.data('contentbuilder').settings.sourceEditor){
                            jQuery(event.target).parents(".ui-draggable").find('.row-tool .row-html').css({ display: 'none' });
                        }
                    }
                    jQuery(event.target).parents(".ui-draggable").find('.row-tool').stop(true, true).css({ display: 'none' }).fadeIn(300);
                    /****************************/

                    return;
                }

                if( jQuery(event.target).parent().attr('id') == 'rte-toolbar' ||
                    jQuery(event.target).parent().parent().attr('id') == 'rte-toolbar' ||
                    jQuery(event.target).parent().hasClass('rte-pop') ||
                    jQuery(event.target).parent().parent().hasClass('rte-pop') ||
                    jQuery(event.target).parent().hasClass('md-modal')) {
                    return;
                }

                if (jQuery(event.target).is('[contenteditable]') ||
                    jQuery(event.target).css('position') == 'absolute' ||
                    jQuery(event.target).css('position') == 'fixed' ||
                    jQuery(event.target).hasClass('md-modal')
                    ) {
                    return;
                }

                var bReturn = false;
                jQuery(event.target).parents().each(function (e) {

                    if (jQuery(this).is('[contenteditable]') ||
                        jQuery(this).css('position') == 'absolute' ||
                        jQuery(this).css('position') == 'fixed' ||
                        jQuery(this).hasClass('md-modal')
                        ) {
                        bReturn=true;
                        return;
                    }

                });
                if(bReturn) return;

                //Clear Controls (clearControls)
                jQuery('.row-tool').stop(true, true).fadeOut(0);
                //jQuery(".ui-draggable").removeClass('code');
                jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                jQuery('#rte-toolbar').css('display', 'none');
                jQuery('.rte-pop').css('display', 'none');
            });

        };

        /**** Apply Draggable to Snippets ****/
        this.applyDraggable = function (obj) {

            var bJUIStable = false;
            if(jQuery.ui.version=='1.11.0'){
                bJUIStable = true;
            }

            if(bJUIStable){

                jQuery(cb_snippetList + ' > div').draggable({
                    cursor: 'move',
                    helper: function () {
                        return jQuery("<div class='dynamic'></div>")[0];
                    },
                    delay: 200,
                    /*drag: function(e, ui) {

                        var yPos = jQuery(this).offset().top;

                        //var sLog = jQuery("#divSnippetList").scrollTop() + ' - ' +  (yPos) + ' - ' + ui.position.top ; // jQuery("#divSnippetList").get(0).scrollHeight
                        //console.log( sLog );
                        //if( yPos - ui.position.top > 110 ) {
                        //    //in Safari iOS, yPos gets incorrect value if the div scrolls down max to the bottom.
                        //    return true;
                        //}

                        if(yPos - ui.position.top > 50 && ui.position.left>-20){
                            //UP
                            jQuery("#divSnippetList").animate({ scrollTop: (jQuery("#divSnippetList").scrollTop() + (yPos - ui.position.top) * 7 ) + "px" });
                            return false;
                        } else if (yPos - ui.position.top < -50 && ui.position.left>-20) {
                            //DOWN
                            jQuery("#divSnippetList").animate({ scrollTop: (jQuery("#divSnippetList").scrollTop() + (yPos - ui.position.top) * 7 ) + "px" });
                            return false;
                        } else {
                            //DRAG
                        }

                    } ,*/
                    connectToSortable: cb_list, /*"#" + $element.attr('id'),*/
                    stop: function (event, ui) {

                        // fix bug
                        jQuery(cb_list).each(function(){
                            var $cb = jQuery(this);

                            $cb.children("div").each(function () {
                                if (jQuery(this).children("img").length == 1) {
                                    jQuery(this).remove();
                                }
                            });
                        });

                    }
                });

            } else {

                jQuery(cb_snippetList + ' > div').draggable({ //jQuery('#divSnippetList > div').draggable({
                    cursor: 'move',
                    //helper: function () { /* Custom helper not returning draggable item using the latest jQuery UI */
                    //    return jQuery("<div class='dynamic'></div>")[0];
                    //},
                    helper: "clone", /* So we use cloned draggable item as the helper */
                    drag: function (event, ui) {

                        /* Needed by latest jQuery UI: styling the helper */
                        jQuery(ui.helper).css("overflow","hidden");
                        jQuery(ui.helper).css("padding-top","60px"); //make helper content empty by adding a top padding the same as height
                        jQuery(ui.helper).css("box-sizing","border-box");
                        jQuery(ui.helper).css("width","150px");
                        jQuery(ui.helper).css("height","60px");
                        jQuery(ui.helper).css("border","rgba(225,225,225,0.9) 5px solid");
                        jQuery(ui.helper).css("background","rgba(225,225,225,0)");
                    },
                    connectToSortable: cb_list, /*"#" + $element.attr('id'),*/
                    stop: function (event, ui) {

                        // fix bug
                        jQuery(cb_list).each(function(){
                            var $cb = jQuery(this);

                            $cb.children("div").each(function () {
                                    if (jQuery(this).children("img").length == 1) {
                                    jQuery(this).remove();
                                }
                            });
                        });

                    }
                });

            }

        };

        /**** Read HTML ****/
        this.html = function () {

            //Make absolute
            if(this.settings.absolutePath) {
                $element.find('a').each(function () {
                    var href = jQuery(this).get(0).href;
                    jQuery(this).attr('href',href);
                });
                $element.find('img').each(function () {
                    var href = jQuery(this).get(0).src;
                    jQuery(this).attr('src',href);
                });
            }

            jQuery('#temp-contentbuilder').html($element.html());
            jQuery('#temp-contentbuilder').find('.row-tool').remove();
            jQuery('#temp-contentbuilder').find('.ovl').remove();
            jQuery('#temp-contentbuilder').find('[contenteditable]').removeAttr('contenteditable');
            jQuery('*[class=""]').removeAttr('class');
            jQuery('#temp-contentbuilder').find('.ui-draggable').replaceWith(function () { return jQuery(this).html() });
            jQuery("#temp-contentbuilder").find("[data-html]").each(function () {//Mode: code
                if(jQuery(this).attr("data-html")!=undefined){
                    jQuery(this).html( decodeURIComponent(jQuery(this).attr("data-html")) );
                }
            });
            var html = jQuery('#temp-contentbuilder').html().trim();
            html = html.replace(/<font/g,'<span').replace(/<\/font/g,'</span');
            return html;

        };

        this.clearControls = function () {
            jQuery('.row-tool').stop(true, true).fadeOut(0);

            //jQuery(".ui-draggable").removeClass('code');
            jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');

            var selectable = this.settings.selectable;
            $element.find(selectable).blur();
        };

        this.viewHtml = function () {

            var html=this.html();
            jQuery('#txtHtml').val(html);

            /**** Custom Modal ****/
            jQuery('#md-html').css('width', '80%');
            jQuery('#md-html').simplemodal({isModal:true});
            jQuery('#md-html').data('simplemodal').show();

            jQuery('#txtHtml').css('display', 'none');

            if(jQuery('#txtHtml').data('CodeMirrorInstance')){

                var $htmlEditor = $('#txtHtml').data('CodeMirrorInstance');
                $htmlEditor.setValue(html);

            } else {
                var myTextArea = jQuery("#txtHtml")[0];

                if (jQuery('.is-cmloaded').length == 0) { //check if js/css already loaded

                    getScripts([sScriptPath + "codemirror/lib/codemirror.js"],
                        function () {

                            getScripts([sScriptPath + "codemirror/mode/xml/xml.js",
                                sScriptPath + "codemirror/mode/javascript/javascript.js",
                                sScriptPath + "codemirror/mode/css/css.js"],
                                function () {

                                    jQuery('body').addClass('is-cmloaded');

                                    var $htmlEditor = CodeMirror.fromTextArea(myTextArea, {
                                        value: html,
                                        mode: "text/html",
                                        lineWrapping: true,
                                        lineNumbers: true,
                                        tabMode: "indent"
                                    });
                                    $htmlEditor.on("change", function(cm, change) {
                                        jQuery('#txtHtml').val(cm.getValue());
                                    });

                                    //Save instance
                                    jQuery('#txtHtml').data('CodeMirrorInstance', $htmlEditor);

                                });

                        });

                } else {

                    var $htmlEditor = CodeMirror.fromTextArea(myTextArea, {
                        value: html,
                        mode: "text/html",
                        lineWrapping: true,
                        lineNumbers: true,
                        tabMode: "indent"
                    });
                    $htmlEditor.on("change", function(cm, change) {
                        jQuery('#txtHtml').val(cm.getValue());
                    });

                    //Save instance
                    jQuery('#txtHtml').data('CodeMirrorInstance', $htmlEditor);

                }

            }

            jQuery('#btnHtmlOk').off('click');
            jQuery('#btnHtmlOk').on('click', function (e) {

                var $htmlEditor = $('#txtHtml').data('CodeMirrorInstance');
                jQuery('#txtHtml').val($htmlEditor.getValue());

                $element.html(jQuery('#txtHtml').val());

                jQuery('#md-html').data('simplemodal').hide();

                //Re-Init
                $element.children("*").wrap("<div class='ui-draggable'></div>");
                $element.children("*").append('<div class="row-tool">' +
                    '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                    '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                    '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                    '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                    '</div>');

                //Apply builder behaviors
                $element.data('contentbuilder').applyBehavior();

                // Function to run when column/grid changed
                $element.data('contentbuilder').blockChanged();

                //Trigger Render event
                $element.data('contentbuilder').settings.onRender();

                //Trigger Change event
                $element.data('contentbuilder').settings.onChange();

                //Save for Undo
                saveForUndo();

            });

            jQuery('#btnHtmlCancel').off('click');
            jQuery('#btnHtmlCancel').on('click', function (e) {

                jQuery('#md-html').data('simplemodal').hide();

            });
            /**** /Custom Modal ****/
        };

        this.loadHTML = function (html) {
            $element.html(html);

            //Re-Init
            $element.children("*").wrap("<div class='ui-draggable'></div>");
            $element.children("*").append('<div class="row-tool">' +
                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                '</div>');

            //Apply builder behaviors
            $element.data('contentbuilder').applyBehavior();

            // Function to run when column/grid changed
            $element.data('contentbuilder').blockChanged();

            //Trigger Render event
            $element.data('contentbuilder').settings.onRender();

        };

        this.applyBehavior = function () {

            //Make hyperlinks not clickable
            $element.find('a').click(function () { return false });

            //Make absolute
            if(this.settings.absolutePath) {
                $element.find('a').each(function () {
                    var href = jQuery(this).get(0).href;
                    jQuery(this).attr('href',href);
                });
                $element.find('img').each(function () {
                    var href = jQuery(this).get(0).src;
                    jQuery(this).attr('src',href);
                });
            }

            //Get settings
            var selectable = this.settings.selectable;
            var hq = this.settings.hiquality;
            var imageEmbed = this.settings.imageEmbed;
            var buttons = this.settings.buttons;
            var colors = this.settings.colors;
            var editMode = this.settings.editMode;
            var toolbar = this.settings.toolbar;
            var toolbarDisplay = this.settings.toolbarDisplay;
            var onImageSelectClick = this.settings.onImageSelectClick;
            var onFileSelectClick = this.settings.onFileSelectClick;
            var onImageBrowseClick = this.settings.onImageBrowseClick;
            var onImageSettingClick = this.settings.onImageSettingClick;
            var customTags = this.settings.customTags;

            //Custom Image Select
            var imageselect = this.settings.imageselect;
            var fileselect = this.settings.fileselect;
            var iconselect = this.settings.iconselect;
            var customval = this.settings.customval;
            var largerImageHandler = this.settings.largerImageHandler;

            //Apply ContentEditor plugin
            $element.contenteditor({ fileselect: fileselect, imageselect: imageselect, iconselect: iconselect, onChange: function(){ $element.data('contentbuilder').settings.onChange(); }, editable: selectable, buttons: buttons, colors: colors, editMode: editMode, toolbar: toolbar, toolbarDisplay: toolbarDisplay, onFileSelectClick: onFileSelectClick, onImageSelectClick: onImageSelectClick, customTags: customTags });
            $element.data('contenteditor').render();

            //Apply ImageEmbed plugin
            $element.find('img').each(function () {

                if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code

                if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                jQuery(this).imageembed({ hiquality: hq, imageselect: imageselect, fileselect: fileselect, imageEmbed: imageEmbed, onImageBrowseClick: onImageBrowseClick, onImageSettingClick: onImageSettingClick, onImageSelectClick: onImageSelectClick, onFileSelectClick: onFileSelectClick, largerImageHandler: largerImageHandler, customval: customval });
                //to prevent icon dissapear if hovered above absolute positioned image caption
                if (jQuery(this).parents('figure').length != 0) {
                    if (jQuery(this).parents('figure').find('figcaption').css('position') == 'absolute') {
                        jQuery(this).parents('figure').imageembed({ hiquality: hq, imageselect: imageselect, fileselect: fileselect, imageEmbed: imageEmbed, onImageBrowseClick: onImageBrowseClick, onImageSettingClick: onImageSettingClick, onImageSelectClick: onImageSelectClick, onFileSelectClick: onFileSelectClick, largerImageHandler: largerImageHandler, customval: customval });
                    }
                }

            });

            //Add "Hover on Embed" event
            $element.find(".embed-responsive").each(function () {

                if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code

                if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                if (jQuery(this).find('.ovl').length == 0) {
                    jQuery(this).append('<div class="ovl" style="position:absolute;background:#fff;opacity:0.2;cursor:pointer;top:0;left:0px;width:100%;height:100%;z-index:-1"></div>');
                }
            });

            $element.on('mouseenter mouseleave', '.embed-responsive', function(e) {
                switch(e.type) {
                    case 'mouseenter':
                        if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code

                        if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                        if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                        if (jQuery(this).parents(".ui-draggable").css('outline-style') == 'none') {
                            jQuery(this).find('.ovl').css('z-index', '1');
                        }
                        break;
                    case 'mouseleave':
                        jQuery(this).find('.ovl').css('z-index', '-1');
                        break;
                }
            });

            //Add "Focus" event
            $element.find(selectable).off('focus');
            $element.find(selectable).focus(function () {

                var selectable = $element.data('contentbuilder').settings.selectable;

                var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

                /****** Row Controls ******/
                jQuery(".ui-draggable").removeClass('code');
                if( jQuery(this).parents("[data-html]").length > 0 ) { //Mode: code
                    jQuery(this).parents(".ui-draggable").addClass('code');
                }

                if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) { //Mode: readonly
                    jQuery(this).parents(".ui-draggable").addClass('code');
                }

                if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) { //Mode: readonly & protected
                    jQuery(this).parents(".ui-draggable").addClass('code');
                }

                jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                jQuery(this).parents(".ui-draggable").addClass('ui-dragbox-outlined');
                if(is_firefox) jQuery(this).parents(".ui-draggable").addClass('firefox');

                jQuery('.row-tool').stop(true, true).fadeOut(0);
                if( jQuery(this).parents(".ui-draggable").find("[data-html-edit='off']").length > 0  || !$element.data('contentbuilder').settings.sourceEditor){
                    jQuery(this).parents(".ui-draggable").find('.row-tool .row-html').css({ display: 'none' });
                }
                jQuery(this).parents(".ui-draggable").find('.row-tool').stop(true, true).css({ display: 'none' }).fadeIn(300);
            });

            //Add "Click to Remove" event (row)
            $element.children("div").find('.row-remove').off('click');
            $element.children("div").find('.row-remove').click(function () {

                /**** Custom Modal ****/
                jQuery('#md-delrowconfirm').css('max-width', '550px');
                jQuery('#md-delrowconfirm').simplemodal();
                jQuery('#md-delrowconfirm').data('simplemodal').show();

                $activeRow = jQuery(this).parents('.ui-draggable');

                jQuery('#btnDelRowOk').off('click');
                jQuery('#btnDelRowOk').on('click', function (e) {

                    jQuery('#md-delrowconfirm').data('simplemodal').hide();

                    $activeRow.fadeOut(400, function () {

                        //Clear Controls
                        jQuery("#divToolImg").stop(true, true).fadeOut(0); /* CUSTOM */
                        jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                        jQuery("#divRteLink").stop(true, true).fadeOut(0);
                        jQuery("#divFrameLink").stop(true, true).fadeOut(0);

                        $activeRow.remove();

                        //Apply builder behaviors
                        //$element.data('contentbuilder').applyBehavior();

                        // Function to run when column/grid changed
                        $element.data('contentbuilder').blockChanged();

                        //Trigger Render event
                        $element.data('contentbuilder').settings.onRender();

                        //Trigger Change event
                        $element.data('contentbuilder').settings.onChange();

                        //Save for Undo
                        saveForUndo();

                    });

                });
                jQuery('#btnDelRowCancel').off('click');
                jQuery('#btnDelRowCancel').on('click', function (e) {

                    jQuery('#md-delrowconfirm').data('simplemodal').hide();

                });
                /**** /Custom Modal ****/

            });

            //Add "Click to Duplicate" event (row)
            $element.children("div").find('.row-copy').off('click');
            $element.children("div").find('.row-copy').click(function () {

                    $activeRow = jQuery(this).parents('.ui-draggable');
                    jQuery('#temp-contentbuilder').html($activeRow.html());
                    jQuery('#temp-contentbuilder').find('[contenteditable]').removeAttr('contenteditable');
                    jQuery('#temp-contentbuilder *[class=""]').removeAttr('class');
                    jQuery('#temp-contentbuilder *[style=""]').removeAttr('style');
                    jQuery('#temp-contentbuilder .ovl').remove();
                    /*jQuery('#temp-contentbuilder').find('p').each(function () {
                        if (jQuery.trim(jQuery(this).text()) == '') jQuery(this).remove();
                    });*/
                    jQuery('#temp-contentbuilder .row-tool').remove();
                    var html = jQuery('#temp-contentbuilder').html().trim();

                    //Insert
                    $activeRow.after(html);

                    //Re-Init
                    $element.children("*").each(function () {

                        if (!jQuery(this).hasClass('ui-draggable')) {
                            jQuery(this).wrap("<div class='ui-draggable'></div>");
                        }
                    });

                    $element.children('.ui-draggable').each(function () {
                        if (jQuery(this).find('.row-tool').length == 0) {
                            jQuery(this).append('<div class="row-tool">' +
                            '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                            '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                            '<div class="row-copy"><i class="cb-icon-plus"></i></div>' +
                            '<div class="row-remove"><i class="cb-icon-cancel"></i></div>' +
                            '</div>');
                        }
                    });

                    $element.children('.ui-draggable').each(function () {
                        if (jQuery(this).children('*').length == 1) {
                            jQuery(this).remove();
                        }
                    });

                    //Apply builder behaviors
                    $element.data('contentbuilder').applyBehavior();

                    // Function to run when column/grid changed
                    $element.data('contentbuilder').blockChanged();

                    //Trigger Render event
                    $element.data('contentbuilder').settings.onRender();

                    //Trigger Change event
                    $element.data('contentbuilder').settings.onChange();

                    //Save for Undo
                    saveForUndo();
            });


            //Add "Click to View HTML" event (row)
            $element.children("div").find('.row-html').off('click');
            $element.children("div").find('.row-html').click(function () {

                $activeRow = jQuery(this).parents('.ui-draggable');

                /*
                Module snippet examples:

                <div data-num="301" data-thumb="assets/minimalist-basic/thumbnails/slider1.png" data-cat="0,35">
                    <div class="row clearfix">
                        <div class="column full" data-module="slider" data-module-desc="Slider" data-html="...">

                        </div>
                    </div>
                </div>

                <div data-num="302" data-thumb="assets/minimalist-basic/thumbnails/code.png" data-cat="0,100">
                    <div class="row clearfix" data-module="code" data-dialog-width="80%" data-html="...">
                        <div class="column full">

                        </div>
                    </div>
                </div>
                */

                if($activeRow.find('[data-html]').length > 0){ //Mode: module or custom code

                    var $activeModule = $activeRow.find('[data-html]');

                    //Set Active Module
                    jQuery('body').find("[data-html]").removeAttr('data-module-active');
                    $activeModule.attr('data-module-active', '1');

                    var moduleName = $activeModule.attr('data-module');

                    if($activeModule.attr('data-mode') == 'code') {//data-mode is for backward compatibility
                        moduleName = 'code';
                    }

                    if(moduleName=='code'){

                        jQuery('#infoSource').html($element.data('contentbuilder').settings.snippetCustomCodeMessage);

                        var html = decodeURIComponent($activeModule.attr("data-html"));
                        jQuery('#txtContentCustomCode').val(html);

                        var w = '900px';

                        jQuery("#md-editcontentcustomcode").css("width", "100%");
                        jQuery("#md-editcontentcustomcode").css("max-width", w);
                        jQuery("#md-editcontentcustomcode").simplemodal({ isModal: true });
                        jQuery("#md-editcontentcustomcode").data("simplemodal").show();

                        if(jQuery('#txtContentCustomCode').data('CodeMirrorInstance')){

                            var $codeEditor = $('#txtContentCustomCode').data('CodeMirrorInstance');
                            $codeEditor.setValue(html);

                        } else {
                            var myTextArea = jQuery("#txtContentCustomCode")[0];

                            if (jQuery('.is-cmloaded').length == 0) { //check if js/css already loaded

                                getScripts([sScriptPath + "codemirror/lib/codemirror.js"],
                                    function () {

                                        getScripts([sScriptPath + "codemirror/mode/xml/xml.js",
                                            sScriptPath + "codemirror/mode/javascript/javascript.js",
                                            sScriptPath + "codemirror/mode/css/css.js"],
                                            function () {

                                                jQuery('body').addClass('is-cmloaded');

                                                var $codeEditor = CodeMirror.fromTextArea(myTextArea, {
                                                    value: html,
                                                    mode: "text/html",
                                                    lineWrapping: true,
                                                    lineNumbers: true,
                                                    tabMode: "indent"
                                                });
                                                $codeEditor.on("change", function(cm, change) {
                                                    jQuery('#hidContentCustomCode').val(cm.getValue());
                                                });

                                                //Save instance
                                                jQuery('#txtContentCustomCode').data('CodeMirrorInstance', $codeEditor);

                                            });

                                    });

                            } else {

                                var $codeEditor = CodeMirror.fromTextArea(myTextArea, {
                                    value: html,
                                    mode: "text/html",
                                    lineWrapping: true,
                                    lineNumbers: true,
                                    tabMode: "indent"
                                });
                                $codeEditor.on("change", function(cm, change) {
                                    jQuery('#hidContentCustomCode').val(cm.getValue());
                                });

                                //Save instance
                                jQuery('#txtContentCustomCode').data('CodeMirrorInstance', $codeEditor);

                            }

                        }

                        jQuery('#btnContentCustomCodeOk').off('click');
                        jQuery('#btnContentCustomCodeOk').on('click', function (e) {

                            var $codeEditor = $('#txtContentCustomCode').data('CodeMirrorInstance');
                            jQuery('#hidContentCustomCode').val($codeEditor.getValue());

                            //Save Html (original)
                            $activeModule.attr('data-html', encodeURIComponent(jQuery('#hidContentCustomCode').val()));

                            //Render (programmatically)
                            $activeModule.html(jQuery('#hidContentCustomCode').val());

                            jQuery('#md-editcontentcustomcode').data('simplemodal').hide();

                            //Trigger Change event
                            $element.data('contentbuilder').settings.onChange();

                            //Save for Undo
                            saveForUndo();

                        });

                        jQuery('#btnContentCustomCodeCancel').off('click');
                        jQuery('#btnContentCustomCodeCancel').on('click', function (e) {

                            jQuery('#md-editcontentcustomcode').data('simplemodal').hide();

                        });


                    } else {

                        var moduleDesc = $activeModule.attr('data-module-desc');
                        if (moduleDesc) {
                            jQuery("#md-editcontentmodule").find('.md-title').html(moduleDesc);
                        } else {
                            jQuery("#md-editcontentmodule").find('.md-title').html('Module Settings');
                        }

                        var w = $activeModule.attr('data-dialog-width');
                        if(!w || w==''){
                            w = '65%';
                        }

                        jQuery("#md-editcontentmodule").css("width", "100%");
                        jQuery("#md-editcontentmodule").css("max-width", w);
                        jQuery("#md-editcontentmodule").simplemodal({ isModal: true });
                        jQuery("#md-editcontentmodule").data("simplemodal").show();

                        jQuery('#ifrContentModulePanel').attr('src', $element.data('contentbuilder').settings.modulePath + moduleName + '.html'); //load module panel on iframe

                        jQuery('#btnContentModuleOk').off('click');
                        jQuery('#btnContentModuleOk').on('click', function (e) {

                            //Save Html (original)
                            $activeModule.attr('data-html', encodeURIComponent(jQuery('#hidContentModuleCode').val()));

                            //Save Settings (original)
                            $activeModule.attr('data-settings', encodeURIComponent(jQuery('#hidContentModuleSettings').val()));

                            //Render (programmatically)
                            $activeModule.html(jQuery('#hidContentModuleCode').val());

                            jQuery('#md-editcontentmodule').data('simplemodal').hide();

                            //Trigger Change event
                            $element.data('contentbuilder').settings.onChange();

                            //Save for Undo
                            saveForUndo();

                        });

                        jQuery('#btnContentModuleCancel').off('click');
                        jQuery('#btnContentModuleCancel').on('click', function (e) {

                            jQuery('#md-editcontentmodule').data('simplemodal').hide();

                        });

                    }

                } else {

                    $activeCol = jQuery(this).parents('.ui-draggable').children('*').not('.row-tool'); //refine $activeRow to $activeCol

                    //Normal Static HTML
                    jQuery('#md-html').css('width', '60%');
                    jQuery('#md-html').simplemodal({isModal:true});
                    jQuery('#md-html').data('simplemodal').show();

                    jQuery('#temp-contentbuilder').html($activeCol.html());

                    jQuery('#temp-contentbuilder').find('[contenteditable]').removeAttr('contenteditable');
                    jQuery('#temp-contentbuilder *[class=""]').removeAttr('class');
                    jQuery('#temp-contentbuilder *[style=""]').removeAttr('style');
                    jQuery('#temp-contentbuilder .ovl').remove();
                    /*jQuery('#temp-contentbuilder').find('p').each(function () {
                        if (jQuery.trim(jQuery(this).text()) == '') jQuery(this).remove();
                    });*/
                    var html = jQuery('#temp-contentbuilder').html().trim();
                    html = html.replace(/<font/g,'<span').replace(/<\/font/g,'</span');
                    jQuery('#txtHtml').val(html);

                    jQuery("#txtHtml").css('display', 'none');

                    if(jQuery('#txtHtml').data('CodeMirrorInstance')){

                        var $htmlEditor = $('#txtHtml').data('CodeMirrorInstance');
                        $htmlEditor.setValue(html);

                    } else {
                        var myTextArea = jQuery("#txtHtml")[0];

                        if (jQuery('.is-cmloaded').length == 0) { //check if js/css already loaded

                            getScripts([sScriptPath + "codemirror/lib/codemirror.js"],
                                function () {

                                    getScripts([sScriptPath + "codemirror/mode/xml/xml.js",
                                        sScriptPath + "codemirror/mode/javascript/javascript.js",
                                        sScriptPath + "codemirror/mode/css/css.js"],
                                        function () {

                                            jQuery('body').addClass('is-cmloaded');

                                            var $htmlEditor = CodeMirror.fromTextArea(myTextArea, {
                                                value: html,
                                                mode: "text/html",
                                                lineWrapping: true,
                                                lineNumbers: true,
                                                tabMode: "indent"
                                            });
                                            $htmlEditor.on("change", function(cm, change) {
                                                jQuery('#txtHtml').val(cm.getValue());
                                            });

                                            //Save instance
                                            jQuery('#txtHtml').data('CodeMirrorInstance', $htmlEditor);

                                        });

                                });

                        } else {

                            var $htmlEditor = CodeMirror.fromTextArea(myTextArea, {
                                value: html,
                                mode: "text/html",
                                lineWrapping: true,
                                lineNumbers: true,
                                tabMode: "indent"
                            });
                            $htmlEditor.on("change", function(cm, change) {
                                jQuery('#txtHtml').val(cm.getValue());
                            });

                            //Save instance
                            jQuery('#txtHtml').data('CodeMirrorInstance', $htmlEditor);

                        }

                    }

                    jQuery('#btnHtmlOk').off('click');
                    jQuery('#btnHtmlOk').on('click', function (e) {

                        var $htmlEditor = $('#txtHtml').data('CodeMirrorInstance');
                        jQuery('#txtHtml').val($htmlEditor.getValue());

                        $activeCol.html(jQuery('#txtHtml').val());

                        jQuery('#md-html').data('simplemodal').hide();

                        //Apply builder behaviors
                        $element.data('contentbuilder').applyBehavior();

                        // Function to run when column/grid changed
                        $element.data('contentbuilder').blockChanged();

                        //Trigger Render event
                        $element.data('contentbuilder').settings.onRender();

                        //Trigger Change event
                        $element.data('contentbuilder').settings.onChange();

                        //Save for Undo
                        saveForUndo();

                    });

                    jQuery('#btnHtmlCancel').off('click');
                    jQuery('#btnHtmlCancel').on('click', function (e) {

                        jQuery('#md-html').data('simplemodal').hide();

                    });
                }

            });

        };

        this.blockChanged = function () {

            if($element.children().length==0) {
                $element.addClass('empty');
            } else {
                $element.removeClass('empty');
            }

        };

		this.destroy = function () {
            if(!$element.data('contentbuilder')) return;
            var sHTML = $element.data('contentbuilder').html();
            $element.html(sHTML);

			// ---by jack
			$element.sortable("destroy"); //destroy sortable

			//del element from cb_list
			var cbarr = cb_list.split(","), newcbarr = [];
			for(var i=0; i < cbarr.length; i++) {
				if(cbarr[i] != "#"+$element.attr("id")) {
					newcbarr.push(cbarr[i]);
				}
			}
			cb_list = newcbarr.join(",");
			// ---end by jack

            //added by yus
            for (var i = 0; i < instances.length; i++) {
                if( jQuery(instances[i]).attr('id') == $element.attr('id') ) {
                    instances.splice(i, 1);
                }
            }

            $element.removeClass('connectSortable');
            $element.css({ 'min-height': '' });

            /*
			// ---by jack
			if(cb_list=="") {
				jQuery('#divCb').remove();
                jQuery(document).off('mousedown');
			}
			// ---end by jack
            */

            $element.removeData('contentbuilder');
            $element.removeData('contenteditor');
            //$element.unbind();

            refreshAllObjects();
        };

        this.init();

    };

    jQuery.fn.contentbuilder = function (options) {
        return this.each(function () {

            if (undefined == jQuery(this).data('contentbuilder')) {
                var plugin = new jQuery.contentbuilder(this, options);
                jQuery(this).data('contentbuilder', plugin);

            }

            saveForUndo();

        });
    };
})(jQuery);

function refreshAllObjects() {
    try {
        var cbarr = cb_list.split(","), newcbarr = [];
        for (var i = 0; i < cbarr.length; i++) {
            //console.log(cbarr[i]);
            jQuery(cbarr[i]).data('contentbuilder').applyBehavior();
        }
    }
    catch (e) { }
}

/*******************************************************************************************/

var ce_toolbarDisplay = 'auto';
var ce_outline = false;
var instances = [];
var savedSelPublic;

(function (jQuery) {

    var $activeLink;
    var $activeElement;
    var $activeFrame;
    var $activeCell;

    jQuery.contenteditor = function (element, options) {

        var defaults = {
            editable: "h1,h2,h3,h4,h5,h6,p,ul,ol,small,.edit,td",
            editMode: "default",
            hasChanged: false,
            onRender: function () {

            },
            onChange: function () {

            },
            outline: false,
            fileselect: '',
            imageselect: '',
            iconselect: '',
            onFileSelectClick: function () { },
            onImageSelectClick: function () { },
            toolbar: 'top',
            toolbarDisplay: 'auto',
            buttons: ["bold", "italic", "formatting", "textsettings", "color", "font", "formatPara", "align", "list", "table", "image", "createLink", "unlink", "icon", "tags", "removeFormat", "html"],
            colors: ["#ffffc5","#e9d4a7","#ffd5d5","#ffd4df","#c5efff","#b4fdff","#c6f5c6","#fcd1fe","#ececec",
                "#f7e97a","#d09f5e","#ff8d8d","#ff80aa","#63d3ff","#7eeaed","#94dd95","#ef97f3","#d4d4d4",
                "#fed229","#cc7f18","#ff0e0e","#fa4273","#00b8ff","#0edce2","#35d037","#d24fd7","#888888",
                "#ff9c26","#955705","#c31313","#f51f58","#1b83df","#0bbfc5","#1aa71b","#ae19b4","#333333"],
            customTags: []
            /*
                [["First Name", "{%first_name%}"],
                ["Last Name", "{%last_name%}"],
                ["Email", "{%email%}"]]
            */
        };

        this.settings = {};

        var $element = jQuery(element),
             element = element;

        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

            //Custom File Select
            var bUseCustomFileSelect = false;
            if(this.settings.fileselect!='') bUseCustomFileSelect=true;

            var sFunc = (this.settings.onFileSelectClick+'').replace( /\s/g, '');
            if(sFunc != 'function(){}'){ //If custom event set, enable the button
                bUseCustomFileSelect=true;
            }

            //Custom Image Select
            var bUseCustomImageSelect = false;
            if(this.settings.imageselect!='') bUseCustomImageSelect=true;

            var sFunc = (this.settings.onImageSelectClick+'').replace( /\s/g, '');
            if(sFunc != 'function(){}'){ //If custom event set, enable the button
                bUseCustomImageSelect=true;
            }

            /**** Localize All ****/
            if (jQuery('#divCb').length == 0) {
                jQuery('body').append('<div id="divCb"></div>');
            }

            ce_toolbarDisplay = this.settings.toolbarDisplay;
            ce_outline = this.settings.outline;

            var toolbar_attr = '';
            if(this.settings.toolbar=='left')toolbar_attr=' class="rte-side"';
            if(this.settings.toolbar=='right')toolbar_attr=' class="rte-side right"';

            var icon_button = '';
            if(this.settings.iconselect!='') icon_button = '<button data-rte-cmd="icon" title="Icon"> <i class="cb-icon-smile"></i> </button>';

            var customtag_button = '';
            if( this.settings.customTags.length > 0 ) customtag_button = '<button data-rte-cmd="tags" title="Tags"> <i class="cb-icon-ticket"></i> </button>';

            var html_rte= '<div id="rte-toolbar"' + toolbar_attr + '><div class="rte-draggable"><i class="cb-icon-dot"></i></div>';
                for (var j = 0; j < this.settings.buttons.length; j++) {
                    var btn = this.settings.buttons[j];
                    if(btn=='bold') html_rte += '<button href="#" data-rte-cmd="bold" title="Bold"> <i class="cb-icon-bold"></i> </button>';
                    if(btn=='italic') html_rte += '<button data-rte-cmd="italic" title="Italic"> <i class="cb-icon-italic"></i> </button>';
                    if(btn=='underline') html_rte += '<button data-rte-cmd="underline" title="Underline"> <i class="cb-icon-underline"></i> </button>';
                    if(btn=='strikethrough') html_rte += '<button data-rte-cmd="strikethrough" title="Strikethrough"> <i class="cb-icon-strike"></i> </button>';
                    if(btn=='formatting') html_rte += '<button data-rte-cmd="formatting" title="Formatting"> <i class="cb-icon-font"></i> </button>';
                    if(btn=='textsettings') html_rte += '<button data-rte-cmd="textsettings" title="Text Settings"> <i class="cb-icon-sliders" style="font-size:16px;line-height: 16px;"></i> </button>';
                    if(btn=='color') html_rte += '<button data-rte-cmd="color" title="Color"> <i class="cb-icon-color"></i> </button>';
                    if(btn=='fontsize') html_rte += '<button data-rte-cmd="fontsize2" title="Font Size"> <i class="cb-icon-fontsize"></i> </button>';
                    if(btn=='removeFormat') html_rte += '<button data-rte-cmd="removeFormat" title="Clean"> <i class="cb-icon-eraser"></i> </button>';
                    if(btn=='formatPara') html_rte += '<button data-rte-cmd="formatPara" title="Paragraph"> <i class="cb-icon-header"></i> </button>';
                    if(btn=='font') html_rte += '<button data-rte-cmd="font" title="Font"> <i class="cb-icon-font-family" style="font-size:11px"></i> </button>';
                    if(btn=='align') html_rte += '<button data-rte-cmd="align" title="Alignment"> <i class="cb-icon-align-justify"></i> </button>';
                    if(btn=='list') html_rte += '<button data-rte-cmd="list" title="List"> <i class="cb-icon-list-bullet"></i> </button>';
                    if(btn=='image') html_rte += '<button href="#" data-rte-cmd="image" title="Image"> <i class="cb-icon-picture"></i> </button>';
                    if(btn=='createLink') html_rte += '<button data-rte-cmd="createLink" title="Link"> <i class="cb-icon-link"></i> </button>';
                    if(btn=='unlink') html_rte += '<button data-rte-cmd="unlink" title="Remove Link"> <i class="cb-icon-unlink"></i> </button>';
                    if(btn=='table') html_rte += '<button href="#" data-rte-cmd="table" title="table"> <i class="cb-icon-table" style="font-size:14px;line-height:14px;"></i> </button>';
                    if(btn=='icon') html_rte += icon_button;
                    if(btn=='tags') html_rte += customtag_button;
                    if(btn=='html') html_rte += '<button data-rte-cmd="html" title="HTML"> <i class="cb-icon-code"></i> </button>';
                }

            var html_table = '<table id="tableInsert" class="table-insert" style="border-collapse:collapse;border-radius:5px;overflow:hidden;">';
            for (var i = 1; i <= 5; i++) {
                html_table += '<tr>';
                for (var j = 1; j <= 5; j++) {
                    html_table += '<td data-row="' + i + '" data-col="' + j + '">' + i + 'x' + j + '</td>';
                }
                html_table += '</tr>';
            }
            html_table += '</table>';

            html_rte += '</div>' +
				'' +
				'<div id="divRteLink">' +
					'<i class="cb-icon-link"></i> Edit' +
				'</div>' +
				'' +
				'<div id="divFrameLink">' +
					'<i class="cb-icon-link"></i> Edit' +
				'</div>' +
				'' +
				'<div id="divRteTable">' +
					'<button id="btnEditTable" title="Edit"><i class="cb-icon-pencil"></i></button>' +
					'<button id="btnDeleteTable" title="Delete"><i class="cb-icon-cancel"></i></button>' +
				'</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-createlink">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<div class="md-label">Link:</div>' +
                            (bUseCustomFileSelect ? '<input type="text" id="txtLink" class="inptxt" style="float:left;width:60%;" value="http:/' + '/"></input><i class="cb-icon-link md-btnbrowse" id="btnLinkBrowse" style="width:10%;"></i>' : '<input type="text" id="txtLink" class="inptxt" value="http:/' + '/" style="float:left;width:70%"></input>') +
                            '<br style="clear:both">' +
                            '<div class="md-label">Text:</div>' +
                            '<input type="text" id="txtLinkText" class="inptxt" style="float:right;width:70%"></input>' +
                            '<br style="clear:both">' +
                            '<div class="md-label">Title:</div>' +
                            '<input type="text" id="txtLinkTitle" class="inptxt" style="float:right;width:70%"></input>' +
                            '<br style="clear:both">' +
                            '<div class="md-label">Target:</div>' +
                            '<label style="float:left;" for="chkNewWindow" class="inpchk"><input type="checkbox" id="chkNewWindow"></input> New Window</label>' +
                            '<br style="clear:both">' +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnLinkOk"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-insertimage">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<div class="md-browse">' +
                                '<div class="md-drop-area">' +
                                    '<input id="fileInsertImage" type="file" accept="image/*" />' +
                                    '<div class="drag-text">' +
                                        '<p><i class="cb-icon-camera"></i> Drag and drop an image or click to browse.</p>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="md-preview-area">' +
                                    '<div><img id="imgInsertImagePreview" src="#" alt="your image" /><i class="ion-ios-close-empty"></i></div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="md-label">Or Specify Image Source:</div>' +
                            (bUseCustomImageSelect ? '<input type="text" id="txtImgUrl_rte" class="inptxt" style="float:left;width:60%"></input><i class="cb-icon-link md-btnbrowse" id="btnImageBrowse_rte" style="width:10%;"></i>' : '<input type="text" id="txtImgUrl_rte" class="inptxt" style="float:left;width:70%"></input>') +
                            '<br style="clear:both">' +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnImgOk_rte"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal" id="md-createsrc">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<input type="text" id="txtSrc" class="inptxt" value="http:/' + '/"></input>' +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnSrcOk"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal" id="md-createiframe">' +
                    '<div class="md-content">' +
                        '<div class="md-body">' +
                            '<textarea id="txtIframe" class="inptxt" style="height:350px;"></textarea>' +
                        '</div>' +
                        '<div class="md-footer">' +
                            '<button id="btnIframeOk"> Ok </button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-table">' +
                    html_table +
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-align">' +
                    '<button class="md-pickalign" data-align="left" title="Left"> <i class="cb-icon-align-left"></i> </button>' +
                    '<button class="md-pickalign" data-align="center" title="Center"> <i class="cb-icon-align-center"></i> </button>' +
                    '<button class="md-pickalign" data-align="right" title="Right"> <i class="cb-icon-align-right"></i> </button>' +
                    '<button class="md-pickalign" data-align="justify" title="Full"> <i class="cb-icon-align-justify"></i> </button>' +
                '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-edittable">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<div class="md-tabs">' +
                                '<span id="tabTableDesign" class="active">Design</span>' +
                                '<span id="tabTableLayout">Layout</span>' +
                            '</div>' +
                            '<div id="divTableDesign" style="overflow-y:auto;overflow-x:hidden;box-sizing:border-box;padding:10px 10px 10px">' +
                                '' +
                                '<div>' +
                                    'Background:<br>' +
                                    '<input type="text" id="inpCellBgColor" value=""/>' +
                                '</div>' +
                                '<div>' +
                                    'Text Color:<br>' +
                                    '<input type="text" id="inpCellTextColor" value=""/>' +
                                '</div>' +
                                '<div>' +
                                    'Border Thickness:<br>' +
                                    '<select id="selCellBorderWidth" style="width:120px;"><option value="0">No Border</option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>' +
				                '</div>' +
                                '<div>' +
                                    'Border Color:<br>' +
                                    '<input type="text" id="inpCellBorderColor" value=""/>' +
                                '</div>' +
                                '<div>' +
                                    'Apply To:<br>' +
                                    '<select id="selTableApplyTo" style="width:120px;">' +
                                        '<option value="table">Table</option>' +
                                        '<option value="currentrow">Current Row</option>' +
                                        '<option value="currentcol">Current Column</option>' +
                                        '<option value="evenrows">Even Rows</option>' +
                                        '<option value="oddrows">Odd Rows</option>' +
                                        '<option value="currentcell">Current Cell</option>' +
                                    '</select>' +
				                '</div>' +
                                '' +
				            '</div>' +
                            '<div id="divTableLayout" style="overflow-y:auto;overflow-x:hidden;display:none;box-sizing:border-box;padding:10px 10px 10px">' +
                                '<div>' +
                                    'Insert Row:<br>' +
                                    '<button data-rte-cmd="rowabove" title="Insert Row (Above)" style="width:100px;margin-right:5px"> Above </button>' +
                                    '<button data-rte-cmd="rowbelow" title="Insert Row (Below)" style="width:100px;"> Below </button>' +
                                '</div>' +
                                '<div>' +
                                    'Insert Column:<br>' +
                                    '<button data-rte-cmd="columnleft" title="Insert Column (Left)" style="width:100px;margin-right:5px"> Left </button>' +
                                    '<button data-rte-cmd="columnright" title="Insert Column (Right)" style="width:100px;"> Right </button>' +
                                '</div>' +
                                '<div>' +
                                    'Delete:<br>' +
                                    '<button data-rte-cmd="delrow" title="Delete Row" style="width:100px;margin-right:5px"> Row </button>' +
                                    '<button data-rte-cmd="delcolumn" title="Delete Column" style="width:100px;"> Column </button>' +
                                '</div>' +
                                '<div style="margin-bottom:15px;">' +
                                    'Merge:<br>' +
                                    '<button data-rte-cmd="mergecell" title="Merge Cell" style="width:205px"> Merge Cell </button>' +
                                '</div>' +
                                '' +
				            '</div>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal" id="md-deltableconfirm">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div style="padding:20px 20px 25px;text-align:center;">' +
                            '<p>Are you sure you want to delete this table?</p>' +
                            '<button id="btnDelTableCancel"> CANCEL </button>' +
                            '<button id="btnDelTableOk" style="margin-left:12px"> OK </button>' +
                            '</div>' +
				        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="rte-pop" id="pop-list">' +
                    '<button class="md-picklist half" data-list="indent" title="Indent" style="margin-right:0px"> <i class="cb-icon-indent-right"></i> </button>' +
                    '<button class="md-picklist half" data-list="outdent" title="Outdent"> <i class="cb-icon-indent-left"></i> </button>' +
                    '<button class="md-picklist" data-list="insertUnorderedList" title="Bulleted List"> <i class="cb-icon-list-bullet"></i> </button>' +
                    '<button class="md-picklist" data-list="insertOrderedList" title="Numbered List"> <i class="cb-icon-list-numbered"></i> </button>' +
                    /*'<button class="md-picklist" data-list="normal" title="Remove List"> <i class="cb-icon-eraser"></i> </button>' +*/
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-formatting">' +
                    '<div>' +
                      /*  '<button href="#" data-rte-cmd="bold" title="Bold"> <i class="cb-icon-bold"></i> </button>' +
                        '<button data-rte-cmd="italic" title="Italic"> <i class="cb-icon-italic"></i> </button>' + */
                        '<button data-rte-cmd="underline" title="Underline"> <i class="cb-icon-underline"></i> </button>' +
                        '<button data-rte-cmd="strikethrough" title="Strikethrough"> <i class="cb-icon-strike"></i> </button>' +
                        '<button data-rte-cmd="superscript" title="Superscript"> <i class="cb-icon-superscript"></i> </button>' +
                        '<button data-rte-cmd="subscript" title="Subscript"> <i class="cb-icon-subscript"></i> </button>' +
                        '<button data-rte-cmd="uppercase" title="Uppercase"> <i class="cb-icon-uppercase"></i> </button>' +
                      /*  '<button data-rte-cmd="font" title="Font Family"> <i class="cb-icon-font-family" style="font-size:11px"></i> </button>' + */
                    '</div>' +
                '</div>' +
                '' +
                '<div class="rte-pop arrow-left" id="pop-textsettings">' +
                    '<div>' +
                        'Font Size: <span id="outFontSize"></span><br>' +
                        '<button data-rte-cmd="fontsize" data-val="decrease" class="updown"> - </button>' +
                        '<button data-rte-cmd="fontsize" data-val="increase" class="updown"> + </button>' +
                        '<button data-rte-cmd="fontsize" data-val="clear" class="updown" style="font-size:11px"> <i class="cb-icon-eraser"></i> </button>' +
                        '<br style="clear:both">' +
                    '</div>' +
                    '<div>' +
                        'Letter Spacing: <span id="outLetterSpacing"></span><br>' +
                        '<button data-rte-cmd="letterspacing" data-val="decrease" class="updown"> - </button>' +
                        '<button data-rte-cmd="letterspacing" data-val="increase" class="updown"> + </button>' +
                        '<button data-rte-cmd="letterspacing" data-val="clear" class="updown" style="font-size:11px"> <i class="cb-icon-eraser"></i> </button>' +
                        '<br style="clear:both">' +
                    '</div>' +
                    '<div>' +
                        'Line Height: <span id="outLineHeight"></span><br>' +
                        '<button data-rte-cmd="lineheight" data-val="decrease" class="updown"> - </button>' +
                        '<button data-rte-cmd="lineheight" data-val="increase" class="updown"> + </button>' +
                        '<button data-rte-cmd="lineheight" data-val="clear" class="updown" style="font-size:11px"> <i class="cb-icon-eraser"></i> </button>' +
                        '<br style="clear:both">' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-fontfamily">' +
                    '<div>' +
                        '<iframe id="ifrFonts" src="' + sScriptPath + 'blank.html"></iframe>' +
                        '<button class="md-pickfontfamily" data-font-family="" data-provider="" style="display:none"></button>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-headings">' +
                    '<div>' +
                        '<iframe id="ifrHeadings" src="' + sScriptPath + 'blank.html"></iframe>' +
                        '<button class="md-pickheading" data-font-family="" data-provider="" style="display:none"></button>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="rte-pop" id="pop-colors">' +
                    '<div style="margin:8px;">' +
                        '<input type="text" id="inpTextColor"/>' +
                        '<button id="btnTextColorClear" style="margin-left:9px;margin-bottom: 2px;padding:0 12px;width:42px;height:37px;border-radius:4px;"> <i class="cb-icon-eraser"></i> </button>' +
                        '<div style="overflow-x:auto;overflow-y:hidden;width:245px;height:170px">' +
                            '<div class="cust_colors">' +
                            '[COLORS]' +
                            '</div>' +
                        '</div>' +
                        '<div style="width:100%;margin-top:6px;">' +
                            '<select id="selColorApplyTo" style="width:120px;"><option value="1">Text Color</option><option value="2">Background</option><option value="3">Block Background</option></select>' + /*<option value="3">Block Background</option>*/
                        '</div>' +
                        '<br style="clear:both" />' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-fontsize" style="border-radius:12px">' +
			        '<div class="md-content" style="border-radius:12px">' +
				        '<div class="md-body">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<iframe id="ifrFontSize" style="width:100%;height:319px;border: none;display: block;" src="' + sScriptPath + 'blank.html"></iframe>' +
                            '<button class="md-pickfontsize" data-font-size="" style="display:none"></button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-html">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div class="md-modal-handle" style="display:none">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<textarea id="txtHtml" class="inptxt" style="height:450px;"></textarea>' +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnHtmlCancel" class="secondary"> Cancel </button>' +
                            '<button id="btnHtmlOk" class="primary"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-editcontentmodule">' +
                    '<div class="md-content">' +
                        '<div class="md-modal-handle">' +
                            '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                        '</div>' +
                        '<div class="md-body">' +
                            '<iframe id="ifrContentModulePanel" style="width:100%;height:500px;display:block;border:none;" src="' + sScriptPath + 'blank.html"></iframe>' +
                            '<input id="hidContentModuleCode" type="hidden" />' +
                            '<input id="hidContentModuleSettings" type="hidden" />' +
                        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnContentModuleCancel" class="secondary"> Cancel </button>' +
                            '<button id="btnContentModuleOk" class="primary"> Ok </button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="md-modal md-draggable" id="md-editcontentcustomcode">' +
                    '<div class="md-content">' +
                        '<div class="md-modal-handle"></div>' +
                        '<div class="md-body" style="background: #fff;">' +
                            '<div id="infoSource">IMPORTANT</b>: This is a custom section. Custom javascript code (&lt;script&gt; block) is allowed here but may not always work or compatible with the content builder, so proceed at your own risk. We do not support problems with custom code.</div>' +
                            '<textarea id="txtContentCustomCode" class="inptxt" style="background: #fff;"></textarea>' +
                            '<input id="hidContentCustomCode" type="hidden" />' +
                        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnContentCustomCodeCancel" class="secondary"> Cancel </button>' +
                            '<button id="btnContentCustomCodeOk" class="primary"> Ok </button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div class="md-modal" id="md-fileselect">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            (bUseCustomFileSelect ? '<iframe id="ifrFileBrowse" style="width:100%;height:400px;border: none;display: block;" src="' + sScriptPath + 'blank.html"></iframe>' : '') +
				        '</div>' +
			        '</div>' +
		        '</div>' +
                '<input type="hidden" id="active-input" />' +
                '' +
                '<div class="md-modal" id="md-delrowconfirm">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<div style="padding:20px 20px 25px;text-align:center;">' +
                            '<p>Are you sure you want to delete this block?</p>' +
                            '<button id="btnDelRowCancel"> CANCEL </button>' +
                            '<button id="btnDelRowOk" style="margin-left:12px"> OK </button>' +
                            '</div>' +
				        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
		        '<div class="md-modal md-draggable" id="md-icon-select">' +
                    '<div class="md-content">' +
                        '<div class="md-body md-settings">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<iframe id="ifrIconSelect" style="width:100%;height:500px;hidden;border:none;float:left;" src="' + sScriptPath + 'blank.html"></iframe>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' +
		        '<div class="md-modal md-draggable" id="md-tags-select">' +
                    '<div class="md-content">' +
                        '<div class="md-body md-settings">' +
                            '<div class="md-modal-handle">' +
                                '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                            '</div>' +
                            '<div id="divCustomTags" style="width:100%;"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' +
                '<div id="temp-contenteditor"></div>' +
                '';

            var html_colors = '';
            /*
            arrC = new Array("#000000", "#0000ff", "#3300ff", "#6600ff", "#9900ff", "#cc00ff", "#ff00ff", "#ff0099", "#cc0099", "#990099", "#660099", "#330099", "#000099", "#000033", "#330033", "#660033", "#990033", "#cc0033", "#ff0033", "|",
                "#444", "#0066ff", "#3366ff", "#6666ff", "#9966ff", "#cc66ff", "#ff66ff", "#ff6699", "#cc6699", "#996699", "#666699", "#336699", "#006699", "#006633", "#336633", "#666633", "#996633", "#cc6633", "#ff6633", "|",
                "#888", "#0099ff", "#3399ff", "#6699ff", "#9999ff", "#cc99ff", "#ff99ff", "#ff9999", "#cc9999", "#999999", "#669999", "#339999", "#009999", "#009933", "#339933", "#669933", "#999933", "#cc9933", "#ff9933", "|",
                "#ccc", "#00ccff", "#33ccff", "#66ccff", "#99ccff", "#ccccff", "#ffccff", "#ffcc99", "#cccc99", "#99cc99", "#66cc99", "#33cc99", "#00cc99", "#00cc33", "#33cc33", "#66cc33", "#99cc33", "#cccc33", "#ffcc33", "|",
                "#ffffff", "#00ffff", "#33ffff", "#66ffff", "#99ffff", "#ccffff", "#ffffff", "#ffff99", "#ccff99", "#99ff99", "#66ff99", "#33ff99", "#00ff99", "#00ff33", "#33ff33", "#66ff33", "#99ff33", "#ccff33", "#ffff33");
            */
            arrC = new Array(
                "#000000", "#3300ff", "#9900ff", "#ff0099", "#cc0099", "#990099", "#990033", "#cc0033", "#ff0033", "|",
                "#444444", "#3366ff", "#9966ff", "#ff6699", "#cc6699", "#996699", "#996633", "#cc6633", "#ff6633", "|",
                "#888888", "#3399ff", "#9999ff", "#ff9999", "#cc9999", "#999999", "#999933", "#cc9933", "#ff9933", "|",
                "#cccccc", "#33ccff", "#99ccff", "#ffcc99", "#cccc99", "#99cc99", "#99cc33", "#cccc33", "#ffcc33", "|",
                "#ffffff", "#33ffff", "#99ffff", "#ffff99", "#ccff99", "#99ff99", "#99ff33", "#ccff33", "#ffff33");
            html_colors += '<div style="clear:both;height:30px;">';
            for (var i = 0; i < arrC.length; i++) {
                if(arrC[i] !='|'){
                    var whitecell = '';
                    if( arrC[i] == '#ffffff' && i==98 ) whitecell = '';
                    html_colors += '<button class="md-pick" style="background:' + arrC[i] + whitecell + ';"></button>';
                } else {
                    html_colors += '</div><div style="clear:both;height:30px;">';
                }
            }
            html_colors += '</div>';
            html_rte = html_rte.replace('[COLORS]', html_colors);

            /*
            var html_colors = '';
            for(var i=0;i<this.settings.colors.length;i++){
                if(this.settings.colors[i]=='#ececec'){
                    html_colors+='<button class="md-pick" style="background:' + this.settings.colors[i] + ';border:#e7e7e7 1px solid"></button>';
                }else{
                    html_colors+='<button class="md-pick" style="background:' + this.settings.colors[i] + ';border:' + this.settings.colors[i] + ' 1px solid"></button>';
                }
            }
            html_rte = html_rte.replace('[COLORS]', html_colors);
            */

            if (jQuery('#rte-toolbar').length == 0) {

                jQuery('#divCb').append(html_rte);

                //this.prepareRteCommand('bold');
                //this.prepareRteCommand('italic');
                //this.prepareRteCommand('underline');
                //this.prepareRteCommand('strikethrough');
                this.prepareRteCommand('superscript');
                this.prepareRteCommand('subscript');
                this.prepareRteCommand('undo');
                this.prepareRteCommand('redo');

                jQuery('#rte-toolbar').draggable({
                    cursor: "move",
                    handle: ".rte-draggable",
                    start: function( event, ui ) {
                        jQuery('.rte-pop').css('display','none');
                    }
                });

                if(this.settings.toolbar=='left'){

                } else if(this.settings.toolbar=='right'){
                    jQuery('.rte-pop').addClass('arrow-right');
                } else {
                    jQuery('.rte-pop').addClass('arrow-top');
                }

            }


            var isCtrl = false;

            $element.on('keyup', function (e) {
                $element.data('contenteditor').realtime();
            });
            $element.on('mouseup', function (e) {
                $element.data('contenteditor').realtime();
            });
            /* Paste Content: Right Click */
            jQuery(document).on("paste",'#' + $element.attr('id'),function(e) {
                pasteContent($activeElement);
            });


            $element.on('keydown', function (e) {

                // Fix Select-All on <p> and then delete or backspace it.
                if (e.which == 46 || e.which == 8) {
                    var el;
                    try{
                        if (window.getSelection) {
                            el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                        }
                        else if (document.selection) {
                            el = document.selection.createRange().parentElement();
                        }

                        if(el.nodeName.toLowerCase()=='p'){
                            var t = '';
                            if(window.getSelection){
                                t = window.getSelection().toString();
                            }else if(document.getSelection){
                                t = document.getSelection().toString();
                            }else if(document.selection){
                                t = document.selection.createRange().text;
                            }
                            if(t==el.innerText) {
                                jQuery(el).html('<br>');
                                return false;
                            }
                        }

                    } catch(e) {}
                }

                /* Paste Content: CTRL-V */
                if (e.which == 17) {
                    isCtrl = true;
                    return;
                }
                if ((e.which == 86 && isCtrl == true) || (e.which == 86 && e.metaKey)) {

                    pasteContent($activeElement);

                }

                /* CTRL-A */
                if (e.ctrlKey) {
                    if (e.keyCode == 65 || e.keyCode == 97) { // 'A' or 'a'
                        e.preventDefault();

                        var is_ie = detectIE();
                        var el;
                        try{
                            if (window.getSelection) {
                                el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                            }
                            else if (document.selection) {
                                el = document.selection.createRange().parentElement();
                            }
                        } catch(e) {
                            return;
                        }
                        if (is_ie) {
                            var range = document.body.createTextRange();
                            range.moveToElementText(el);
                            range.select();
                        }
                        else {
                            var range = document.createRange();
                            range.selectNodeContents(el);
                            var oSel = window.getSelection();
                            oSel.removeAllRanges();
                            oSel.addRange(range);
                        }
                    }
                }

            }).keyup(function (e) {
                if (e.which == 17) {
                    isCtrl = false; // no Ctrl
                }

                //clean: remove unwanted span with font-size & line-height generated unexpectly.
                $element.find('[style]').each(function(){
                    if(jQuery(this).attr('style').indexOf('font-size')!=-1){
                        //console.log(jQuery(this).attr('style') + ' compare:' + jQuery(this).css('font-size') + ' | ' + jQuery(this).parent().css('font-size'));
                        if(jQuery(this).css('font-size') == jQuery(this).parent().css('font-size')) {
                            jQuery(this).css('font-size','');
                        }
                    }
                    if(jQuery(this).attr('style').indexOf('line-height')!=-1){
                        if(jQuery(this).css('line-height') == jQuery(this).parent().css('line-height')) {
                            jQuery(this).css('line-height','');
                        }
                    }

                });

            });

            // finish editing on click outside
            jQuery(document).on('mousedown', function (event) {

                var $active_element;
                if(jQuery(event.target).parents(".ui-draggable").length>0){
	                if( jQuery(event.target).parents(".ui-draggable").parent().data('contentbuilder') ) {
		                $active_element = jQuery(event.target).parents(".ui-draggable").parent(); //Get current Builder element
	                }
                }

                var bEditable = false;

                if (jQuery('#rte-toolbar').css('display') == 'none') return;

                var el = jQuery(event.target).prop("tagName").toLowerCase();


                jQuery(event.target).parents().each(function (e) {
                    if (jQuery(this).is('[contenteditable]') ||
                        jQuery(this).hasClass('md-modal') ||
                        jQuery(this).hasClass('cp-color-picker') ||
                        jQuery(this).attr('id') == 'divCb'
                        ) {
                        bEditable = true;
                        return;
                    }
                });

                if (jQuery(event.target).is('[contenteditable]')) {
                    bEditable = true;
                    return;
                }

                /*
                if ((jQuery(event.target).is('[contenteditable]') ||
                    jQuery(event.target).css('position') == 'absolute' ||
                    jQuery(event.target).css('position') == 'fixed' ||
                    jQuery(event.target).attr('id') == 'rte-toolbar') &&
                    el != 'img' &&
                    el != 'hr'
                    ) {
                    bEditable = true;
                    return;
                }

                jQuery(event.target).parents().each(function (e) {

                    if (jQuery(this).is('[contenteditable]') ||
                        jQuery(this).css('position') == 'absolute' ||
                        jQuery(this).css('position') == 'fixed' ||
                        jQuery(this).attr('id') == 'rte-toolbar'
                        ) {
                        bEditable = true;
                        return;
                    }

                });
                */

                if (!bEditable) {
                    $activeElement = null;

                    if (ce_toolbarDisplay=='auto') {

                        try{
                            var el;
                            if (window.getSelection) {
                                el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                            }
                            else if (document.selection) {
                                el = document.selection.createRange().parentElement();
                            }

                            var found=false;
                            jQuery(el).parents().each(function () {
                                if (jQuery(this).data('contentbuilder')) {
                                    found=true;
                                }
                            });

                            if(!found)
                            jQuery('#rte-toolbar').css('display', 'none');

                            //jQuery('.rte-pop').css('display', 'none');
                            //$element.data('contenteditor').closePop(); //Cannot use $element.data('contenteditor') on INIT AND on jQuery(document), because an instance can be dynamically destroyed which cause $element.data('contenteditor') become null)
                            ce_closePop();

                        } catch(e) {};

                    }

                    if (ce_outline) {
                        for (var i = 0; i < instances.length; i++) {
                            jQuery(instances[i]).css('outline', '');
                            jQuery(instances[i]).find('*').css('outline', '');
                        }
                    }

                    //Clear Controls (clearControls)
                    jQuery('.row-tool').stop(true, true).fadeOut(0);
                    //jQuery(".ui-draggable").removeClass('code');
                    jQuery(".ui-draggable").removeClass('ui-dragbox-outlined');
                    jQuery('#rte-toolbar').css('display', 'none');

                    //jQuery('.rte-pop').css('display', 'none');
                    //$element.data('contenteditor').closePop(); //Cannot use $element.data('contenteditor') on INIT AND on jQuery(document), because an instance can be dynamically destroyed which cause $element.data('contenteditor') become null)
                    ce_closePop();

                    jQuery("#divRteTable").stop(true, true).fadeOut(0);

                    //Auto Close Modal
                    if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();
                    if(jQuery("#md-createlink").data("simplemodal")) jQuery("#md-createlink").data("simplemodal").hide();
                    if($activeLink) if ($activeLink.attr('href') == 'http://') $activeLink.replaceWith($activeLink.html());
                    if(jQuery("#md-insertimage").data("simplemodal")) jQuery("#md-insertimage").data("simplemodal").hide();
                    if(jQuery("#md-img").data("simplemodal")) jQuery("#md-img").data("simplemodal").hide();
                    if(jQuery("#md-createsrc").data("simplemodal")) jQuery("#md-createsrc").data("simplemodal").hide();
                    if(jQuery("#md-createiframe").data("simplemodal")) jQuery("#md-createiframe").data("simplemodal").hide();
                    if(jQuery("#md-icon-select").data("simplemodal")) jQuery("#md-icon-select").data("simplemodal").hide();
                    if(jQuery("#md-tags-select").data("simplemodal")) jQuery("#md-tags-select").data("simplemodal").hide();

                }
            });


            $element.on('focus', function() {
                var $this = $(this);
                $this.data('before', $this.html());
                return $this;
            }).on('keyup', function() {//blur paste input
                var $this = $(this);
                if ($this.data('before') !== $this.html()) {
                    $this.data('before', $this.html());

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange(); //No problem using $element.data('contenteditor') here because it is inside $element.on (unique for each instance)

                    //Save for Undo
                    saveForUndo();
                }
                return $this;
            });


        };


        this.contentRender = function () {

            this.settings = jQuery.extend({}, defaults, options);

            var iconselect = this.settings.iconselect;

            if (iconselect != '') {
                $element.find('.ui-draggable > div:first-child i').each(function () {

                    if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code
                    if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                    if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                    if (jQuery(this).html() == '') {//if i has no content, means it is an icon
                        jQuery(this).off('click');
                        jQuery(this).click(function () {
                            $activeIcon = jQuery(this);
                            if( jQuery('#ifrIconSelect').attr('src').indexOf('blank.html') != -1) {
                                jQuery('#ifrIconSelect').attr('src', iconselect);
                            }
                            jQuery('#md-icon-select').css('max-width', '775px');
                            jQuery('#md-icon-select').simplemodal({noOverlay:true});
                            jQuery('#md-icon-select').data('simplemodal').show();
                            $element.data('contenteditor').closePop();
                        });
                    }
                });
            }

        };

        this.realtime = function(){

            var is_ie = detectIE();

            var el;
            var curr;
            try{
                var el;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    el = curr.parentNode;
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = curr.parentElement();
                }
            } catch(e) {
                return;
            }

            if( jQuery(el).parents("[data-html]").length > 0 ) return; //Mode: code

            if( jQuery(el).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly

            if( jQuery(el).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

            if(el.nodeName.toLowerCase()=='a'){
                if (is_ie) {
                    //already selected when clicked
                    /*if (document.selection.type != "Control") {
                        try {
                            var range = document.body.createTextRange();
                            range.moveToElementText(el);
                            range.select();
                        } catch (e) { return; }
                    }*/
                }
                else {
                    /*var range = document.createRange();
                    range.selectNodeContents(el);
                    var oSel = window.getSelection();
                    oSel.removeAllRanges();
                    oSel.addRange(range);*/
                }

                if(jQuery('#md-createlink').css('display')!='block') jQuery("#divRteLink").addClass('forceshow');
            } else {
                jQuery("#divRteLink").removeClass('forceshow');
            }

            /*** New Toolbar Activation ***/
            if(curr) {
                if (jQuery(curr).is('[contenteditable]')) {
                   jQuery("#rte-toolbar").stop(true, true).fadeIn(200);
                }
            }
            if (jQuery(el).is('[contenteditable]')) {
                   jQuery("#rte-toolbar").stop(true, true).fadeIn(200);
            }
            if (jQuery(el).parents('[contenteditable]').length > 0) {
                   jQuery("#rte-toolbar").stop(true, true).fadeIn(200);
            }
            /*** /New Toolbar Activation ***/
            //jQuery('.rte-pop').css('display', 'none');
            $element.data('contenteditor').closePop();

            var editable = $element.data('contenteditor').settings.editable;

            if (editable == '') {

            } else {

                /*** Old Toolbar Activation **/

                $element.find(editable).off('mousedown');
                $element.find(editable).on('mousedown', function (e) {

                    $activeElement = jQuery(this);

                    //alert(jQuery(this).prop("tagName").toLowerCase());
                    jQuery("#rte-toolbar").stop(true, true).fadeIn(200);

                    if (ce_outline) {
                        for (var i = 0; i < instances.length; i++) {
                            jQuery(instances[i]).css('outline', '');
                            jQuery(instances[i]).find('*').css('outline', '');
                        }
                        jQuery(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                    }

                });

                /*** /Old Toolbar Activation **/

                //Kalau di dalam .edit ada contenteditable, hapus, krn tdk perlu & di IE membuat keluar handler.
                $element.find('.edit').find(editable).removeAttr('contenteditable');

            }

            /* Table */
            if( jQuery(el).parents("table").length > 0 ) {
                var $table = jQuery(el).parents("table").first();
                var _top = $table.offset().top - 30;
                var _left = $table.offset().left + $table.width() - parseInt(jQuery("#divRteTable").css("width"));

                jQuery("#divRteTable").css("top", _top + "px");
                jQuery("#divRteTable").css("left", _left + "px");

                if(jQuery("#divRteTable").css('display')=='none')
                jQuery("#divRteTable").stop(true, true).css({ display: 'none' }).fadeIn(20);

            } else {
                jQuery("#divRteTable").stop(true, true).fadeOut(0);
            }


            //For: md-insertimage
            savedSelPublic = saveSelection();
            $activeIcon = null;

            //Auto Close Modal
            //Get active cell
            if(jQuery(curr).prop("tagName")) {
                if (jQuery(el).parents('[contenteditable]').length > 0) {
                    var sTagName = jQuery(curr).prop("tagName").toLowerCase();
                    if(sTagName=='td' || sTagName=='th'){
                        $activeCell = jQuery(curr);
                    } else if(jQuery(curr).parents('td,th').length>0) {
                        $activeCell = jQuery(curr).parents('td,th').first();
                    } else {
                        $activeCell = null;
                        if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();
                    }
                }
            } else {
                //console.log(jQuery(curr).parents('td,th').length)
                if( jQuery(curr).parents('td,th').length > 0 ) { //re-check (for FF & IE). When typing text inside td, it goes here.
                    $activeCell = jQuery(curr).parents('td,th').first();
                } else {
                    $activeCell = null;
                    if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();
                }
            }

            //Auto Close Modal
            if(jQuery("#md-createlink").data("simplemodal")) jQuery("#md-createlink").data("simplemodal").hide();
            if($activeLink) if ($activeLink.attr('href') == 'http://') $activeLink.replaceWith($activeLink.html());
            if(jQuery("#md-img").data("simplemodal")) jQuery("#md-img").data("simplemodal").hide();
            if(jQuery("#md-createsrc").data("simplemodal")) jQuery("#md-createsrc").data("simplemodal").hide();
            if(jQuery("#md-createiframe").data("simplemodal")) jQuery("#md-createiframe").data("simplemodal").hide();
            //if($activeIcon) if(jQuery("#md-icon-select").data("simplemodal")) jQuery("#md-icon-select").data("simplemodal").hide();

            //AUTO CLOSE SNIPPET PANEL
            var $active_element;
            if(jQuery(el).parents(".ui-draggable").length>0){
                if( jQuery(el).parents(".ui-draggable").parent().data('contentbuilder') ) {
                    $active_element = jQuery(el).parents(".ui-draggable").parent(); //Get current Builder element
                }
            }
            if($active_element){
                var cb_snippetPageSliding = $active_element.data('contentbuilder').settings.snippetPageSliding;
                var $window = jQuery(window);
                var windowsize = $window.width();
                var toolwidth = 255;
                if (windowsize < 600) {
                    toolwidth = 150;
                }
                if ($active_element.data('contentbuilder').settings.snippetTool == 'right') {
                    if(cb_snippetPageSliding ||
                        ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)))
                        ) {
                        if (parseInt(jQuery('#divTool').css('right')) == 0) {
                            //Close
                            jQuery('#divTool').animate({
                                right: '-=' + toolwidth + 'px'
                            }, 200);
                            jQuery('body').animate({
                            marginRight: '-=' + toolwidth + 'px'
                            }, 250);
                            jQuery('#rte-toolbar').animate({ // Slide the editor toolbar
                            paddingRight: '-=' + toolwidth + 'px'
                            }, 250);
                            jQuery('#lnkToolOpen i').attr('class','cb-icon-left-open-big');

                            jQuery('#divSnippetScrollUp').fadeOut(300);
                            jQuery('#divSnippetScrollDown').fadeOut(300);
                        }
                    } else {
                        if (parseInt(jQuery('#divTool').css('right')) == 0) {
                            //Close
                            jQuery('#divTool').animate({
                                right: '-=' + toolwidth + 'px'
                            }, 200);
                            jQuery('#lnkToolOpen i').attr('class','cb-icon-left-open-big');

                            jQuery('#divSnippetScrollUp').css('display','none');
                            jQuery('#divSnippetScrollDown').css('display','none');
                        }
                    }
                } else {
                    if(cb_snippetPageSliding ||
                        ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)))
                        ) {
                        if (parseInt(jQuery('#divTool').css('left')) == 0) {
                            //Close
                            jQuery('#divTool').animate({
                                left: '-=' + (toolwidth + 0) + 'px'
                            }, 200);
                            jQuery('body').animate({
                            marginLeft: '-=' + toolwidth + 'px'
                            }, 250);
                            jQuery('#rte-toolbar').animate({
                            paddingLeft: '-=' + toolwidth + 'px'
                            }, 250);
                            jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');

                            jQuery('#divSnippetScrollUp').fadeOut(300);
                            jQuery('#divSnippetScrollDown').fadeOut(300);
                        }
                    } else {
                        if (parseInt(jQuery('#divTool').css('left')) == 0) {
                            //Close
                            jQuery('#divTool').animate({
                                left: '-=' + (toolwidth + 0) + 'px'
                            }, 200);
                            jQuery("#lnkToolOpen i").attr('class','cb-icon-right-open-big');

                            jQuery('#divSnippetScrollUp').css('display','none');
                            jQuery('#divSnippetScrollDown').css('display','none');
                        }
                    }
                }
            }

            $element.data('contenteditor').getState();

        };

        this.getState = function(){
            //Toolbar
            if(document.queryCommandState("bold")){
                jQuery('[data-rte-cmd=bold]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=bold]').removeClass('on');
            }
            if(document.queryCommandState("italic")){
                jQuery('[data-rte-cmd=italic]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=italic]').removeClass('on');
            }
            if(document.queryCommandState("underline")){
                jQuery('[data-rte-cmd=underline]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=underline]').removeClass('on');
            }
            if(document.queryCommandState("strikethrough")){
                jQuery('[data-rte-cmd=strikethrough]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=strikethrough]').removeClass('on');
            }
            if(document.queryCommandState("superscript")){
                jQuery('[data-rte-cmd=superscript]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=superscript]').removeClass('on');
            }
            if(document.queryCommandState("subscript")){
                jQuery('[data-rte-cmd=subscript]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=subscript]').removeClass('on');
            }

            if(document.queryCommandState("JustifyFull")){
                jQuery('[data-align=justify]').addClass('on');
            } else {
                jQuery('[data-align=justify]').removeClass('on');
            }
            if(document.queryCommandState("JustifyLeft")){
                jQuery('[data-align=left]').addClass('on');
            } else {
                jQuery('[data-align=left]').removeClass('on');
            }
            if(document.queryCommandState("JustifyRight")){
                jQuery('[data-align=right]').addClass('on');
            } else {
                jQuery('[data-align=right]').removeClass('on');
            }
            if(document.queryCommandState("JustifyCenter")){
                jQuery('[data-align=center]').addClass('on');
            } else {
                jQuery('[data-align=center]').removeClass('on');
            }

            var s = document.queryCommandValue("FontName");
            var fontname = s.split(',')[0];
            fontname = fontname.replace('"','').replace('"','');
            fontname = jQuery.trim(fontname).toLowerCase();

            if(jQuery('#ifrFonts').attr('src').indexOf('fonts.html') == -1) {
                jQuery('#ifrFonts').attr('src',sScriptPath+'fonts.html?1');
            }
            jQuery('#ifrFonts').contents().find('[data-font-family]').removeClass('on');
            jQuery('#ifrFonts').contents().find('[data-font-family]').each(function(){
                var f = jQuery(this).attr('data-font-family');
                f = f.split(',')[0];
                f = jQuery.trim(f).toLowerCase();

                if(f==fontname && f!='') {
                    jQuery(this).addClass('on');
                }
            });

            var block = document.queryCommandValue("FormatBlock");
            block = block.toLowerCase();

            if(block=='normal')block='p';
            if(block=='heading 1')block='h1';
            if(block=='heading 2')block='h2';
            if(block=='heading 3')block='h3';
            if(block=='heading 4')block='h4';
            if(block=='heading 5')block='h5';
            if(block=='heading 6')block='h6';
            if(block=='formatted')block='pre';

            if(jQuery('#ifrHeadings').attr('src').indexOf('headings.html') == -1) {
                jQuery('#ifrHeadings').attr('src',sScriptPath+'headings.html?1');
            }
            jQuery('#ifrHeadings').contents().find('[data-heading]').removeClass('on');
            jQuery('#ifrHeadings').contents().find('[data-heading]').each(function(){
                var p = jQuery(this).attr('data-heading');

                if(p==block && block!='') {
                    jQuery(this).addClass('on');
                }
            });

            var el;
            var curr;
            if (window.getSelection) {
                curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                if(curr.nodeType==3) {  //ini text node
                    el = curr.parentNode;
                } else {
                    el = curr;
                }
            }
            else if (document.selection) {
                curr = document.selection.createRange();
                el = document.selection.createRange().parentElement();
            }

            if(jQuery(el).css('text-transform')=='uppercase'){
                jQuery('[data-rte-cmd=uppercase]').addClass('on');
            } else {
                jQuery('[data-rte-cmd=uppercase]').removeClass('on');
            }

        };

        this.closePop = function () {
            jQuery('.rte-pop').css('display','none');

            jQuery('[data-rte-cmd="formatting"]').removeClass('on');
            jQuery('[data-rte-cmd="textsettings"]').removeClass('on');
            jQuery('[data-rte-cmd="color"]').removeClass('on');
            jQuery('[data-rte-cmd="font"]').removeClass('on');
            jQuery('[data-rte-cmd="formatPara"]').removeClass('on');
            jQuery('[data-rte-cmd="align"]').removeClass('on');
            jQuery('[data-rte-cmd="list"]').removeClass('on');
            jQuery('[data-rte-cmd="table"]').removeClass('on');


        };

        this.render = function () {

            var editable = $element.data('contenteditor').settings.editable;
            if (editable == '') {

                $element.attr('contenteditable', 'true');

                $element.off('mousedown');
                $element.on('mousedown', function (e) {

                    $activeElement = jQuery(this);

                    jQuery("#rte-toolbar").stop(true, true).fadeIn(200);

                    if (ce_outline) {
                        for (var i = 0; i < instances.length; i++) {
                            jQuery(instances[i]).css('outline', '');
                            jQuery(instances[i]).find('*').css('outline', '');
                        }
                        jQuery(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                    }

                });

            } else {

                $element.find(editable).each(function () {

                    var editMode = $element.data('contenteditor').settings.editMode;
                    if (editMode == 'default') {

                        //do nothing (parent will set editable)

                        //but force .edit editable inside code block
                        if( jQuery(this).parents("[data-html]").length > 0 )  {
                            if( jQuery(this).hasClass('edit') ) {
                                jQuery(this).attr('contenteditable', 'true');
                            }
                        }

                    } else {

                        if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code
                        if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                        if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                        var attr = jQuery(this).attr('contenteditable');

                        if (typeof attr !== typeof undefined && attr !== false) {

                        } else {

                            jQuery(this).attr('contenteditable', 'true');

                        }

                    }

                });

                $element.find(editable).off('mousedown');
                $element.find(editable).on('mousedown', function (e) {

                    $activeElement = jQuery(this);

                    if (ce_outline) {
                        for (var i = 0; i < instances.length; i++) {
                            jQuery(instances[i]).css('outline', '');
                            jQuery(instances[i]).find('*').css('outline', '');
                        }
                        jQuery(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                    }

                });


                //Kalau di dalam .edit ada contenteditable, hapus, krn tdk perlu & di IE membuat keluar handler.
                $element.find('.edit').find(editable).removeAttr('contenteditable');

            }


            /*
            $element.find('a').each(function(){
                if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code
                if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                //jQuery(this).attr('contenteditable', 'true');
            });*/

            /* Make all buttons not editable */
            $element.find('.is-btn').attr('contenteditable', 'false');
            $element.find('.is-btn').each(function(){
                jQuery(this).focus(function(){jQuery(this).blur()}); //also prevent cursor to focus (and link not working during editing)
            });


            //APPLYING SOME EDITING BEHAVIORS
            var editMode = $element.data('contenteditor').settings.editMode;
            if (editMode == 'default') {

                $element.find("h1,h2,h3,h4,h5,h6").off('keydown'); //keypress
                $element.find("h1,h2,h3,h4,h5,h6").on('keydown', function (e) {

                    if (e.keyCode == 13) {

                        var is_ie = detectIE();
                        if (is_ie && is_ie<=10) {
                            var oSel = document.selection.createRange();
                            if (oSel.parentElement) {
                                oSel.pasteHTML('<br>');
                                e.cancelBubble = true;
                                e.returnValue = false;
                                oSel.select();
                                oSel.moveEnd("character", 1);
                                oSel.moveStart("character", 1);
                                oSel.collapse(false);
                                return false;
                            }
                        } else {
                            //document.execCommand('insertHTML', false, '<br><br>');
                            //return false;

                            var oSel = window.getSelection();
                            var range = oSel.getRangeAt(0);
                            range.extractContents();
                            range.collapse(true);
                            var docFrag = range.createContextualFragment('<br>');
                            //range.collapse(false);
                            var lastNode = docFrag.lastChild;
                            range.insertNode(docFrag);
                            //try { oEditor.document.designMode = "on"; } catch (e) { }
                            range.setStartAfter(lastNode);
                            range.setEndAfter(lastNode);

                            //workaround.for unknown reason, chrome need 2 br to make new line if cursor located at the end of document.
                            if (range.endContainer.nodeType == 1) {
                                //
                                if (range.endOffset == range.endContainer.childNodes.length - 1) {
                                    range.insertNode(range.createContextualFragment("<br />"));
                                    range.setStartAfter(lastNode);
                                    range.setEndAfter(lastNode);
                                }
                            }
                            //

                            var comCon = range.commonAncestorContainer;
                            if (comCon && comCon.parentNode) {
                                try { comCon.parentNode.normalize(); } catch (e) { }
                            }

                            oSel.removeAllRanges();
                            oSel.addRange(range);

                            return false;
                        }

                    }

                });


                //Make PARENT editable
                $element.children('div.ui-draggable').each(function(){
                    try {
                        var attr = jQuery(this).children().first().children().first().attr('data-html');
                        if (typeof attr !== typeof undefined && attr !== false) {
                            return; //Mode: code
                        }
                        if( jQuery(this).children().first().children().first().parents("[data-html]").length > 0 ) return; //Mode: code
                        if( jQuery(this).children().first().children().first().parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly
                        if( jQuery(this).children().first().children().first().parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected
                    } catch(e) {}

                    //Email Mode (structure uses table)
                    var bEmailMode =false;
                    try {
                        if(jQuery(this).children().first().children().first().prop("tagName").toLowerCase() == 'table' ) bEmailMode = true;
                    } catch(e) {}

                    if(bEmailMode) {

                        jQuery(this).find('td,th').each(function(){
                            if(jQuery(this).children().length == 1) {
                                if( jQuery(this).children().first().prop("tagName").toLowerCase() == 'table' ) {

                                } else {
                                    jQuery(this).attr('contenteditable',true);
                                }
                            } else {
                                jQuery(this).attr('contenteditable',true);
                            }
                        });

                    } else {

                        jQuery(this).children().first().children().each(function(){
                            jQuery(this).attr('contenteditable',true);
                        });

                    }

                    //Email Mode (structure uses table) => For IE
                    var is_ie = detectIE();
                    var is_edge = detectEdge();
                    if ((is_ie && is_ie <= 11) || is_edge) {
                        //jQuery(this); //div.ui-draggable
                        //jQuery(this).children().first(); //div
                        //jQuery(this).children().first().children().first() //table
                        try{
                            if( jQuery(this).children().first().children().first().prop("tagName").toLowerCase() == 'table') {
                                jQuery(this).children().first().attr('contenteditable',true); //div //Convert to natural editing
                                //table cannot be set contenteditable=true (IE)
                        }
                        } catch(e){}
                    }

                });


                //Fix few problems (on Chrome, Opera)
                $element.find("div").off('keyup');
                $element.find("div").on('keyup', function (e) {

                    var el;
                    var curr;
                    try{
                        if (window.getSelection) {
                            curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                            el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                        }
                        else if (document.selection) {
                            curr = document.selection.createRange();
                            el = document.selection.createRange().parentElement();
                        }
                    } catch(e) {return;} //Use try to prevent lost selection after undo

                    if (e.keyCode == 13 && !e.shiftKey){
                        var is_ie = detectIE();
                        if (is_ie>0) {

                        } else {
                            //So that enter at the end of list returns <p>
                            var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
                            var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
                            var isOpera = window.opera;
                            var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                            if(isChrome || isOpera) {
                                //Without this, pressing ENTER at the end of list will returns <p> on Chrome but then it become <div> (On Opera it returns <div>)
                                //With this, we change it into <p>
                                if(jQuery(el).prop("tagName").toLowerCase()=='p' || jQuery(el).prop("tagName").toLowerCase() =='div') {
                                    document.execCommand('formatBlock', false, '<p>');
                                }
                            }
                            if(isFirefox) {
                                //On FF (when enter at the end of list) jQuery(curr).html() returns undefined
                                if(!jQuery(curr).html()) document.execCommand('formatBlock', false, '<p>');
                            }
                        }
                    }

                    //Safe image delete by applying paragraph (no empty div)
                    /*if(e.keyCode == 8 || e.keyCode == 46) { //Delete key
                        if(jQuery(el).prop("tagName").toLowerCase() =='div'){
                            document.execCommand('formatBlock', false, '<p>');
                        }
                    }*/
                    /*
                    if(e.keyCode == 8 || e.keyCode == 46) { //Delete key
                        if(jQuery(curr)){
                            var currTag = jQuery(curr).prop("tagName").toLowerCase();
                            if(currTag=='h1' || currTag=='h1' || currTag=='h2' || currTag=='h3' || currTag=='h4' || currTag=='h5' || currTag=='h6'){
                               if(jQuery(curr).text()==''){
                                    // document.execCommand('formatBlock', false, '<p>');
                               }
                            }
                        }
                    }
                    */
                });

                $element.find("div").off('keydown');
                $element.find("div").on('keydown', function (e) {
                    var el;
                    var curr;
                    try{
                        if (window.getSelection) {
                            curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                            el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                        }
                        else if (document.selection) {
                            curr = document.selection.createRange();
                            el = document.selection.createRange().parentElement();
                        }
                    } catch(e) {return;} //Use try to prevent lost selection after undo


                    if(e.keyCode == 8 || e.keyCode == 46) { //Delete key

                        if(jQuery(curr).html()){
                            var currTag = jQuery(curr).prop("tagName").toLowerCase();
                            if(currTag=='h1' || currTag=='h1' || currTag=='h2' || currTag=='h3' || currTag=='h4' || currTag=='h5' || currTag=='h6' || currTag=='p'){

                               if(jQuery(curr).text()==''){

                                    document.execCommand('removeFormat', false, null);

                                    jQuery(curr).remove();

                                    var oSel = window.getSelection();
                                    var range = oSel.getRangeAt(0);
                                    range.extractContents();
                                    range.collapse(true);
                                    oSel.removeAllRanges();
                                    oSel.addRange(range);


                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                               }
                            }
                        }
                    }
                });

            } else {


                //Apply BR on Paragraph Enter
                //p enter ganti div gak bisa di-edit, kalo pake p buggy di IE, jadi pake <br>
                $element.find("p").off('keydown'); //keypress
                $element.find("p").on('keydown', function (e) {
                    /*if (e.keyCode == 13) {
                    jQuery(this).parent().attr('contenteditable', 'true');
                    }*/

                    if (e.keyCode == 13 && $element.find("li").length == 0) {  // don't apply br on li

                        var UA = navigator.userAgent.toLowerCase();
                        var LiveEditor_isIE = (UA.indexOf('msie') >= 0) ? true : false;
                        if (LiveEditor_isIE) {
                            var oSel = document.selection.createRange();
                            if (oSel.parentElement) {
                                oSel.pasteHTML('<br>');
                                e.cancelBubble = true;
                                e.returnValue = false;
                                oSel.select();
                                oSel.moveEnd("character", 1);
                                oSel.moveStart("character", 1);
                                oSel.collapse(false);
                                return false;
                            }
                        } else {
                            //document.execCommand('insertHTML', false, '<br><br>');
                            //return false;

                            var oSel = window.getSelection();
                            var range = oSel.getRangeAt(0);
                            range.extractContents();
                            range.collapse(true);
                            var docFrag = range.createContextualFragment('<br>');
                            //range.collapse(false);
                            var lastNode = docFrag.lastChild;
                            range.insertNode(docFrag);
                            //try { oEditor.document.designMode = "on"; } catch (e) { }
                            range.setStartAfter(lastNode);
                            range.setEndAfter(lastNode);

                            //workaround.for unknown reason, chrome need 2 br to make new line if cursor located at the end of document.
                            if (range.endContainer.nodeType == 1) {
                                //
                                if (range.endOffset == range.endContainer.childNodes.length - 1) {
                                    range.insertNode(range.createContextualFragment("<br />"));
                                    range.setStartAfter(lastNode);
                                    range.setEndAfter(lastNode);
                                }
                            }
                            //

                            var comCon = range.commonAncestorContainer;
                            if (comCon && comCon.parentNode) {
                                try { comCon.parentNode.normalize(); } catch (e) { }
                            }

                            oSel.removeAllRanges();
                            oSel.addRange(range);

                            return false;
                        }

                    }
                });

            }

            jQuery('[data-rte-cmd="fontsize"]').off('click');
            jQuery('[data-rte-cmd="fontsize"]').click(function(){

                if(savedSelPublic){
                    restoreSelection(savedSelPublic);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    //jQuery(el).css('font-size',value + 'px');

                    if(jQuery(el).attr('contenteditable')!='true'){
                        if(jQuery(el).parents('[contenteditable]').length==0) {
                            return;
                        }
                    }

                    if(jQuery(this).attr('data-val')!='clear') {

                        var value = parseInt(jQuery(el).css('font-size'));
                        if(jQuery(this).attr('data-val')=='increase') {
                            value=value+1;
                        }
                        if(jQuery(this).attr('data-val')=='decrease') {
                            value=value-1;
                        }
                        var s = value + 'px';

                        var text = getSelected();

                        if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                            document.execCommand("fontSize", false, "7");
                            var fontElements = document.getElementsByTagName("font");
                            for (var i = 0, len = fontElements.length; i < len; ++i) {
                                if (fontElements[i].size == "7") {
                                    fontElements[i].removeAttribute("size");
                                    fontElements[i].style.fontSize = s;
                                }
                            }
                            savedSelPublic = saveSelection();
                        }
                        else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                            if(jQuery(el).html()){
                                jQuery(el).css('font-size', s);
                            } else {
                                jQuery(el).parent().css('font-size', s);
                            }
                        }
                        else{
                            jQuery(el).css('font-size', s);
                        };

                        jQuery('#outFontSize').html(s);

                    } else {

                        jQuery(el).css('font-size', '');

                        //Get Current
                        var el;
                        var curr;
                        if (window.getSelection) {
                            curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                            if(curr.nodeType==3) {  //ini text node
                                el = curr.parentNode;
                            } else {
                                el = curr;
                            }
                        }
                        else if (document.selection) {
                            curr = document.selection.createRange();
                            el = document.selection.createRange().parentElement();
                        }

                        var currentFontSize = parseInt(jQuery(el).css('font-size'));
                        jQuery('#outFontSize').html(currentFontSize+'px');
                        jQuery('#outFontSize').attr('data-initial-value',currentFontSize);

                        //savedSelPublic = saveSelection();

                    }

                    //Save for Undo
                    saveForUndo();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                }

            });

            jQuery('[data-rte-cmd="letterspacing"]').off('click');
            jQuery('[data-rte-cmd="letterspacing"]').click(function(){

                if(savedSelPublic){
                    restoreSelection(savedSelPublic);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    //jQuery(el).css('font-size',value + 'px');

                    if(jQuery(el).attr('contenteditable')!='true'){
                        if(jQuery(el).parents('[contenteditable]').length==0) {
                            return;
                        }
                    }


                    if(jQuery(this).attr('data-val')!='clear') {

                        var value = parseInt(jQuery(el).css('letter-spacing'));
                        if(jQuery(this).attr('data-val')=='increase') {
                            value=value+1;
                        }
                        if(jQuery(this).attr('data-val')=='decrease') {
                            value=value-1;
                        }
                        jQuery(el).css('letter-spacing',value + 'px');
                        jQuery('#outLetterSpacing').html(value+'px');

                    } else {

                        jQuery(el).css('letter-spacing', '');

                        //Get Current
                        var el;
                        var curr;
                        if (window.getSelection) {
                            curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                            if(curr.nodeType==3) {  //ini text node
                                el = curr.parentNode;
                            } else {
                                el = curr;
                            }
                        }
                        else if (document.selection) {
                            curr = document.selection.createRange();
                            el = document.selection.createRange().parentElement();
                        }

                        var currentLetterSpacing = parseInt(jQuery(el).css('letter-spacing'));
                        jQuery('#outLetterSpacing').html(currentLetterSpacing+'px');
                        jQuery('#outLetterSpacing').attr('data-initial-value',currentLetterSpacing);

                        //savedSelPublic = saveSelection();

                    }

                    //Save for Undo
                    saveForUndo();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                }

            });


            jQuery('[data-rte-cmd="lineheight"]').off('click');
            jQuery('[data-rte-cmd="lineheight"]').click(function(){

                if(savedSelPublic){
                    restoreSelection(savedSelPublic);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    //jQuery(el).css('font-size',value + 'px');

                    if(jQuery(el).attr('contenteditable')!='true'){
                        if(jQuery(el).parents('[contenteditable]').length==0) {
                            return;
                        }
                    }

                    if(jQuery(this).attr('data-val')!='clear') {

                        var value = parseInt(jQuery(el).css('line-height'));
                        if(jQuery(this).attr('data-val')=='increase') {
                            value=value+1;
                        }
                        if(jQuery(this).attr('data-val')=='decrease') {
                            value=value-1;
                        }
                        jQuery(el).css('line-height',value + 'px');
                        jQuery('#outLineHeight').html(value+'px');

                    } else {

                        jQuery(el).css('line-height', '');

                        //Get Current
                        var el;
                        var curr;
                        if (window.getSelection) {
                            curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                            if(curr.nodeType==3) {  //ini text node
                                el = curr.parentNode;
                            } else {
                                el = curr;
                            }
                        }
                        else if (document.selection) {
                            curr = document.selection.createRange();
                            el = document.selection.createRange().parentElement();
                        }

                        var currentLineHeight = parseInt(jQuery(el).css('line-height'));
                        jQuery('#outLineHeight').html(currentLineHeight+'px');
                        jQuery('#outLineHeight').attr('data-initial-value',currentLineHeight);

                        //savedSelPublic = saveSelection();
                    }

                    //Save for Undo
                    saveForUndo();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                }

            });


            jQuery('[data-rte-cmd="removeElement"]').off('click');
            jQuery('[data-rte-cmd="removeElement"]').click(function (e) {

                $activeElement.remove();

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                $element.data('contenteditor').settings.hasChanged = true;
                $element.data('contenteditor').render();

                //Save for Undo
                saveForUndo();

                e.preventDefault();
            });

            jQuery('[data-rte-cmd="fontsize2"]').off('click');
            jQuery('[data-rte-cmd="fontsize2"]').click(function (e) {

                var savedSel = saveSelection();

                /**** Custom Modal ****/
                jQuery('#md-fontsize').css('max-width', '190px');
                jQuery('#md-fontsize').simplemodal();
                jQuery('#md-fontsize').data('simplemodal').show(savedSel);
                $element.data('contenteditor').closePop();
                e.preventDefault();

                if(jQuery('#ifrFontSize').attr('src').indexOf('fontsize.html') == -1) {
                    jQuery('#ifrFontSize').attr('src',sScriptPath+'fontsize.html');
                }

                //Prepare
                var text = getSelected();

                jQuery('.md-pickfontsize').off('click');
                jQuery('.md-pickfontsize').click(function(){

                    restoreSelection(savedSel);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                        //el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    if(jQuery(el).parents('[contenteditable]').length==0) {
                        jQuery('#md-fontsize').data('simplemodal').hide();
                        return;
                    }

                    var s = jQuery(this).attr('data-font-size');

                    if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                        document.execCommand("fontSize", false, "7");
                        var fontElements = document.getElementsByTagName("font");
                        for (var i = 0, len = fontElements.length; i < len; ++i) {
                            if (fontElements[i].size == "7") {
                                fontElements[i].removeAttribute("size");
                                fontElements[i].style.fontSize = s;
                            }
                        }
                    }
                    else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                        if(jQuery(el).html()){
                            jQuery(el).css('font-size', s);
                        } else {
                            jQuery(el).parent().css('font-size', s);
                        }
                    }
                    else{
                        jQuery(el).css('font-size', s);
                    };

                    jQuery(this).blur();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    $element.data('contenteditor').settings.hasChanged = true;
                    e.preventDefault();

                    //Save for Undo
                    saveForUndo();

                    //jQuery('#md-fontsize').data('simplemodal').hide();

                });
                /**** /Custom Modal ****/
            });


            jQuery('[data-rte-cmd="removeFormat"]').off('click');
            jQuery('[data-rte-cmd="removeFormat"]').click(function (e) {

                document.execCommand('removeFormat', false, null);
                document.execCommand('removeFormat', false, null);

                jQuery(this).blur();

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                $element.data('contenteditor').settings.hasChanged = true;
                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });


            jQuery('[data-rte-cmd="unlink"]').off('click');
            jQuery('[data-rte-cmd="unlink"]').click(function (e) {

                document.execCommand('unlink', false, null);
                jQuery("#divRteLink").removeClass('forceshow');

                jQuery(this).blur();

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                $element.data('contenteditor').settings.hasChanged = true;
                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });

            var storedEl;
            jQuery('[data-rte-cmd="html"]').off('click');
            jQuery('[data-rte-cmd="html"]').click(function (e) {

                var el;
                if (window.getSelection) {
                    el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                }
                else if (document.selection) {
                    el = document.selection.createRange().parentElement();
                }

                var found=false;
                jQuery(el).parents().each(function () {
                    if (jQuery(this).data('contentbuilder')) {
                        jQuery(this).data('contentbuilder').viewHtml();

                        found=true;
                        storedEl = el;
                    }
                });

                //In case of not focus
                if(!found && storedEl){
                    el = storedEl;
                    jQuery(el).parents().each(function () {
                        if (jQuery(this).data('contentbuilder')) {
                            jQuery(this).data('contentbuilder').viewHtml();
                        }
                    });
                }
                e.preventDefault();

            });

            jQuery('[data-rte-cmd="formatPara"]').off('click');
            jQuery('[data-rte-cmd="formatPara"]').click(function (e) {

                savedSelPublic = saveSelection();
                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-headings').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-57-132;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }

                jQuery('#pop-headings').css('position', 'fixed');
                jQuery('#pop-headings').css('top', top + 'px');
                jQuery('#pop-headings').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-headings').css('display','block');
                jQuery(this).addClass('on');

                if(jQuery('#ifrHeadings').attr('src').indexOf('headings.html') == -1) {
                    jQuery('#ifrHeadings').attr('src',sScriptPath+'headings.html?1');
                }

                var is_ie = detectIE();
                if(is_ie) restoreSelection(savedSelPublic); //Only needed for IE. If not, sometimes the document.queryCommandValue("FormatBlock") inside getState() returns empty
                $element.data('contenteditor').getState();

                //Get active heading
                try{
                    var $contents = jQuery('#ifrHeadings').contents();
                    var $parentDiv = $contents.find('#divHeadings');
                    var $innerListItem = $contents.find('.on');
                    $parentDiv.animate({
                        scrollTop: $parentDiv.scrollTop() + $innerListItem.position().top - 7
                    }, 1000);
                } catch(e){}

                jQuery('.md-pickheading').off('click');
                jQuery('.md-pickheading').click(function(){

                    restoreSelection(savedSelPublic);

                    var s = jQuery(this).attr('data-heading');

                    //$element.attr('contenteditable', true);
                    document.execCommand('formatBlock', false, '<' + s + '>');
                    //$element.removeAttr('contenteditable');
                    //$element.data('contenteditor').render();

                    $element.data('contenteditor').getState();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    $element.data('contenteditor').settings.hasChanged = true;
                    e.preventDefault();

                    savedSelPublic = saveSelection();

                    //Save for Undo
                    saveForUndo();

                });
            });


            jQuery('[data-rte-cmd="font"]').off('click');
            jQuery('[data-rte-cmd="font"]').click(function (e) {

                savedSelPublic = saveSelection();
                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-fontfamily').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-57-132;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }

                jQuery('#pop-fontfamily').css('position', 'fixed');
                jQuery('#pop-fontfamily').css('top', top + 'px');
                jQuery('#pop-fontfamily').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-fontfamily').css('display','block');
                jQuery(this).addClass('on');

                if(jQuery('#ifrFonts').attr('src').indexOf('fonts.html') == -1) {
                    jQuery('#ifrFonts').attr('src',sScriptPath+'fonts.html?1');
                }

                //Prepare
                var text = getSelected();

                $element.data('contenteditor').getState();

                //Get active font
                try{
                    var $contents = jQuery('#ifrFonts').contents();
                    var $parentDiv = $contents.find('#divFontList');
                    var $innerListItem = $contents.find('.on');
                    $parentDiv.animate({
                        scrollTop: $parentDiv.scrollTop() + $innerListItem.position().top - 7
                    }, 1000);
                } catch(e){}

                jQuery('.md-pickfontfamily').off('click');
                jQuery('.md-pickfontfamily').click(function(){

                    restoreSelection(savedSelPublic);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                        //el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;

                        //TODO
                        if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                            el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                            el.nodeName != 'P') {
                            el = el.parentNode;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                        if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                            el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                            el.nodeName != 'P') {
                            el = el.parentElement();
                        }
                    }

                    var s = jQuery(this).attr('data-font-family');

                    //jQuery(el).css('font-family', s);
                    if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                        document.execCommand("fontName", false, s);
                        var fontElements = document.getElementsByTagName("font");
                        for (var i = 0, len = fontElements.length; i < len; ++i) {
                            if (fontElements[i].face == s) {
                                fontElements[i].removeAttribute("face");
                                fontElements[i].style.fontFamily = s;
                            }
                        }
                    }
                    else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                        if(jQuery(el).html()){
                            jQuery(el).css('font-family', s);
                        } else {
                            jQuery(el).parent().css('font-family', s);
                        }
                    }
                    else{
                        jQuery(el).css('font-family', s);
                    };


                    var o = jQuery(this).attr('data-font-style');
                    if (!o) { o = '' } else { o = ':' + o };

                    var fontname = s.split(',')[0];
                    var provider = jQuery(this).attr('data-provider');
                    if(provider=='google'){
                        var bExist = false;
                        var links=document.getElementsByTagName("link");
                        for(var i=0;i<links.length;i++) {
                            var sSrc=links[i].href.toLowerCase();
                            sSrc = sSrc.replace(/\+/g,' ').replace(/%20/g,' ');
                            if(sSrc.indexOf(fontname.toLowerCase())!=-1) bExist=true;
                        }
                        if(!bExist) {
                            //$element.append('<link href="//fonts.googleapis.com/css?family='+fontname+'" rel="stylesheet" property="stylesheet" type="text/css">');
                            jQuery(el).parents().each(function () {
                                if (jQuery(this).data('contentbuilder')) {
                                    jQuery(this).append('<link href="//fonts.googleapis.com/css?family=' + fontname + o + '" rel="stylesheet" property="stylesheet" type="text/css">');
                                }
                            });
                            /*
                            Or simply use this:
                            jQuery(el).parents(".ui-draggable").parent().append('<link href="//fonts.googleapis.com/css?family='+fontname+'" rel="stylesheet" property="stylesheet" type="text/css">');
                            */
                        }
                    }


                    //TODO: make function
                    //Cleanup Google font css link
                    jQuery(cb_list).each(function(){
                        var $cb = jQuery(this);
                        $cb.find('link').each(function(){


                            var sSrc=jQuery(this).attr('href').toLowerCase();
                            if(sSrc.indexOf('googleapis')!=-1) {
                                //get fontname
                                sSrc = sSrc.replace(/\+/g,' ').replace(/%20/g,' ');
                                var fontname = sSrc.substr( sSrc.indexOf('family=') + 7 );
                                if(fontname.indexOf(':') != -1){
                                    fontname = fontname.split(':')[0];
                                }
                                if(fontname.indexOf('|') != -1){
                                    fontname = fontname.split('|')[0];
                                }
                                //check if fontname used in content
                                var tmp = $cb.data('contentbuilder').html().toLowerCase();

                                var count = tmp.split(fontname).length;

                                if(count<3){
                                    //not used
                                    jQuery(this).attr('data-rel','_del');
                                }
                            }
                        });
                    });

                    $element.find('[data-rel="_del"]').remove();//del not used google font css link

                    $element.data('contenteditor').getState();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    $element.data('contenteditor').settings.hasChanged = true;
                    e.preventDefault();

                    //Save for Undo
                    saveForUndo();
                });

            });

            /* Insert Image */
            jQuery('[data-rte-cmd="image"]').off('click');
            jQuery('[data-rte-cmd="image"]').click(function (e) {

                savedSelPublic = saveSelection();

                jQuery('#md-insertimage').css('max-width', '550px');
                jQuery('#md-insertimage').simplemodal({noOverlay:true});
                jQuery('#md-insertimage').data('simplemodal').show(savedSel);
                $element.data('contenteditor').closePop();

                //Clear previous
                jQuery('#fileInsertImage').clearInputs();
                jQuery('.md-preview-area').hide();
                jQuery('.md-drop-area').show();
		        jQuery('.md-drop-area').removeClass('image-dropping');

                //Clear image source input
                jQuery('#txtImgUrl_rte').val('');

                jQuery('#btnImgOk_rte').off('click');
                jQuery('#btnImgOk_rte').click(function(){

                    if(!savedSelPublic) return;
                    restoreSelection(savedSelPublic);

                    var val = '';
                    if(jQuery('.md-drop-area').css('display') =='none'){
                        val = jQuery('#imgInsertImagePreview').attr('src');
                    } else {
                        val = jQuery('#txtImgUrl_rte').val();
                    }

                    if(val == '') return;

                    pasteHtmlAtCaret('<img src="' + val + '" />',true);
                    jQuery('#md-insertimage').data('simplemodal').hide();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Save for Undo
                    saveForUndo();

                    //Apply builder behaviors
                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }
                    jQuery(el).parents().each(function () {
                        if (jQuery(this).data('contentbuilder')) {
                            jQuery(this).data('contentbuilder').applyBehavior();
                        }
                    });

                    //Remove selection
                    var sel;
                    if (window.getSelection) {
                        sel = window.getSelection();
                    } else if (document.selection) {
                        sel = document.selection
                    }
                    sel.removeAllRanges();

                    jQuery('#rte-toolbar').css('display', 'none'); //because the cursor is not on content (removeAllRanges)

                });

                e.preventDefault();

                return;

            });

            jQuery('#fileInsertImage').off('change');
            jQuery('#fileInsertImage').on('change', function(e){

                var input = e.target;

                if (input.files && input.files[0]) {

                    var reader = new FileReader();

                    reader.onload = function(e) {
                        jQuery('.md-drop-area').hide();

                        jQuery('#imgInsertImagePreview').attr('src', e.target.result);
                        jQuery('.md-preview-area').show();

                        jQuery('.image-title').html(input.files[0].name);
                    };

                    reader.readAsDataURL(input.files[0]);

                    jQuery('#txtImgUrl_rte').val(''); //Clear manually specified image soure

                }

            });

            jQuery('.md-drop-area').off('dragover');
            jQuery('.md-drop-area').on('dragover', function () {
		        jQuery('.md-drop-area').addClass('image-dropping');
	        });

            jQuery('.md-drop-area').off('dragleave');
	        jQuery('.md-drop-area').on('dragleave', function () {
		        jQuery('.md-drop-area').removeClass('image-dropping');
            });

            jQuery('.md-preview-area i').off('click');
            jQuery('.md-preview-area i').click(function(e){
                jQuery('#fileInsertImage').clearInputs();
                jQuery('.md-preview-area').hide();
                jQuery('.md-drop-area').show();
		        jQuery('.md-drop-area').removeClass('image-dropping');
            });

            jQuery('#txtImgUrl_rte').off('keyup');
            jQuery('#txtImgUrl_rte').on('keyup', function () {
                //Clear drop image area
                jQuery('#fileInsertImage').clearInputs();
                jQuery('.md-preview-area').hide();
                jQuery('.md-drop-area').show();
		        jQuery('.md-drop-area').removeClass('image-dropping');
	        });

            //Open Custom Image Select
            jQuery("#btnImageBrowse_rte").off('click');
            jQuery("#btnImageBrowse_rte").on('click', function (e) {

                var sFunc = ($element.data('contenteditor').settings.onImageSelectClick+'').replace( /\s/g, '');
                if(sFunc != 'function(){}'){

                    //$element.data('imageembed').settings.onImageSelectClick();
                    $element.data('contenteditor').settings.onImageSelectClick({targetInput: jQuery("#txtImgUrl_rte").get(0), theTrigger: jQuery("#btnImageBrowse_rte").get(0)});

                } else {

                    jQuery('#ifrImageBrowse').attr('src',$element.data('contenteditor').settings.imageselect);
                    jQuery('#active-input').val('txtImgUrl_rte');

                    /**** Custom Modal ****/
                    jQuery('#md-imageselect').css('width', '65%');
                    jQuery('#md-imageselect').simplemodal({
                    onFinish: function () {
                            if ( jQuery('#txtImgUrl_rte').val()!='' ) {
                                //Clear drop image area
                                jQuery('#fileInsertImage').clearInputs();
                                jQuery('.md-preview-area').hide();
                                jQuery('.md-drop-area').show();
		                        jQuery('.md-drop-area').removeClass('image-dropping');
                            }
                        }
                    });
                    jQuery('#md-imageselect').data('simplemodal').show();
                    /**** /Custom Modal ****/

                }

            });

            /* Text Formatting */
            jQuery('[data-rte-cmd="formatting"]').off('click');
            jQuery('[data-rte-cmd="formatting"]').click(function (e) {

                savedSelPublic = saveSelection();
                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-formatting').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-58;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-formatting').css('position', 'fixed');
                jQuery('#pop-formatting').css('top', top + 'px');
                jQuery('#pop-formatting').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-formatting').css('display','block');
                jQuery(this).addClass('on');

                $element.data('contenteditor').getState();

                //Get Current Letter Spacing
                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                e.preventDefault();
            });

            jQuery('[data-rte-cmd="textsettings"]').off('click');
            jQuery('[data-rte-cmd="textsettings"]').click(function (e) {

                savedSelPublic = saveSelection();
                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-textsettings').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-57-132;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-textsettings').css('position', 'fixed');
                jQuery('#pop-textsettings').css('top', top + 'px');
                jQuery('#pop-textsettings').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-textsettings').css('display','block');
                jQuery(this).addClass('on');

                //Get Current
                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var currentFontSize = parseInt(jQuery(el).css('font-size'));
                jQuery('#outFontSize').html(currentFontSize+'px');
                jQuery('#outFontSize').attr('data-initial-value',currentFontSize);

                var currentLetterSpacing = parseInt(jQuery(el).css('letter-spacing'));
                jQuery('#outLetterSpacing').html(currentLetterSpacing+'px');
                jQuery('#outLetterSpacing').attr('data-initial-value',currentLetterSpacing);

                var currentLineHeight = parseInt(jQuery(el).css('line-height'));
                jQuery('#outLineHeight').html(currentLineHeight+'px');
                jQuery('#outLineHeight').attr('data-initial-value',currentLineHeight);

                e.preventDefault();

            });


            jQuery('[data-rte-cmd="color"]').off('click');
            jQuery('[data-rte-cmd="color"]').click(function (e) {

                savedSelPublic = saveSelection();
                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-colors').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-57-215;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-colors').css('position', 'fixed');
                jQuery('#pop-colors').css('top', top + 'px');
                jQuery('#pop-colors').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-colors').css('display','block');
                jQuery(this).addClass('on');

                //Get current color
                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                jQuery('#selColorApplyTo').off('change');
                jQuery('#selColorApplyTo').on('change', function(){
                    var selColMode = jQuery('#selColorApplyTo').val(); //1 color 2 background 3 background block
                    if(selColMode==1) {
                        //Set text color
                        var s = jQuery(el).css("color");
                        jQuery('#inpTextColor').val( s );
                        jQuery('#inpTextColor').css('background-color', s );
                        jQuery('#inpTextColor').contrastingText();
                    }
                    if(selColMode==2) {
                        //Set text background
                        var s = jQuery(el).css("background-color");
                        jQuery('#inpTextColor').val( s );
                        jQuery('#inpTextColor').css('background-color', s );
                        jQuery('#inpTextColor').contrastingText();
                    }
                    if(selColMode==3) {
                        //Set text background
                        var s = jQuery(el).parents('.ui-draggable').children().first().css('background-color');
                        jQuery('#inpTextColor').val( s );
                        jQuery('#inpTextColor').css('background-color', s );
                        jQuery('#inpTextColor').contrastingText();
                    }
                });
                jQuery('#selColorApplyTo').change();

                //Email Mode (structure uses table)
                var emailmode = false;
                try{
                    if( $element.children('div.ui-draggable').first().children().first().children().first().prop("tagName").toLowerCase() == 'table' ) {
                        emailmode = true;
                    }
                }catch(e){}
                if(emailmode){ //Remove 'Block Background' option in Email Mode
                    jQuery('#selColorApplyTo').children().each(function(){
                        if( jQuery(this).attr('value')=='3' ) jQuery(this).remove();
                    });
                }

                //Prepare
                var text = getSelected();

                jQuery('.md-pick').off('click');
                jQuery('.md-pick').click(function(){

                    var s = jQuery(this).css("background-color");
                    jQuery('#inpTextColor').val( s );
                    jQuery('#inpTextColor').css('background-color', s );

                    jQuery('#inpTextColor').contrastingText();

                    restoreSelection(savedSelPublic);

                    $element.data('contenteditor').applyColor( s, text );

                    savedSelPublic = saveSelection();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();

                });

                if(!jQuery('#inpTextColor').colorPicker)$('#inpTextColor').colorPicker.destroy();
                jQuery('#inpTextColor').colorPicker({
                        dark: '#222',
                        light: '#DDD',
                        renderCallback: function($elm,toggled) {
                            if (toggled === true) {
                                // open
                                jQuery('#inpTextColor').attr('data-initial-color', jQuery('#inpTextColor').val());
                                //console.log('Open ' + jQuery('#inpTextColor').attr('data-initial-color') + ' - ' + jQuery('#inpTextColor').val())
                            } else if (toggled === false) {
                                // close
                                if(jQuery('#inpTextColor').attr('data-initial-color') != jQuery('#inpTextColor').val()){

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;

                                    //Save for Undo
                                    saveForUndo();

                                    jQuery('#inpTextColor').attr('data-initial-color', jQuery('#inpTextColor').val());
                                }
                            } else {
                                //var s = $elm.text;
                                var s = jQuery('#inpTextColor').val();

                                restoreSelection(savedSelPublic);

                                $element.data('contenteditor').applyColor( s, text );

                                savedSelPublic = saveSelection();
                            }
                        }
                    }
                );

                var bManualColorChange=false;

                jQuery('#inpTextColor').off('keyup');
                jQuery('#inpTextColor').on('keyup',function(e){
                    bManualColorChange=true;
                });

                jQuery('#inpTextColor').off('blur');
                jQuery('#inpTextColor').on('blur',function(e){

                    var s = jQuery('#inpTextColor').val();

                    restoreSelection(savedSelPublic);

                    $element.data('contenteditor').applyColor( s, text );

                    savedSelPublic = saveSelection();

                    if((jQuery('#inpTextColor').attr('data-initial-color') != jQuery('#inpTextColor').val()) || bManualColorChange){

                        //Trigger Change event
                        $element.data('contenteditor').settings.onChange();

                        $element.data('contenteditor').settings.hasChanged = true;

                        //Save for Undo
                        saveForUndo();

                        jQuery('#inpTextColor').attr('data-initial-color', jQuery('#inpTextColor').val());

                        bManualColorChange=false;
                    }

                });

                jQuery('#btnTextColorClear').off('click');
                jQuery('#btnTextColorClear').click(function(){

                    restoreSelection(savedSelPublic);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                        //el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    var selColMode = jQuery('#selColorApplyTo').val(); //1 color 2 background 3 background block

                    if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                        if(selColMode==1) {
                            //Set text color
                            document.execCommand("ForeColor",false,'');
                        }
                        if(selColMode==2) {
                            //Set text background
                            document.execCommand("BackColor",false,'');
                        }
                        //Cleanup FONTs
                        var fontElements = document.getElementsByTagName("font");
                        for (var i = 0, len = fontElements.length; i < len; ++i) {
                            var s = fontElements[i].color;
                            fontElements[i].removeAttribute("color");
                            fontElements[i].style.color = s;
                        }
                    }
                    else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                        if(selColMode==1) {
                            //Set element color
                            if(jQuery(el).html()){
                                jQuery(el).css('color', '');
                            } else {
                                jQuery(el).parent().css('color', '');
                            }
                        }
                        if(selColMode==2) {
                            //Set element background
                            if(jQuery(el).html()){
                                jQuery(el).css('background-color', '');
                            } else {
                                jQuery(el).parent().css('background-color', '');
                            }
                        }
                    }
                    else{
                        if(selColMode==1) {
                            //Set element color
                            jQuery(el).css('color', '');
                        }
                        if(selColMode==2) {
                            //Set element background
                            jQuery(el).css('background-color', '');
                        }
                    };

                    if(selColMode==3) {
                        //Set block background
                        //jQuery(el).parents('.ui-draggable').children('div').first().css('background-color', '' );
                        jQuery(el).parents('.ui-draggable').children().first().css('background-color', '' );
                    }

                    jQuery('#selColorApplyTo').change();

                });

            });

            jQuery('[data-rte-cmd="bold"]').off('click');
            jQuery('[data-rte-cmd="bold"]').click(function (e) {

                var savedSel = saveSelection();

                var text = getSelected();

                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var s;
                if(isNaN(jQuery(el).css('font-weight'))){
                    if (jQuery(el).css('font-weight') == 'bold') {
                        s='normal';
                    } else {
                        s = 'bold';
                    }
                } else {
                    if (jQuery(el).css('font-weight') <= 500) {
                        s='bold';
                    } else {
                        s = 'normal';
                    }
                }

                if (jQuery.trim(text) != '') {
                    try {
                        document.execCommand('bold', false, null);
                    } catch (e) {
                        //FF fix
                        $element.attr('contenteditable', true);
                        document.execCommand('bold', false, null);
                        $element.removeAttr('contenteditable');
                        $element.data('contenteditor').render();
                    }

                    savedSel = saveSelection();
                } else {

                    var sTagName = jQuery(el).prop("tagName").toLowerCase();
                    if(sTagName=='b'){

                        selectElementContents(el);

                        try {
                            document.execCommand('bold', false, null);
                        } catch (e) {
                            //FF fix
                            $element.attr('contenteditable', true);
                            document.execCommand('bold', false, null);
                            $element.removeAttr('contenteditable');
                            $element.data('contenteditor').render();
                        }

                    } else {
                        jQuery(el).css('font-weight', s);
                    }

                };

                if(jQuery.trim(text) == ''){
                    restoreSelection(savedSel);
                } else {
                    //for IE, prevent restore if there is text selection (seems can change the selection)
                }

                $element.data('contenteditor').getState();

                $element.data('contenteditor').settings.hasChanged = true;

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });

            jQuery('[data-rte-cmd="italic"]').off('click');
            jQuery('[data-rte-cmd="italic"]').click(function (e) {

                var savedSel = saveSelection();

                var text = getSelected();

                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var s;
                if (jQuery(el).css('font-style') == 'italic') {
                    s='normal';
                } else {
                    s = 'italic';
                }

                if (jQuery.trim(text) != '') {
                    try {
                        document.execCommand('italic', false, null);
                    } catch (e) {
                        //FF fix
                        $element.attr('contenteditable', true);
                        document.execCommand('italic', false, null);
                        $element.removeAttr('contenteditable');
                        $element.data('contenteditor').render();
                    }

                    savedSel = saveSelection();
                } else {

                    var sTagName = jQuery(el).prop("tagName").toLowerCase();
                    if(sTagName=='i' || sTagName=='em'){

                        selectElementContents(el);

                        try {
                            document.execCommand('italic', false, null);
                        } catch (e) {
                            //FF fix
                            $element.attr('contenteditable', true);
                            document.execCommand('italic', false, null);
                            $element.removeAttr('contenteditable');
                            $element.data('contenteditor').render();
                        }

                    } else {
                        jQuery(el).css('font-style', s);
                    }

                };

                if(jQuery.trim(text) == ''){
                    restoreSelection(savedSel);
                } else {
                    //for IE, prevent restore if there is text selection (seems can change the selection)
                }

                $element.data('contenteditor').getState();

                $element.data('contenteditor').settings.hasChanged = true;

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });

            jQuery('[data-rte-cmd="underline"]').off('click');
            jQuery('[data-rte-cmd="underline"]').click(function (e) {

                var savedSel = saveSelection();

                var text = getSelected();

                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var s;
                if (jQuery(el).css('text-decoration').indexOf('underline') !=-1) {
                    s='';
                } else {
                    s = 'underline';
                }

                if (jQuery.trim(text) != '') {
                    try {
                        document.execCommand('underline', false, null);
                    } catch (e) {
                        //FF fix
                        $element.attr('contenteditable', true);
                        document.execCommand('underline', false, null);
                        $element.removeAttr('contenteditable');
                        $element.data('contenteditor').render();
                    }

                    savedSel = saveSelection();
                } else {

                    var sTagName = jQuery(el).prop("tagName").toLowerCase();
                    if(sTagName=='u'){

                        selectElementContents(el);

                        try {
                            document.execCommand('underline', false, null);
                        } catch (e) {
                            //FF fix
                            $element.attr('contenteditable', true);
                            document.execCommand('underline', false, null);
                            $element.removeAttr('contenteditable');
                            $element.data('contenteditor').render();
                        }

                    } else {
                        jQuery(el).css('text-decoration', s);
                    }

                };

                if(jQuery.trim(text) == ''){
                    restoreSelection(savedSel);
                } else {
                    //for IE, prevent restore if there is text selection (seems can change the selection)
                }

                $element.data('contenteditor').getState();

                $element.data('contenteditor').settings.hasChanged = true;

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });

            jQuery('[data-rte-cmd="strikethrough"]').off('click');
            jQuery('[data-rte-cmd="strikethrough"]').click(function (e) {

                var savedSel = saveSelection();

                var text = getSelected();

                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var s;
                if (jQuery(el).css('text-decoration').indexOf('line-through') !=-1) {
                    s='';
                } else {
                    s = 'line-through';
                }

                if (jQuery.trim(text) != '') {
                    try {
                        document.execCommand('strikethrough', false, null);
                    } catch (e) {
                        //FF fix
                        $element.attr('contenteditable', true);
                        document.execCommand('strikethrough', false, null);
                        $element.removeAttr('contenteditable');
                        $element.data('contenteditor').render();
                    }

                    savedSel = saveSelection();
                } else {

                    var sTagName = jQuery(el).prop("tagName").toLowerCase();
                    if(sTagName=='strike'){

                        selectElementContents(el);

                        try {
                            document.execCommand('strikethrough', false, null);
                        } catch (e) {
                            //FF fix
                            $element.attr('contenteditable', true);
                            document.execCommand('strikethrough', false, null);
                            $element.removeAttr('contenteditable');
                            $element.data('contenteditor').render();
                        }

                    } else {
                        jQuery(el).css('text-decoration', s);
                    }

                };

                if(jQuery.trim(text) == ''){
                    restoreSelection(savedSel);
                } else {
                    //for IE, prevent restore if there is text selection (seems can change the selection)
                }

                $element.data('contenteditor').getState();

                $element.data('contenteditor').settings.hasChanged = true;

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                e.preventDefault();

                //Save for Undo
                saveForUndo();
            });

            /* Uppercase */
            jQuery('[data-rte-cmd="uppercase"]').off('click');
            jQuery('[data-rte-cmd="uppercase"]').click(function (e) {

                var savedSel = saveSelection();

                var text = getSelected();

                var el;
                var curr;
                if (window.getSelection) {
                    curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                    if(curr.nodeType==3) {  //ini text node
                        el = curr.parentNode;
                    } else {
                        el = curr;
                    }
                }
                else if (document.selection) {
                    curr = document.selection.createRange();
                    el = document.selection.createRange().parentElement();
                }

                var s;
                if (jQuery(el).css('text-transform') == 'uppercase') {
                    s='';
                } else {
                    s = 'uppercase';
                }

                /*
                if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                    document.execCommand("fontName", false, s);
                    var fontElements = document.getElementsByTagName("font");
                    for (var i = 0, len = fontElements.length; i < len; ++i) {
                        if (fontElements[i].face == s) {
                            fontElements[i].removeAttribute("face");

                            //Need to get again (for IE & FF)
                            if(fontElements[i].style.textTransform=='uppercase'){
                                s ='';
                            } else {
                                s ='uppercase';
                            }

                            fontElements[i].style.textTransform = s;
                        }
                    }
                    savedSel = saveSelection();
                }
                else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                    if(jQuery(el).html()){
                        jQuery(el).css('text-transform', s);
                    } else {
                        jQuery(el).parent().css('text-transform', s);
                    }
                }
                else{
                    jQuery(el).css('text-transform', s);
                };
                */
                jQuery(el).css('text-transform', s);

                if(jQuery.trim(text) == ''){
                    restoreSelection(savedSel);
                } else {
                    //for IE, prevent restore if there is text selection (seems can change the selection)
                }

                $element.data('contenteditor').getState();

                $element.data('contenteditor').settings.hasChanged = true;

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                e.preventDefault();

                //Save for Undo
                saveForUndo();

            });

            /* Table */
            jQuery('[data-rte-cmd="table"]').off('click');
            jQuery('[data-rte-cmd="table"]').click(function (e) {

                var savedSel = saveSelection();

                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-table').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-57-163;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-table').css('position', 'fixed');
                jQuery('#pop-table').css('top', top + 'px');
                jQuery('#pop-table').css('left', left + 'px');

                $element.data('contenteditor').closePop();
                jQuery('#pop-table').css('display','block');
                jQuery(this).addClass('on');

                e.preventDefault();

                jQuery('#tableInsert td').off('mouseover');
                jQuery('#tableInsert td').on('mouseover', function (e) {

                    var row = jQuery(this).attr('data-row');
                    var col = jQuery(this).attr('data-col');

                    var i=0;
                    jQuery('#tableInsert tr').each(function(){
                        var j=0;
                        var $tr = jQuery(this);
                        $tr.children('td').each(function(){

                            var $td = jQuery(this);
                            if (i < row && j < col) {
                                $td.addClass('highlight');
                            }
                            else {
                                $td.removeClass('highlight');
                            }
                            j++;
                        });
                        i++;
                    });

                });

                jQuery('#tableInsert').off('mouseout');
                jQuery('#tableInsert').on('mouseout', function (e) {

                    jQuery('#tableInsert tr').each(function(){
                        var $tr = jQuery(this);
                        $tr.children('td').each(function(){
                            var $td = jQuery(this);
                            $td.removeClass('highlight');
                        });
                    });

                });

                jQuery('#tableInsert td').off('click');
                jQuery('#tableInsert td').click(function (e) {

                    restoreSelection(savedSel);

                    var row = jQuery(this).attr('data-row');
                    var col = jQuery(this).attr('data-col');
                    var sHTML = '<table class="default" style="border-collapse:collapse;width:100%;">';
                    for (var i = 1; i <= row; i++) {
                        sHTML += "<tr>";
                        for (var j = 1; j <= col; j++) {
                            sHTML += '<td valign="top"><br></td>';
                        }
                        sHTML += '</tr>';
                    }
                    sHTML += '</table>';

                    pasteHtmlAtCaret(sHTML);

                    $element.data('contenteditor').closePop();

                    $element.data('contenteditor').render();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    e.preventDefault();

                    //Save for Undo
                    saveForUndo();
                });

            });

            jQuery('#btnDeleteTable').off('click');
            jQuery('#btnDeleteTable').click(function (e) {

                if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();

                jQuery('#md-deltableconfirm').css('max-width', '550px');
                jQuery('#md-deltableconfirm').simplemodal();
                jQuery('#md-deltableconfirm').data('simplemodal').show();

                jQuery('#btnDelTableOk').off('click');
                jQuery('#btnDelTableOk').on('click', function (e) {

                    jQuery('#md-deltableconfirm').data('simplemodal').hide();

                    var $table = $activeCell.parents('table').first();
                    $table.fadeOut(400, function () {

                        //Clear Controls
                        jQuery("#divRteTable").stop(true, true).fadeOut(0);

                        $table.remove();

                        $element.data('contenteditor').render();

                        $element.data('contenteditor').settings.hasChanged = true;

                        //Trigger Change event
                        $element.data('contenteditor').settings.onChange();

                        //Save for Undo
                        saveForUndo();
                    });

                });
                jQuery('#btnDelTableCancel').off('click');
                jQuery('#btnDelTableCancel').on('click', function (e) {

                    jQuery('#md-deltableconfirm').data('simplemodal').hide();

                });

            });

            jQuery('#btnEditTable').off('click');
            jQuery('#btnEditTable').click(function (e) {

                var savedSel = saveSelection();

                $element.data('contenteditor').closePop();

                jQuery("#md-edittable").css("width", '267px');
                jQuery("#md-edittable").simplemodal({noOverlay:true});
                jQuery("#md-edittable").data("simplemodal").show();

                /* Tabs */
                jQuery('#tabTableDesign').off('click');
                jQuery('#tabTableDesign').on('click', function (e) {
                    jQuery('#tabTableDesign').addClass('active');
                    jQuery('#tabTableLayout').removeClass('active');
                    jQuery('#divTableLayout').fadeOut(300, function(){
                        jQuery('#divTableDesign').fadeIn(0);
                    });
                });
                jQuery('#tabTableLayout').off('click');
                jQuery('#tabTableLayout').on('click', function (e) {
                    jQuery('#tabTableDesign').removeClass('active');
                    jQuery('#tabTableLayout').addClass('active');
                    jQuery('#divTableDesign').fadeOut(0, function(){
                        jQuery('#divTableLayout').fadeIn(300);
                    });
                });


                if(!jQuery('#inpCellBgColor').colorPicker)$('#inpCellBgColor').colorPicker.destroy();
                jQuery('#inpCellBgColor').colorPicker({
                        dark: '#222',
                        light: '#DDD',
                        renderCallback: function($elm,toggled) {
                            if (toggled === true) {
                                // open
                                jQuery('#inpCellBgColor').attr('data-initial-color', jQuery('#inpCellBgColor').val());
                            } else if (toggled === false) {
                                // close
                                if(jQuery('#inpCellBgColor').attr('data-initial-color') != jQuery('#inpCellBgColor').val()){

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;

                                    //Save for Undo
                                    saveForUndo();

                                    jQuery('#inpCellBgColor').attr('data-initial-color', jQuery('#inpCellBgColor').val());
                                }
                            } else {
                                if(!$activeCell) return;

                                //var val = $elm.text;
                                var val = jQuery('#inpCellBgColor').val();

                                restoreSelection(savedSel);

                                //Apply format
                                var applyto = jQuery('#selTableApplyTo').val();
                                var oTable = $activeCell.parents('table').first()[0];
                                var oRow = $activeCell.parents('tr').first()[0];
                                var oCell = $activeCell[0];

                                if (applyto == 'currentcell') {
                                    $activeCell.css('background-color', val);
                                }
                                for (var i = 0; i < oTable.rows.length; i++) {
                                    var oTR = oTable.rows[i];
                                    for (var j = 0; j < oTR.cells.length; j++) {
                                        var oTD = oTR.cells[j];

                                        if (applyto == 'table') {
                                            jQuery(oTD).css('background-color', val);
                                        }
                                        if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                            jQuery(oTD).css('background-color', val);
                                        }
                                        if (applyto == 'oddrows' && !isEven(i + 1)) {
                                            jQuery(oTD).css('background-color', val);
                                        }
                                        if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                            jQuery(oTD).css('background-color', val);
                                        }
                                        if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                            jQuery(oTD).css('background-color', val);
                                        }
                                    }
                                }

                            }
                        }
                    }
                );


                var bManualColorChange=false;

                jQuery('#inpCellBgColor').off('keyup');
                jQuery('#inpCellBgColor').on('keyup',function(e){
                    bManualColorChange=true;
                });

                jQuery('#inpCellBgColor').off('blur');
                jQuery('#inpCellBgColor').on('blur', function(){
                    if(!$activeCell) return;

                    restoreSelection(savedSel);

                    var val = jQuery('#inpCellBgColor').val();

                    //Apply format
                    var applyto = jQuery('#selTableApplyTo').val();
                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    if (applyto == 'currentcell') {
                        $activeCell.css('background-color', val);
                    }
                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oTR = oTable.rows[i];
                        for (var j = 0; j < oTR.cells.length; j++) {
                            var oTD = oTR.cells[j];

                            if (applyto == 'table') {
                                jQuery(oTD).css('background-color', val);
                            }
                            if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                jQuery(oTD).css('background-color', val);
                            }
                            if (applyto == 'oddrows' && !isEven(i + 1)) {
                                jQuery(oTD).css('background-color', val);
                            }
                            if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                jQuery(oTD).css('background-color', val);
                            }
                            if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                jQuery(oTD).css('background-color', val);
                            }
                        }
                    }

                    if((jQuery('#inpCellBgColor').attr('data-initial-color') != jQuery('#inpCellBgColor').val()) || bManualColorChange){

                        //Trigger Change event
                        $element.data('contenteditor').settings.onChange();

                        $element.data('contenteditor').settings.hasChanged = true;

                        //Save for Undo
                        saveForUndo();

                        jQuery('#inpCellBgColor').attr('data-initial-color', jQuery('#inpCellBgColor').val());

                        bManualColorChange=false;
                    }

                });


                if(!jQuery('#inpCellTextColor').colorPicker)$('#inpCellTextColor').colorPicker.destroy();
                jQuery('#inpCellTextColor').colorPicker({
                        dark: '#222',
                        light: '#DDD',
                        renderCallback: function($elm,toggled) {
                            if (toggled === true) {
                                // open
                                jQuery('#inpCellTextColor').attr('data-initial-color', jQuery('#inpCellTextColor').val());
                            } else if (toggled === false) {
                                // close
                                if(jQuery('#inpCellTextColor').attr('data-initial-color') != jQuery('#inpCellTextColor').val()){

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;

                                    //Save for Undo
                                    saveForUndo();

                                    jQuery('#inpCellTextColor').attr('data-initial-color', jQuery('#inpCellTextColor').val());
                                }
                            } else {
                                if(!$activeCell) return;

                                //var val = $elm.text;
                                var val = jQuery('#inpCellTextColor').val();

                                restoreSelection(savedSel);

                                //Apply format
                                var applyto = jQuery('#selTableApplyTo').val();
                                var oTable = $activeCell.parents('table').first()[0];
                                var oRow = $activeCell.parents('tr').first()[0];
                                var oCell = $activeCell[0];

                                if (applyto == 'currentcell') {
                                    $activeCell.css('color', val);
                                }
                                for (var i = 0; i < oTable.rows.length; i++) {
                                    var oTR = oTable.rows[i];
                                    for (var j = 0; j < oTR.cells.length; j++) {
                                        var oTD = oTR.cells[j];

                                        if (applyto == 'table') {
                                            jQuery(oTD).css('color', val);
                                        }
                                        if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                            jQuery(oTD).css('color', val);
                                        }
                                        if (applyto == 'oddrows' && !isEven(i + 1)) {
                                            jQuery(oTD).css('color', val);
                                        }
                                        if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                            jQuery(oTD).css('color', val);
                                        }
                                        if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                            jQuery(oTD).css('color', val);
                                        }
                                    }
                                }
                            }
                        }
                    }
                );


                jQuery('#inpCellTextColor').off('keyup');
                jQuery('#inpCellTextColor').on('keyup',function(e){
                    bManualColorChange=true;
                });

                jQuery('#inpCellTextColor').off('blur');
                jQuery('#inpCellTextColor').on('blur', function(){
                    if(!$activeCell) return;

                    restoreSelection(savedSel);

                    var val = jQuery('#inpCellTextColor').val();

                    //Apply format
                    var applyto = jQuery('#selTableApplyTo').val();
                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    if (applyto == 'currentcell') {
                        $activeCell.css('color', val);
                    }
                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oTR = oTable.rows[i];
                        for (var j = 0; j < oTR.cells.length; j++) {
                            var oTD = oTR.cells[j];

                            if (applyto == 'table') {
                                jQuery(oTD).css('color', val);
                            }
                            if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                jQuery(oTD).css('color', val);
                            }
                            if (applyto == 'oddrows' && !isEven(i + 1)) {
                                jQuery(oTD).css('color', val);
                            }
                            if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                jQuery(oTD).css('color', val);
                            }
                            if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                jQuery(oTD).css('color', val);
                            }
                        }
                    }

                    if((jQuery('#inpCellTextColor').attr('data-initial-color') != jQuery('#inpCellTextColor').val()) || bManualColorChange){

                        //Trigger Change event
                        $element.data('contenteditor').settings.onChange();

                        $element.data('contenteditor').settings.hasChanged = true;

                        //Save for Undo
                        saveForUndo();

                        jQuery('#inpCellTextColor').attr('data-initial-color', jQuery('#inpCellTextColor').val());

                        bManualColorChange=false;
                    }

                });


                if(!jQuery('#inpCellBorderColor').colorPicker)$('#inpCellBorderColor').colorPicker.destroy();
                jQuery('#inpCellBorderColor').colorPicker({
                        dark: '#222',
                        light: '#DDD',
                        renderCallback: function($elm,toggled) {
                            if (toggled === true) {
                                // open
                                jQuery('#inpCellBorderColor').attr('data-initial-color', jQuery('#inpCellBorderColor').val());
                            } else if (toggled === false) {
                                // close
                                if(jQuery('#inpCellBorderColor').attr('data-initial-color') != jQuery('#inpCellBorderColor').val()){

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;

                                    //Save for Undo
                                    saveForUndo();

                                    jQuery('#inpCellBorderColor').attr('data-initial-color', jQuery('#inpCellBorderColor').val());
                                }
                            } else {
                                if(!$activeCell) return;

                                //var val = $elm.text;
                                var val = jQuery('#inpCellBorderColor').val();

                                var borderwidth = jQuery('#selCellBorderWidth').val();
                                if(borderwidth=='0'){
                                    jQuery('#selCellBorderWidth').val(1);
                                    borderwidth = 1;
                                }

                                restoreSelection(savedSel);

                                //Apply format
                                var applyto = jQuery('#selTableApplyTo').val();
                                var oTable = $activeCell.parents('table').first()[0];
                                var oRow = $activeCell.parents('tr').first()[0];
                                var oCell = $activeCell[0];

                                if (applyto == 'currentcell') {
                                    $activeCell.css('border-color', val);
                                    $activeCell.css('border-width', borderwidth+'px');
                                    $activeCell.css('border-style', 'solid');
                                }
                                for (var i = 0; i < oTable.rows.length; i++) {
                                    var oTR = oTable.rows[i];
                                    for (var j = 0; j < oTR.cells.length; j++) {
                                        var oTD = oTR.cells[j];

                                        if (applyto == 'table') {
                                            jQuery(oTD).css('border-color', val);
                                            jQuery(oTD).css('border-width', borderwidth+'px');
                                            jQuery(oTD).css('border-style', 'solid');
                                        }
                                        if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                            jQuery(oTD).css('border-color', val);
                                            jQuery(oTD).css('border-width', borderwidth+'px');
                                            jQuery(oTD).css('border-style', 'solid');
                                        }
                                        if (applyto == 'oddrows' && !isEven(i + 1)) {
                                            jQuery(oTD).css('border-color', val);
                                            jQuery(oTD).css('border-width', borderwidth+'px');
                                            jQuery(oTD).css('border-style', 'solid');
                                        }
                                        if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                            jQuery(oTD).css('border-color', val);
                                            jQuery(oTD).css('border-width', borderwidth+'px');
                                            jQuery(oTD).css('border-style', 'solid');
                                        }
                                        if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                            jQuery(oTD).css('border-color', val);
                                            jQuery(oTD).css('border-width', borderwidth+'px');
                                            jQuery(oTD).css('border-style', 'solid');
                                        }
                                    }
                                }
                            }
                        }
                    }
                );

                jQuery('#inpCellBorderColor').off('keyup');
                jQuery('#inpCellBorderColor').on('keyup',function(e){
                    bManualColorChange=true;
                });

                jQuery('#inpCellBorderColor').off('blur');
                jQuery('#inpCellBorderColor').on('blur', function(){
                    if(!$activeCell) return;

                    restoreSelection(savedSel);

                    var val = jQuery('#inpCellBorderColor').val();
                    var borderwidth = jQuery('#selCellBorderWidth').val();

                    //Apply format
                    var applyto = jQuery('#selTableApplyTo').val();
                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    if (applyto == 'currentcell') {
                        $activeCell.css('border-color', val);
                        $activeCell.css('border-width', borderwidth+'px');
                        $activeCell.css('border-style', 'solid');
                        if(val==''){
                            $activeCell.css('border-color', '');
                            $activeCell.css('border-width', '');
                            $activeCell.css('border-style', '');
                            jQuery('#selCellBorderWidth').val(0);
                        }
                    }
                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oTR = oTable.rows[i];
                        for (var j = 0; j < oTR.cells.length; j++) {
                            var oTD = oTR.cells[j];

                            if (applyto == 'table') {
                                jQuery(oTD).css('border-color', val);
                                jQuery(oTD).css('border-width', borderwidth+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                if(val==''){
                                    jQuery(oTD).css('border-color', '');
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery('#selCellBorderWidth').val(0);
                                }
                            }
                            if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                jQuery(oTD).css('border-color', val);
                                jQuery(oTD).css('border-width', borderwidth+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                if(val==''){
                                    jQuery(oTD).css('border-color', '');
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery('#selCellBorderWidth').val(0);
                                }
                            }
                            if (applyto == 'oddrows' && !isEven(i + 1)) {
                                jQuery(oTD).css('border-color', val);
                                jQuery(oTD).css('border-width', borderwidth+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                if(val==''){
                                    jQuery(oTD).css('border-color', '');
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery('#selCellBorderWidth').val(0);
                                }
                            }
                            if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                jQuery(oTD).css('border-color', val);
                                jQuery(oTD).css('border-width', borderwidth+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                if(val==''){
                                    jQuery(oTD).css('border-color', '');
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery('#selCellBorderWidth').val(0);
                                }
                            }
                            if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                jQuery(oTD).css('border-color', val);
                                jQuery(oTD).css('border-width', borderwidth+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                if(val==''){
                                    jQuery(oTD).css('border-color', '');
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery('#selCellBorderWidth').val(0);
                                }
                            }
                        }
                    }

                    if((jQuery('#inpCellBorderColor').attr('data-initial-color') != jQuery('#inpCellBorderColor').val()) || bManualColorChange){

                        //Trigger Change event
                        $element.data('contenteditor').settings.onChange();

                        $element.data('contenteditor').settings.hasChanged = true;

                        //Save for Undo
                        saveForUndo();

                        jQuery('#inpCellBorderColor').attr('data-initial-color', jQuery('#inpCellBorderColor').val());

                        bManualColorChange=false;
                    }

                });

                jQuery('#selCellBorderWidth').off('change');
                jQuery('#selCellBorderWidth').on('change', function(){
                    if(!$activeCell) return;

                    var val = jQuery('#selCellBorderWidth').val();
                    var bordercolor = jQuery('#inpCellBorderColor').val();

                    if(bordercolor==''){
                        jQuery('#inpCellBorderColor').val('rgb(0, 0, 0)');
                        jQuery('#inpCellBorderColor').css('background-color', 'rgb(0, 0, 0)' );
                        jQuery('#inpCellBorderColor').css('color', '#ddd' );
                        bordercolor = 'rgb(0, 0, 0)';
                    }

                    restoreSelection(savedSel);

                    //Apply format
                    var applyto = jQuery('#selTableApplyTo').val();
                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    if (applyto == 'currentcell') {
                        $activeCell.css('border-width', val+'px');
                        $activeCell.css('border-style', 'solid');
                        $activeCell.css('border-color', bordercolor);
                        if(val=='0'){
                            $activeCell.css('border-width', '');
                            $activeCell.css('border-style', '');
                            $activeCell.css('border-color', '');

                            jQuery('#inpCellBorderColor').val('');
                            jQuery('#inpCellBorderColor').css('background-color', '' );
                        }
                    }
                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oTR = oTable.rows[i];
                        for (var j = 0; j < oTR.cells.length; j++) {
                            var oTD = oTR.cells[j];

                            if (applyto == 'table') {
                                jQuery(oTD).css('border-width', val+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                jQuery(oTD).css('border-color', bordercolor);
                                if(val=='0'){
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery(oTD).css('border-color', '');

                                    jQuery('#inpCellBorderColor').val('');
                                    jQuery('#inpCellBorderColor').css('background-color', '' );
                                }
                            }
                            if (applyto == 'evenrows' && isEven(i + 1)) {//even=genap
                                jQuery(oTD).css('border-width', val+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                jQuery(oTD).css('border-color', bordercolor);
                                if(val=='0'){
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery(oTD).css('border-color', '');

                                    jQuery('#inpCellBorderColor').val('');
                                    jQuery('#inpCellBorderColor').css('background-color', '' );
                                }
                            }
                            if (applyto == 'oddrows' && !isEven(i + 1)) {
                                jQuery(oTD).css('border-width', val+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                jQuery(oTD).css('border-color', bordercolor);
                                if(val=='0'){
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery(oTD).css('border-color', '');

                                    jQuery('#inpCellBorderColor').val('');
                                    jQuery('#inpCellBorderColor').css('background-color', '' );
                                }
                            }
                            if (applyto == 'currentrow' && oTR == $activeCell.parents('tr').first()[0] ) {
                                jQuery(oTD).css('border-width', val+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                jQuery(oTD).css('border-color', bordercolor);
                                if(val=='0'){
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery(oTD).css('border-color', '');

                                    jQuery('#inpCellBorderColor').val('');
                                    jQuery('#inpCellBorderColor').css('background-color', '' );
                                }
                            }
                            if (applyto == 'currentcol' && j == getCellIndex(oTable, oRow, oCell)) {
                                jQuery(oTD).css('border-width', val+'px');
                                jQuery(oTD).css('border-style', 'solid');
                                jQuery(oTD).css('border-color', bordercolor);
                                if(val=='0'){
                                    jQuery(oTD).css('border-width', '');
                                    jQuery(oTD).css('border-style', '');
                                    jQuery(oTD).css('border-color', '');

                                    jQuery('#inpCellBorderColor').val('');
                                    jQuery('#inpCellBorderColor').css('background-color', '' );
                                }
                            }
                        }
                    }
                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                //Table Layout
                jQuery('[data-rte-cmd="rowabove"]').off('click');
                jQuery('[data-rte-cmd="rowabove"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];

                    var oNewRow = oTable.insertRow(oRow.rowIndex);

                    for (var i = 0; i < oRow.cells.length; i++) {
                        var oNewCell = oNewRow.insertCell(oNewRow.cells.length);
                        jQuery(oNewCell).attr('style', $activeCell.attr('style'));
                        jQuery(oNewCell).attr('valign', 'top');
                        jQuery(oNewCell).html('<br>');
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="rowbelow"]').off('click');
                jQuery('[data-rte-cmd="rowbelow"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];

                    var oNewRow = oTable.insertRow(oRow.rowIndex + 1);

                    for (var i = 0; i < oRow.cells.length; i++) {
                        var oNewCell = oNewRow.insertCell(oNewRow.cells.length);
                        jQuery(oNewCell).attr('style', $activeCell.attr('style'));
                        jQuery(oNewCell).attr('valign', 'top');
                        jQuery(oNewCell).html('<br>');
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="columnleft"]').off('click');
                jQuery('[data-rte-cmd="columnleft"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    var nCellIndex = oCell.cellIndex;

                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oRowTmp = oTable.rows[i];
                        var oNewCell = oRowTmp.insertCell(nCellIndex);
                        jQuery(oNewCell).attr('style', $activeCell.attr('style'));
                        jQuery(oNewCell).attr('valign', 'top');
                        jQuery(oNewCell).html('<br>');
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="columnright"]').off('click');
                jQuery('[data-rte-cmd="columnright"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    var nCellIndex = oCell.cellIndex;

                    for (var i = 0; i < oTable.rows.length; i++) {
                        var oRowTmp = oTable.rows[i];
                        var oNewCell = oRowTmp.insertCell(nCellIndex + 1);
                        jQuery(oNewCell).attr('style', $activeCell.attr('style'));
                        jQuery(oNewCell).attr('valign', 'top');
                        jQuery(oNewCell).html('<br>');
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="delrow"]').off('click');
                jQuery('[data-rte-cmd="delrow"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];

                    oTable.deleteRow(oRow.rowIndex);

                    $activeCell = null;

                    if (oTable.rows.length == 0) {
                        oTable.parentNode.removeChild(oTable);

                        jQuery("#divRteTable").stop(true, true).fadeOut(0);
                        if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="delcolumn"]').off('click');
                jQuery('[data-rte-cmd="delcolumn"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    var nCellIndex = oCell.cellIndex;
                    for (var i = 0; i < oTable.rows.length; i++) oTable.rows[i].deleteCell(nCellIndex);

                    $activeCell = null;

                    if (oTable.rows[0].cells.length == 0) {
                        oTable.parentNode.removeChild(oTable);

                        jQuery("#divRteTable").stop(true, true).fadeOut(0);
                        if(jQuery("#md-edittable").data("simplemodal")) jQuery("#md-edittable").data("simplemodal").hide();
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

                jQuery('[data-rte-cmd="mergecell"]').off('click');
                jQuery('[data-rte-cmd="mergecell"]').click(function (e) {
                    if(!$activeCell) return;

                    var oTable = $activeCell.parents('table').first()[0];
                    var oRow = $activeCell.parents('tr').first()[0];
                    var oCell = $activeCell[0];

                    oCell.colSpan = oCell.colSpan + 1; /*TODO: Merge 2 cell which has already colspan.*/

                    if (oCell.cellIndex + 1 < oTable.rows[oRow.rowIndex].cells.length) {
                        oTable.rows[oRow.rowIndex].deleteCell(oCell.cellIndex + 1);
                    }

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    //Save for Undo
                    saveForUndo();
                });

            });


            /* Align */
            jQuery('[data-rte-cmd="align"]').off('click');
            jQuery('[data-rte-cmd="align"]').click(function (e) {

                var savedSel = saveSelection();

                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-align').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-58;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-align').css('position', 'fixed');
                jQuery('#pop-align').css('top', top + 'px');
                jQuery('#pop-align').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-align').css('display','block');
                jQuery(this).addClass('on');

                e.preventDefault();

                jQuery('.md-pickalign').off('click');
                jQuery('.md-pickalign').click(function(){

                    /*
                    restoreSelection(savedSel);

                    var el;
                    if (window.getSelection) {
                        el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                        if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                            el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                            el.nodeName != 'P') {
                            el = el.parentNode;
                        }
                    }
                    else if (document.selection) {
                        el = document.selection.createRange().parentElement();
                        if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                            el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                            el.nodeName != 'P') {
                            el = el.parentElement();
                        }
                    }

                    var s = jQuery(this).data('align');
                    el.style.textAlign = s;

                    jQuery(this).blur();

                    $element.data('contenteditor').settings.hasChanged = true;
                    e.preventDefault();
                    */


                    restoreSelection(savedSel);

                    var el;
                    var curr;
                    if (window.getSelection) {
                        curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                        if(curr.nodeType==3) {  //ini text node
                            el = curr.parentNode;
                        } else {
                            el = curr;
                        }
                    }
                    else if (document.selection) {
                        curr = document.selection.createRange();
                        el = document.selection.createRange().parentElement();
                    }

                    var s = jQuery(this).data('align');

                    var sTagName = jQuery(el).prop("tagName").toLowerCase();
                    if(sTagName=='h1' || sTagName=='h2' || sTagName=='h3' || sTagName=='h4' || sTagName=='h5' || sTagName=='h6' || sTagName=='p' || sTagName=='div') {
                        jQuery(el).css('text-align', s);
                    } else {
                        jQuery(el).parents('h1,h2,h3,h4,h5,h6,p,div').first().css('text-align', s);
                    }

                    jQuery(this).blur();

                    $element.data('contenteditor').getState();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    e.preventDefault();

                    /*
                    restoreSelection(savedSel);

                    var s = jQuery(this).data('align');

                    if(s=='left') document.execCommand('JustifyLeft', false, null);
                    if(s=='right') document.execCommand('JustifyRight', false, null);
                    if(s=='center') document.execCommand('JustifyCenter', false, null);
                    if(s=='justify') document.execCommand('JustifyFull', false, null);

                    $element.data('contenteditor').getState();

                    $element.data('contenteditor').settings.hasChanged = true;
                    e.preventDefault();
                    */

                    //Save for Undo
                    saveForUndo();
                });
                /**** /Custom Modal ****/
            });

            jQuery('[data-rte-cmd="list"]').off('click');
            jQuery('[data-rte-cmd="list"]').click(function (e) {

                var savedSel = saveSelection();

                var top = jQuery(this).offset().top - jQuery(window).scrollTop();
                var left = jQuery(this).offset().left;
                if( jQuery('#rte-toolbar').hasClass('rte-side') ) {
                    jQuery('#pop-list').addClass('rte-side');
                    if( jQuery('#rte-toolbar').hasClass('right') ) {
                        left=left-58;
                    } else {
                        left=left+57;
                    }
                } else {
                    top=top+51;
                }
                jQuery('#pop-list').css('position', 'fixed');
                jQuery('#pop-list').css('top', top + 'px');
                jQuery('#pop-list').css('left', left + 'px');
                //jQuery('.rte-pop').css('display','none');
                $element.data('contenteditor').closePop();
                jQuery('#pop-list').css('display','block');
                jQuery(this).addClass('on');

                e.preventDefault();

                jQuery('.md-picklist').off('click');
                jQuery('.md-picklist').click(function(){

                    restoreSelection(savedSel);

                    var s = jQuery(this).data('list');

                    try {
                        if(s=='normal') {
                            document.execCommand('outdent', false, null);
                            document.execCommand('outdent', false, null);
                            document.execCommand('outdent', false, null);
                        } else {
                            document.execCommand(s, false, null);

                            /* Applying <blockquote> inside <p> resulting: <p></p><blockquote>Lorem Ipsum..</blockquote><p></p>
                            Debug here:
                            var el;
                            if (window.getSelection) {
                                el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                                el = el.parentNode;
                            }
                            else if (document.selection) {
                                el = document.selection.createRange().parentElement();
                                el = el.parentElement();
                            }
                            alert(el.nodeName); //returns P. Blockquote cannot be inside P, that's why the output is the above result. */

                            /* In safe mode, when applying ul/ol on paragraph (p), the p should be removed */
                            var el;
                            if (window.getSelection) {
                                el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                                el = el.parentNode;
                            }
                            else if (document.selection) {
                                el = document.selection.createRange().parentElement();
                                el = el.parentElement();
                            }
                            if(el.nodeName=='UL' || el.nodeName=='OL') {
                                if( jQuery(el).parent().prop("tagName").toLowerCase() == "p" ) {
                                    el.setAttribute('contenteditable', true); //make ul/ol editable
                                    jQuery(el).parent().replaceWith(function() { return this.innerHTML; }); //remove unwanted paragraph
                                }
                            }

                        }
                    } catch (e) {
                        //FF fix
                        $activeElement.parents('div').addClass('edit');
                        var el;
                        if (window.getSelection) {
                            el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                            el = el.parentNode;
                        }
                        else if (document.selection) {
                            el = document.selection.createRange().parentElement();
                            el = el.parentElement();
                        }
                        //alert(el.nodeName)
                        el.setAttribute('contenteditable', true);
                        if(s=='normal') {
                            document.execCommand('outdent', false, null);
                            document.execCommand('outdent', false, null);
                            document.execCommand('outdent', false, null);
                        } else {
                            document.execCommand(s, false, null);
                        }
                        el.removeAttribute('contenteditable');
                        $element.data('contenteditor').render();
                    }

                    $element.data('contenteditor').getState();

                    $element.data('contenteditor').settings.hasChanged = true;

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    e.preventDefault();

                    //Save for Undo
                    saveForUndo();
                });
                /**** /Custom Modal ****/
            });

            jQuery('[data-rte-cmd="createLink"]').off('click');
            jQuery('[data-rte-cmd="createLink"]').click(function (e) {

                // source: 	http://stackoverflow.com/questions/6251937/how-to-get-selecteduser-highlighted-text-in-contenteditable-element-and-replac
                //   		http://stackoverflow.com/questions/4652734/return-html-from-a-user-selection/4652824#4652824
                var html = "";
                if (typeof window.getSelection != "undefined") {
                    var sel = window.getSelection();
                    if (sel.rangeCount) {
                        var container = document.createElement("div");
                        for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                            container.appendChild(sel.getRangeAt(i).cloneContents());
                        }
                        html = container.innerHTML;
                    }
                } else if (typeof document.selection != "undefined") {
                    if (document.selection.type == "Text") {
                        html = document.selection.createRange().htmlText;
                    }
                }

                if (html == '') {

                    // Select word if no text selection
                    // source: https://stackoverflow.com/questions/7563169/detect-which-word-has-been-clicked-on-within-a-text

                    var s = window.getSelection();
                    var range = s.getRangeAt(0);
                    var node = s.anchorNode;

                    while (range.startOffset !== 0) {
                        range.setStart(node, range.startOffset - 1);
                        if (range.toString().search(/\s/) === 0) {
                            range.setStart(node, range.startOffset + 1);
                            break;
                        }
                    }
                    while (range.endOffset < node.length) {
                        range.setEnd(node, range.endOffset + 1);
                        if (range.toString().search(/\s/) !== -1) {
                            range.setEnd(node, range.endOffset - 1);
                            break;
                        }
                    }

                    //var str = range.toString().trim();
                    //alert(str);

                    selectRange(range); //needed for IE
                }

                var el;
                if (window.getSelection) {//https://www.jabcreations.com/blog/javascript-parentnode-of-selected-text
                    el = window.getSelection().getRangeAt(0).commonAncestorContainer;
                }
                else if (document.selection) {
                    el = document.selection.createRange();
                }
                if(el.nodeName.toLowerCase()=='a'){
                    $activeLink = jQuery(el);
                } else {
                    document.execCommand('createLink', false, 'http://dummy');
                    $activeLink = jQuery("a[href='http://dummy']").first();
                    $activeLink.attr('href', 'http://');
                }

                /**** Custom Modal ****/
                jQuery('#md-createlink').css('max-width', '550px');
                jQuery('#md-createlink').simplemodal({
                    noOverlay:true,
                    onCancel: function () {
                        if ($activeLink.attr('href') == 'http://')
                            $activeLink.replaceWith($activeLink.html());
                    }
                });
                jQuery('#md-createlink').data('simplemodal').show();
                $element.data('contenteditor').closePop();

                jQuery('#txtLink').val($activeLink.attr('href'));
                jQuery('#txtLinkText').val($activeLink.html());
                jQuery('#txtLinkTitle').val($activeLink.attr('title'));
                if($activeLink.attr('target')=='_blank'){
                    jQuery('#chkNewWindow').prop('checked', true);
                } else {
                    jQuery('#chkNewWindow').prop('checked', false);
                }

                jQuery('#btnLinkOk').off('click');
                jQuery('#btnLinkOk').on('click', function (e) {
                    $activeLink.attr('href', jQuery('#txtLink').val());

                    if (jQuery('#txtLink').val() == 'http://' || jQuery('#txtLink').val() == '') {
                        $activeLink.replaceWith($activeLink.html());
                    }
                    $activeLink.html(jQuery('#txtLinkText').val());
                    $activeLink.attr('title',jQuery('#txtLinkTitle').val());
                    if(jQuery('#chkNewWindow').is(":checked")){
                        $activeLink.attr('target','_blank');
                    } else {
                        $activeLink.removeAttr('target');
                    }

                    jQuery('#md-createlink').data('simplemodal').hide();

                    //Trigger Change event
                    $element.data('contenteditor').settings.onChange();

                    $element.data('contenteditor').settings.hasChanged = true;
                    $element.data('contenteditor').render();
                    /*
                    for (var i = 0; i < instances.length; i++) {
                        jQuery(instances[i]).data('contenteditor').settings.hasChanged = true;
                        jQuery(instances[i]).data('contenteditor').render();
                    }*/

                    //Save for Undo
                    saveForUndo();

                });
                /**** /Custom Modal ****/

                e.preventDefault(); //spy wkt rte's link btn di-click, browser scroll tetap.

            });

            jQuery('[data-rte-cmd="icon"]').off('click');
            jQuery('[data-rte-cmd="icon"]').click(function (e) {


                $savedSel = saveSelection();

                $activeIcon = null;

                var iconselect = $element.data('contenteditor').settings.iconselect;
                if( jQuery('#ifrIconSelect').attr('src').indexOf('blank.html') != -1) {
                    jQuery('#ifrIconSelect').attr('src', iconselect);
                }
                jQuery('#md-icon-select').css('max-width', '775px');
                jQuery('#md-icon-select').simplemodal({noOverlay:true});
                jQuery('#md-icon-select').data('simplemodal').show($savedSel);
                $element.data('contenteditor').closePop();

                e.preventDefault();
                return;

            });

            jQuery('[data-rte-cmd="tags"]').off('click');
            jQuery('[data-rte-cmd="tags"]').click(function (e) {

                //var savedSel = saveSelection();

                jQuery('#md-tags-select').css('max-width', '255px');
                jQuery('#md-tags-select').simplemodal({noOverlay:true});
                jQuery('#md-tags-select').data('simplemodal').show(savedSel);
                $element.data('contenteditor').closePop();

                var s = '';
                for (var j = 0; j < $element.data('contenteditor').settings.customTags.length; j++) {
                    s+='<button class="md-pick-tag" style="width:100%" data-value="' + $element.data('contenteditor').settings.customTags[j][1] + '"> ' + $element.data('contenteditor').settings.customTags[j][0] + ' </button>';
                }
                jQuery('#divCustomTags').html(s);

                jQuery('.md-pick-tag').off('click');
                jQuery('.md-pick-tag').click(function(){

                    //restoreSelection(savedSel);

                    var val = jQuery(this).data("value");
                    pasteHtmlAtCaret(val,true);
                    jQuery('#md-tags-select').data('simplemodal').hide();

                    //Save for Undo
                    saveForUndo();
                });

                e.preventDefault();
                return;

            });

            $element.off('mouseenter mouseleave', '.embed-responsive');
            $element.on('mouseenter mouseleave', '.embed-responsive', function(e) {
                switch(e.type) {
                    case 'mouseenter':

                        if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code

                        if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly

                        if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                        var _top; var _left;
                        var scrolltop = jQuery(window).scrollTop();
                        var offsettop = jQuery(this).offset().top;
                        var offsetleft = jQuery(this).offset().left;
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        var is_ie = detectIE();
                        var browserok = true;
                        if (is_firefox||is_ie) browserok = false;
                        if(browserok){
                            //Chrome 37, Opera 24
                            _top = (offsettop - 20) + (scrolltop - scrolltop);
                            _left = offsetleft;
                        } else {
                            if(is_ie){
                                //IE 11 (Adjustment required)

                                //Custom formula for adjustment in IE11
                                var space = $element.getPos().top;
                                var adjy_val = (-space/1.1) + space/1.1;
                                var space2 = $element.getPos().left;
                                var adjx_val = -space2 + space2;

                                var p = jQuery(this).getPos();
                                _top = (p.top - 20) + adjy_val;
                                _left = p.left + adjx_val;
                            }
                            if(is_firefox) {
                                //Firefox (No Adjustment required)
                                _top = offsettop - 20;
                                _left = offsetleft;
                            }
                        }
                        jQuery("#divFrameLink").css("top", _top + "px");
                        jQuery("#divFrameLink").css("left", _left + "px");


                        jQuery("#divFrameLink").stop(true, true).css({ display: 'none' }).fadeIn(20);

                        $activeFrame = jQuery(this).find('iframe');

                        jQuery("#divFrameLink").off('click');
                        jQuery("#divFrameLink").on('click', function (e) {
                            var currentSrcUrl = $activeFrame.attr('src');
                            var embeddedYoutubeRegex = /^.*\/\/www.youtube.com\/embed\//;
                            var embeddedVimeoRegex = /^.*\/\/player.vimeo.com\/video\//;

                            if (embeddedYoutubeRegex.exec(currentSrcUrl) != null || embeddedVimeoRegex.exec(currentSrcUrl) != null) {

                                if(jQuery('#md-createiframe').data('simplemodal')) jQuery('#md-createiframe').data('simplemodal').hide();

                                /**** Custom Modal for just SRC ****/
                                jQuery('#md-createsrc').css('max-width', '550px');
                                jQuery('#md-createsrc').simplemodal({noOverlay:true});
                                jQuery('#md-createsrc').data('simplemodal').show();
                                $element.data('contenteditor').closePop();

                                jQuery('#txtSrc').val($activeFrame.attr('src'));

                                jQuery('#btnSrcOk').off('click');
                                jQuery('#btnSrcOk').on('click', function (e) {
                                    var srcUrl = jQuery('#txtSrc').val();

                                    var youRegex = /^http[s]?:\/\/(((www.youtube.com\/watch\?(feature=player_detailpage&)?)v=)|(youtu.be\/))([^#\&\?]*)/;
                                    var vimeoRegex = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/)|(video\/))?([0-9]+)\/?/;
                                    var youRegexMatches = youRegex.exec(srcUrl);
                                    var vimeoRegexMatches = vimeoRegex.exec(srcUrl);
                                    if (youRegexMatches != null || vimeoRegexMatches != null) {
                                        if (youRegexMatches != null && youRegexMatches.length >= 7) {
                                            var youMatch = youRegexMatches[6];
                                            srcUrl = '//www.youtube.com/embed/' + youMatch + '?rel=0';
                                        }
                                        if (vimeoRegexMatches != null && vimeoRegexMatches.length >= 7) {
                                            var vimeoMatch = vimeoRegexMatches[6];
                                            srcUrl = '//player.vimeo.com/video/' + vimeoMatch;
                                        }
                                    }
                                    $activeFrame.attr('src', srcUrl);

                                    if (jQuery('#txtSrc').val() == '') {
                                        $activeFrame.attr('src', '');
                                    }

                                    jQuery('#md-createsrc').data('simplemodal').hide();

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;
                                    $element.data('contenteditor').render();
                                    /*
                                    for (var i = 0; i < instances.length; i++) {
                                        jQuery(instances[i]).data('contenteditor').settings.hasChanged = true;
                                        jQuery(instances[i]).data('contenteditor').render();
                                    }*/

                                    //Save for Undo
                                    saveForUndo();

                                });
                                /**** /Custom Modal for just SRC ****/
                            } else {

                                if(jQuery('#md-createsrc').data('simplemodal')) jQuery('#md-createsrc').data('simplemodal').hide();

                                /**** Custom Modal for IFRAME ****/
                                jQuery('#md-createiframe').css('max-width', '550px');
                                jQuery('#md-createiframe').simplemodal({noOverlay:true});
                                jQuery('#md-createiframe').data('simplemodal').show();
                                $element.data('contenteditor').closePop();

                                jQuery('#txtIframe').val($activeFrame[0].outerHTML);

                                jQuery('#btnIframeOk').off('click');
                                jQuery('#btnIframeOk').on('click', function (e) {
                                    var iframeSrc = jQuery('#txtIframe').val();

                                    if (iframeSrc != '') {
                                        $activeFrame.replaceWith(iframeSrc);
                                    }

                                    jQuery('#md-createiframe').data('simplemodal').hide();

                                    //Trigger Change event
                                    $element.data('contenteditor').settings.onChange();

                                    $element.data('contenteditor').settings.hasChanged = true;
                                    $element.data('contenteditor').render();
                                    /*
                                    for (var i = 0; i < instances.length; i++) {
                                        jQuery(instances[i]).data('contenteditor').settings.hasChanged = true;
                                        jQuery(instances[i]).data('contenteditor').render();
                                    }*/

                                    //Save for Undo
                                    saveForUndo();

                                });
                                /**** /Custom Modal for IFRAME ****/
                            }

                        });

                        jQuery('#divFrameLink').off('mouseenter mouseleave');
                        jQuery('#divFrameLink').on('mouseenter mouseleave', function(e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    jQuery(this).stop(true, true).css("display", "block"); // Spy tdk flickr
                                    break;
                                case 'mouseleave':
                                    jQuery(this).stop(true, true).fadeOut(0);
                                    break;
                            }
                        });

                        if (jQuery(this).parents(".ui-draggable").css('outline-style') == 'none') {
                            jQuery(this).find('.ovl').css('z-index', '1');
                        }
                        break;

                    case 'mouseleave':

                        jQuery(this).find('.ovl').css('z-index', '-1');
                        jQuery("#divFrameLink").stop(true, true).fadeOut(0);

                        break;
                }
            });

            $element.off('mouseenter mouseleave', 'a');
            $element.on('mouseenter mouseleave', 'a', function(e) {

                if( jQuery(this).hasClass('not-a') ) return;

                switch(e.type) {
                    case 'mouseenter':
                        if(jQuery('#md-createlink').css('display')=='block') return; //Link dialog still opens

                        if( jQuery(this).parents("[data-html]").length > 0 ) return; //Mode: code

                        if( jQuery(this).parents("[data-mode='readonly']").length > 0 ) return; //Mode: readonly

                        if( jQuery(this).parents("[data-mode='readonly-protected']").length > 0 ) return; //Mode: readonly & protected

                        if (jQuery(this).children('img').length == 1 && jQuery(this).children().length == 1) return;

                        var _top; var _left;
                        var scrolltop = jQuery(window).scrollTop();
                        var offsettop = jQuery(this).offset().top;
                        var offsetleft = jQuery(this).offset().left;
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        var is_ie = detectIE();
                        var browserok = true;
                        if (is_firefox||is_ie) browserok = false;
                        if(browserok){
                            //Chrome 37, Opera 24
                            _top = (offsettop - 27) + (scrolltop - scrolltop);
                            _left = offsetleft;
                        } else {
                            if(is_ie){
                                //IE 11 (Adjustment required)

                                //Custom formula for adjustment in IE11
                                var space = $element.getPos().top;
                                var adjy_val = (-space/1.1) + space/1.1;
                                var space2 = $element.getPos().left;
                                var adjx_val = -space2 + space2;

                                var p = jQuery(this).getPos();
                                _top = (p.top - 25) + adjy_val;
                                _left = p.left + adjx_val;
                            }
                            if(is_firefox) {
                                //Firefox (No Adjustment required)
                                _top = offsettop - 25;
                                _left = offsetleft;
                            }
                        }
                        jQuery("#divRteLink").css("top", _top + "px");
                        jQuery("#divRteLink").css("left", _left + "px");


                        jQuery("#divRteLink").stop(true, true).css({ display: 'none' }).fadeIn(20);

                        $activeLink = jQuery(this);

                        jQuery("#divRteLink").off('click');
                        jQuery("#divRteLink").on('click', function (e) {

                            /**** Custom Modal ****/
                            jQuery('#md-createlink').css('max-width', '550px');
                            jQuery('#md-createlink').simplemodal({
                                noOverlay:true,
                                onCancel: function () {
                                    if ($activeLink.attr('href') == 'http://')
                                        $activeLink.replaceWith($activeLink.html());
                                }
                            });
                            jQuery('#md-createlink').data('simplemodal').show();
                            $element.data('contenteditor').closePop();

                            jQuery('#txtLink').val($activeLink.attr('href'));
                            jQuery('#txtLinkText').val($activeLink.html());
                            jQuery('#txtLinkTitle').val($activeLink.attr('title'));
                            if($activeLink.attr('target')=='_blank'){
                                jQuery('#chkNewWindow').prop('checked', true);
                            } else {
                                jQuery('#chkNewWindow').prop('checked', false);
                            }

                            jQuery('#btnLinkOk').off('click');
                            jQuery('#btnLinkOk').on('click', function (e) {

                                $activeLink.attr('href', jQuery('#txtLink').val());

                                if (jQuery('#txtLink').val() == 'http://' || jQuery('#txtLink').val() == '') {
                                    $activeLink.replaceWith($activeLink.html());
                                }
                                $activeLink.html(jQuery('#txtLinkText').val());
                                $activeLink.attr('title',jQuery('#txtLinkTitle').val());
                                if(jQuery('#chkNewWindow').is(":checked")){
                                    $activeLink.attr('target','_blank');
                                } else {
                                    $activeLink.removeAttr('target');
                                }

                                jQuery('#md-createlink').data('simplemodal').hide();

                                //Trigger Change event
                                $element.data('contenteditor').settings.onChange();

                                $element.data('contenteditor').settings.hasChanged = true;
                                $element.data('contenteditor').render();
                                /*
                                for (var i = 0; i < instances.length; i++) {
                                    jQuery(instances[i]).data('contenteditor').settings.hasChanged = true;
                                    jQuery(instances[i]).data('contenteditor').render();
                                }*/

                                //Save for Undo
                                saveForUndo();
                            });
                            /**** /Custom Modal ****/

                        });

                        jQuery("#divRteLink").off('mouseenter mouseleave');
                        jQuery("#divRteLink").on('mouseenter mouseleave', function (e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    jQuery(this).stop(true, true).css("display", "block"); // Spy tdk flickr
                                    break;
                                case 'mouseleave':
                                    jQuery(this).stop(true, true).fadeOut(0);
                                    break;
                            }
                        });

                        break;

                    case 'mouseleave':

                        jQuery("#divRteLink").stop(true, true).fadeOut(0);

                        break;
                }
            });

            //Custom File Select
            jQuery("#btnLinkBrowse").off('click');
            jQuery("#btnLinkBrowse").on('click', function (e) {

                //Clear Controls
                jQuery("#divToolImg").stop(true, true).fadeOut(0);
                jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                jQuery("#divRteLink").stop(true, true).fadeOut(0);
                jQuery("#divFrameLink").stop(true, true).fadeOut(0);

                var sFunc = ($element.data('contenteditor').settings.onFileSelectClick+'').replace( /\s/g, '');
                if(sFunc != 'function(){}'){

                    //$element.data('imageembed').settings.onFileSelectClick();
                    $element.data('contenteditor').settings.onFileSelectClick({targetInput: jQuery("#txtLink").get(0), theTrigger: jQuery("#btnLinkBrowse").get(0)});

                } else {

                    jQuery('#ifrFileBrowse').attr('src',$element.data('contenteditor').settings.fileselect);
                    jQuery('#active-input').val('txtLink');

                    /**** Custom Modal ****/
                    jQuery('#md-fileselect').css('width', '65%');
                    jQuery('#md-fileselect').simplemodal();
                    jQuery('#md-fileselect').data('simplemodal').show();
                    /**** /Custom Modal ****/
                    $element.data('contenteditor').closePop();
                }

            });

            $element.data('contenteditor').settings.onRender();
            $element.data('contenteditor').contentRender();
        };

        this.prepareRteCommand = function (s) {
            jQuery('[data-rte-cmd="' + s + '"]').off('click');
            jQuery('[data-rte-cmd="' + s + '"]').click(function (e) {
                try {
                    document.execCommand(s, false, null);
                } catch (e) {
                    //FF fix
                    $element.attr('contenteditable', true);
                    document.execCommand(s, false, null);
                    $element.removeAttr('contenteditable');
                    $element.data('contenteditor').render();
                }

                $element.data('contenteditor').getState();

                //Trigger Change event
                $element.data('contenteditor').settings.onChange();

                $element.data('contenteditor').settings.hasChanged = true;
                e.preventDefault();
            });
        };

        this.applyColor = function (s, text) {

            var el;
            var curr;
            if (window.getSelection) {
                curr = window.getSelection().getRangeAt(0).commonAncestorContainer;
                if(curr.nodeType==3) {  //ini text node
                    el = curr.parentNode;
                } else {
                    el = curr;
                }
                //el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
            }
            else if (document.selection) {
                curr = document.selection.createRange();
                el = document.selection.createRange().parentElement();
            }

            var selColMode = jQuery('#selColorApplyTo').val(); //1 color 2 background 3 background block

            if (jQuery.trim(text) != '' && jQuery(el).text() != text) {
                if(selColMode==1) {
                    //Set text color
                    document.execCommand("ForeColor",false,s);
                }
                if(selColMode==2) {
                    //Set text background
                    document.execCommand("BackColor",false,s);
                }
                //Cleanup FONTs
                var fontElements = document.getElementsByTagName("font");
                for (var i = 0, len = fontElements.length; i < len; ++i) {
                    var s = fontElements[i].color;
                    if(s!='') {
                        fontElements[i].removeAttribute("color");
                        fontElements[i].style.color = s;
                    }
                }
                //Cleanup multiple span (IE)
                var is_ie = detectIE();
                if (is_ie) {
                    $activeElement.find('span').each(function(){
                        if(jQuery(this).find('span').length==1){
                            if(jQuery(this).text()==jQuery(this).find('span:first').text()){
                                var innerspanstyle = jQuery(this).find('span:first').attr('style');
                                jQuery(this).html(jQuery(this).find('span:first').html());
                                var newstyle = jQuery(this).attr('style')+';'+innerspanstyle;
                                jQuery(this).attr('style',newstyle);
                            }
                        }
                    });
                }

            }
            else if (jQuery(el).text() == text) {//selection fully mode on text AND element. Use element then.
                if(selColMode==1) {
                    //Set element color
                    if(jQuery(el).html()){
                        jQuery(el).css('color', s);
                    } else {
                        jQuery(el).parent().css('color', s);
                    }
                }
                if(selColMode==2) {
                    //Set element background
                    if(jQuery(el).html()){
                        jQuery(el).css('background-color', s);
                    } else {
                        jQuery(el).parent().css('background-color', s);
                    }
                }
            }
            else{
                if(selColMode==1) {
                    //Set element color
                    jQuery(el).css('color', s);
                }
                if(selColMode==2) {
                    //Set element background
                    jQuery(el).css('background-color', s);
                }
            };
            if(selColMode==3) {
                //Set block background
                //jQuery(el).parents('.ui-draggable').children('div').first().css('background-color', jQuery(this).css("background-color") );
                jQuery(el).parents('.ui-draggable').children().first().css('background-color', s );
            }
            /*if(selColMode==4) {
                //Set content background
                $element.css('background-color', s );
            }*/

        };

        this.init();
    };

    jQuery.fn.contenteditor = function (options) {

        return this.each(function () {

            instances.push(this);

            if (undefined == jQuery(this).data('contenteditor')) {
                var plugin = new jQuery.contenteditor(this, options);
                jQuery(this).data('contenteditor', plugin);

            }

        });
    };
})(jQuery);


function ce_closePop() {
    jQuery('.rte-pop').css('display','none');

    jQuery('[data-rte-cmd="formatting"]').removeClass('on');
    jQuery('[data-rte-cmd="textsettings"]').removeClass('on');
    jQuery('[data-rte-cmd="color"]').removeClass('on');
    jQuery('[data-rte-cmd="font"]').removeClass('on');
    jQuery('[data-rte-cmd="formatPara"]').removeClass('on');
    jQuery('[data-rte-cmd="align"]').removeClass('on');
    jQuery('[data-rte-cmd="list"]').removeClass('on');
    jQuery('[data-rte-cmd="table"]').removeClass('on');
};

function pasteContent($activeElement) {

    var savedSel = saveSelection();

    jQuery('#idContentWord').remove();
    var tmptop = $activeElement.offset().top;
    jQuery('#divCb').append("<div style='position:absolute;z-index:-1000;top:" + tmptop + "px;left:-1000px;width:1px;height:1px;overflow:auto;' name='idContentWord' id='idContentWord' contenteditable='true'></div>");

    var pasteFrame = document.getElementById("idContentWord");
    pasteFrame.focus();

    setTimeout(function () {
        try {
            restoreSelection(savedSel);
            var $node = jQuery(getSelectionStartNode());

            // Insert pasted text
            if (jQuery('#idContentWord').length == 0) return; //protection

            var sPastedText = '';

            var bRichPaste = false;

            if(jQuery('#idContentWord table').length > 0 ||
                jQuery('#idContentWord img').length > 0 ||
                jQuery('#idContentWord p').length > 0 ||
                jQuery('#idContentWord a').length > 0){
                bRichPaste = true;
            }

            if(bRichPaste){

                //Clean Word
                sPastedText = jQuery('#idContentWord').html();
                sPastedText = cleanHTML(sPastedText);

                jQuery('#idContentWord').html(sPastedText);
                if(jQuery('#idContentWord').children('p,h1,h2,h3,h4,h5,h6,ul,li').length>1){
                    //Fix text that doesn't have paragraph
                    jQuery('#idContentWord').contents().filter(function() {
                        return (this.nodeType == 3 && jQuery.trim(this.nodeValue)!='');
                    }).wrap( "<p></p>" ).end().filter("br").remove();
                }
                sPastedText = '<div class="edit">'+ jQuery('#idContentWord').html() + '</div>';

            } else {
                jQuery('#idContentWord').find('p,h1,h2,h3,h4,h5,h6').each(function(){
                    jQuery(this).html(jQuery(this).html()+' '); //add space (&nbsp;)
                });

                sPastedText = jQuery('#idContentWord').text();
            }

            jQuery('#idContentWord').remove();

            var oSel = window.getSelection();
            var range = oSel.getRangeAt(0);
            range.extractContents();
            range.collapse(true);
            var docFrag = range.createContextualFragment(sPastedText);
            var lastNode = docFrag.lastChild;

            range.insertNode(docFrag);

            range.setStartAfter(lastNode);
            range.setEndAfter(lastNode);
            range.collapse(false);
            var comCon = range.commonAncestorContainer;
            if (comCon && comCon.parentNode) {
                try { comCon.parentNode.normalize(); } catch (e) { };
            }
            oSel.removeAllRanges();
            oSel.addRange(range);

        } catch (e) {

            jQuery('#idContentWord').remove();
        };

    }, 200);

}

// source: http://stackoverflow.com/questions/5605401/insert-link-in-contenteditable-element
var savedSel;
function saveSelection() {
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            var ranges = [];
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                ranges.push(sel.getRangeAt(i));
            }
            return ranges;
        }
    } else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
};
function restoreSelection(savedSel) {
    if (savedSel) {
        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            for (var i = 0, len = savedSel.length; i < len; ++i) {
                sel.addRange(savedSel[i]);
            }
        } else if (document.selection && savedSel.select) {
            savedSel.select();
        }
    }
};
// source: http://stackoverflow.com/questions/2459180/how-to-edit-a-link-within-a-contenteditable-div
function getSelectionStartNode() {
    var node, selection;
    if (window.getSelection) { // FF3.6, Safari4, Chrome5 (DOM Standards)
        selection = getSelection();
        node = selection.anchorNode;
    }
    if (!node && document.selection) { // IE
        selection = document.selection;
        var range = selection.getRangeAt ? selection.getRangeAt(0) : selection.createRange();
        node = range.commonAncestorContainer ? range.commonAncestorContainer :
			   range.parentElement ? range.parentElement() : range.item(0);
    }
    if (node) {
        return (node.nodeName == "#text" ? node.parentNode : node);
    }
};
//
var getSelectedNode = function () {
    var node, selection;
    if (window.getSelection) {
        selection = getSelection();
        node = selection.anchorNode;
    }
    if (!node && document.selection) {
        selection = document.selection;
        var range = selection.getRangeAt ? selection.getRangeAt(0) : selection.createRange();
        node = range.commonAncestorContainer ? range.commonAncestorContainer :
               range.parentElement ? range.parentElement() : range.item(0);
    }
    if (node) {
        return (node.nodeName == "#text" ? node.parentNode : node);
    }
};

function getSelected() {
    if (window.getSelection) {
        return window.getSelection();
    }
    else if (document.getSelection) {
        return document.getSelection();
    }
    else {
        var selection = document.selection && document.selection.createRange();
        if (selection.text) {
            return selection.text;
        }
        return false;
    }
    return false;
};

/* http://stackoverflow.com/questions/6690752/insert-html-at-caret-in-a-contenteditable-div */
function pasteHtmlAtCaret(html, selectPastedContent) {
    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            var el = document.createElement("div");
            el.innerHTML = html;
            var frag = document.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
                lastNode = frag.appendChild(node);
            }
            var firstNode = frag.firstChild;
            range.insertNode(frag);

            if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                if (selectPastedContent) {
                    range.setStartBefore(firstNode);
                } else {
                    range.collapse(true);
                }
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    } else if ( (sel = document.selection) && sel.type != "Control") {
        var originalRange = sel.createRange();
        originalRange.collapse(true);
        sel.createRange().pasteHTML(html);
        if (selectPastedContent) {
            range = sel.createRange();
            range.setEndPoint("StartToStart", originalRange);
            range.select();
        }
    }
}

var $savedSel;
var $activeIcon;
function applyIconClass(s) {

    if ($activeIcon) {

        var sClassSize = "";
        if ($activeIcon.hasClass('size-12')) sClassSize = 'size-12';
        if ($activeIcon.hasClass('size-14')) sClassSize = 'size-14';
        if ($activeIcon.hasClass('size-16')) sClassSize = 'size-16';
        if ($activeIcon.hasClass('size-18')) sClassSize = 'size-18';
        if ($activeIcon.hasClass('size-21')) sClassSize = 'size-21';
        if ($activeIcon.hasClass('size-24')) sClassSize = 'size-24';
        if ($activeIcon.hasClass('size-32')) sClassSize = 'size-32';
        if ($activeIcon.hasClass('size-48')) sClassSize = 'size-48';
        if ($activeIcon.hasClass('size-64')) sClassSize = 'size-64';
        if ($activeIcon.hasClass('size-80')) sClassSize = 'size-80';
        if ($activeIcon.hasClass('size-96')) sClassSize = 'size-96';

        $activeIcon.css('font-size', '');

        if (s.indexOf('size-') == -1 && s != '') {
            //Change icon
            $activeIcon.attr('class', s);
            if (sClassSize != '') $activeIcon.addClass(sClassSize);
        } else {
            //Change size
            $activeIcon.removeClass('size-12');
            $activeIcon.removeClass('size-14');
            $activeIcon.removeClass('size-16');
            $activeIcon.removeClass('size-18');
            $activeIcon.removeClass('size-21');
            $activeIcon.removeClass('size-24');
            $activeIcon.removeClass('size-32');
            $activeIcon.removeClass('size-48');
            $activeIcon.removeClass('size-64');
            $activeIcon.removeClass('size-80');
            $activeIcon.removeClass('size-96');
            $activeIcon.addClass(s);
        }

    } else {

        restoreSelection(savedSelPublic);

        var tmpId = makeid();
        pasteHtmlAtCaret(' <i id="' + tmpId + '" class="' + s + '"></i> ',true);

        $activeIcon = jQuery('#' + tmpId);
        $activeIcon.removeAttr('id');

        /*
        var is_ie = detectIE();
        var is_edge = detectEdge();
        if ((is_ie && is_ie <= 11) || is_edge) {
            //failed
            restoreSelection($savedSel);

            var oSel = document.selection.createRange();
            if (oSel.parentElement) {
                oSel.pasteHTML('<i class="' + s + '"></i>');
                e.cancelBubble = true;
                e.returnValue = false;
                oSel.select();
                oSel.moveEnd("character", 1);
                oSel.moveStart("character", 1);
                oSel.collapse(false);
            }
        } else {
            var oSel = window.getSelection();
            var range = oSel.getRangeAt(0);
            range.extractContents();
            range.collapse(true);
            var docFrag = range.createContextualFragment(' <i class="' + s + '"></i> ');
            //range.collapse(false);
            var lastNode = docFrag.lastChild;
            range.insertNode(docFrag);
            range.setStartAfter(lastNode);
            range.setEndAfter(lastNode);

            var comCon = range.commonAncestorContainer;
            if (comCon && comCon.parentNode) {
                try { comCon.parentNode.normalize(); } catch (e) { }
            }

            oSel.removeAllRanges();
            oSel.addRange(range);
        }
        */

        jQuery(cb_list).each(function(){
            jQuery(this).data('contenteditor').contentRender();
        });

    }
}
/*******************************************************************************************/

var $imgActive;

(function (jQuery) {

    var tmpCanvas;
    var tmpCanvasNoCrop;
    var nInitialWidth;
    var nInitialHeight;

    jQuery.imageembed = function (element, options) {

        var defaults = {
            hiquality: false,
            imageselect: '',
            fileselect: '',
            imageEmbed: true,
            linkDialog: true,
            zoom: 0,
            customval: 0,
            largerImageHandler: '',
            onChanged: function () { },
            onImageBrowseClick: function () { },
            onImageSettingClick: function () { },
            onImageSelectClick: function () { },
            onFileSelectClick: function () { }
        };

        this.settings = {};

        var $element = jQuery(element),
                    element = element;

        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

            /**** Localize All ****/
            if (jQuery('#divCb').length == 0) {
                jQuery('body').append('<div id="divCb"></div>');
            }


            var html_photo_file = '';
            var html_photo_file2 = '';
            //if (this.settings.imageEmbed) {
                if (navigator.appName.indexOf('Microsoft') != -1) {
                    html_photo_file = '<div id="divToolImg"><div class="fileinputs"><input type="file" name="fileImage" id="fileImage" class="my-file" /><div class="fakefile"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAC+klEQVRoQ+2au24aQRSGz+ySkEvPA9AQubNEhXgCSogEShmZGkSQpTS8AjUNSAjXlCRNStpQ8QK8AI6UOLazM5lZvGRvswsz43hYz0iWZe3uzPnOf25rQOVymcAzWsgAZ1xto3DGBQajsFE4Yx4wIZ0xQSM4RmGjcMY8YEI6Y4LKFy0H/9TCJ7b1VsiOo0PaAAv5Wf4ho/CBPjQhneYokRyezWZQKpW4WzuOA71eD5bLZdrx++vahnSz2YRutwu5XC4RZrPZQL1eP33g4XAI1Wo1FeRYlbVQ+FA1U+kfblitVtBut2Nvf3LgQqEAk8kE2G9VC2MM4/EYRqNRZMsnBy4WizCdTiGfz6vidffhqaw98Ha7hU6nA+v1OuCQfr8PLBV46ySB/bAeoL8qJ0GfHLA/D8P9OOmap/jJAXvq1mq12NB1lW404LL/GVqtD5QTPfwwZEJz+DtcXHwEDPf0z3+f+2mbw17oxvZjhIBgGz71LqFSqcQ6xK8wgT+AyZ0L/t+AMflNz3MiNYZXpXkKI2SDhfKw3V67xYwXAdGQJhT6lj77SqgbHP3ywMLMITeB8GIn84C9PJ3P5/s+vYPdGbxYLGAwGABv3k4aPkSIBYAZMg0tfBs4L6kP+yvy7OoKzt6dg3+UTJrQtABmpOHQThs8PGjbeuMrSuDmbdLLhTbAYZXTgJmTEMrBj+sbbs6yPb1KzMIewOJOWiLh7Nog85UH/7vxobO0bb12QYJrV4jCxZA56OuXb26Oq1pSwOGwTgtPz2gLvaRqv9gzOORXpAiyiywN3jdagXtlwaWACbnf9UWBxdRjbWmnLA1l3qK92kYs79UsOeCYaq3GrOAuokNGnC1SwLRWg4NpT37kpREwHUIwzb9HXs8LWKccZsKK/Nv24IBwYdkIGm5jB+8QuVEyh+WA2XDBqjVygfyvheJAaU9KA6cdoNt1A6ybIqrtMQqr9qhu+xmFdVNEtT1GYdUe1W0/o7Buiqi2xyis2qO67WcU1k0R1fb8BZv85KDCNGIQAAAAAElFTkSuQmCC" /></div></div></div>';
                    html_photo_file2 = '';
                } else {
                    html_photo_file = '<div style="display:none"><input type="file" name="fileImage" id="fileImage" class="my-file"></div>';
                    html_photo_file2 = '<div id="divToolImg">' +
                            '<i id="lnkEditImage" class="cb-icon-camera"></i>' +
                        '</div>';
                }
            //}

            var html_photo_tool = '<div id="divTempContent" style="display:none"></div>' +
                    '<div class="overlay-bg" style="position:fixed;top:0;left:0;width:1;height:1;z-index:10000;background:#fff;opacity:0.8"></div>' +
                    '<div id="divImageEdit" style="position:absolute;display:none;z-index:10000">' +
                        '<div id="my-mask" style="width:200px;height:200px;overflow:hidden;">' +
                            '<img id="my-image" src="" style="max-width:none" />' +
                        '</div>' +
                        '<div id="img-control" style="margin-top:1px;position:absolute;top:-31px;left:0px;width:235px;opacity:0.8">' +
					        '<button id="btnImageCancel" type="button" value="Cancel" ><i class="cb-icon-back"></i></button>' +
                            '<button id="btnZoomOut" type="button" value="-" ><i class="cb-icon-minus"></i></button>' +
                            '<button id="btnZoomIn" type="button" value="+" ><i class="cb-icon-plus"></i></button>' +
                            '<button id="btnImageMore" type="button" value="..." >...</button>' +
                            '<button id="btnChangeImage" type="button" value="Ok" ><i class="cb-icon-ok"></i> Ok</button>' +
                        '</div>' +
                        '<div id="divImageMore" style="display:none">' +
				            '<label for="chkImageNoCrop"><input id="chkImageNoCrop" type="checkbox" />No Crop</label>' +
                            '<br>' +
                            (this.settings.largerImageHandler == '' ? '' : '<label for="chkImageClickToEnlarge"><input id="chkImageClickToEnlarge" type="checkbox" />Click to enlarge</label><br>') +
                            '<button id="btnImageMoreOk" type="button" value="Ok" ><i class="cb-icon-ok"></i> Ok</button>' +
                        '</div>' +
                    '</div>' +
                    '<div style="display:none;">' +
                        '<canvas id="myCanvas"></canvas>' +
				        '<canvas id="myTmpCanvas"></canvas>' +
				        '<canvas id="myTmpCanvasNoCrop"></canvas>' +
                    '</div>' +
                    '<form id="canvasform" name="canvasform" method="post" action="" target="canvasframe" enctype="multipart/form-data">' +
                        html_photo_file +
				        '<input id="hidRefId" name="hidRefId" type="hidden" value="' + this.settings.customval + '" />' + /* for larger image upload */
                    '</form>' +
                    '<iframe id="canvasframe" name="canvasframe" style="width:1px;height:1px;border:none;visibility:hidden;position:absolute"></iframe>';

            //Custom Image Select
            var bUseCustomImageSelect = false;
            if(this.settings.imageselect!='') bUseCustomImageSelect=true;

            var sFunc = (this.settings.onImageSelectClick+'').replace( /\s/g, '');
            if(sFunc != 'function(){}'){ //If custom event set, enable the button
                bUseCustomImageSelect=true;
            }

            //Custom File Select
            var bUseCustomFileSelect = false;
            if(this.settings.fileselect!='') bUseCustomFileSelect=true;

            var sFunc = (this.settings.onFileSelectClick+'').replace( /\s/g, '');
            if(sFunc != 'function(){}'){ //If custom event set, enable the button
                bUseCustomFileSelect=true;
            }

            var imageEmbed = this.settings.imageEmbed;

            var html_hover_icons = html_photo_file2 +
                    '<div id="divToolImgSettings">' +
                        '<i id="lnkImageSettings" class="cb-icon-link"></i>' +
                    '</div>' +
                    '<div id="divToolImgLoader">' +
                        '<i id="lnkImageLoader" class="cb-icon-spin animate-spin"></i>' +
                    '</div>' +
                    '' +
                    '<div class="md-modal md-draggable" id="md-img">' +
			            '<div class="md-content">' +
				            '<div class="md-body">' +
                                '<div class="md-modal-handle">' +
                                    '<i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i>' +
                                '</div>' +
                                '<div class="md-tabs">' +
                                    '<span id="tabImgLnk" class="active">IMAGE</span>' +
                                    '<span id="tabImgPl">CHANGE DIMENSION</span>' +
                                '</div>' +
                                '<div id="divImgPl" style="overflow-y:auto;overflow-x:hidden;display:none;box-sizing:border-box;padding:10px 10px 10px">';
                                    html_hover_icons += '<div style="padding:12px 0 0;width:100%;text-align:center;">';
                                    html_hover_icons += 'DIMENSION (WxH): &nbsp; <select id="selImgW">';
                                    var valW =50;
                                    for(var i=0;i<231;i++) {
                                        var selected = '';
                                        if(i==90) selected = ' selected="selected"';
                                        html_hover_icons +=  '<option value="' + valW + '"' + selected + '>' + valW + 'px</option>';
                                        valW += 5;
                                    }
                                    html_hover_icons += '</select> &nbsp; ';

                                    html_hover_icons += '<select id="selImgH">';
                                    var valH =50;
                                    for(var i=0;i<111;i++) {
                                        var selected = '';
                                        if(i==40) selected = ' selected="selected"';
                                        html_hover_icons +=  '<option value="' + valH + '"' + selected + '>' + valH + 'px</option>';
                                        valH += 5;
                                    }
                                    html_hover_icons += '</select> &nbsp; ';

                                    html_hover_icons += '<select id="selImgStyle">';
                                    html_hover_icons +=  '<option value="square">Square</option>';
                                    html_hover_icons +=  '<option value="circle">Circle</option>';
                                    html_hover_icons += '</select><br>';
                                    html_hover_icons += '<button id="btnInsertPlh" style="margin-left:12px;margin-top:12px;"> REPLACE </button><br>';
                                    html_hover_icons += '<p>(Re-embedding/uploading image needed)</p>';
                                    html_hover_icons += '</div>' +
                                '</div>' +
                                '<div id="divImgLnk">' +
                                    '<div class="md-label">Source:</div>' +
                                    (bUseCustomImageSelect ? '<input type="text" id="txtImgUrl" class="inptxt" style="float:left;width:60%"></input><i class="cb-icon-link md-btnbrowse" id="btnImageBrowse" style="width:10%;"></i>' : '<input type="text" id="txtImgUrl" class="inptxt" style="float:left;width:70%"></input>') +
                                    '<br style="clear:both">' +
                                    '<div class="md-label">Title:</div>' +
                                    '<input type="text" id="txtAltText" class="inptxt" style="float:right;width:70%"></input>' +
                                    '<br style="clear:both">' +
                                    '<div class="md-label">Link:</div>' +
                                    (bUseCustomFileSelect ? '<input type="text" id="txtLinkUrl" class="inptxt" style="float:left;width:60%"></input><i class="cb-icon-link md-btnbrowse" id="btnFileBrowse" style="width:10%;"></i>' : '<input type="text" id="txtLinkUrl" class="inptxt" style="float:left;width:70%"></input>') +
				                    '<br style="clear:both">' +
                                    '<div class="md-label">Target:</div>' +
                                    '<label style="float:left;" for="chkNewWindow2" class="inpchk"><input type="checkbox" id="chkNewWindow2"></input> New Window</label>' +
                                    '<br style="clear:both">' +
                                    '<div id="divEmbedOriginal">' +
                                        '<div class="md-label">&nbsp;</div>' +
                                        '<label style="float:left;" for="chkCrop" class="inpchk"><input type="checkbox" id="chkCrop"></input> Crop</label>' +
                                        '<br style="clear:both" />' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
					        '<div id="divImgLnkOk" class="md-footer">' +
                                '<button id="btnImgOk"> Ok </button>' +
                            '</div>' +
			            '</div>' +
		            '</div>' +
                    '' +
                    '<div class="md-modal" id="md-imageselect">' +
			            '<div class="md-content">' +
				            '<div class="md-body">' +
                                (bUseCustomImageSelect ? '<iframe id="ifrImageBrowse" style="width:100%;height:400px;border: none;display: block;" src="' + sScriptPath + 'blank.html"></iframe>' : '') +
				            '</div>' +
			            '</div>' +
		            '</div>' +
                    '';
                    if (jQuery('#md-fileselect').length==0) {
                        html_hover_icons += '<div class="md-modal" id="md-fileselect">' +
			                '<div class="md-content">' +
				                '<div class="md-body">' +
                                    (bUseCustomFileSelect ? '<iframe id="ifrFileBrowse" style="width:100%;height:400px;border: none;display: block;" src="' + sScriptPath + 'blank.html"></iframe>' : '') +
				                '</div>' +
			                '</div>' +
		                '</div>';
                    }
                    if (jQuery('#active-input').length==0) {
                        html_hover_icons += '<input type="hidden" id="active-input" />';
                    }

            if (jQuery('#divToolImg').length == 0) {
                //if (this.settings.imageEmbed) {
                    jQuery('#divCb').append(html_photo_tool);
                //}
                jQuery('#divCb').append(html_hover_icons);
            }


            tmpCanvas = document.getElementById('myTmpCanvas');
            tmpCanvasNoCrop = document.getElementById('myTmpCanvasNoCrop');

            $element.on('mouseenter mouseleave', function(e) {
                switch(e.type) {
                    case 'mouseenter':

                        var zoom;

                        if (localStorage.getItem("zoom") != null) {
                            zoom = localStorage.zoom;
                        } else {
                            zoom = $element.parents('[style*="zoom"]').css('zoom');
                            if (zoom == 'normal') zoom = 1;
                            if (zoom == undefined) zoom = 1;
                        }

                        //FF fix
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        //if (is_firefox) zoom = '1';

                        //IE fix
                        zoom = zoom + ''; //Fix undefined
                        if (zoom.indexOf('%') != -1) {
                            zoom = zoom.replace('%', '') / 100;
                        }
                        if (zoom == 'NaN') {
                            zoom = 1;
                        }

                        localStorage.zoom = zoom;

                        zoom = zoom*1;

                        if(cb_list=='') zoom = 1;//if contentbuilder not used

                        /*var adjy = $element.data('imageembed').settings.adjy*1;
                        var adjy_val = (-adjy/0.2)*zoom + (adjy/0.2);
                        var adjH = -30;
                        var adjW = -30;
                        var p = jQuery(this).getPos();

                        jQuery("#divToolImg").css("top", ((p.top + parseInt(jQuery(this).css('height')) / 2) + adjH) * zoom + adjy_val + "px");
                        jQuery("#divToolImg").css("left", ((p.left + parseInt(jQuery(this).css('width')) / 2) + adjW) * zoom + "px");
                        jQuery("#divToolImg").stop(true, true).css({ display: 'none' }).fadeIn(20);

                        jQuery("#divToolImgSettings").css("top", (((p.top + parseInt(jQuery(this).css('height')) / 2) + adjH) * zoom) + _top_adj + adjy_val + "px");
                        jQuery("#divToolImgSettings").css("left", (((p.left + parseInt(jQuery(this).css('width')) / 2) + adjW) * zoom) + "px");
                        jQuery("#divToolImgSettings").stop(true, true).css({ display: 'none' }).fadeIn(20);*/

                        if($element.data("imageembed").settings.zoom==1){
                            zoom = 1;
                        }

                        /* Get position for image controls */
                        var _top; var _top2; var _left;
                        var scrolltop = jQuery(window).scrollTop();
                        var offsettop = jQuery(this).offset().top;
                        var offsetleft = jQuery(this).offset().left;
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        var is_ie = detectIE();
                        var is_edge = detectEdge();
                        var browserok = true;
                        if (is_firefox||is_ie||is_edge) browserok = false;

                        var _top_adj = !jQuery(this).data("imageembed").settings.imageEmbed ? 9 : -35;

                        if(browserok){
                            //Chrome 37, Opera 24
                            _top = ((offsettop + parseInt(jQuery(this).css('height')) / 2) - 15) * zoom  + (scrolltop - scrolltop * zoom) ;
                            _left = ((offsetleft + parseInt(jQuery(this).css('width')) / 2) - 15) * zoom;
                            _top2 = _top + _top_adj;
                        } else {
                            if(is_edge){
                                //
                            }
                            if(is_ie){
                                //IE 11 (Adjustment required)

                                //Custom formula for adjustment in IE11
                                var space = 0; var space2 = 0;
                                $element.parents().each(function () {
                                    if (jQuery(this).data('contentbuilder')) {
                                        space = jQuery(this).getPos().top;
                                        space2 = jQuery(this).getPos().left;
                                    }
                                });
                                var adjy_val = -space*zoom + space;
                                var adjx_val = -space2*zoom + space2;

                                var p = jQuery(this).getPos();
                                _top = ((p.top - 15 + parseInt(jQuery(this).css('height')) / 2)) * zoom + adjy_val;
                                _left = ((p.left - 15 + parseInt(jQuery(this).css('width')) / 2)) * zoom + adjx_val;
                                _top2 = _top + _top_adj;

                            }
                            if(is_firefox) {
                                //Firefox (No Adjustment required)
                                var imgwidth = parseInt(jQuery(this).css('width'));
                                var imgheight = parseInt(jQuery(this).css('height'));

                                _top = offsettop - 15 + imgheight*zoom/2;
                                _left = offsetleft - 15 + imgwidth*zoom/2;
                                _top2 = _top + _top_adj;
                            }
                        }

                        /* <img data-fixed="1" src=".." /> (image must be fixed, cannot be replaced) */
                        var fixedimage = false;
                        $imgActive = jQuery(this);

                        if($imgActive.attr('data-fixed')==1) {
                            fixedimage = true;
                        }

                        /* Show Image Controls */
                        if(cb_edit && !fixedimage){
                            jQuery("#divToolImg").css("top", _top + "px");
                            jQuery("#divToolImg").css("left", _left + "px");
                            if (jQuery(this).data("imageembed").settings.imageEmbed) {
                                jQuery("#divToolImg").stop(true, true).css({ display: 'none' }).fadeIn(20);
                            }

                            if( jQuery(this).data("imageembed").settings.linkDialog ) {
                                jQuery("#divToolImgSettings").css("top", _top2 + "px");
                                jQuery("#divToolImgSettings").css("left", _left + "px");
                                jQuery("#divToolImgSettings").stop(true, true).css({ display: 'none' }).fadeIn(20);
                            } else {
                                jQuery("#divToolImgSettings").css("top", "-10000px"); //hide it
                            }
                        }

                        /* Fix the need to tap twice */
                        if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
                            /* File Input programmatically click failed in iOS9
                            jQuery("#divToolImg").on('touchstart mouseenter focus', function(e) {
                                if(e.type == 'touchstart') {
                                    e.stopImmediatePropagation();
                                    e.preventDefault();
                                }

                                jQuery("#divToolImg").click();

                                e.preventDefault();
                                e.stopImmediatePropagation();
                            });
                            */
                            jQuery("#lnkImageSettings").on('touchstart mouseenter focus', function(e) {
                                if(e.type == 'touchstart') {
                                    e.stopImmediatePropagation();
                                    e.preventDefault();
                                }

                                jQuery("#lnkImageSettings").click();

                                e.preventDefault();
                                e.stopImmediatePropagation();
                            });
                        }

                        /* Browse local Image */
                        jQuery("#divToolImg").off('click');
                        jQuery("#divToolImg").on('click', function (e) {

                            jQuery("#divToolImg").data('image', $imgActive); //img1: Simpan wkt click browse, krn @imgActive berubah2 tergantung hover

                            var sFunc = ($element.data('imageembed').settings.onImageBrowseClick+'').replace( /\s/g, '');
                            if(sFunc != 'function(){}'){

                                $element.data('imageembed').settings.onImageBrowseClick();

                            } else {

                                jQuery('input.my-file[type=file]').click();

                            }

                            e.preventDefault();
                            e.stopImmediatePropagation();
                        });

                        jQuery("#divToolImg").off('mouseenter mouseleave');
                        jQuery("#divToolImg").on('mouseenter mouseleave', function(e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    if (imageEmbed) { // $element.data('imageembed').settings.imageEmbed
                                        jQuery("#divToolImg").stop(true, true).css("display", "block"); // Spy tdk flickr
                                    }
                                    jQuery("#divToolImgSettings").stop(true, true).css("display", "block"); // Spy tdk flickr
                                    break;
                                case 'mouseleave':
                                    jQuery("#divToolImg").stop(true, true).fadeOut(0);
                                    jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                                    break;
                            }
                        });
                        $element.off('mouseenter mouseleave', 'figcaption');
                        $element.on('mouseenter mouseleave', 'figcaption', function(e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    if (imageEmbed) {
                                        jQuery("#divToolImg").stop(true, true).css("display", "block");
                                    }
                                    jQuery("#divToolImgSettings").stop(true, true).css("display", "block");
                                    break;
                                case 'mouseleave':
                                    jQuery("#divToolImg").stop(true, true).fadeOut(0);
                                    jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                                    break;
                            }
                        });
                        jQuery("#divToolImgSettings").off('mouseenter mouseleave');
                        jQuery("#divToolImgSettings").on('mouseenter mouseleave', function(e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    if (imageEmbed) {
                                        jQuery("#divToolImg").stop(true, true).css("display", "block");
                                    }
                                    jQuery("#divToolImgSettings").stop(true, true).css("display", "block");
                                    break;
                                case 'mouseleave':
                                    jQuery("#divToolImg").stop(true, true).fadeOut(0);
                                    jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                                    break;
                            }
                        });

                        /* Open Image Settings Dialog */
                        jQuery("#lnkImageSettings").off('click');
                        jQuery("#lnkImageSettings").on('click', function (e) {

                            jQuery("#divToolImg").data('image', $imgActive); //img1: Simpan wkt click browse, krn @imgActive berubah2 tergantung hover

                            //Clear Controls
                            jQuery("#divToolImg").stop(true, true).fadeOut(0);
                            jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);

                            var sFunc = ($element.data('imageembed').settings.onImageSettingClick+'').replace( /\s/g, '');
                            if(sFunc != 'function(){}'){

                                $element.data('imageembed').settings.onImageSettingClick();

                                return;

                            }

                            /**** Custom Modal ****/
                            jQuery('#md-img').css('max-width', '550px');
                            jQuery('#md-img').simplemodal({noOverlay:true});
                            jQuery('#md-img').data('simplemodal').show();

                            //Get ContentEditor plugin. Close all pops
                            var editor;
                            $element.parents().each(function () {
                                if (jQuery(this).data('contenteditor')) {
                                    editor = jQuery(this).data('contenteditor');
                                    editor.closePop();
                                }
                            });


                            //Check if hovered element is <img> or <figure>
                            var $img = $element;
                            if ($element.prop("tagName").toLowerCase() == 'figure') {
                                $img = $element.find('img:first');
                            }

                            //Get image properties (src, alt & link)
                            if($img.attr('src').indexOf('base64') == -1) {
                                jQuery('#txtImgUrl').val($img.attr('src'));
                            } else {
                                jQuery('#txtImgUrl').val('[Image Data]');
                            }
                            jQuery('#txtAltText').val($img.attr('alt'));
                            jQuery('#txtLinkUrl').val('');
                            jQuery('#chkNewWindow2').prop('checked', false);

                            if ($img.parents('a:first') != undefined) {
                                jQuery('#txtLinkUrl').val($img.parents('a:first').attr('href'));

                                if($img.parents('a:first').attr('target')=='_blank'){
                                    jQuery('#chkNewWindow2').prop('checked', true);
                                } else {
                                    jQuery('#chkNewWindow2').prop('checked', false);
                                }
                            }

                            if (!$element.data('imageembed').settings.imageEmbed) {

                                jQuery('#divEmbedOriginal').css("display","none");

                            } else {

                                jQuery('#divEmbedOriginal').css("display","none");

                                jQuery('#btnImgOk').off('keyup');
                                jQuery('#txtImgUrl').on('keyup', function() {

                                    //If image url is changed, display "Crop" option. If not, hide the "Crop" option.
                                    if( $img.attr('src') == jQuery('#txtImgUrl').val() ) {
                                        jQuery('#divEmbedOriginal').css("display","none");
                                    } else {
                                        jQuery('#divEmbedOriginal').css("display","block");
                                    }

                                });

                            }

                            jQuery('#chkCrop').removeAttr('checked');

                            /*
                            jQuery('#tabImgLnk').css({'text-decoration':'','cursor':'','background':'#515151','color':'#fff'});
                            jQuery('#tabImgPl').css({'text-decoration':'underline','cursor':'pointer','background':'#fafafa','color':'#333'});
                            jQuery('#divImgPl').css('display', 'none');
                            jQuery('#divImgLnk').css('display', 'block');
                            jQuery('#divImgLnkOk').css('display', 'block');
                            */

                            jQuery('#btnImgOk').off('click');
                            jQuery('#btnImgOk').on('click', function (e) {

                                //Get Content Builder plugin
                                var builder;
                                $element.parents().each(function () {
                                    if (jQuery(this).data('contentbuilder')) {
                                        builder = jQuery(this).data('contentbuilder');
                                    }
                                });

                                //Replace image
                                var insertOri=false;
                                if(jQuery('#chkCrop').is(":checked")){

                                } else {
                                    insertOri=true;
                                }

                                if(insertOri==false){
                                    if(jQuery('#txtImgUrl').val().indexOf("http")!=-1) {
                                        //alert("External image will be embedded as is");
                                        insertOri = true;
                                    }
                                }

                                if($img.attr('src')!=jQuery('#txtImgUrl').val()) {
                                    if(insertOri) {
                                        //Remove wxh from blank placeholder if replaced with other image
                                        if( $img.attr('src').indexOf(sScriptPath + 'image.png') != -1 && jQuery('#txtImgUrl').val().indexOf(sScriptPath + 'image.png') == -1 ){ //if( $img.attr('src').indexOf('scripts/image.png') != -1 && jQuery('#txtImgUrl').val().indexOf('scripts/image.png') == -1 ){
                                            $img.css('width', '');
                                            $img.css('height', '');
                                        }

                                        if(jQuery('#txtImgUrl').val().indexOf('[Image Data]') == -1){
                                            $img.attr('src', jQuery('#txtImgUrl').val()); //replaced with processImage() above
                                        } else {
                                            //No Change
                                        }
                                    } else {

                                        processImage( jQuery('#txtImgUrl').val() );

                                    }
                                }

                                //Set image properties
                                $img.attr('alt', jQuery('#txtAltText').val());

                                if (jQuery('#txtLinkUrl').val() == 'http://' || jQuery('#txtLinkUrl').val() == '') {
                                    //remove link
                                    $img.parents('a:first').replaceWith($img.parents('a:first').html());
                                } else {
                                    var imagelink = jQuery('#txtLinkUrl').val();

                                    if ($img.parents('a:first').length == 0) {
                                        //create link
                                        $img.wrap('<a href="' + imagelink + '"></a>');
                                    } else {
                                        //apply link
                                        $img.parents('a:first').attr('href', imagelink);
                                    }

                                    $img.parents('a:first').attr('title', jQuery('#txtAltText').val());

                                    if(jQuery('#chkNewWindow2').is(":checked")){
                                        $img.parents('a:first').attr('target','_blank');
                                    } else {
                                        $img.parents('a:first').removeAttr('target');
                                    }

                                    if(imagelink.toLowerCase().indexOf('.jpg')!=-1 || imagelink.toLowerCase().indexOf('.jpeg')!=-1 || imagelink.toLowerCase().indexOf('.png')!=-1 || imagelink.toLowerCase().indexOf('.gif')!=-1) {
                                        $img.parents('a:first').addClass('is-lightbox');
                                        //$img.parents('a:first').attr('target', '_blank');
                                    } else {
                                        $img.parents('a:first').removeClass('is-lightbox');
                                        //$img.parents('a:first').removeAttr('target');
                                    }

                                }

                                //Apply Content Builder Behavior
                                if (builder) builder.applyBehavior();

                                jQuery('#md-img').data('simplemodal').hide();

                            });


                            var actualW = $img[0].naturalWidth; //parseInt($img.css('width'));
                            var actualH = $img[0].naturalHeight; //parseInt($img.css('height'));

                            //If it is image placeholder with specified css width/height
                            if( $img.attr('src').indexOf(sScriptPath + 'image.png') != -1 ){ //if( $img.attr('src').indexOf('scripts/image.png') != -1 ){
                                for(var i=0;i<$img.attr("style").split(";").length;i++) {
                                    var cssval = $img.attr("style").split(";")[i];
                                    if(jQuery.trim(cssval.split(":")[0]) == "width") {
                                        actualW = parseInt(jQuery.trim(cssval.split(":")[1]));
                                    }
                                    if(jQuery.trim(cssval.split(":")[0]) == "height") {
                                        actualH = parseInt(jQuery.trim(cssval.split(":")[1]));
                                    }
                                }
                            }

                            var valW =50;
                            for(var i=0;i<231;i++) {
                                if(valW>=actualW) {
                                    i = 231; //stop
                                    jQuery('#selImgW').val(valW);
                                    }
                                valW += 5;
                            }
                            var valH =50;
                            for(var i=0;i<111;i++) {
                                if(valH>=actualH) {
                                    i = 111; //stop
                                    jQuery('#selImgH').val(valH);
                                }
                                valH += 5;
                            }
                            if(parseInt($img.css('border-radius'))==500) {
                                jQuery('#selImgStyle').val('circle');
                                jQuery('#selImgH').css('display','none');
                            } else {
                                jQuery('#selImgStyle').val('square');
                                jQuery('#selImgH').css('display','inline');
                            }


                            jQuery('#selImgStyle').off('change');
                            jQuery('#selImgStyle').on('change', function (e) {
                                if(jQuery('#selImgStyle').val()=='circle'){
                                    jQuery('#selImgH').css('display','none');
                                    jQuery('#selImgH').val(jQuery('#selImgW').val());
                                } else {
                                    jQuery('#selImgH').css('display','inline');
                                    jQuery('#selImgH').val(jQuery('#selImgW').val());
                                }
                            });
                            jQuery('#selImgW').off('change');
                            jQuery('#selImgW').on('change', function (e) {
                                if(jQuery('#selImgStyle').val()=='circle'){
                                    jQuery('#selImgH').val(jQuery('#selImgW').val());
                                }
                            });
                            jQuery('#btnInsertPlh').off('click');
                            jQuery('#btnInsertPlh').on('click', function (e) {
                                //Get Content Builder plugin
                                var builder;
                                $element.parents().each(function () {
                                    if (jQuery(this).data('contentbuilder')) {
                                        builder = jQuery(this).data('contentbuilder');
                                    }
                                });

                                //Set image properties
                                $img.attr('src', sScriptPath + 'image.png');
                                $img.attr('alt', jQuery('#txtAltText').val());

                                if(jQuery('#selImgStyle').val()=='circle'){
                                    $img.css('border-radius','500px');
                                    jQuery('#selImgH').val(jQuery('#selImgW').val());
                                } else {
                                    $img.css('border-radius','');
                                    $img.removeClass('circle');
                                }

                                //Set image properties
                                $img.css('width', jQuery('#selImgW').val() + 'px');
                                $img.css('height', jQuery('#selImgH').val() + 'px');

                                //Apply Content Builder Behavior
                                if (builder) builder.applyBehavior();

                                jQuery('#md-img').data('simplemodal').hide();
                            });
                            /**** /Custom Modal ****/

                            e.preventDefault();
                            e.stopImmediatePropagation();
                        });

                        //Open Custom Image Select
                        jQuery("#btnImageBrowse").off('click');
                        jQuery("#btnImageBrowse").on('click', function (e) {

                            //Clear Controls
                            jQuery("#divToolImg").stop(true, true).fadeOut(0);
                            jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                            jQuery("#divRteLink").stop(true, true).fadeOut(0);
                            jQuery("#divFrameLink").stop(true, true).fadeOut(0);

                            var sFunc = ($element.data('imageembed').settings.onImageSelectClick+'').replace( /\s/g, '');
                            if(sFunc != 'function(){}'){

                                //$element.data('imageembed').settings.onImageSelectClick();
                                $element.data('imageembed').settings.onImageSelectClick({targetInput: jQuery("#txtImgUrl").get(0), theTrigger: jQuery("#btnImageBrowse").get(0)});

                            } else {

                                jQuery('#ifrImageBrowse').attr('src',$element.data('imageembed').settings.imageselect);
                                jQuery('#active-input').val('txtImgUrl');

                                /**** Custom Modal ****/
                                jQuery('#md-imageselect').css('width', '65%');
                                jQuery('#md-imageselect').simplemodal();
                                jQuery('#md-imageselect').data('simplemodal').show();
                                /**** /Custom Modal ****/
                            }

                        });

                        //Open Custom File Select
                        jQuery("#btnFileBrowse").off('click');
                        jQuery("#btnFileBrowse").on('click', function (e) {

                            //Clear Controls
                            jQuery("#divToolImg").stop(true, true).fadeOut(0);
                            jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);
                            jQuery("#divRteLink").stop(true, true).fadeOut(0);
                            jQuery("#divFrameLink").stop(true, true).fadeOut(0);

                            var sFunc = ($element.data('imageembed').settings.onFileSelectClick+'').replace( /\s/g, '');
                            if(sFunc != 'function(){}'){

                                //$element.data('imageembed').settings.onFileSelectClick();
                                $element.data('imageembed').settings.onFileSelectClick({targetInput: jQuery("#txtLinkUrl").get(0), theTrigger: jQuery("#btnFileBrowse").get(0)});

                            } else {

                                jQuery('#ifrFileBrowse').attr('src',$element.data('imageembed').settings.fileselect);
                                jQuery('#active-input').val('txtLinkUrl');

                                /**** Custom Modal ****/
                                jQuery('#md-fileselect').css('width', '65%');
                                jQuery('#md-fileselect').simplemodal();
                                jQuery('#md-fileselect').data('simplemodal').show();
                                /**** /Custom Modal ****/

                            }

                        });

                        /* On Change, call the IMAGE EMBEDDING PROCESS */
                        jQuery('.my-file[type=file]').off('change');
                        jQuery('.my-file[type=file]').on('change', function (e) {

                            changeImage(e);

                            jQuery('#my-image').attr('src', ''); //reset

                            /*
                            if($imgActive.parent().hasClass("is-lightbox")){

                            } else {
                                //alert('normal')
                                jQuery(this).clearInputs(); //=> won't upload the large file (by clearing file input.my-file)
                            }
                            */

                        });

                        /* Image Settings Dialog Tabs */
                        jQuery('#tabImgLnk').off('click');
                        jQuery('#tabImgLnk').on('click', function (e) {
                            jQuery('#tabImgLnk').addClass('active'); //.css({'text-decoration':'','cursor':'','background':'rgba(0, 0, 0, 0.88)','color':'rgba(255, 255, 255, 0.86)'});
                            jQuery('#tabImgPl').removeClass('active'); //.css({'text-decoration':'underline','cursor':'pointer','background':'rgba(255, 255, 255, 0.72)','color':'rgba(0, 0, 0, 1)'});
                            jQuery('#divImgPl').fadeOut(300, function(){
                                jQuery('#divImgLnk').fadeIn(0);
                                jQuery('#divImgLnkOk').fadeIn(0);
                            });
                        });
                        jQuery('#tabImgPl').off('click');
                        jQuery('#tabImgPl').on('click', function (e) {
                            jQuery('#tabImgLnk').removeClass('active'); //.css({'text-decoration':'underline','cursor':'pointer','background':'rgba(255, 255, 255, 0.72)','color':'rgba(0, 0, 0, 1)'});
                            jQuery('#tabImgPl').addClass('active'); //.css({'text-decoration':'','cursor':'','background':'rgba(0, 0, 0, 0.88)','color':'rgba(255, 255, 255, 0.86)'});
                            jQuery('#divImgLnk').fadeOut(0);
                            jQuery('#divImgLnkOk').fadeOut(0, function(){
                                jQuery('#divImgPl').fadeIn(300);
                            });
                        });

                        break;

                    case 'mouseleave':

                        jQuery("#divToolImg").stop(true, true).fadeOut(0);
                        jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);

                        break;
                }
            });

        };


        /* IMAGE EMBEDDING PROCESS */
        var changeImage = function (e) {

            if (typeof FileReader == "undefined") return true;

            var file = e.target.files[0];

            if ( !file ) return;

            var extension = file.name.substr((file.name.lastIndexOf('.') + 1)).toLowerCase();
            if (extension != 'jpg' && extension != 'jpeg' && extension != 'png' && extension != 'gif' && extension != 'bmp') {
                alert('Please select an image');
                return;
            }

            //Start Loading Image
            jQuery("#divToolImg").stop(true, true).fadeOut(0);
            jQuery("#divToolImgSettings").stop(true, true).fadeOut(0);

            jQuery("#divToolImgLoader").css('top', jQuery('#divToolImg').css('top'));
            jQuery("#divToolImgLoader").css('left', jQuery('#divToolImg').css('left'));
            jQuery("#divToolImgLoader").css('display', 'block');

            jQuery('.overlay-bg').css('background', 'none'); //prevent problem processing img
            jQuery('.overlay-bg').css('width', '100%');
            jQuery('.overlay-bg').css('height', '100%');
            //jQuery('body').css('overflow', 'hidden'); // This makes unwanted zoom-in in iOS Safari

            processImage(file);
        };


        var processImage = function (file) { //file can also be an URL (from the same host), ex. file = "/ContentBuilder/assets/minimalist/a05-2.jpg";

            var imgname, extension;
            if(!file.name){
                //file is an URL
                imgname = file.substr((file.lastIndexOf('/') + 1));
                extension = file.substr((file.lastIndexOf('.') + 1)).toLowerCase();
            } else {
                //file is an image file
                imgname = file.name;
                extension = file.name.substr((file.name.lastIndexOf('.') + 1)).toLowerCase();
            }

            var hiquality = false;
            try {
                hiquality = $element.data('imageembed').settings.hiquality;
            } catch (e) { };
            var type, quality;
            if (hiquality == false) {
                if (extension == 'jpg' || extension == 'jpeg') {
                    type = 'image/jpeg';
                    quality = 0.92;
                } else {
                    type = 'image/png';
                    quality = 1;
                }
            } else {
                type = 'image/png';
                quality = 1;
            }

            loadImage.parseMetaData(file, function (data) {

                var orientation_num;
                if (data.exif) {
                    orientation_num = data.exif.get('Orientation');
                }

                loadImage(
                    file,
                    function (img) {


                        //Prepare things
                        jQuery('.overlay-bg').css('background', 'none'); //prevent problem processing img
                        jQuery('.overlay-bg').css('width', '100%');
                        jQuery('.overlay-bg').css('height', '100%');
                        //jQuery('body').css('overflow', 'hidden');  // This makes unwanted zoom-in in iOS Safari


                        //Embedding Image Step 1: Read the image (base64 string)
                        //Load into tmpCanvas first, then read using tmpCanvas.toDataURL. Output to "image" variable.
                        //Limit dimension to save resource (reduce dimension if larger than 2500px):
                        var cW, cH;
                        if(img.width > 3200 || img.height > 3200){
                            cW = img.width/2;
                            cH = img.height/2;
                        }
                        else if(img.width > 2500 || img.height > 2500){
                            cW = img.width/1.25;
                            cH = img.height/1.25;
                        } else {
                            cW = img.width;
                            cH = img.height;
                        }

                        /*
                        Check orientation
                        http://stackoverflow.com/questions/20600800/js-client-side-exif-orientation-rotate-and-mirror-jpeg-images
                        */
                        if (4 < orientation_num && orientation_num < 9) {
                            //potrait
                            nInitialWidth = cH;
                            nInitialHeight = cW;
                        } else {
                            //landscape
                            nInitialWidth = cW;
                            nInitialHeight = cH;
                        }


                        /***** Draw NoCropped Image on myTmpCanvasNoCrop *****/

                        //Specify resize size
                        var bResize = false;
                        var oW; var oH;
                        if( nInitialHeight <= $imgActive.height() && nInitialWidth > $imgActive.width() ) {
                            //Original height is smaller than placeholder height. Original width is bigger than placeholder width.
                            oW = $imgActive.width();
                            oH = (nInitialHeight * $imgActive.width())/nInitialWidth;
                            bResize = true;
                        } else if ( nInitialWidth <= $imgActive.width() && nInitialHeight > $imgActive.height() ) {
                            //Original width is smaller than placeholder width. Original height is bigger than placeholder height.
                            oH = $imgActive.height();
                            oW = (nInitialWidth * $imgActive.height())/ nInitialHeight;
                            bResize = true;
                        } else if  ( nInitialWidth <= $imgActive.width() && nInitialHeight <= $imgActive.height() ) {
                            //no resize (original image is smaller than placeholder)
                            oW = nInitialWidth;
                            oH = nInitialHeight;
                        } else {
                            oW = $imgActive.width();
                            oH = (nInitialHeight * $imgActive.width())/nInitialWidth;
                            bResize = true;
                        }

                        var isSafari =  /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                        var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                        if (isSafari || iOS) {

                            //FIRST RENDER (tmpCanvasNoCrop)
                            var mpImg = new MegaPixImage(img);
                            mpImg.render(tmpCanvasNoCrop, { width: cW, height: cH, orientation: orientation_num }, function(){

                                //RESIZE (tmpCanvasNoCrop) with good quality.
                                if(bResize){
                                    var tmpImg = new Image();
                                    var nW = nInitialWidth;
                                    var nH = nInitialHeight;
                                    tmpImg.onload = function() {
                                        nW /= 2;
                                        nH /= 2;
                                        if ( nW < oW || nH < oH ) { nW = oW; nH = oH; }

                                        var mpImg = new MegaPixImage(tmpImg);
                                        mpImg.render(tmpCanvasNoCrop, { width: nW, height: nH }, function(){ /* must specify both width & height correctly (proportionally) */

                                            if ( nW <= oW || nH <= oH ) {

                                                return;

                                            }
                                            tmpImg.src = tmpCanvasNoCrop.toDataURL(type, quality);

                                        });
                                    };
                                    tmpImg.src = tmpCanvasNoCrop.toDataURL( type, quality );
                                }

                            });

                        } else {

                            //FIRST RENDER (tmpCanvasNoCrop)
                            var contextNoCrop = tmpCanvasNoCrop.getContext("2d");
                            //Set proper canvas dimension
                            if (4 < orientation_num && orientation_num < 9) {
                                tmpCanvasNoCrop.width = cH;
                                tmpCanvasNoCrop.height = cW;
                            } else {
                                tmpCanvasNoCrop.width = cW;
                                tmpCanvasNoCrop.height = cH;
                            }
                            //Fix orientation before drawing image
                            switch (orientation_num) {
                                case 2: contextNoCrop.transform(-1, 0, 0, 1, cW, 0); break;
                                case 3: contextNoCrop.transform(-1, 0, 0, -1, cW, cH ); break;
                                case 4: contextNoCrop.transform(1, 0, 0, -1, 0, cH ); break;
                                case 5: contextNoCrop.transform(0, 1, 1, 0, 0, 0); break;
                                case 6: contextNoCrop.transform(0, 1, -1, 0, cH , 0); break;
                                case 7: contextNoCrop.transform(0, -1, -1, 0, cH , cW); break;
                                case 8: contextNoCrop.transform(0, -1, 1, 0, 0, cW); break;
                                default: break;
                            }
                            contextNoCrop.drawImage( img, 0, 0, cW, cH );

                            //RESIZE (tmpCanvasNoCrop) with good quality.
                            if(bResize){
                                var tmpImg = new Image();
                                var nW = nInitialWidth;
                                var nH = nInitialHeight;
                                tmpImg.onload = function() {
                                    nW /= 2;
                                    nH /= 2;
                                    if ( nW < oW || nH < oH ) { nW = oW; nH = oH; }

                                    tmpCanvasNoCrop.width = nW;
                                    tmpCanvasNoCrop.height = nH;
                                    contextNoCrop = tmpCanvasNoCrop.getContext( '2d' );
                                    contextNoCrop.drawImage( tmpImg, 0, 0, nW, nH );

                                    if ( nW <= oW || nH <= oH ) {
                                        //panSetup();crop(); //just to refresh. If not, myTmpCanvas (resized) won't be copied to myCanvas (cropped).
                                        return;
                                        }
                                    tmpImg.src = tmpCanvasNoCrop.toDataURL( type, quality );

                                };
                                tmpImg.src = tmpCanvasNoCrop.toDataURL( type, quality );
                            }
                        }
                        /***** /Draw NoCropped Image on myTmpCanvasNoCrop *****/



                        //Embedding Image Step 2: Resize the div image mask according to image placeholder dimension (proportion)
                        //and enlarge it to the actual image placeholder (in case the image placeholder get smaller in mobile screen)
                        //so that embedding image from mobile will still embed actual (larger) dimension to be seen on desktop

                        $imgActive = jQuery("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                        var zoom = localStorage.zoom;
                        if($element.data('imageembed').settings.zoom==1){
                            zoom = 1;
                        }

                        var enlarge;
                        if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                            enlarge = $imgActive[0].naturalWidth / $imgActive.width(); //2
                        } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') { //new fix
                            enlarge = $imgActive.find('img')[0].naturalWidth / $imgActive.find('img').width();
                        }

                        //If it is image placeholder with specified css width/height
                        var specifiedCssWidth=0;
                        var specifiedCssHeight=0;
                        if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                            if($imgActive.attr("src").indexOf(sScriptPath + "image.png")!=-1){ //if($imgActive.attr("src").indexOf("scripts/image.png")!=-1){
                                for(var i=0;i<$imgActive.attr("style").split(";").length;i++) {
                                    var cssval = $imgActive.attr("style").split(";")[i];
                                    if(jQuery.trim(cssval.split(":")[0]) == "width") {
                                        specifiedCssWidth = parseInt(jQuery.trim(cssval.split(":")[1]));

                                        enlarge = specifiedCssWidth / $imgActive.width();
                                    }
                                    if(jQuery.trim(cssval.split(":")[0]) == "height") {
                                        specifiedCssHeight = parseInt(jQuery.trim(cssval.split(":")[1]));
                                    }
                                }
                            }

                            //todo: in iOS sometimes get blank when dragging (if a blank spot seen within area when dragging)

                        } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') { //new fix

                            if($imgActive.find('img').attr("src").indexOf(sScriptPath + "image.png")!=-1){ //if($imgActive.find('img').attr("src").indexOf("scripts/image.png")!=-1){
                                for(var i=0;i<$imgActive.find('img').attr("style").split(";").length;i++) {
                                    var cssval = $imgActive.find('img').attr("style").split(";")[i];
                                    if(jQuery.trim(cssval.split(":")[0]) == "width") {
                                        specifiedCssWidth = parseInt(jQuery.trim(cssval.split(":")[1]));
                                        enlarge = specifiedCssWidth / $imgActive.find('img').width();
                                    }
                                    if(jQuery.trim(cssval.split(":")[0]) == "height") {
                                        specifiedCssHeight = parseInt(jQuery.trim(cssval.split(":")[1]));
                                    }
                                }
                            }

                        }

                        var maskAdj = 0; // 1.1; //Adjustment to reduce the mask dimension. This is for fixing bug of unwanted black line added in the image edge as a result of
                                            //reading canvas image using canvas.toDataURL("image/jpeg", 0.92). No problem if using "image/png".
                                            //Usage: jQuery("#my-mask").css('width', ($imgActive.width() * enlarge) - maskAdj + 'px');
                        if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                            jQuery("#my-mask").css('width', ($imgActive.width() * enlarge) - maskAdj + 'px'); //multiply width & height with enlarge value
                            jQuery("#my-mask").css('height', ($imgActive.height() * enlarge) - maskAdj + 'px');
                        } else {
                            jQuery("#my-mask").css('width', ($imgActive.innerWidth() * enlarge) - maskAdj + 'px');
                            jQuery("#my-mask").css('height', ($imgActive.innerHeight() * enlarge) - maskAdj + 'px');
                        }

                        //If it is image placeholder with specified css width/height
                        if(specifiedCssWidth!=0) jQuery("#my-mask").css('width', specifiedCssWidth + 'px');
                        if(specifiedCssHeight!=0) jQuery("#my-mask").css('height', specifiedCssHeight + 'px');

                        jQuery("#my-mask").css('zoom', zoom / enlarge); //divide zoom with enlarge value
                        jQuery("#my-mask").css('-moz-transform', 'scale(' + zoom / enlarge + ')');



                        //Embedding Image Step 3: Get dimension (programmatically) for chosen image to fit with its image placeholder
                        var newW;
                        var newY;

                        /* source: http://stackoverflow.com/questions/3987644/resize-and-center-image-with-jquery */
                        var maskWidth = $imgActive.width(); //image placeholder width
                        var maskHeight = $imgActive.height(); //image placeholder height

                        var photoAspectRatio = nInitialWidth / nInitialHeight;
                        var canvasAspectRatio = maskWidth / maskHeight;
                        if (photoAspectRatio < canvasAspectRatio) {
                            newW = maskWidth;
                            newY = (nInitialHeight * maskWidth) / nInitialWidth;
                        }
                        else {
                            newW = (nInitialWidth * maskHeight) / nInitialHeight;
                            newY = maskHeight;
                        }


                        //Embedding Image Step 4: Apply the dimension and enlarge it according to the enlarge value
                        //so that embedding image from mobile will still embed actual (larger) dimension to be seen on desktop
                        newW = newW * enlarge; //multiply width & height with 2
                        newY = newY * enlarge;


                        $imgActive = jQuery("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                        jQuery("#my-image").css('top', '0px');
                        jQuery("#my-image").css('left', '0px');

                        jQuery("#my-image").css('width', newW + 'px'); //Set with the new dimension
                        jQuery("#my-image").css('height', newY + 'px');

                        var zoom = localStorage.zoom;

                        zoom = zoom*1;

                        if($element.data('imageembed').settings.zoom==1){
                            zoom = 1;
                        }

                        //Embedding Image Step 5: Show image control (zoom, etc) with correct position
                        /*var adjy = $element.data('imageembed').settings.adjy*1;
                        var adjy_val = (-adjy/0.183)*zoom + (adjy/0.183);

                        var p = $imgActive.getPos();
                        jQuery('#divImageEdit').css('display', 'inline-block');
                        if ($imgActive.attr('class') == 'img-polaroid') {
                            jQuery("#divImageEdit").css("top", (p.top + 5) * zoom + adjy_val + "px");
                            jQuery("#divImageEdit").css("left", (p.left + 5) * zoom + "px");
                        } else {
                            jQuery("#divImageEdit").css("top", (p.top) * zoom + adjy_val + "px");
                            jQuery("#divImageEdit").css("left", (p.left) * zoom + "px");
                        }*/
                        var _top; var _left; var _top_polaroid; var _left_polaroid;
                        var scrolltop = jQuery(window).scrollTop();
                        var offsettop = $imgActive.offset().top;
                        var offsetleft = $imgActive.offset().left;
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        var is_ie = detectIE();
                        var browserok = true;
                        if (is_firefox||is_ie) browserok = false;
                        if(browserok){
                            //Chrome 37, Opera 24
                            _top = (offsettop * zoom) + (scrolltop - scrolltop * zoom);
                            _left = offsetleft * zoom;
                            _top_polaroid = ((offsettop + 5) * zoom) + (scrolltop - scrolltop * zoom);
                            _left_polaroid = (offsetleft + 5) * zoom;
                        } else {
                            if(is_ie){
                                //IE 11 (Adjustment required)

                                //Custom formula for adjustment in IE11
                                var space = 0;var space2 = 0;
                                $element.parents().each(function () {
                                    if (jQuery(this).data('contentbuilder')) {
                                        space = jQuery(this).getPos().top;
                                        space2 = jQuery(this).getPos().left;
                                    }
                                });
                                var adjy_val = -space*zoom + space;
                                var adjx_val = -space2*zoom + space2;

                                var p = $imgActive.getPos();
                                _top = (p.top * zoom) + adjy_val;
                                _left = (p.left * zoom) + adjx_val;
                                _top_polaroid = ((p.top + 5) * zoom) + adjy_val;
                                _left_polaroid = ((p.left + 5) * zoom) + adjx_val;
                            }
                            if(is_firefox) {
                                //Firefox (No Adjustment required)
                                /*
                                In Firefox, if my-mask is zoomed, it will be centered within it's container divImageEdit.
                                Only because of this, an adjustment is needed for divImageEdit & img-control
                                */
                                var imgwidth = parseInt($imgActive.css('width'));
                                var imgheight = parseInt($imgActive.css('height'));
                                var adjx_val = imgwidth/2 - (imgwidth/2)*zoom;
                                var adjy_val = imgheight/2 - (imgheight/2)*zoom;

                                jQuery('#img-control').css('top',5+adjy_val + 'px');
                                jQuery('#img-control').css('left',7+adjx_val + 'px');

                                _top = offsettop-adjy_val;
                                _left = offsetleft-adjx_val;
                                _top_polaroid = offsettop-adjy_val + 5;
                                _left_polaroid = offsetleft-adjx_val + 5;
                            }
                        }
                        jQuery('#divImageEdit').css('display', 'inline-block');
                        if ($imgActive.attr('class') == 'img-polaroid') {
                            jQuery("#divImageEdit").css("top", _top_polaroid + "px");
                            jQuery("#divImageEdit").css("left", _left_polaroid + "px");
                        } else {
                            jQuery("#divImageEdit").css("top", _top + "px");
                            jQuery("#divImageEdit").css("left", _left + "px");
                        }

                        if(parseInt(jQuery("#divImageEdit").css("top"))<25) {
                            jQuery('#img-control').css('top','auto');
                            jQuery('#img-control').css('bottom', "-24px");
                        }

                        jQuery("#my-mask").css('transform-origin','left top'); //Without this will result incorrect positioning during image embed in FF.

                        //Embedding Image Step 6: Enable "DRAG TO PAN" image within its mask ('<div id="my-mask"><img id="my-image"></div>)
                        //Remember that the image can be bigger (in proportion) than the mask (which has the same dimension with image placeholder)

                        panSetup();

                        //Embedding Image Step 7: The resulting "DRAG TO PAN" will be transfered to a temporary canvas (<canvas id="myTmpCanvas">)

                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                        if (is_firefox) sleep(700);//fix bug on Firefox

                        //To be displayed only after the resize process completed
                        jQuery("#btnImageCancel").css('display', 'none');
                        jQuery("#btnZoomOut").css('display', 'none');
                        jQuery("#btnZoomIn").css('display', 'none');
                        jQuery("#btnImageMore").css('display', 'none');
                        jQuery("#btnChangeImage").css('display', 'none');

                        var isSafari =  /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                        var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                        if (isSafari || iOS) {

                            //FIRST RENDER (tmpCanvas)
                            var mpImg = new MegaPixImage(img);
                            mpImg.render(tmpCanvas, { width: cW, height: cH, orientation: orientation_num }, function(){

                                //Embedding Image Step 8: Load chosen image in an IMG element ('<div id="my-mask"><img id="my-image"></div>)
                                //and set with the new dimension. Remember we have made the container (<div id="my-mask">) 2 times bigger.
                                jQuery('#my-image').attr('src', tmpCanvas.toDataURL( type, quality ));

                                //RESIZE (tmpCanvas) with good quality.
                                //var imageObj = jQuery("#my-image")[0];
                                //context.drawImage(imageObj, 0, 0, newW, newY); //This is replaced with the resize approach below.
                                var tmp = new Image();
                                var nW = nInitialWidth;
                                var nH = nInitialHeight;
                                tmp.onload = function() {
                                    nW /= 2;
                                    nH /= 2;
                                    if ( nW < newW || nH < newY ) { nW = newW; nH = newY; }

                                    var mpImg = new MegaPixImage(tmp);
                                    mpImg.render(tmpCanvas, { width: nW, height: nH }, function(){ /* must specify both width & height correctly (proportionally) */

                                        if ( nW <= newW || nH <= newY ) {

                                            //Embedding Image Step 9: Do the cropping (image cropped based on placeholder dimension)
                                            //and move from "myTmpCanvas" to "myCanvas" (<canvas id="myCanvas"><canvas id="myTmpCanvas">)
                                            crop();

                                            if ($imgActive.attr('class') == 'img-circle') {
                                                jQuery('#my-mask').css('-webkit-border-radius', '500px');
                                                jQuery('#my-mask').css('-moz-border-radius', '500px');
                                                jQuery('#my-mask').css('border-radius', '500px');
                                            } else {
                                                jQuery('#my-mask').css('-webkit-border-radius', '0px');
                                                jQuery('#my-mask').css('-moz-border-radius', '0px');
                                                jQuery('#my-mask').css('border-radius', '0px');
                                            }

                                            if ($imgActive.prop("tagName").toLowerCase() == 'img') {

                                            } else {
                                                jQuery('#btnZoomIn').click(); jQuery('#btnZoomIn').click(); //fix bug
                                            }

                                            //Resize process completed, now display the controls
                                            jQuery("#divToolImgLoader").css('display', 'none');

                                            jQuery("#btnImageCancel").css('display', 'inline-block');
                                            jQuery("#btnZoomOut").css('display', 'inline-block');
                                            jQuery("#btnZoomIn").css('display', 'inline-block');
                                            jQuery("#btnImageMore").css('display', 'inline-block');
                                            jQuery("#btnChangeImage").css('display', 'inline-block');

                                            //Show overlay only after image is processed to prevent blank/dark image result problem.
                                            jQuery('.overlay-bg').css('background', '#fff');

                                            return;

                                        }
                                        tmp.src = tmpCanvas.toDataURL(type, quality);
                                        //sleep(1000);
                                    });
                                };
                                tmp.src = tmpCanvas.toDataURL( type, quality );
                                //sleep(1000);

                            });

                        } else {

                            //FIRST RENDER (tmpCanvas)
                            var context = tmpCanvas.getContext("2d");
                            //Set proper canvas dimension
                            if (4 < orientation_num && orientation_num < 9) {
                                tmpCanvas.width = cH;
                                tmpCanvas.height = cW;
                            } else {
                                tmpCanvas.width = cW;
                                tmpCanvas.height = cH;
                            }
                            //Fix orientation before drawing image
                            switch (orientation_num) {
                                case 2: context.transform(-1, 0, 0, 1, cW, 0); break;
                                case 3: context.transform(-1, 0, 0, -1, cW, cH ); break;
                                case 4: context.transform(1, 0, 0, -1, 0, cH ); break;
                                case 5: context.transform(0, 1, 1, 0, 0, 0); break;
                                case 6: context.transform(0, 1, -1, 0, cH , 0); break;
                                case 7: context.transform(0, -1, -1, 0, cH , cW); break;
                                case 8: context.transform(0, -1, 1, 0, 0, cW); break;
                                default: break;
                            }
                            context.drawImage( img, 0, 0, cW, cH );


                            //Embedding Image Step 8: Load chosen image in an IMG element ('<div id="my-mask"><img id="my-image"></div>)
                            //and set with the new dimension. Remember we have made the container (<div id="my-mask">) 2 times bigger.
                            jQuery('#my-image').attr('src', tmpCanvas.toDataURL(type, quality));


                            //RESIZE (tmpCanvas) with good quality.
                            var tmp = new Image();
                            var nW = nInitialWidth;
                            var nH = nInitialHeight;
                            tmp.onload = function() {
                                nW /= 2;
                                nH /= 2;
                                if ( nW < newW || nH < newY ) { nW = newW; nH = newY; }

                                tmpCanvas.width = nW;
                                tmpCanvas.height = nH;
                                context = tmpCanvas.getContext( '2d' );
                                context.drawImage( tmp, 0, 0, nW, nH );

                                if ( nW <= newW || nH <= newY ) {

                                    //Embedding Image Step 9: Do the cropping (image cropped based on placeholder dimension)
                                    //and move from "myTmpCanvas" to "myCanvas" (<canvas id="myCanvas"><canvas id="myTmpCanvas">)
                                    crop();

                                    if ($imgActive.attr('class') == 'img-circle') {
                                        jQuery('#my-mask').css('-webkit-border-radius', '500px');
                                        jQuery('#my-mask').css('-moz-border-radius', '500px');
                                        jQuery('#my-mask').css('border-radius', '500px');
                                    } else {
                                        jQuery('#my-mask').css('-webkit-border-radius', '0px');
                                        jQuery('#my-mask').css('-moz-border-radius', '0px');
                                        jQuery('#my-mask').css('border-radius', '0px');
                                    }

                                    //jQuery('#my-image').off('load'); //spy tdk load berulang2

                                    if ($imgActive.prop("tagName").toLowerCase() == 'img') {

                                    } else {
                                        jQuery('#btnZoomIn').click(); jQuery('#btnZoomIn').click(); //fix bug
                                    }

                                    //Resize process completed, now display the controls
                                    jQuery("#divToolImgLoader").css('display', 'none');

                                    jQuery("#btnImageCancel").css('display', 'inline-block');
                                    jQuery("#btnZoomOut").css('display', 'inline-block');
                                    jQuery("#btnZoomIn").css('display', 'inline-block');
                                    jQuery("#btnImageMore").css('display', 'inline-block');
                                    jQuery("#btnChangeImage").css('display', 'inline-block');

                                    //Show overlay only after image is processed to prevent blank/dark image result problem.
                                    jQuery('.overlay-bg').css('background', '#fff');

                                    return;
                                    }
                                tmp.src = tmpCanvas.toDataURL( type, quality );

                            };
                            tmp.src = tmpCanvas.toDataURL( type, quality );

                        }

                        jQuery('#btnImageMore').off('click');
                        jQuery('#btnImageMore').on('click', function () {

                            if( jQuery('#divImageMore').css('display') == 'block' ) {
                                jQuery('#divImageMore').css('display','none');
                            } else {
                                jQuery('#divImageMore').css('display','block');

                                jQuery('#chkImageNoCrop').attr('checked', false); //uncheck

                                if ($imgActive.parents('a:first').length == 0) {
                                    //link not exist
                                    jQuery('#chkImageClickToEnlarge').attr('checked', false);  //uncheck
                                } else {
                                    //link exist. Check if href is image
                                    if( $imgActive.parents('a:first').attr('href').toLowerCase().indexOf('.jpg') != -1 ||
                                    $imgActive.parents('a:first').attr('href').toLowerCase().indexOf('.png') != -1)
                                    jQuery('#chkImageClickToEnlarge').attr('checked', true);
                                }

                            }

                            jQuery('.overlay-bg').off('click');
                            jQuery('.overlay-bg').on('click', function () {
                                jQuery('#divImageMore').css('display','none');

                            });
                            jQuery('#my-mask').off('click');
                            jQuery('#my-mask').on('click', function () {
                                jQuery('#divImageMore').css('display','none');
                            });
                        });

                        jQuery('#btnImageMoreOk').off('click');
                        jQuery('#btnImageMoreOk').on('click', function () {

                            //If 'Click to Enlarge' is checked, create a dummy link with class='is-lightbox'
                            if( jQuery('#chkImageClickToEnlarge').is(':checked') ) {

                                var imagelink = '#';
                                if ($imgActive.parents('a:first').length == 0) {
                                    //create link
                                    $imgActive.wrap('<a href="' + imagelink + '"></a>');
                                } else {
                                    //apply link
                                    $imgActive.parents('a:first').attr('href', imagelink);
                                }

                                $imgActive.parents('a:first').attr('title', '');
                                $imgActive.parents('a:first').addClass('is-lightbox');
                                //$imgActive.parents('a:first').attr('target', '_blank');

                            }

                            //If 'No Crop' is checked
                            if( jQuery('#chkImageNoCrop').is(':checked') ) {

                                var canvasNoCrop = document.getElementById('myTmpCanvasNoCrop');
                                //var canvasNoCrop = document.getElementById('myTmpCanvas'); //can use this actually

                                //Embed Image
                                var image;
                                if (hiquality == false) {
                                    if (extension == 'jpg' || extension == 'jpeg') {
                                        image = canvasNoCrop.toDataURL("image/jpeg", 0.92); //bug: sometimes the result shows a black line on the image edge
                                    } else {
                                        image = canvasNoCrop.toDataURL("image/png", 1);
                                    }
                                } else {
                                    image = canvasNoCrop.toDataURL("image/png", 1);
                                }

                                if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                    $imgActive.attr('src', image);
                                    $imgActive.data('filename', imgname); //Set data attribute for filename
                                } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') {
                                    $imgActive.find('img').attr('src', image);
                                    $imgActive.find('img').data('filename', imgname); //Set data attribute for filename
                                } else {
                                    $imgActive.css('background-image', 'url(data:' + image + ')');
                                    $imgActive.data('filename', imgname); //Set data attribute for filename
                                }

                                //Upload larger image
                                if($imgActive.parent().hasClass("is-lightbox") ){
                                    //alert('larger')
                                    jQuery('#canvasform').attr('action', $element.data('imageembed').settings.largerImageHandler);
                                    jQuery('#canvasform').submit();

                                } else {
                                    //alert('normal')
                                    jQuery('.my-file[type=file]').clearInputs(); //=> won't upload the large file (by clearing file input.my-file)
                                }

                            } else {

                                //Do Cropping
                                jQuery('#btnChangeImage').click();

                            }

                            jQuery('#divImageEdit').css('display', 'none');
                            jQuery('.overlay-bg').css('width', '1px');
                            jQuery('.overlay-bg').css('height', '1px');
                            jQuery('body').css('overflow', '');

                            if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                $imgActive.css('width', '');
                                $imgActive.css('height', '');
                            } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') {
                                $imgActive.find('img').css('width', '');
                                $imgActive.find('img').css('height', '');
                            }

                            $element.data('imageembed').settings.onChanged();

                            //Finished Loading Image
                            jQuery("#divToolImgLoader").css('display', 'none');

                            jQuery('#divImageMore').css('display','none');

                            //Get Content Builder plugin
                            var builder;
                            $element.parents().each(function () {
                                if (jQuery(this).data('contentbuilder')) {
                                    builder = jQuery(this).data('contentbuilder');
                                }
                            });

                            //If 'Click to Enlarge' is unchecked, remove link (only if there is existing image link)
                            if( ! jQuery('#chkImageClickToEnlarge').is(':checked') ) {

                                //Check if there is existing image link
                                if( $imgActive.parents('a:first').length > 0 ){
                                    if( $imgActive.parents('a:first').attr('href').toLowerCase().indexOf('.jpg') != -1 ||
                                        $imgActive.parents('a:first').attr('href').toLowerCase().indexOf('.png') != -1) {

                                        //remove larger image link
                                        $imgActive.parents('a:first').replaceWith($imgActive.parents('a:first').html()); //This must be put at the end of this function, because this will make $element.data('imageembed') empty.

                                    }
                                }

                            }

                            //Apply Content Builder Behavior
                            if (builder) {
                                builder.applyBehavior();

                                builder.settings.onRender(); //Trigger Render event
                            }
                        });

                        //Embedding Image Step 10 (finish): When user click "Ok", read the result (base64 string) from "myCanvas"
                        //and assign it to image placeholder ($imgActive)
                        jQuery('#btnChangeImage').off('click');
                        jQuery('#btnChangeImage').on('click', function () {

                            var canvas = document.getElementById('myCanvas');

                            $imgActive = jQuery("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                            //Embed Image
                            var image;
                            if (hiquality == false) {
                                if (extension == 'jpg' || extension == 'jpeg') {
                                    image = canvas.toDataURL("image/jpeg", 0.92); //bug: sometimes the result shows a black line on the image edge
                                } else {
                                    image = canvas.toDataURL("image/png", 1);
                                }
                            } else {
                                image = canvas.toDataURL("image/png", 1);
                            }

                            if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                $imgActive.attr('src', image);
                                $imgActive.data('filename', imgname); //Set data attribute for filename
                            } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') {
                                $imgActive.find('img').attr('src', image);
                                $imgActive.find('img').data('filename', imgname); //Set data attribute for filename
                            } else {
                                $imgActive.css('background-image', 'url(data:' + image + ')');
                                $imgActive.data('filename', imgname); //Set data attribute for filename
                            }

                            //Upload larger image
                            if($imgActive.parent().hasClass("is-lightbox") && jQuery('#fileImage').val() !=''){
                                //alert('larger')
                                jQuery('#canvasform').attr('action', $element.data('imageembed').settings.largerImageHandler);
                                jQuery('#canvasform').submit();

                            } else {
                                //alert('normal')
                                jQuery('.my-file[type=file]').clearInputs(); //=> won't upload the large file (by clearing file input.my-file)
                            }

                            jQuery('#divImageEdit').css('display', 'none');
                            jQuery('.overlay-bg').css('width', '1px');
                            jQuery('.overlay-bg').css('height', '1px');
                            jQuery('body').css('overflow', '');

                            if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                $imgActive.css('width', '');
                                $imgActive.css('height', '');
                            } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') {
                                $imgActive.find('img').css('width', '');
                                $imgActive.find('img').css('height', '');
                            }

                            $element.data('imageembed').settings.onChanged();

                            jQuery('#divImageMore').css('display','none');

                        });

                        jQuery('#btnImageCancel').off('click');
                        jQuery('#btnImageCancel').on('click', function () {
                            var canvas = document.getElementById('myCanvas');

                            $imgActive = jQuery("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                            jQuery('#divImageEdit').css('display', 'none');
                            jQuery('.overlay-bg').css('width', '1px');
                            jQuery('.overlay-bg').css('height', '1px');
                            jQuery('body').css('overflow', '');

                            jQuery('#divImageMore').css('display','none');

                            jQuery('.my-file[type=file]').clearInputs();

                        });

                        jQuery('#btnZoomIn').off('click');
                        jQuery('#btnZoomIn').on('click', function () {

                            var nCurrentWidth = parseInt(jQuery("#my-image").css('width'));
                            var nCurrentHeight = parseInt(jQuery("#my-image").css('height'));

                            //if (nInitialWidth <= (nCurrentWidth / 0.9)) return;
                            //if (nInitialHeight <= (nCurrentHeight / 0.9)) return;

                            jQuery("#my-image").css('width', (nCurrentWidth / 0.9) + 'px');
                            jQuery("#my-image").css('height', (nCurrentHeight / 0.9) + 'px');

                            panSetup();

                            tmpCanvas.width = (nCurrentWidth / 0.9);
                            tmpCanvas.height = (nCurrentHeight / 0.9);

                            var imageObj = jQuery("#my-image")[0];
                            var context = tmpCanvas.getContext('2d');

                            //Fix to resize image with good quality.
                            var tmp = new Image(), context, cW, cH;
                            cW = nInitialWidth;
                            cH = nInitialHeight;
                            tmp.src = imageObj.src;
                            tmp.onload = function() {
                                cW /= 2;
                                cH /= 2;
                                if ( cW < imageObj.width ) cW = (nCurrentWidth / 0.9);
                                if ( cH < imageObj.height ) cH = (nCurrentHeight / 0.9);

                                tmpCanvas.width = cW;
                                tmpCanvas.height = cH;
                                context = tmpCanvas.getContext( '2d' );
                                context.drawImage( tmp, 0, 0, cW, cH );

                                if ( cW <= (nCurrentWidth / 0.9) || cH <= (nCurrentHeight / 0.9) ) {
                                    panSetup();crop(); //just to refresh. If not, myTmpCanvas (resized) won't not copied to myCanvas (cropped).
                                    return;
                                    }
                                tmp.src = tmpCanvas.toDataURL( type, quality );
                            };
                            //context.drawImage(imageObj, 0, 0, (nCurrentWidth / 0.9), (nCurrentHeight / 0.9)); //This is replaced with the above fix.

                            crop();

                        });

                        jQuery('#btnZoomOut').off('click');
                        jQuery('#btnZoomOut').on('click', function () {

                            var nCurrentWidth = parseInt(jQuery("#my-image").css('width'));
                            var nCurrentHeight = parseInt(jQuery("#my-image").css('height'));

                            if ( (nCurrentWidth / 1.1) < jQuery("#my-mask").width()) return;
                            if ( (nCurrentHeight / 1.1) < jQuery("#my-mask").height()) return;

                            //if ((nCurrentWidth / 1.1) >= parseInt(jQuery("#my-mask").css('width')) && (nCurrentHeight / 1.1) >= parseInt(jQuery("#my-mask").css('height'))) {
                            jQuery("#my-image").css('width', (nCurrentWidth / 1.1) + 'px');
                            jQuery("#my-image").css('height', (nCurrentHeight / 1.1) + 'px');

                            panSetup();

                            tmpCanvas.width = (nCurrentWidth / 1.1);
                            tmpCanvas.height = (nCurrentHeight / 1.1);

                            var imageObj = jQuery("#my-image")[0];
                            var context = tmpCanvas.getContext('2d');

                            //Fix to resize image with good quality.
                            var tmp = new Image(), context, cW, cH;
                            cW = nInitialWidth;
                            cH = nInitialHeight;
                            tmp.src = imageObj.src;
                            tmp.onload = function() {
                                cW /= 2;
                                cH /= 2;
                                if ( cW < imageObj.width ) cW = (nCurrentWidth / 1.1);
                                if ( cH < imageObj.height ) cH = (nCurrentHeight / 1.1);

                                tmpCanvas.width = cW;
                                tmpCanvas.height = cH;
                                context = tmpCanvas.getContext( '2d' );
                                context.drawImage( tmp, 0, 0, cW, cH );

                                if ( cW <= (nCurrentWidth / 1.1) || cH <= (nCurrentHeight / 1.1) ) {
                                    panSetup();crop(); //just to refresh. If not, myTmpCanvas (resized) won't not copied to myCanvas (cropped).
                                    return;
                                    }
                                tmp.src = tmpCanvas.toDataURL( type, quality );
                            };
                            //context.drawImage(imageObj, 0, 0, (nCurrentWidth / 1.1), (nCurrentHeight / 1.1)); //This is replaced with the above fix.

                            crop();

                        });
                        /*
                        sleep(500);
                        jQuery('#btnZoomIn').click();
                        sleep(500);
                        jQuery('#btnZoomOut').click();
                        */
                    },
                    {
                        canvas: false
                    }
                );

            });
        };



        var crop = function () {
            //Crop & move from "myTmpCanvas" to "myCanvas" (<canvas id="myCanvas"><canvas id="myTmpCanvas">)

            var maskAdj = 1.1; //adjustment
            var x = parseInt(jQuery("#my-image").css('left')) - maskAdj;
            var y = parseInt(jQuery("#my-image").css('top')) - maskAdj;

            var dw = parseInt(jQuery("#my-mask").css('width'));
            var dh = parseInt(jQuery("#my-mask").css('height'));

            var canvas = document.getElementById('myCanvas');
            var context = canvas.getContext('2d');
            canvas.width = dw;
            canvas.height = dh;

            var sourceX = -1 * x;
            var sourceY = -1 * y;

            if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) { //adjustment
                var iosAdj = 0.7;
                sourceX = -1 * x + (x - x/iosAdj);
                sourceY = -1 * y + (y - y/iosAdj);
            }

            /* checking
            alert(sourceX + ' - ' + sourceY); // 1.1 - 3.3
            alert("tmpCanvas.height=" + tmpCanvas.height + " | dh=" + dh);
            alert("tmpCanvas.width=" + tmpCanvas.width + " | dw=" + dw);
            */

            //Prevent blank area
            if (sourceY > (tmpCanvas.height - dh)) { sourceY = tmpCanvas.height - dh; }
            if (sourceX > (tmpCanvas.width - dw)) { sourceX = tmpCanvas.width - dw; }

            context.drawImage(tmpCanvas, sourceX, sourceY, dw, dh, 0, 0, dw, dh);
        };

        /* source: http://stackoverflow.com/questions/1590840/drag-a-zoomed-image-within-a-div-clipping-mask-using-jquery-draggable */
        var panSetup = function () {

            jQuery("#my-image").css({ top: 0, left: 0 });

            var maskWidth = jQuery("#my-mask").width();
            var maskHeight = jQuery("#my-mask").height();
            var imgPos = jQuery("#my-image").offset();
            var imgWidth = jQuery("#my-image").width();
            var imgHeight = jQuery("#my-image").height();

            var x1 = (imgPos.left + maskWidth) - imgWidth;
            var y1 = (imgPos.top + maskHeight) - imgHeight;
            var x2 = imgPos.left;
            var y2 = imgPos.top;

            jQuery("#my-image").draggable({
                revert: false, containment: [x1, y1, x2, y2], scroll: false, drag: function () {

                    crop();
                }
            });
            jQuery("#my-image").css({ cursor: 'move' });
        };

        this.init();

    };

    jQuery.fn.imageembed = function (options) {
        return this.each(function () {

            if (undefined == jQuery(this).data('imageembed')) {
                var plugin = new jQuery.imageembed(this, options);
                jQuery(this).data('imageembed', plugin);

            }
        });
    };
})(jQuery);

function applyLargerImage(s){
    $imgActive.parents("a").attr("href",s);
    jQuery('.my-file[type=file]').clearInputs();
}

/* Utils */
function makeid() {//http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    for (var i = 0; i < 2; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    var text2 = "";
    var possible2 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (var i = 0; i < 5; i++)
        text2 += possible2.charAt(Math.floor(Math.random() * possible2.length));

    return text + text2;
}
function sleep(milliseconds) {//http://www.phpied.com/sleep-in-javascript/
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}
function getScripts(scripts, callback) {
    var progress = 0;
    scripts.forEach(function (script) {
        $.getScript(script, function () {
            if (++progress == scripts.length) callback();
        });
    });
}

/*******************************************************************************************/


/*
source:
http://stackoverflow.com/questions/1043957/clearing-input-type-file-using-jquery
https://github.com/malsup/form/blob/master/jquery.form.js
*/
jQuery.fn.clearFields = jQuery.fn.clearInputs = function (includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function () {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
        else if (t == "file") {
            if (/MSIE/.test(navigator.userAgent)) {
                jQuery(this).replaceWith(jQuery(this).clone(true));
            } else {
                jQuery(this).val('');
            }
        }
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  jQuery('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ((includeHidden === true && /hidden/.test(t)) ||
                (typeof includeHidden == 'string' && jQuery(this).is(includeHidden)))
                this.value = '';
        }
    });
};



/* Simple Modal - Inspired by modalEffects.js from http://www.codrops.com , http://tympanus.net/codrops/2013/06/25/nifty-modal-window-effects/ */
var zindex = 10000;
(function (jQuery) {

    jQuery.simplemodal = function (element, options) {

        var defaults = {
            onCancel: function () { },
            onFinish: function () { },
            isModal: false,
            noOverlay: false
        };

        this.settings = {};

        var $element = jQuery(element),
             element = element;

        var $ovlid;

        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

            //var html_overlay = '<div class="md-overlay"></div>';
            //if (jQuery('.md-overlay').length == 0) jQuery('body').append(html_overlay);

            /**** Localize All ****/
            if (jQuery('#divCb').length == 0) {
                jQuery('body').append('<div id="divCb"></div>');
            }

        };

        this.hide = function () {
            $element.css('display', 'none');
            $element.removeClass('md-show');

            if(!this.settings.noOverlay){
                $ovlid.remove();
            }

			zindex = zindex-2;

            $element.data('simplemodal').settings.onFinish();
        };

        this.show = function (savedSel) {

			zindex = zindex+1;

            if(!this.settings.noOverlay){
                var rnd = makeid();
                var html_overlay = '<div id="md-overlay-' + rnd + '" class="md-overlay" style="z-index:' + zindex + '"></div>';
                if(this.settings.isModal){
                    html_overlay = '<div id="md-overlay-' + rnd + '" class="md-overlay" style="z-index:' + zindex + ';background:rgba(0, 0, 0, 0.1)"></div>';
                }
                jQuery('#divCb').append(html_overlay);
                $ovlid = jQuery('#md-overlay-' + rnd);
            }

            /*setTimeout(function () {
                $element.addClass('md-show');
            }, 1);*/

			zindex = zindex+1;
			$element.css('z-index',zindex);

            $element.addClass('md-show');
            $element.stop(true, true).css('display', 'none').fadeIn(200);

            if($element.hasClass('md-draggable')){
                var mw = parseInt($element.css("width"));
                var mh = parseInt($element.css("height"));
                $element.css("top", Math.max(0, (jQuery(window).height() - mh) / 2) +  "px");
                $element.css("left", Math.max(0, (jQuery(window).width() - mw) / 2) + "px");

                if($element.find('.md-modal-handle').length > 0){
                    $element.find('.md-modal-handle').css("cursor", "move");
                    $element.draggable({ handle: ".md-modal-handle" });
                } else {
                    $element.draggable();
                }
            }

            if($element.find('.md-modal-close').length > 0){
                $element.find('.md-modal-close').click(function(){

                    $element.data('simplemodal').hide();

                });
            }

            if(!this.settings.noOverlay){
                var savedSel = savedSel;
                jQuery('#md-overlay-' + rnd).off('click');
                jQuery('#md-overlay-' + rnd).click(function () {
                    if($element.data('simplemodal').settings.isModal) return;

                    $element.stop(true, true).fadeOut(100, function(){
                        $element.removeClass('md-show');
                    });
                    $ovlid.remove();//

				    zindex = zindex-2;

                    if(savedSel) restoreSelection(savedSel);

                    $element.data('simplemodal').settings.onCancel();
                });
            }

        };

        this.init();
    };

    jQuery.fn.simplemodal = function (options) {

        return this.each(function () {

            if (undefined == jQuery(this).data('simplemodal')) {
                var plugin = new jQuery.simplemodal(this, options);
                jQuery(this).data('simplemodal', plugin);

            }

        });
    };
})(jQuery);


/* Undo Redo */
jQuery(document).keydown(function(e) {
    //console.log(e.which)
    //Undo Redo
    if (e.which === 90 && e.ctrlKey) {//CTRL-Z
        if(e.shiftKey) doRedo();
        else {
            if (!e.altKey) doUndo();
        }
    }
    if (e.which === 89 && e.ctrlKey) {//CTRL-Y
        if (!e.altKey) doRedo();
    }
});

function saveForUndo() {

    var bChanged = false;

    jQuery(cb_list).each(function(){
        var $cb = jQuery(this);
        var $el = $cb.data('contentbuilder');

        if ($el.undoList[0]) {
            if ($cb.html() != $el.undoList[0][0]) bChanged=true;
        } else {
            bChanged = true;
        }
    });

    if(!bChanged)return;

    jQuery(cb_list).each(function(){
        var $cb = jQuery(this);
        var $el = $cb.data('contentbuilder');

        //if ($el.undoList[0])
        //    if ($cb.html() == $el.undoList[0][0]) return;

        for (var i = 20; i > 1; i--) $el.undoList[i - 1] = $el.undoList[i - 2];

        var curr;
        if (window.getSelection) {
            try{
                curr = window.getSelection().getRangeAt(0);
                $el.undoList[0] = [$cb.html(), curr.cloneRange()];
            } catch(e) {
                $el.undoList[0] = [$cb.html(), null];
            }
        }
        else if (document.selection) {
            try{
                curr = document.selection.createRange();
                var type = document.selection.type;

                if (type == "Text")
                    $el.undoList[0] = [$cb.html(), curr.getBookmark(), "Text"];
                else if (type == "Control") {
                    curr.item(0).selThis = "selThis";
                    $el.undoList[0] = [$cb.html(), null, "Control"];
                    curr.item(0).removeAttribute("selThis", 0);
                } else {
                    $el.undoList[0] = [$cb.html(), curr.getBookmark(), "None"];
                }
            } catch(e) {
                if (type == "Text")
                    $el.undoList[0] = [$cb.html(), null, "Text"];
                else if (type == "Control") {
                    curr.item(0).selThis = "selThis";
                    $el.undoList[0] = [$cb.html(), null, "Control"];
                    curr.item(0).removeAttribute("selThis", 0);
                } else {
                    $el.undoList[0] = [$cb.html(), null, "None"];
                }
            }

        }

        $el.redoList = []; //clear redo list

    });
}

var numUndo = 0;
function doUndo(){
    var bChanged = false;

    jQuery(cb_list).each(function(){
        var $cb = jQuery(this);
        var $el = $cb.data('contentbuilder');

        if ($el.undoList[0]) {
            if ($cb.html() != $el.undoList[0][0]) bChanged=true;
        } else {
            bChanged = true;
        }
    });

    jQuery(cb_list).each(function(){
        var $cb = jQuery(this);
        var $el = $cb.data('contentbuilder');

        if (!$el.undoList[0]) return;

        for (var i = 20; i > 1; i--) $el.redoList[i - 1] = $el.redoList[i - 2];

        var curr;
        if (window.getSelection) {
            try{
                curr = window.getSelection().getRangeAt(0);
                $el.redoList[0] = [$cb.html(), curr.cloneRange()];
            } catch(e) {
                $el.redoList[0] = [$cb.html(), null];
            }
        }
        else if (document.selection) {
            curr = document.selection.createRange();
            var type = document.selection.type;

            if (type == "Text")
                $el.redoList[0] = [$cb.html(), curr.getBookmark(), "Text"];
            else if (type == "Control") {
                curr.item(0).selThis = "selThis";
                $el.redoList[0] = [$cb.html(), null, "Control"];
                curr.item(0).removeAttribute("selThis", 0);
            } else {
                $el.redoList[0] = [$cb.html(), curr.getBookmark(), "None"];
            }
        }

        sHTML = $el.undoList[0][0];
        $cb.html(sHTML);

        for (var i = 0; i < 19; i++) $el.undoList[i] = $el.undoList[i + 1];
        $el.undoList[19] = null;

        //Apply builder behaviors
        $el.applyBehavior();

        // Function to run when column/grid changed
        $el.blockChanged();

        //Trigger Render event
        $el.settings.onRender();

    });

    //console.log('undo');
    if(bChanged==false && numUndo<1) {
        numUndo=numUndo+1;
        doUndo();
        return;
    }
    numUndo=0;
}

function doRedo(){

    jQuery(cb_list).each(function(){
        var $cb = jQuery(this);
        var $el = $cb.data('contentbuilder');

        if (!$el.redoList[0]) return;

        for (var i = 20; i > 1; i--) $el.undoList[i - 1] = $el.undoList[i - 2];

        var curr;
        if (window.getSelection) {
            try{
                curr = window.getSelection().getRangeAt(0);
                $el.undoList[0] = [$cb.html(), curr.cloneRange()];
            } catch(e) {
                $el.undoList[0] = [$cb.html(), null];
            }
        }
        else if (document.selection) {
            curr = document.selection.createRange();
            var type = document.selection.type;

            if (type == "Text")
                $el.undoList[0] = [$cb.html(), curr.getBookmark(), "Text"];
            else if (type == "Control") {
                curr.item(0).selThis = "selThis";
                $el.undoList[0] = [$cb.html(), null, "Control"];
                curr.item(0).removeAttribute("selThis", 0);
            } else {
                $el.undoList[0] = [$cb.html(), curr.getBookmark(), "None"];
            }
        }

        sHTML = $el.redoList[0][0];
        $cb.html(sHTML);

        for (var i = 0; i < 19; i++) $el.redoList[i] = $el.redoList[i + 1];
        $el.redoList[19] = null;

        //Apply builder behaviors
        $el.applyBehavior();

        // Function to run when column/grid changed
        $el.blockChanged();

        //Trigger Render event
        $el.settings.onRender();

    });
}


/* source: http://stackoverflow.com/questions/1002934/jquery-x-y-document-coordinates-of-dom-object */
jQuery.fn.getPos = function () {
    var o = this[0];
    var left = 0, top = 0, parentNode = null, offsetParent = null;
    offsetParent = o.offsetParent;
    var original = o;
    var el = o;
    while (el.parentNode != null) {
        el = el.parentNode;
        if (el.offsetParent != null) {
            var considerScroll = true;
            if (window.opera) {
                if (el == original.parentNode || el.nodeName == "TR") {
                    considerScroll = false;
                }
            }
            if (considerScroll) {
                if (el.scrollTop && el.scrollTop > 0) {
                    top -= el.scrollTop;
                }
                if (el.scrollLeft && el.scrollLeft > 0) {
                    left -= el.scrollLeft;
                }
            }
        }
        if (el == offsetParent) {
            left += o.offsetLeft;
            if (el.clientLeft && el.nodeName != "TABLE") {
                left += el.clientLeft;
            }
            top += o.offsetTop;
            if (el.clientTop && el.nodeName != "TABLE") {
                top += el.clientTop;
            }
            o = el;
            if (o.offsetParent == null) {
                if (o.offsetLeft) {
                    left += o.offsetLeft;
                }
                if (o.offsetTop) {
                    top += o.offsetTop;
                }
            }
            offsetParent = o.offsetParent;
        }
    }
    return {
        left: left,
        top: top
    };
};

//Clean Word. Source: http://patisserie.keensoftware.com/en/pages/remove-word-formatting-from-rich-text-editor-with-javascript
//http://community.sitepoint.com/t/strip-unwanted-formatting-from-pasted-content/16848/3
//Other: http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php
function cleanHTML(input) {
    var stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
    var output = input.replace(stringStripper, ' ');

    var commentSripper = new RegExp('<!--(.*?)-->', 'g');
    var output = output.replace(commentSripper, '');
    var tagStripper = new RegExp('<(/)*(meta|link|span|\\?xml:|st1:|o:|font)(.*?)>', 'gi');

    output = output.replace(tagStripper, '');

    var badTags = ['style', 'script', 'applet', 'embed', 'noframes', 'noscript'];

    for (var i = 0; i < badTags.length; i++) {
        tagStripper = new RegExp('<' + badTags[i] + '.*?' + badTags[i] + '(.*?)>', 'gi');
        output = output.replace(tagStripper, '');
    }

    var badAttributes = ['style', 'start'];
    for (var i = 0; i < badAttributes.length; i++) {
        var attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"', 'gi');
        output = output.replace(attributeStripper, '');
    }
    return output;
}

function selectRange(range) {
    if (range) {
        if (typeof range.select != "undefined") {
            range.select();
        } else if (typeof window.getSelection != "undefined") {
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
}

/* https://stackoverflow.com/questions/6139107/programmatically-select-text-in-a-contenteditable-html-element */
function selectElementContents(el) {
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
}

/* Table */
function isEven(someNumber) {
    return (someNumber % 2 == 0) ? true : false;
}

function getCellIndex(oTable, oTR, oTD) {
    var nCount = 0;
    var bFinish = false;
    for (var i = 0; i < oTR.cells.length; i++) {
        if (bFinish == false) {
            nCount += oTR.cells[i].colSpan;
        }
        if (oTD == oTR.cells[i]) bFinish = true;
    }
    nCount = nCount - (oTD.colSpan - 1);

    var nCellIndex = nCount - 1;
    return nCellIndex;
}

/* Browser */
function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');
    var edge = ua.indexOf('Edge/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (edge > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    // other browser
    return false;
}

function detectEdge() {
    var ua = window.navigator.userAgent;
    var edge = ua.indexOf('Edge/');

    if (edge > 0) {
        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    return false;
}

/* http://stackoverflow.com/questions/11867545/change-text-color-based-on-brightness-of-the-covered-background-area */
(function ($) {
    $.fn.contrastingText = function () {
        var el = this,
            transparent;
        transparent = function (c) {
            var m = c.match(/[0-9]+/g);
            if (m !== null) {
                return !!m[3];
            }
            else return false;
        };
        while (transparent(el.css('background-color'))) {
            el = el.parent();
        }
        parts = el.css('background-color').match(/[0-9]+/g);
        this.lightBackground = !!Math.round(
            (
                parseInt(parts[0], 10) + // red
                parseInt(parts[1], 10) + // green
                parseInt(parts[2], 10) // blue
            ) / 765 // 255 * 3, so that we avg, then normalise to 1
        );
        if (this.lightBackground) {
            this.css('color', 'black');
        } else {
            this.css('color', 'rgba(255, 255, 255, 0.7)');
        }
        return this;
    };
}(jQuery));

/* Load CodeMirror script */
getScripts([sScriptPath + "codemirror/lib/codemirror.js"],
    function () {
        getScripts([sScriptPath + "codemirror/mode/xml/xml.js",
            sScriptPath + "codemirror/mode/javascript/javascript.js",
            sScriptPath + "codemirror/mode/css/css.js"],
            function () {
                jQuery('body').addClass('is-cmloaded');
            });
    });


/*! Mega pixel image rendering library for iOS6 Safari | Copyright (c) 2012 Shinichi Tomita <shinichi.tomita@gmail.com> | MIT license | https://github.com/stomita/ios-imagefile-megapixel */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(n(){n 1n(h){9 p=h.U,m=h.M;f(p*m>1b*1b){9 e=O.Q(\'e\');e.a=e.b=1;9 c=e.W(\'X\');c.12(h,-p+1,0);y c.1h(0,0,1,1).11[3]===0}x{y 1Z}}n 1p(h,p,m){9 e=O.Q(\'e\');e.a=1;e.b=m;9 c=e.W(\'X\');c.12(h,0,0);9 11=c.1h(0,0,1,m).11;9 r=0;9 16=m;9 s=m;17(s>r){9 1q=11[(s-1)*4+3];f(1q===0){16=s}x{r=s}s=(16+r)>>1}9 18=(s/m);y(18===0)?1:18}n 1j(h,j,v){9 e=O.Q(\'e\');1c(h,e,j,v);y e.1X("1v/1w",j.1V||0.8)}n 1c(h,e,j,v){9 p=h.U,m=h.M;f(!(p+m))y;9 a=j.a,b=j.b;9 c=e.W(\'X\');c.1U();1i(e,c,a,b,j.10);9 1k=1n(h);f(1k){p/=2;m/=2}9 d=1b;9 F=O.Q(\'e\');F.a=F.b=d;9 Z=F.W(\'X\');9 1u=v?1p(h,p,m):1;9 1d=w.1x(d*a/p);9 14=w.1x(d*b/m/1u);9 r=0;9 1a=0;17(r<m){9 P=0;9 19=0;17(P<p){Z.1S(0,0,d,d);Z.12(h,-P,-r);c.12(F,0,0,d,d,19,1a,1d,14);P+=d;19+=1d}r+=d;1a+=14}c.1Q();F=Z=T}n 1i(e,c,a,b,10){1m(10){o 5:o 6:o 7:o 8:e.a=b;e.b=a;q;1r:e.a=a;e.b=b}1m(10){o 2:c.C(a,0);c.Y(-1,1);q;o 3:c.C(a,b);c.L(w.G);q;o 4:c.C(0,b);c.Y(1,-1);q;o 5:c.L(0.5*w.G);c.Y(1,-1);q;o 6:c.L(0.5*w.G);c.C(0,-b);q;o 7:c.L(0.5*w.G);c.C(a,-b);c.Y(-1,1);q;o 8:c.L(-0.5*w.G);c.C(-a,0);q;1r:q}}9 t=u.t&&u.t.13?u.t:u.15&&u.15.13?u.15:T;n E(l){f(u.1l&&l 1P 1l){f(!t){1N 1M("1L 13 n 1K 1I 1H A 1G");}9 h=1D 1C();h.1e=t.13(l);g.A=l;l=h}f(!l.U&&!l.M){9 I=g;l.1O=l.1B=n(){9 S=I.H;f(S){I.H=T;1y(9 i=0,1t=S.1E;i<1t;i++){S[i]()}}};g.H=[]}g.l=l}E.1F.1s=n(z,j,N){f(g.H){9 I=g;g.H.1J(n(){I.1s(z,j,N)});y}j=j||{};9 B=g.l.U,D=g.l.M,a=j.a,b=j.b,J=j.J,K=j.K,v=!g.A||g.A.1A===\'1v/1w\';f(a&&!b){b=(D*a/B)<<0}x f(b&&!a){a=(B*b/D)<<0}x{a=B;b=D}f(J&&a>J){a=J;b=(D*a/B)<<0}f(K&&b>K){b=K;a=(B*b/D)<<0}9 V={a:a,b:b};1y(9 k 1R j)V[k]=j[k];9 R=z.R.1T();f(R===\'h\'){z.1e=1j(g.l,V,v)}x f(R===\'e\'){1c(g.l,z,V,v)}f(1g g.1z===\'n\'){g.1z(z)}f(N){N()}f(g.A){g.A=T;t.1W(g.l.1e)}};f(1g 1f===\'n\'&&1f.1Y){1f([],n(){y E})}x f(1g 1o===\'20\'){21.1o=E}x{g.E=E}})();',62,126,'|||||||||var|width|height|ctx||canvas|if|this|img||options||srcImage|ih|function|case|iw|break|sy|py|URL|window|doSquash|Math|else|return|target|blob|imgWidth|translate|imgHeight|MegaPixImage|tmpCanvas|PI|imageLoadListeners|_this|maxWidth|maxHeight|rotate|naturalHeight|callback|document|sx|createElement|tagName|listeners|null|naturalWidth|opt|getContext|2d|scale|tmpCtx|orientation|data|drawImage|createObjectURL|dh|webkitURL|ey|while|ratio|dx|dy|1024|renderImageToCanvas|dw|src|define|typeof|getImageData|transformCoordinate|renderImageToDataURL|subsampled|Blob|switch|detectSubsampling|exports|detectVerticalSquash|alpha|default|render|len|vertSquashRatio|image|jpeg|ceil|for|onrender|type|onerror|Image|new|length|prototype|url|create|to|push|found|No|Error|throw|onload|instanceof|restore|in|clearRect|toLowerCase|save|quality|revokeObjectURL|toDataURL|amd|false|object|module'.split('|'),0,{}));

/*! jQuery UI Touch Punch 0.2.3 | Copyright 2011–2014, Dave Furfero | Dual licensed under the MIT or GPL Version 2 licenses. */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(7(4){4.w.8=\'H\'G p;c(!4.w.8){f}d 6=4.U.D.L,g=6.g,h=6.h,a;7 5(2,r){c(2.k.F.J>1){f}2.B();d 8=2.k.q[0],l=p.N(\'O\');l.S(r,i,i,V,1,8.W,8.X,8.Y,8.A,b,b,b,b,0,C);2.z.E(l)}6.m=7(2){d 3=e;c(a||!3.I(2.k.q[0])){f}a=i;3.j=b;5(2,\'K\');5(2,\'s\');5(2,\'M\')};6.n=7(2){c(!a){f}e.j=i;5(2,\'s\')};6.o=7(2){c(!a){f}5(2,\'P\');5(2,\'Q\');c(!e.j){5(2,\'R\')}a=b};6.g=7(){d 3=e;3.u.T({v:4.9(3,\'m\'),x:4.9(3,\'n\'),y:4.9(3,\'o\')});g.t(3)};6.h=7(){d 3=e;3.u.Z({v:4.9(3,\'m\'),x:4.9(3,\'n\'),y:4.9(3,\'o\')});h.t(3)}})(4);',62,62,'||event|self|jQuery|simulateMouseEvent|mouseProto|function|touch|proxy|touchHandled|false|if|var|this|return|_mouseInit|_mouseDestroy|true|_touchMoved|originalEvent|simulatedEvent|_touchStart|_touchMove|_touchEnd|document|changedTouches|simulatedType|mousemove|call|element|touchstart|support|touchmove|touchend|target|clientY|preventDefault|null|mouse|dispatchEvent|touches|in|ontouchend|_mouseCapture|length|mouseover|prototype|mousedown|createEvent|MouseEvents|mouseup|mouseout|click|initMouseEvent|bind|ui|window|screenX|screenY|clientX|unbind'.split('|'),0,{}));

/*! tinyColorPicker - v1.1.1 2016-08-30 | (c) 2016 Peter Dematté | MIT license | http://www.dematte.at/tinyColorPicker/ */
!function(a,b){"object"==typeof exports?module.exports=b(a):"function"==typeof define&&define.amd?define("colors",[],function(){return b(a)}):a.Colors=b(a)}(this,function(a,b){"use strict";function c(a,c,d,f,g){if("string"==typeof c){var c=v.txt2color(c);d=c.type,p[d]=c[d],g=g!==b?g:c.alpha}else if(c)for(var h in c)a[d][h]=k(c[h]/l[d][h][1],0,1);return g!==b&&(a.alpha=k(+g,0,1)),e(d,f?a:b)}function d(a,b,c){var d=o.options.grey,e={};return e.RGB={r:a.r,g:a.g,b:a.b},e.rgb={r:b.r,g:b.g,b:b.b},e.alpha=c,e.equivalentGrey=n(d.r*a.r+d.g*a.g+d.b*a.b),e.rgbaMixBlack=i(b,{r:0,g:0,b:0},c,1),e.rgbaMixWhite=i(b,{r:1,g:1,b:1},c,1),e.rgbaMixBlack.luminance=h(e.rgbaMixBlack,!0),e.rgbaMixWhite.luminance=h(e.rgbaMixWhite,!0),o.options.customBG&&(e.rgbaMixCustom=i(b,o.options.customBG,c,1),e.rgbaMixCustom.luminance=h(e.rgbaMixCustom,!0),o.options.customBG.luminance=h(o.options.customBG,!0)),e}function e(a,b){var c,e,k,q=b||p,r=v,s=o.options,t=l,u=q.RND,w="",x="",y={hsl:"hsv",rgb:a},z=u.rgb;if("alpha"!==a){for(var A in t)if(!t[A][A]){a!==A&&(x=y[A]||"rgb",q[A]=r[x+"2"+A](q[x])),u[A]||(u[A]={}),c=q[A];for(w in c)u[A][w]=n(c[w]*t[A][w][1])}z=u.rgb,q.HEX=r.RGB2HEX(z),q.equivalentGrey=s.grey.r*q.rgb.r+s.grey.g*q.rgb.g+s.grey.b*q.rgb.b,q.webSave=e=f(z,51),q.webSmart=k=f(z,17),q.saveColor=z.r===e.r&&z.g===e.g&&z.b===e.b?"web save":z.r===k.r&&z.g===k.g&&z.b===k.b?"web smart":"",q.hueRGB=v.hue2RGB(q.hsv.h),b&&(q.background=d(z,q.rgb,q.alpha))}var B,C,D,E=q.rgb,F=q.alpha,G="luminance",H=q.background;return B=i(E,{r:0,g:0,b:0},F,1),B[G]=h(B,!0),q.rgbaMixBlack=B,C=i(E,{r:1,g:1,b:1},F,1),C[G]=h(C,!0),q.rgbaMixWhite=C,s.customBG&&(D=i(E,H.rgbaMixCustom,F,1),D[G]=h(D,!0),D.WCAG2Ratio=j(D[G],H.rgbaMixCustom[G]),q.rgbaMixBGMixCustom=D,D.luminanceDelta=m.abs(D[G]-H.rgbaMixCustom[G]),D.hueDelta=g(H.rgbaMixCustom,D,!0)),q.RGBLuminance=h(z),q.HUELuminance=h(q.hueRGB),s.convertCallback&&s.convertCallback(q,a),q}function f(a,b){var c={},d=0,e=b/2;for(var f in a)d=a[f]%b,c[f]=a[f]+(d>e?b-d:-d);return c}function g(a,b,c){return(m.max(a.r-b.r,b.r-a.r)+m.max(a.g-b.g,b.g-a.g)+m.max(a.b-b.b,b.b-a.b))*(c?255:1)/765}function h(a,b){for(var c=b?1:255,d=[a.r/c,a.g/c,a.b/c],e=o.options.luminance,f=d.length;f--;)d[f]=d[f]<=.03928?d[f]/12.92:m.pow((d[f]+.055)/1.055,2.4);return e.r*d[0]+e.g*d[1]+e.b*d[2]}function i(a,c,d,e){var f={},g=d!==b?d:1,h=e!==b?e:1,i=g+h*(1-g);for(var j in a)f[j]=(a[j]*g+c[j]*h*(1-g))/i;return f.a=i,f}function j(a,b){var c=1;return c=a>=b?(a+.05)/(b+.05):(b+.05)/(a+.05),n(100*c)/100}function k(a,b,c){return a>c?c:b>a?b:a}var l={rgb:{r:[0,255],g:[0,255],b:[0,255]},hsv:{h:[0,360],s:[0,100],v:[0,100]},hsl:{h:[0,360],s:[0,100],l:[0,100]},alpha:{alpha:[0,1]},HEX:{HEX:[0,16777215]}},m=a.Math,n=m.round,o={},p={},q={r:.298954,g:.586434,b:.114612},r={r:.2126,g:.7152,b:.0722},s=function(a){this.colors={RND:{}},this.options={color:"rgba(0,0,0,0)",grey:q,luminance:r,valueRanges:l},t(this,a||{})},t=function(a,d){var e,f=a.options;u(a);for(var g in d)d[g]!==b&&(f[g]=d[g]);e=f.customBG,f.customBG="string"==typeof e?v.txt2color(e).rgb:e,p=c(a.colors,f.color,b,!0)},u=function(a){o!==a&&(o=a,p=a.colors)};s.prototype.setColor=function(a,d,f){return u(this),a?c(this.colors,a,d,b,f):(f!==b&&(this.colors.alpha=k(f,0,1)),e(d))},s.prototype.setCustomBackground=function(a){return u(this),this.options.customBG="string"==typeof a?v.txt2color(a).rgb:a,c(this.colors,b,"rgb")},s.prototype.saveAsBackground=function(){return u(this),c(this.colors,b,"rgb",!0)},s.prototype.toString=function(a,b){return v.color2text((a||"rgb").toLowerCase(),this.colors,b)};var v={txt2color:function(a){var b={},c=a.replace(/(?:#|\)|%)/g,"").split("("),d=(c[1]||"").split(/,\s*/),e=c[1]?c[0].substr(0,3):"rgb",f="";if(b.type=e,b[e]={},c[1])for(var g=3;g--;)f=e[g]||e.charAt(g),b[e][f]=+d[g]/l[e][f][1];else b.rgb=v.HEX2rgb(c[0]);return b.alpha=d[3]?+d[3]:1,b},color2text:function(a,b,c){var d=c!==!1&&n(100*b.alpha)/100,e="number"==typeof d&&c!==!1&&(c||1!==d),f=b.RND.rgb,g=b.RND.hsl,h="hex"===a&&e,i="hex"===a&&!h,j="rgb"===a||h,k=j?f.r+", "+f.g+", "+f.b:i?"#"+b.HEX:g.h+", "+g.s+"%, "+g.l+"%";return i?k:(h?"rgb":a)+(e?"a":"")+"("+k+(e?", "+d:"")+")"},RGB2HEX:function(a){return((a.r<16?"0":"")+a.r.toString(16)+(a.g<16?"0":"")+a.g.toString(16)+(a.b<16?"0":"")+a.b.toString(16)).toUpperCase()},HEX2rgb:function(a){return a=a.split(""),{r:+("0x"+a[0]+a[a[3]?1:0])/255,g:+("0x"+a[a[3]?2:1]+(a[3]||a[1]))/255,b:+("0x"+(a[4]||a[2])+(a[5]||a[2]))/255}},hue2RGB:function(a){var b=6*a,c=~~b%6,d=6===b?0:b-c;return{r:n(255*[1,1-d,0,0,d,1][c]),g:n(255*[d,1,1,1-d,0,0][c]),b:n(255*[0,0,d,1,1,1-d][c])}},rgb2hsv:function(a){var b,c,d,e=a.r,f=a.g,g=a.b,h=0;return g>f&&(f=g+(g=f,0),h=-1),c=g,f>e&&(e=f+(f=e,0),h=-2/6-h,c=m.min(f,g)),b=e-c,d=e?b/e:0,{h:1e-15>d?p&&p.hsl&&p.hsl.h||0:b?m.abs(h+(f-g)/(6*b)):0,s:e?b/e:p&&p.hsv&&p.hsv.s||0,v:e}},hsv2rgb:function(a){var b=6*a.h,c=a.s,d=a.v,e=~~b,f=b-e,g=d*(1-c),h=d*(1-f*c),i=d*(1-(1-f)*c),j=e%6;return{r:[d,h,g,g,i,d][j],g:[i,d,d,h,g,g][j],b:[g,g,i,d,d,h][j]}},hsv2hsl:function(a){var b=(2-a.s)*a.v,c=a.s*a.v;return c=a.s?1>b?b?c/b:0:c/(2-b):0,{h:a.h,s:a.v||c?c:p&&p.hsl&&p.hsl.s||0,l:b/2}},rgb2hsl:function(a,b){var c=v.rgb2hsv(a);return v.hsv2hsl(b?c:p.hsv=c)},hsl2rgb:function(a){var b=6*a.h,c=a.s,d=a.l,e=.5>d?d*(1+c):d+c-c*d,f=d+d-e,g=e?(e-f)/e:0,h=~~b,i=b-h,j=e*g*i,k=f+j,l=e-j,m=h%6;return{r:[e,l,f,f,k,e][m],g:[k,e,e,l,f,f][m],b:[f,f,k,e,e,l][m]}}};return s}),function(a,b){"object"==typeof exports?module.exports=b(a,require("jquery"),require("colors")):"function"==typeof define&&define.amd?define(["jquery","colors"],function(c,d){return b(a,c,d)}):b(a,a.jQuery,a.Colors)}(this,function(a,b,c,d){"use strict";function e(a){return a.value||a.getAttribute("value")||b(a).css("background-color")||"#FFF"}function f(a){return a=a.originalEvent&&a.originalEvent.touches?a.originalEvent.touches[0]:a,a.originalEvent?a.originalEvent:a}function g(a){return b(a.find(r.doRender)[0]||a[0])}function h(c){var d=b(this),f=d.offset(),h=b(a),k=r.gap;c?(s=g(d),s._colorMode=s.data("colorMode"),p.$trigger=d,(t||i()).css(r.positionCallback.call(p,d)||{left:(t._left=f.left)-((t._left+=t._width-(h.scrollLeft()+h.width()))+k>0?t._left+k:0),top:(t._top=f.top+d.outerHeight())-((t._top+=t._height-(h.scrollTop()+h.height()))+k>0?t._top+k:0)}).show(r.animationSpeed,function(){c!==!0&&(y.toggle(!!r.opacity)._width=y.width(),v._width=v.width(),v._height=v.height(),u._height=u.height(),q.setColor(e(s[0])),n(!0))}).off(".tcp").on(D,".cp-xy-slider,.cp-z-slider,.cp-alpha",j)):p.$trigger&&b(t).hide(r.animationSpeed,function(){n(!1),p.$trigger=null}).off(".tcp")}function i(){return b("head")[r.cssPrepend?"prepend":"append"]('<style type="text/css" id="tinyColorPickerStyles">'+(r.css||I)+(r.cssAddon||"")+"</style>"),b(H).css({margin:r.margin}).appendTo("body").show(0,function(){p.$UI=t=b(this),F=r.GPU&&t.css("perspective")!==d,u=b(".cp-z-slider",this),v=b(".cp-xy-slider",this),w=b(".cp-xy-cursor",this),x=b(".cp-z-cursor",this),y=b(".cp-alpha",this),z=b(".cp-alpha-cursor",this),r.buildCallback.call(p,t),t.prepend("<div>").children().eq(0).css("width",t.children().eq(0).width()),t._width=this.offsetWidth,t._height=this.offsetHeight}).hide()}function j(a){var c=this.className.replace(/cp-(.*?)(?:\s*|$)/,"$1").replace("-","_");(a.button||a.which)>1||(a.preventDefault&&a.preventDefault(),a.returnValue=!1,s._offset=b(this).offset(),(c="xy_slider"===c?k:"z_slider"===c?l:m)(a),n(),A.on(E,function(){A.off(".tcp")}).on(C,function(a){c(a),n()}))}function k(a){var b=f(a),c=b.pageX-s._offset.left,d=b.pageY-s._offset.top;q.setColor({s:c/v._width*100,v:100-d/v._height*100},"hsv")}function l(a){var b=f(a).pageY-s._offset.top;q.setColor({h:360-b/u._height*360},"hsv")}function m(a){var b=f(a).pageX-s._offset.left,c=b/y._width;q.setColor({},"rgb",c)}function n(a){var b=q.colors,c=b.hueRGB,e=(b.RND.rgb,b.RND.hsl,r.dark),f=r.light,g=q.toString(s._colorMode,r.forceAlpha),h=b.HUELuminance>.22?e:f,i=b.rgbaMixBlack.luminance>.22?e:f,j=(1-b.hsv.h)*u._height,k=b.hsv.s*v._width,l=(1-b.hsv.v)*v._height,m=b.alpha*y._width,n=F?"translate3d":"",p=s[0].value,t=s[0].hasAttribute("value")&&""===p&&a!==d;v._css={backgroundColor:"rgb("+c.r+","+c.g+","+c.b+")"},w._css={transform:n+"("+k+"px, "+l+"px, 0)",left:F?"":k,top:F?"":l,borderColor:b.RGBLuminance>.22?e:f},x._css={transform:n+"(0, "+j+"px, 0)",top:F?"":j,borderColor:"transparent "+h},y._css={backgroundColor:"#"+b.HEX},z._css={transform:n+"("+m+"px, 0, 0)",left:F?"":m,borderColor:i+" transparent"},s._css={backgroundColor:t?"":g,color:t?"":b.rgbaMixBGMixCustom.luminance>.22?e:f},s.text=t?"":p!==g?g:"",a!==d?o(a):G(o)}function o(a){v.css(v._css),w.css(w._css),x.css(x._css),y.css(y._css),z.css(z._css),r.doRender&&s.css(s._css),s.text&&s.val(s.text),r.renderCallback.call(p,s,"boolean"==typeof a?a:d)}var p,q,r,s,t,u,v,w,x,y,z,A=b(document),B=b(),C="touchmove.tcp mousemove.tcp pointermove.tcp",D="touchstart.tcp mousedown.tcp pointerdown.tcp",E="touchend.tcp mouseup.tcp pointerup.tcp",F=!1,G=a.requestAnimationFrame||a.webkitRequestAnimationFrame||function(a){a()},H='<div class="cp-color-picker"><div class="cp-z-slider"><div class="cp-z-cursor"></div></div><div class="cp-xy-slider"><div class="cp-white"></div><div class="cp-xy-cursor"></div></div><div class="cp-alpha"><div class="cp-alpha-cursor"></div></div></div>',I=".cp-color-picker{position:absolute;overflow:hidden;padding:6px 6px 0;background-color:#444;color:#bbb;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:400;cursor:default;border-radius:5px}.cp-color-picker>div{position:relative;overflow:hidden}.cp-xy-slider{float:left;height:128px;width:128px;margin-bottom:6px;background:linear-gradient(to right,#FFF,rgba(255,255,255,0))}.cp-white{height:100%;width:100%;background:linear-gradient(rgba(0,0,0,0),#000)}.cp-xy-cursor{position:absolute;top:0;width:10px;height:10px;margin:-5px;border:1px solid #fff;border-radius:100%;box-sizing:border-box}.cp-z-slider{float:right;margin-left:6px;height:128px;width:20px;background:linear-gradient(red 0,#f0f 17%,#00f 33%,#0ff 50%,#0f0 67%,#ff0 83%,red 100%)}.cp-z-cursor{position:absolute;margin-top:-4px;width:100%;border:4px solid #fff;border-color:transparent #fff;box-sizing:border-box}.cp-alpha{clear:both;width:100%;height:16px;margin:6px 0;background:linear-gradient(to right,#444,rgba(0,0,0,0))}.cp-alpha-cursor{position:absolute;margin-left:-4px;height:100%;border:4px solid #fff;border-color:#fff transparent;box-sizing:border-box}",J=function(a){q=this.color=new c(a),r=q.options,p=this};J.prototype={render:n,toggle:h},b.fn.colorPicker=function(c){var d=this,f=function(){};return c=b.extend({animationSpeed:150,GPU:!0,doRender:!0,customBG:"#FFF",opacity:!0,renderCallback:f,buildCallback:f,positionCallback:f,body:document.body,scrollResize:!0,gap:4,dark:"#222",light:"#DDD"},c),!p&&c.scrollResize&&b(a).on("resize.tcp scroll.tcp",function(){p.$trigger&&p.toggle.call(p.$trigger[0],!0)}),B=B.add(this),this.colorPicker=p||new J(c),this.options=c,b(c.body).off(".tcp").on(D,function(a){-1===B.add(t).add(b(t).find(a.target)).index(a.target)&&h()}),this.on("focusin.tcp click.tcp",function(a){p.color.options=b.extend(p.color.options,r=d.options),h.call(this,a)}).on("change.tcp",function(){q.setColor(this.value||"#FFF"),d.colorPicker.render(!0)}).each(function(){var a=e(this),d=a.split("("),f=g(b(this));f.data("colorMode",d[1]?d[0].substr(0,3):"HEX").attr("readonly",r.preventFocus),c.doRender&&f.css({"background-color":a,color:function(){return q.setColor(a).rgbaMixBGMixCustom.luminance>.22?c.dark:c.light}})})},b.fn.colorPicker.destroy=function(){b("*").off(".tcp"),p.toggle(!1),B=b()}});

