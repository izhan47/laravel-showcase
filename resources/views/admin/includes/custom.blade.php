    <script>

        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 5000
        };

        @if(\Session::has('error'))
            toastr.error('{!! str_replace("'", " ", \Session::get('error')) !!}', 'Error');
        @endif
        @if(\Session::has('success'))
            toastr.success('{!! \Session::get('success') !!}', 'Success');
        @endif

        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

        function deleteRecordByAjax(deleteUrl, moduleName, dataTablesName) {
            var deleteAlertStr = "You want to delete "+moduleName.toLowerCase()+"?";

            swal({
                    title: "Are you sure?",
                    text: deleteAlertStr,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, remove it!",
                    cancelButtonText: "No, cancel!",
                    showLoaderOnConfirm: true,
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    preConfirm: function (email) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function() {
                                jQuery.ajax({
                                    url: deleteUrl,
                                    type: 'DELETE',
                                    dataType: 'json',
                                    data: {
                                        "_token": window.Laravel.csrfToken
                                    },
                                    success: function (result) {
                                        if( dataTablesName ) {
                                            dataTablesName.draw();
                                        } else {
                                            location.reload(true); 
                                        }
                                        swal("success!", moduleName+" deleted.", "success");
                                        fnToastSuccess(result.message);
                                    },
                                    error: function (xhr, status, error) {
                                        if(xhr.responseJSON && xhr.responseJSON.message!=""){
                                            swal("ohh snap!", xhr.responseJSON.message, "error");
                                        } else {
                                            swal("ohh snap!", "Something went wrong", "error");
                                        }
                                        ajaxError(xhr, status, error);
                                    }
                                });
                            }, 0)
                        })
                    },
                }).then(function(json_data) {
                }, function(dismiss) {});
        }

        function changeBlogStatusAjaxCustom(eventURL, title, text, method, successMessage, submitForm = false, redirectURL) {

            swal({
                    title: title,
                    text: text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    showLoaderOnConfirm: true,
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    preConfirm: function (email) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function() {
                                jQuery.ajax({
                                    url: eventURL,
                                    type: method,
                                    dataType: 'json',
                                    data: {
                                        "_token": window.Laravel.csrfToken,
                                    },
                                    success: function (result) {                                          
                                        fnToastSuccess(successMessage);
                                        setTimeout(() => {                                        
                                            if( submitForm ){
                                                $("#form_validate").submit();                                                
                                            } else {
                                                window.location.replace(redirectURL);
                                            }
                                        }, 1000);
                                    },
                                    error: function (xhr, status, error) {
                                        $(location).attr("{!! url('admin/watch-and-learn');  !!}");
                                        if(xhr.responseJSON && xhr.responseJSON.message!=""){
                                            swal("ohh snap!", xhr.responseJSON.message, "error");
                                        } else {
                                            swal("ohh snap!", "Something went wrong", "error");
                                        }
                                        ajaxError(xhr, status, error);
                                    }
                                });
                            }, 0)
                        })
                    },
                }).then(function(json_data) {
                }, function(dismiss) {});
        }

        function suspendRecordByAjax(suspendUrl, moduleName, dataTablesName, id) {
            var suspendAlertStr = "You want to suspend "+moduleName+"?";

            swal({
                    title: "Are you sure?",
                    text: suspendAlertStr,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Suspend it!",
                    cancelButtonText: "No, cancel!",
                    showLoaderOnConfirm: true,
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    preConfirm: function (email) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function() {
                                jQuery.ajax({
                                    url: suspendUrl,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        "_token": window.Laravel.csrfToken,
                                        id: id
                                    },
                                    success: function (result) {
                                        dataTablesName.draw();
                                        swal("success!", moduleName+" Suspend successfully.", "success");
                                        fnToastSuccess(result.message);
                                    },
                                    error: function (xhr, status, error) {
                                        if(xhr.responseJSON && xhr.responseJSON.message!=""){
                                            swal("ohh snap!", xhr.responseJSON.message, "error");
                                        } else {
                                            swal("ohh snap!", "Something went wrong", "error");
                                        }
                                        ajaxError(xhr, status, error);
                                    }
                                });
                            }, 0)
                        })
                    },
                }).then(function(json_data) {
                }, function(dismiss) {});
        }

        function changeStatusRecordByAjax(url, moduleName, dataTablesName, id, status) {
            var suspendAlertStr = "Do you want to change this "+moduleName+"'s status?";

            swal({
                    title: "Are you sure?",
                    text: suspendAlertStr,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, change the status!",
                    cancelButtonText: "No, cancel!",
                    showLoaderOnConfirm: true,
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    preConfirm: function (email) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function() {
                                jQuery.ajax({
                                    url: url,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        "_token": window.Laravel.csrfToken,
                                        id: id,
                                        status: status
                                    },
                                    success: function (result) {
                                        dataTablesName.draw();
                                        swal("success!", moduleName+"'s status changed successfully.", "success");
                                        fnToastSuccess(result.message);
                                    },
                                    error: function (xhr, status, error) {
                                        if(xhr.responseJSON && xhr.responseJSON.message!=""){
                                            swal("ohh snap!", xhr.responseJSON.message, "error");
                                        } else {
                                            swal("ohh snap!", "Something went wrong", "error");
                                        }
                                        ajaxError(xhr, status, error);
                                    }
                                });
                            }, 0)
                        })
                    },
                }).then(function(json_data) {
                }, function(dismiss) {});
        }

        function toggleStatusRecordByAjax(Url, dataTablesName) {
            var activeAlertStr = "You want to change status?";

            swal({
                    title: "Are you sure?",
                    text: activeAlertStr,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Change it!",
                    cancelButtonText: "No, cancel!",
                    showLoaderOnConfirm: true,
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    preConfirm: function (email) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function() {
                                jQuery.ajax({
                                    url: Url,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        "_token": window.Laravel.csrfToken,                                        
                                    },
                                    success: function (result) {
                                        dataTablesName.draw();
                                       swal("success!", "Change status successfully.", "success");
                                        fnToastSuccess(result.message);
                                    },
                                    error: function (xhr, status, error) {
                                        if(xhr.responseJSON && xhr.responseJSON.message!=""){
                                            swal("ohh snap!", xhr.responseJSON.message, "error");
                                        } else {
                                            swal("ohh snap!", "Something went wrong", "error");
                                        }
                                        ajaxError(xhr, status, error);
                                    }
                                });
                            }, 0)
                        })
                    },
                }).then(function(json_data) {
                }, function(dismiss) {});
        }

        function fnToastSuccess(message) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 4000
            };
            toastr.success(message);
        }

        function fnToastError(message) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 4000
            };
            toastr.error(message);
        }

        function ajaxError(xhr, status, error) {
            if(xhr.status ==401){
                fnToastError("You are not logged in. please login and try again");
            }else if(xhr.status == 403){
                fnToastError("You have not permission for perform this operations");
            }else if(xhr.responseJSON && xhr.responseJSON.message!=""){
                fnToastError(xhr.responseJSON.message);
            }else{
                fnToastError("Something went wrong , Please try again later.");
            }
        }

        $(".change-image-track").change(function(){
            displayImageOnFileSelect(this, $('.changed-image-preview'));
        });

        function displayImageOnFileSelect(input, thumbElement) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(thumbElement).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function nl2br (str, is_xhtml) {
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        }

        function convertToSlug(Text)
{
            return Text
                .toLowerCase()
                .replace(/ /g,'-')
                .replace(/[^\w-]+/g,'')
                ;
        }

        function simpleLoad(btn, state,button_text) {
            button_text=typeof button_text==undefined?'Save':button_text;
            if (state) {
                    btn.children().first().addClass('fa fa-spinner fa-spin');
                    btn.contents().last().replaceWith(" Loading");
                    btn.prop('disabled',true);
            } else {
                btn.children().first().removeClass('fa fa-spinner fa-spin');
                btn.contents().last().replaceWith(button_text);
                btn.prop('disabled',false);
            }
        }

        var _URL = window.URL || window.webkitURL;
        $("input:file").change(function(){
            $(this).parents('.form-group').find($('.img-thumbnail')).css("display", 'block');
            displayImageOnFileSelect(this, $(this).parents('.form-group').find($('.img-thumbnail')));

            isValidImageSize = 1;
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function () {
                    if(this.height != '572' || this.width != '1102') {
                        isValidImageSize = 0;
                    }
                };
                img.src = _URL.createObjectURL(file);
            }
        });

        function htmlDecode(input){
          var e = document.createElement('div');
          e.innerHTML = input;
          return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
        }
    </script>