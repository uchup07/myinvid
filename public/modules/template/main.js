/**
 * Created by Barind on 27/09/17.
 */

$("#btn-AddTemplate").click(function() {
    App.startPageLoading({animate: true});
    ClearForm();
    $('#statusform').val('add');
    $('#ModalAddTemplate').modal('show');
    App.stopPageLoading();
});

$("form#form_template").submit(function(){
    App.startPageLoading({animate: true});
    var formData = new FormData(this);
    $.ajax({
        url: BASE_URL + '/template/save',
        type: 'POST',
        data: formData,
        async: false,
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                var table = $('#tbl_template').DataTable();
                ClearForm();
                if(json.output.statusform == 'add'){
                    table.row.add(
                        {"id"               : json.output.id,
                            "name"          : json.output.name,
                            "price"         : json.output.price,
                            "preview"       : '<img src="' + json.output.preview + '" style="width:100px;">',
                            "is_active"     : '<span class="label label-sm label-success">active</span>',
                            "href"          : '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('+ json.output.id +')"><i class="glyphicon glyphicon-pencil"></i></a> <a href="javascript:void(0);" class="btn btn-danger" onclick="deleteList('+ json.output.id +')"><i class="fa fa-ban"></i></a>'}
                    ).draw();
                }
                $('#ModalAddTemplate').modal('hide');
                App.stopPageLoading();
            }else{
                $.each( json.validator, function( key, value ) {
                    toastr.error(value);
                    $('#row-' + key).addClass('has-error');
                });
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
});

function deleteList(id){
    $('#id').val(id);
    $('#ConfirmDelete').modal('show');
}

function ActionDelete(){
    var id = $('#id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/template/delete",
        type: 'POST',
        data: {
            id              : id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ModalAddTemplate').modal('hide');
                window.setTimeout(function() {
                    var msg = btoa(json.message);
                    window.location = BASE_URL + "/template/wmsg/" + msg;
                    App.stopPageLoading();
                }, 2000);
                App.stopPageLoading();
            }else{
                toastr.error(json.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });
}

function editList(id) {
    App.startPageLoading({animate: true});
    $.ajax({
        url: BASE_URL + "/template/get",
        type: 'POST',
        data: {
            id              : id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.info(json.message);
                $('#name').val(json.output.name);
                $('#demo_url').val(json.output.demo_url);
                $('#price_up').val(json.output.price_up);
                $('#price').val(json.output.price);
                $('#template_id').val(id);
                $('#statusform').val('edit');

                // STATUS DESKTOP //
                if(json.output.statusfileDesktop == 1){ // File Ditemukan //
                    $('#imageNewDesktop').hide();
                    $('#imageEditDesktop').show();
                    $('#imageFileEditDesktop').val(json.output.imagefileDesktop);
                }else if(json.output.statusfileDesktop == 2){ // File tidak Ditemukan //
                    $('#imageEditDesktop').hide();
                    $('#imageNewDesktop').show();
                    $('#imageFileEditDesktop').val("");
                }
                // STATUS DESKTOP //

                // STATUS TABLET //
                if(json.output.statusfileTablet == 1){ // File Ditemukan //
                    $('#imageNewTablet').hide();
                    $('#imageEditTablet').show();
                    $('#imageFileEditTablet').val(json.output.imagefileTablet);
                }else if(json.output.statusfileTablet == 2){ // File tidak Ditemukan //
                    $('#imageEditTablet').hide();
                    $('#imageNewTablet').show();
                    $('#imageFileEditTablet').val("");
                }
                // STATUS TABLET //

                // STATUS MOBILE //
                if(json.output.statusfileMobile == 1){ // File Ditemukan //
                    $('#imageNewMobile').hide();
                    $('#imageEditMobile').show();
                    $('#imageFileEditMobile').val(json.output.imagefileMobile);
                }else if(json.output.statusfileMobile == 2){ // File tidak Ditemukan //
                    $('#imageEditMobile').hide();
                    $('#imageNewMobile').show();
                    $('#imageFileEditMobile').val("");
                }
                // STATUS MOBILE //

                $('#ModalAddTemplate').modal('show');
                App.stopPageLoading();
            }else{
                toastr.error(json.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });

}

$("#btn-ResetFormTemplate").click(function() {
    ClearForm();
});

function ClearForm(){
    App.startPageLoading({animate: true});
    $('#name').val('');
    $('#demo_url').val('');
    $('#price_up').val('');
    $('#price').val('');
    $('.fileinput').fileinput('clear');
    $('#template_id').val('');
    $('#imageFileEditDesktop').val("");
    $('#imageEditDesktop').hide();
    $('#imageNewDesktop').show();

    toastr.info("Reset Form Berhasil");
    App.stopPageLoading();
}

$("#btn-PreviewStory").click(function() {
    var website_id          = $('#website_id').val();
    window.open(BASE_URL + "/storyoflove/preview/" + website_id,"","width=800,height=600");
});

$("#btn-DeleteImageDesktopEdit").click(function() {
    $('#ConfirmDeleteImageDesktop').modal('show');
});


$("#btn-DeleteImageTabletEdit").click(function() {
    $('#ConfirmDeleteImageTablet').modal('show');
});


$("#btn-DeleteImageMobileEdit").click(function() {
    $('#ConfirmDeleteImageMobile').modal('show');
});


$("#btn-ActionDeleteImageDesktop").click(function() {
    var template_id      = $('#template_id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/template/delete_image_desktop",
        type: 'POST',
        data: {
            id              : template_id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ConfirmDeleteImageDesktop').modal('hide');
                $('#imageNewDesktop').show();
                $('#imageEditDesktop').hide();
                $('#imageFileEditDesktop').val("");
                App.stopPageLoading();
            }else{
                toastr.error(json.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });
});


$("#btn-ActionDeleteImageTablet").click(function() {
    var template_id      = $('#template_id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/template/delete_image_tablet",
        type: 'POST',
        data: {
            id              : template_id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ConfirmDeleteImageTablet').modal('hide');
                $('#imageNewTablet').show();
                $('#imageEditTablet').hide();
                $('#imageFileEditTablet').val("");
                App.stopPageLoading();
            }else{
                toastr.error(json.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });
});



$("#btn-ActionDeleteImageMobile").click(function() {
    var template_id      = $('#template_id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/template/delete_image_mobile",
        type: 'POST',
        data: {
            id              : template_id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ConfirmDeleteImageMobile').modal('hide');
                $('#imageNewMobile').show();
                $('#imageEditMobile').hide();
                $('#imageFileEditMobile').val("");
                App.stopPageLoading();
            }else{
                toastr.error(json.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });
});

$( "#template" ).change(function() {
    App.startPageLoading({animate: true});
    if($(this).val() > 0){
        $("#loadTemplate").load( BASE_URL + "/template/wedding/load/" + $(this).val());
    }else{
        toastr.warning('Mohon pilih Template.');
    }
    App.stopPageLoading();
});

$("#btn-PreviewTemplate").click(function() {
    var website_id              = $('#website_id').val();
    var template_id             = $('#template').val();
    if(template_id > 0){
        window.open(BASE_URL + "/id/preview/" + website_id + "/" + template_id,"");
    }else{
        $('#row-template').addClass("has-error");
        toastr.warning('Mohon pilih Template.');
    }
});

