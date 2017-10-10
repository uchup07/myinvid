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

function ConfirmVerifiedNow(ConfirmationID){
    App.startPageLoading({animate: true});
    $.ajax({
        url: BASE_URL + "/confirmation/get",
        type: 'POST',
        data: {
            id              : ConfirmationID,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                $('#no_invoice').val(json.output.no_invoice);
                $('#confirmation_id').val(ConfirmationID);
                $('#name').val(json.output.name);
                $('#email').val(json.output.email);
                $('#domain').val(json.output.domain);
                $('#tagihan').val(json.output.tagihan_display);
                $('#nama_pengirim').val(json.output.pengirim);
                $('#no_rekening').val(json.output.no_rekening);
                $('#bank').val(json.output.bank);
                $('#nominal').val(json.output.nominal_display);
                $('#status_confirmation').val(json.output.status_approve_display);
                if(json.output.status_approve == 2 || json.output.status_approve == 3){
                    $('#btn-RejectConfirmation').prop('disabled',true);
                    $('#btn-ApproveConfirmation').prop('disabled',true);
                    toastr.warning('Data Konfirmasi ' + json.output.status_approve_display + ' tersebut sudah tidak dapat diubah.');
                }else{
                    $('#btn-RejectConfirmation').prop('disabled',false);
                    $('#btn-ApproveConfirmation').prop('disabled',false);
                }
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


$("#btn-RejectConfirmation").click(function() {
    $('#ConfirmReject').modal('show');
});

$("#btn-ApproveConfirmation").click(function() {
    $('#ConfirmApprove').modal('show');
});

$("#btn-RejectAction").click(function() {
    var confirmation_id                     = $('#confirmation_id').val();
    App.startPageLoading({animate: true});
    window.location = BASE_URL + "/confirmation/reject/" + confirmation_id;
});

$("#btn-ApproveAction").click(function() {
    var confirmation_id                     = $('#confirmation_id').val();
    App.startPageLoading({animate: true});
    window.location = BASE_URL + "/confirmation/approve/" + confirmation_id;
});


