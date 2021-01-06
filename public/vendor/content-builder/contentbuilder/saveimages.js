(function (jQuery) {

    jQuery.saveimages = function (element, options) {

        var defaults = {
            handler: 'saveimage.php',
            onComplete: function () { },
			customval: ''
        };

        this.settings = {};

        var $element = jQuery(element),
                element = element;

        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

        };

        this.save = function (s) {

            var handler = this.settings.handler;
            var customval = this.settings.customval;
            var _token = this.settings._token;

            var formDataToUpload = new FormData();

            //Check all images
            $element.find('img').not('#divCb img').each(function () {

                //Find base64 images
                if (jQuery(this).attr('src').indexOf('base64') != -1) {

                    //Read image (base64 string)
                    var ImageURL = jQuery(this).attr('src');
                    image = ImageURL.replace(/^data:image\/(png|jpeg);base64,/, "");

                    // Split the base64 string in data and contentType
                    var block = ImageURL.split(";");

                    var contentType = block[0].split(":")[1];// In this case "image/gif"
                    // get the real base64 content of the file
                    var realData = block[1].split(",")[1];

                    // Convert it to a blob to upload
                    var blob = b64toBlob(realData, contentType);

                    // Create a FormData and append the file with "image" as parameter name
                    formDataToUpload.append("file[]", blob);

                    //Note: the submitted image will be saved on the server
                    //by saveimage.php (if using PHP) or saveimage.ashx (if using .NET)
                    //and the image src will be changed with the new saved image.
                }
            });

            if (formDataToUpload.has('file[]')) {
                jQuery.ajax({
                    type: "POST",
                    url: handler,
                    headers: {
                        'X-CSRF-TOKEN': _token
                    },
                    data: formDataToUpload,
                    contentType:false,
                    processData:false,
                    success: function (response) {
                            let data = response.data;
                            let key = 0;

                            $element.find('img').not('#divCb img').each(function () {
                                if (jQuery(this).attr('src').indexOf('base64') != -1) {
                                    if (data[key]['img_url']) {
                                        jQuery(this).attr('src', data[key]['img_url']);
                                    }
                                    key++;
                                }
                            });
                    },
                    error:function (error) {
                        console.error(error.responseJSON.message);
                    }
                });
            }

            //Check per 2 sec if all images have been changed with the new saved images.
            var int = setInterval(function () {

                var finished = true;
                $element.find('img').not('#divCb img').each(function () {
                    if (jQuery(this).attr('src').indexOf('base64') != -1) { //if there is still base64 image, means not yet finished.
                        finished = false;
                    }
                });

                if (finished) {

                    $element.data('saveimages').settings.onComplete();

                    window.clearInterval(int);
                }
            }, 2000);

        };

        this.init();

    };

    jQuery.fn.saveimages = function (options) {

        return this.each(function () {

            if (undefined == jQuery(this).data('saveimages')) {
                var plugin = new jQuery.saveimages(this, options);
                jQuery(this).data('saveimages', plugin);

            }

        });
    };


    /**
     * Convert a base64 string in a Blob according to the data and contentType.
     *
     * @param b64Data {String} Pure base64 string without contentType
     * @param contentType {String} the content type of the file i.e (image/jpeg - image/png - text/plain)
     * @param sliceSize {Int} SliceSize to process the byteCharacters
     * @see http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
     * @return Blob
     */
    function b64toBlob(b64Data, contentType, sliceSize) {
        contentType = contentType || '';
        sliceSize = sliceSize || 512;

        var byteCharacters = atob(b64Data);
        var byteArrays = [];

        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            var slice = byteCharacters.slice(offset, offset + sliceSize);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }

        var blob = new Blob(byteArrays, {type: contentType});
        return blob;
    }

})(jQuery);