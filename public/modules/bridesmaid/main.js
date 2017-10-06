/**
 * Created by Barind on 27/09/17.
 */

$("#btn-AddBridesmaid").click(function() {
    App.startPageLoading({animate: true});
    ClearForm();
    $('#statusform').val('add');
    $('#ModalAddBridesmaid').modal('show');
    App.stopPageLoading();
});

$("form#form_bridesmaid").submit(function(){
    App.startPageLoading({animate: true});
    var formData = new FormData(this);
    $.ajax({
        url: BASE_URL + '/bridesmaid/save',
        type: 'POST',
        data: formData,
        async: false,
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                var table = $('#tbl_bridesmaid').DataTable();
                ClearForm();
                if(json.output.statusform == 'add'){
                    table.row.add(
                        {"id" : json.output.id,
                            "name"      : json.output.name,
                            "gender"    : json.output.gender,
                            "file"      : '<img src="' + json.output.file + '" style="width:100px;">',
                            "href"      : '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('+ json.output.id +')"><i class="glyphicon glyphicon-pencil"></i></a> <a href="javascript:void(0);" class="btn btn-danger" onclick="deleteList('+ json.output.id +')"><i class="fa fa-ban"></i></a>'}
                    ).draw();
                }
                $('#ModalAddBridesmaid').modal('hide');
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
        url: BASE_URL + "/bridesmaid/delete",
        type: 'POST',
        data: {
            id              : id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ModalAddBridesmaid').modal('hide');
                window.setTimeout(function() {
                    var msg = btoa(json.message);
                    window.location = BASE_URL + "/bridesmaid/wmsg/" + json.website_id + "/" + msg;
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
        url: BASE_URL + "/bridesmaid/get",
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
                $("#gender").select2('val', json.output.gender);
                $('#bridesmaid_id').val(id);
                $('#statusform').val('edit');
                if(json.output.statusfile == 1){ // File Ditemukan //
                    $('#imageNew').hide();
                    $('#imageEdit').show();
                    $('#imageFileEdit').val(json.output.imagefile);
                }else if(json.output.statusfile == 2){ // File tidak Ditemukan //
                    $('#imageEdit').hide();
                    $('#imageNew').show();
                    $('#imageFileEdit').val("");
                }
                $('#ModalAddBridesmaid').modal('show');
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

$("#btn-ResetFormBridesmaid").click(function() {
    ClearForm();
});

function ClearForm(){
    App.startPageLoading({animate: true});
    $('#name').val('');
    $("#gender").select2('val', "0");
    $('.fileinput').fileinput('clear');
    $('#video').val('');
    $('#bridesmaid_id').val('');
    $('#imageFileEdit').val("");
    $('#imageEdit').hide();
    $('#imageNew').show();

    toastr.info("Reset Form Berhasil");
    App.stopPageLoading();
}

$("#btn-PreviewBridesmaid").click(function() {
    var website_id          = $('#website_id').val();
    window.open(BASE_URL + "/bridesmaid/preview/" + website_id,"","width=800,height=600");
});

$("#btn-DeleteImageEdit").click(function() {
    $('#ConfirmDeleteImage').modal('show');
});


$("#btn-ActionDeleteImage").click(function() {
    var bridesmaid_id      = $('#bridesmaid_id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/bridesmaid/delete_image",
        type: 'POST',
        data: {
            id              : bridesmaid_id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ConfirmDeleteImage').modal('hide');
                $('#imageNew').show();
                $('#imageEdit').hide();
                $('#imageFileEdit').val("");
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

