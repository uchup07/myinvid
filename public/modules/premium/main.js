/**
 * Created by Barind on 27/09/17.
 */

$( "#domain" ).keyup(function() {
    $.ajax({
        url: BASE_URL + "/website/manage/check_domain",
        type: 'POST',
        data: {
            key             : $(this).val(),
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
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

