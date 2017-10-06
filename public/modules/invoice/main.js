/**
 * Created by Barind on 06/10/17.
 */


function ConfirmNow(InvoiceID){
    App.startPageLoading({animate: true});
    ClearForm();
    $('#ModalConfirm').modal('show');
    App.stopPageLoading();
}


function ClearForm(){

}
