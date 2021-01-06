var cropper;
var tmpCanvas;
$("body").delegate(".my-file", "change", function(ev){  
    $("#img-control").find("#extraImageTool").remove();
    $("#img-control").width(272);
    $("#img-control").prepend('<span id="extraImageTool"><button id="btnCropImage" type="button" value="Crop" style="display: inline-block;">Crop</button><button id="btnRotateImage" type="button" value="Rotate" style="display: inline-block;">Rotate</button></span>');
});

$("body").delegate("#btnCropImage", "click", function(ev){
    'use strict';  
    tmpCanvas = document.getElementById('myTmpCanvas');
    var Cropper = window.Cropper;
    var URL = window.URL || window.webkitURL;
    var container = document.querySelector('#my-mask');
    var image = container.getElementsByTagName('img').item(0);             
    cropper = new Cropper(image, {
        aspectRatio: 16 / 9,
        crop(event) {
            var width = event.detail.width;
            var height = event.detail.height;
            $("#my-image").css('width', event.detail.width);
            $("#my-image").css('height', event.detail.height);
            $("#my-image").css('transform', 'translateX(' + event.detail.scaleX+ 'px)');
            $("#my-image").css('transform', 'translateX(' + event.detail.scaleY+ 'px)');     

            tmpCanvas = document.getElementById('myTmpCanvas');
            tmpCanvas.width = event.detail.width;
            tmpCanvas.height = event.detail.height;

            var context = tmpCanvas.getContext('2d');       
            var tmp = new Image(), context, width, height;

            context = tmpCanvas.getContext( '2d' );
            context.drawImage( tmp, 0, 0, event.detail.width, event.detail.height );
            tmp.src = tmpCanvas.toDataURL( 'image/png', 1 );
            crop();
            console.log("x", event.detail.x);
            console.log("y", event.detail.y);
            console.log("width", event.detail.width);
            console.log("height", event.detail.height);
            //console.log("rotate", event.detail.rotate);
            console.log("scaleX", event.detail.scaleX);
            console.log("scaleY", event.detail.scaleY);
        },
    });
});

$("body").delegate("#btnChangeImage", "click", function(ev){ 
    if( cropper ) {
        cropper.destroy();
    }
});

$("body").delegate("#btnRotateImage", "click", function(ev){ 
    if ($("#my-image").css('transform') == 'none') {
      $("#my-image").css({'transform': 'rotate(-180deg)'});
    } else {
      $("#my-image").css({'transform': ''});
    };
});

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