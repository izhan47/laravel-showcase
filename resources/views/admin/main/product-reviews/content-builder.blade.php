<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="//unpkg.com/grapesjs/dist/css/grapes.min.css">
    
    <!-- plugins -->
    <script src="//unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage"></script>
    <script type="text/javascript" src="{{ asset('vendor/content-builder/contentbuilder/jquery.min.js') }}"></script>
    <script src="https://unpkg.com/grapesjs-blocks-flexbox"></script>
    <style>
        body,
      html {
        height: 100%;
        margin: 0;
      }

      *{
          box-sizing: border-box;
      }
    </style>
    <title>Document</title>
</head>
<body>

  <form id='description-form' method="POST" action="{{ $module_route.'/'.$watchAndLearn['id'].'/save-description' }}" style="display:none">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <textarea name="description" id="description"></textarea>
</form>

    
    <div id="gjs" style="height:0px; overflow:hidden"></div>


  <script>
    const exist = `{!! $watchAndLearn["description"] !!}`;
    var editor = grapesjs.init({
        height: "100%",
        showOffsets: 1,
        noticeOnUnload: 0,
        storageManager: {
            autoload: 0
        },
        container: "#gjs",
        fromElement: true,
        styleManager: {
            clearProperties: 1
        },
        avoidInlineStyle: 1,
        commands: {
            defaults: [{
                id: "save-html",
                run(e) {
                    saveHTML(e);
                },
            }, ],
        },
        plugins: ["gjs-preset-webpage", 'gjs-blocks-flexbox'],
        pluginsOpts: {
            'gjs-preset-webpage': {
                navbarOpts: false,
                countdownOpts: false,
                formsOpts:false,
                blocksBasicOpts: {
                    blocks: ['column1', 'column2', 'column3', 'column3-7', 'text', 'link', 'image', 'video'],
                    flexGrid:true
                }
            },
        },
    });


    if (exist) {
        let dom2 = exist.split('</style>');

        if (dom2.length > 1) {
            let html = dom2[1];
            let style = dom2[0] + '</style>'
            editor.setComponents(html)
            editor.setStyle(style)
        } else {
            editor.setComponents(exist)
        }
    }

    const panelManager = editor.Panels;

    var saveButton = panelManager.addButton("options", {
        id: "save-panel",
        command: "save-html",
        className: "fa fa-save",
    });

    var vieBTN = panelManager.getButton('options', 'sw-visibility');

    if (vieBTN) {
        vieBTN.attributes.active = true;
    }
    async function saveHTML(e) {
        try {
            const loaderDiv = document.createElement('div')
            loaderDiv.style.position = 'absolute';
            loaderDiv.style.top = 0;
            loaderDiv.style.bottom = 0;
            loaderDiv.style.left = 0;
            loaderDiv.style.right = 0;
            loaderDiv.style.display = 'grid';
            loaderDiv.style.placeItems = 'center';
            loaderDiv.style.backgroundColor = 'rgba(0,0,0,0.4)';
            loaderDiv.style.zIndex = 11111111111;

            loaderDiv.innerHTML = `<img src="/vendor/content-builder/assets/loader.gif" style="margin-right: 10px;" /> `;
            document.body.prepend(loaderDiv)

            let html = e.getHtml();
            const css = e.getCss();
            const js = e.getJs();

            const div = document.createElement("div");
            div.innerHTML = html;

            let images = []
            const imgTags = div.querySelectorAll("img");
            for (const key in imgTags) {
                const img = imgTags[key];
                if (img.src && img.src.includes("base64")) {
                    images.push(img)
                };
            }

            let fd = new FormData();
            for (let i = 0; i < images.length; i++) {
                const src = images[i].src;

                const block = src.split(";");
                const contentType = block[0].split(":")[1]; // In this case "image/gif"
                const realData = block[1].split(",")[1];
                const blob = b64toBlob(realData, contentType);
                // upload to server the
                fd.append(`file[]`, blob)

            }

            if (images.length) {
                jQuery.ajax({
                    type: "POST",
                    url: 'http://localhost:8000/admin/watch-and-learn/store-media',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const imagesSrc = response.data;

                        for (let i = 0; i < images.length; i++) {
                            images[i].src = imagesSrc[i].img_url
                        }

                        html = String(div.innerHTML);
                        const styleTag = `<style>${css}</style>`;
                        const formatedHtml = styleTag + html;
                        $("#description-form").find("#description").val(formatedHtml);
                        $("#description-form").submit();
                    },
                    error: function(error) {
                        console.error(error.responseJSON.message);
                    }
                });
            } else {
                html = String(div.innerHTML);
                const styleTag = `<style>${css}</style>`;
                const formatedHtml = styleTag + html;
                $("#description-form").find("#description").val(formatedHtml);
                $("#description-form").submit();
            };
        } catch (error) {
            console.log(error);
        }
    }

    function b64toBlob(b64Data, contentType, sliceSize) {
        contentType = contentType || "";
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

        var blob = new Blob(byteArrays, {
            type: contentType
        });
        return blob;
      }
    </script>
  </body>
</html>