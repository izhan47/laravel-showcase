var cropper;
var tmpCanvas;
var rotateValue = 0;
var isCropImageInitialize = 0;
$("body").delegate(".my-file", "change", function(ev){  
    $("#img-control").find("#extraImageTool").remove();
    $("#img-control").width(272).prepend('<span id="extraImageTool"><button id="btnCropImage" type="button" value="Crop" style="display: inline-block;">Crop</button><button id="btnRotateImage" type="button" value="Rotate" style="display: inline-block;">Rotate</button></span>');     
});

$("body").delegate("#btnCropImage", "click", function(ev){
    'use strict';  
    if( ! isCropImageInitialize ) {
        isCropImageInitialize = 1;        
       
        var Cropper = window.Cropper;    
        var container = document.querySelector('#my-mask');
        var image = container.getElementsByTagName('img').item(0);    

        cropper = new Cropper(image, {
            aspectRatio: NaN,            
        });
    }
});

$("body").delegate("#btnRotateImage", "click", function(ev){ 
    if( (rotateValue + 90) == 360 ) {
        rotateValue = 0;
    } else {
        rotateValue = rotateValue + 90;  
    }

    if( !isCropImageInitialize ) {
        isCropImageInitialize = 1;
       
        var Cropper = window.Cropper;   
        var container = document.querySelector('#my-mask');
        var image = container.getElementsByTagName('img').item(0); 

        cropper = new Cropper(image, {   
            autoCrop: false,
            aspectRatio: NaN,           
            ready() {
                this.cropper.rotateTo(rotateValue);
            },
        }); 
    } else{
        cropper.rotateTo(rotateValue);
    }
});

$("body").delegate("#btnChangeImage", "click", function(ev){ 
    if( cropper  ) {
        ev.preventDefault();           
        jQuery("#divToolImg").data('image').attr("src", cropper.getCroppedCanvas().toDataURL('image/jpeg'));
        cropper.destroy();
        isCropImageInitialize = 0;
    }
});