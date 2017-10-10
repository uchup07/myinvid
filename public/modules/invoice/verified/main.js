/**
 * Created by Barind on 06/10/17.
 */


function ConfirmNow(InvoiceID){
    App.startPageLoading({animate: true});
    $.ajax({
        url: BASE_URL + "/invoice/get",
        type: 'POST',
        data: {
            id              : InvoiceID,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                ClearForm();
                $('#total_tagihan').val(json.output.total_display);
                $('#date_transaction').val(json.output.date_now);
                $('#invoice_id').val(InvoiceID);
                $('#ModalConfirm').modal('show');
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
