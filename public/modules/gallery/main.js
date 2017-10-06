/**
 * Created by Barind on 27/09/17.
 */

var counter = y;
var rcounter = y;

$("#btn-addMoreItem").click(function() {

    if(rcounter < 3){
        var html = '<div class="form-group" id="row-' + counter + '">';
        html += '<label class="control-label col-md-1">Image ' + counter + '</label>';
        html += '<div class="col-md-4">';
        html += '    <div class="fileinput fileinput-new" data-provides="fileinput">';
        html += '        <div class="input-group input-large">';
        html += '            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">';
        html += '                <i class="fa fa-file fileinput-exists"></i>&nbsp;';
        html += '                <span class="fileinput-filename"> </span>';
        html += '            </div>';
        html += '            <span class="input-group-addon btn default btn-file">';
        html += '                <span class="fileinput-new"> Select file </span>';
        html += '                <span class="fileinput-exists"> Change </span>';
        html += '                <input type="file"  id="imageFile' + counter + '" name="imageFile[]"> </span>';
        html += '            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>';
        html += '        </div>';
        html += '    </div>';
        html += '</div>';
        html += '<div class="col-md-1">';
        html += '</div>';
        html += '<div class="col-md-2">';
        html += '<button type="button" class="btn btn-warning mt-ladda-btn ladda-button btn-circle" onclick="removeImage('+counter+')"><i class="fa fa-trash"></i> Delete</button>';
        html += '</div>';
        html += '</div> ';

        $("#UploadImage").append(html);
        counter++;
        rcounter++;
    }
});

function removeImage(id){
    if (rcounter>1){
        $("#row-" + id).remove();
        counter--;
        rcounter--;
    }
}

$("#btn-StartUpload").click(function() {
    App.startPageLoading({animate: true});
});

$("#btn-DeleteGalleryImage").click(function() {
    var gallery_id = $('#GalleryID').val();
    App.startPageLoading({animate: true});
    $.ajax({
        type:"POST",
        url: BASE_URL + "/gallery/upload",
        data: {
            gallery_id      : gallery_id,
            _token          : CSRF_TOKEN
        },
        success: function(response){
            var data = JSON.parse(response);
            if(data.status == true){
                toastr.success(data.message);
                $("#loadGallery").load( BASE_URL + "/gallery/load/" + data.website_id);
                counter--;
                rcounter--;
                window.setTimeout(function() {
                    // window.location = BASE_URL + "/gallery/im/" + data.website_id;
                    App.stopPageLoading();
                }, 2000);
            }else{
                toastr.error(data.message);
                App.stopPageLoading();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            toastr.error(errorThrown);
            App.stopPageLoading();
        }
    });
});


function DeleteGalleryImage(galleryID){
    App.startPageLoading({animate: true});
    $('#GalleryID').val(galleryID);
    $('#ConfirmDelete').modal('show');
    App.stopPageLoading();

}



