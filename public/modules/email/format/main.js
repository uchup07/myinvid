/**
 * Created by Barind on 06/10/17.
 */


function setFormat(websiteID){
    App.startPageLoading({animate: true});
    $.ajax({
        url: BASE_URL + "/email/format/get",
        type: 'POST',
        data: {
            id              : websiteID,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                if(json.flag == 1){
                    $('#email_format_id').val(json.output.id);
                    $('#title').val(json.output.title);
                    $('#content').val(json.output.content);
                    $('#attachment').val(json.output.attachment);
                    if(json.output.statusfile == 1){ // File Ditemukan //
                        $('#imageNew').hide();
                        $('#imageEdit').show();
                        $('#imageFileEdit').val(json.output.imagefile);
                    }else if(json.output.statusfile == 2){ // File tidak Ditemukan //
                        $('#imageEdit').hide();
                        $('#imageNew').show();
                        $('#imageFileEdit').val("");
                    }
                }else if(json.flag == 2){
                    $('#email_format_id').val(0);
                    toastr.info(json.message);
                }
                $('#website_id').val(websiteID);
                $('#ModalSet').modal('show');
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

$("form#form_confirmation").submit(function(){
    App.startPageLoading({animate: true});
    var formData = new FormData(this);
    $.ajax({
        url: BASE_URL + '/invoice/save',
        type: 'POST',
        data: formData,
        async: false,
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                ClearForm();
                $('#ModalConfirm').modal('hide');
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


function ClearForm(){
    App.startPageLoading({animate: true});
    $('#bank_information').val('');
    $('#name').val('');
    $('#total').val('');
    toastr.info("Reset Form Berhasil");
    App.stopPageLoading();
}




$("#btn-ResetFormConfirm").click(function() {
    ClearForm();
});
