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

    <link href="{{ asset('admin-theme/css/grapes-builder.css') }}" rel="stylesheet">

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
    <title>Editor</title>
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
    const editor = grapesjs.init({
  height: "100%",
  showOffsets: 1,
  noticeOnUnload: 0,
  storageManager: {
    autoload: 0,
  },
  container: "#gjs",
  fromElement: true,
  styleManager: {
    clearProperties: 1,
  },
  avoidInlineStyle: 1,
  commands: {
    defaults: [
      {
        id: "save-html",
        run(e) {
          saveHTML(e);
        },
      },
    ],
  },
  plugins: ["gjs-preset-webpage"],
  pluginsOpts: {
    "gjs-preset-webpage": {
      navbarOpts: false,
      countdownOpts: false,
      formsOpts: false,
      aviaryOpts: false,
      blocksBasicOpts: {
        flexGrid: 1,
        blocks: [
          "column1",
          "column2",
          "column3",
          "column3-7",
          "text",
          "link",
          "image",
          "video",
        ],
      },
      customStyleManager: [
        {
          name: "General",
          buildProps: [
            "float",
            "display",
            "position",
            "top",
            "right",
            "left",
            "bottom",
          ],
          properties: [
            {
              name: "Alignment",
              property: "float",
              type: "radio",
              defaults: "none",
              list: [
                { value: "none", className: "fa fa-times" },
                { value: "left", className: "fa fa-align-left" },
                { value: "right", className: "fa fa-align-right" },
              ],
            },
            { property: "position", type: "select" },
          ],
        },
        {
          name: "Dimension",
          open: false,
          buildProps: [
            "width",
            "flex-width",
            "height",
            "max-width",
            "min-height",
            "margin",
            "padding",
          ],
          properties: [
            {
              id: "flex-width",
              type: "integer",
              name: "Width",
              units: ["px", "%"],
              property: "flex-basis",
              toRequire: 1,
            },
            {
              property: "margin",
              properties: [
                { name: "Top", property: "margin-top" },
                { name: "Right", property: "margin-right" },
                { name: "Bottom", property: "margin-bottom" },
                { name: "Left", property: "margin-left" },
              ],
            },
            {
              property: "padding",
              properties: [
                { name: "Top", property: "padding-top" },
                { name: "Right", property: "padding-right" },
                { name: "Bottom", property: "padding-bottom" },
                { name: "Left", property: "padding-left" },
              ],
            },
          ],
        },
        {
          name: "Typography",
          open: false,
          buildProps: [
            "font-family",
            "font-size",
            "font-weight",
            "letter-spacing",
            "color",
            "line-height",
            "text-align",
            "text-decoration",
            "text-shadow",
          ],
          properties: [
            { name: "Font", property: "font-family" },
            { name: "Weight", property: "font-weight" },
            { name: "Font color", property: "color" },
            {
              property: "text-align",
              type: "radio",
              defaults: "left",
              list: [
                { value: "left", name: "Left", className: "fa fa-align-left" },
                {
                  value: "center",
                  name: "Center",
                  className: "fa fa-align-center",
                },
                {
                  value: "right",
                  name: "Right",
                  className: "fa fa-align-right",
                },
                {
                  value: "justify",
                  name: "Justify",
                  className: "fa fa-align-justify",
                },
              ],
            },
            {
              property: "text-decoration",
              type: "radio",
              defaults: "none",
              list: [
                { value: "none", name: "None", className: "fa fa-times" },
                {
                  value: "underline",
                  name: "underline",
                  className: "fa fa-underline",
                },
                {
                  value: "line-through",
                  name: "Line-through",
                  className: "fa fa-strikethrough",
                },
              ],
            },
            {
              property: "text-shadow",
              properties: [
                { name: "X position", property: "text-shadow-h" },
                { name: "Y position", property: "text-shadow-v" },
                { name: "Blur", property: "text-shadow-blur" },
                { name: "Color", property: "text-shadow-color" },
              ],
            },
          ],
        },
        {
          name: "Decorations",
          open: false,
          buildProps: [
            "opacity",
            "border-radius",
            "border",
            "box-shadow",
            "background-bg",
          ],
          properties: [
            {
              type: "slider",
              property: "opacity",
              defaults: 1,
              step: 0.01,
              max: 1,
              min: 0,
            },
            {
              property: "border-radius",
              properties: [
                { name: "Top", property: "border-top-left-radius" },
                { name: "Right", property: "border-top-right-radius" },
                { name: "Bottom", property: "border-bottom-left-radius" },
                { name: "Left", property: "border-bottom-right-radius" },
              ],
            },
            {
              property: "box-shadow",
              properties: [
                { name: "X position", property: "box-shadow-h" },
                { name: "Y position", property: "box-shadow-v" },
                { name: "Blur", property: "box-shadow-blur" },
                { name: "Spread", property: "box-shadow-spread" },
                { name: "Color", property: "box-shadow-color" },
                { name: "Shadow type", property: "box-shadow-type" },
              ],
            },
            {
              id: "background-bg",
              property: "background",
              type: "bg",
            },
          ],
        },
        {
          name: "Extra",
          open: false,
          buildProps: ["transition", "perspective", "transform"],
          properties: [
            {
              property: "transition",
              properties: [
                { name: "Property", property: "transition-property" },
                { name: "Duration", property: "transition-duration" },
                { name: "Easing", property: "transition-timing-function" },
              ],
            },
            {
              property: "transform",
              properties: [
                { name: "Rotate X", property: "transform-rotate-x" },
                { name: "Rotate Y", property: "transform-rotate-y" },
                { name: "Rotate Z", property: "transform-rotate-z" },
                { name: "Scale X", property: "transform-scale-x" },
                { name: "Scale Y", property: "transform-scale-y" },
                { name: "Scale Z", property: "transform-scale-z" },
              ],
            },
          ],
        },
        {
          name: "Flex",
          open: false,
          properties: [
            {
              name: "Flex Container",
              property: "display",
              type: "select",
              defaults: "block",
              list: [
                { value: "block", name: "Disable" },
                { value: "flex", name: "Enable" },
              ],
            },
            {
              name: "Flex Parent",
              property: "label-parent-flex",
              type: "integer",
            },
            {
              name: "Direction",
              property: "flex-direction",
              type: "radio",
              defaults: "row",
              list: [
                {
                  value: "row",
                  name: "Row",
                  className: "icons-flex icon-dir-row",
                  title: "Row",
                },
                {
                  value: "row-reverse",
                  name: "Row reverse",
                  className: "icons-flex icon-dir-row-rev",
                  title: "Row reverse",
                },
                {
                  value: "column",
                  name: "Column",
                  title: "Column",
                  className: "icons-flex icon-dir-col",
                },
                {
                  value: "column-reverse",
                  name: "Column reverse",
                  title: "Column reverse",
                  className: "icons-flex icon-dir-col-rev",
                },
              ],
            },
            {
              name: "Justify",
              property: "justify-content",
              type: "radio",
              defaults: "flex-start",
              list: [
                {
                  value: "flex-start",
                  className: "icons-flex icon-just-start",
                  title: "Start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-just-end",
                },
                {
                  value: "space-between",
                  title: "Space between",
                  className: "icons-flex icon-just-sp-bet",
                },
                {
                  value: "space-around",
                  title: "Space around",
                  className: "icons-flex icon-just-sp-ar",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-just-sp-cent",
                },
              ],
            },
            {
              name: "Align",
              property: "align-items",
              type: "radio",
              defaults: "center",
              list: [
                {
                  value: "flex-start",
                  title: "Start",
                  className: "icons-flex icon-al-start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-al-end",
                },
                {
                  value: "stretch",
                  title: "Stretch",
                  className: "icons-flex icon-al-str",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-al-center",
                },
              ],
            },
            {
              name: "Flex Children",
              property: "label-parent-flex",
              type: "integer",
            },
            {
              name: "Order",
              property: "order",
              type: "integer",
              defaults: 0,
              min: 0,
            },
            {
              name: "Flex",
              property: "flex",
              type: "composite",
              properties: [
                {
                  name: "Grow",
                  property: "flex-grow",
                  type: "integer",
                  defaults: 0,
                  min: 0,
                },
                {
                  name: "Shrink",
                  property: "flex-shrink",
                  type: "integer",
                  defaults: 0,
                  min: 0,
                },
                {
                  name: "Basis",
                  property: "flex-basis",
                  type: "integer",
                  units: ["px", "%", ""],
                  unit: "",
                  defaults: "auto",
                },
              ],
            },
            {
              name: "Align",
              property: "align-self",
              type: "radio",
              defaults: "auto",
              list: [
                {
                  value: "auto",
                  name: "Auto",
                },
                {
                  value: "flex-start",
                  title: "Start",
                  className: "icons-flex icon-al-start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-al-end",
                },
                {
                  value: "stretch",
                  title: "Stretch",
                  className: "icons-flex icon-al-str",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-al-center",
                },
              ],
            },
          ],
        },
      ],
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