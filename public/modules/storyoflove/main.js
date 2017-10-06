/**
 * Created by Barind on 27/09/17.
 */

$("#btn-AddStory").click(function() {
    App.startPageLoading({animate: true});
    ClearForm();
    $('#statusform').val('add');
    $('#ModalAddStory').modal('show');
    App.stopPageLoading();
});

$("form#form_storyoflove").submit(function(){
    App.startPageLoading({animate: true});
    var formData = new FormData(this);
    $.ajax({
        url: BASE_URL + '/storyoflove/save',
        type: 'POST',
        data: formData,
        async: false,
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                var table = $('#tbl_storyoflove').DataTable();
                ClearForm();
                if(json.output.statusform == 'add'){
                    table.row.add(
                        {"id" : json.output.id,
                            "title" : json.output.title,
                            "date_story" : json.output.date_story,
                            "description" : json.output.description,
                            "href" : '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('+ json.output.id +')"><i class="glyphicon glyphicon-pencil"></i></a> <a href="javascript:void(0);" class="btn btn-danger" onclick="deleteList('+ json.output.id +')"><i class="fa fa-ban"></i></a>'}
                    ).draw();
                }
                $('#ModalAddStory').modal('hide');
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
        url: BASE_URL + "/storyoflove/delete",
        type: 'POST',
        data: {
            id              : id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.success(json.message);
                $('#ModalAddStory').modal('hide');
                window.setTimeout(function() {
                    var msg = btoa(json.message);
                    window.location = BASE_URL + "/storyoflove/wmsg/" + json.website_id + "/" + msg;
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
        url: BASE_URL + "/storyoflove/get",
        type: 'POST',
        data: {
            id              : id,
            _token          : CSRF_TOKEN
        },
        success: function (data) {
            var json = JSON.parse(data);
            if(json.status == true){
                toastr.info(json.message);
                $('#title').val(json.output.title);
                $('#description').val(json.output.description);
                $('#date_story').val(json.output.date_story);
                $('#video').val(json.output.video);
                $('#storyoflove_id').val(id);
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
                $('#ModalAddStory').modal('show');
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

$("#btn-ResetFormStory").click(function() {
    ClearForm();
});

function ClearForm(){
    App.startPageLoading({animate: true});
    $('#title').val('');
    $('#description').val('');
    $('#date_story').val('');
    $('.fileinput').fileinput('clear');
    $('#video').val('');
    $('#storyoflove_id').val('');
    $('#imageFileEdit').val("");
    $('#imageEdit').hide();
    $('#imageNew').show();

    toastr.info("Reset Form Berhasil");
    App.stopPageLoading();
}

$("#btn-PreviewStory").click(function() {
    var website_id          = $('#website_id').val();
    window.open(BASE_URL + "/storyoflove/preview/" + website_id,"","width=800,height=600");
});

$("#btn-DeleteImageEdit").click(function() {
    $('#ConfirmDeleteImage').modal('show');
});


$("#btn-ActionDeleteImage").click(function() {
    var storyoflove_id      = $('#storyoflove_id').val();
    App.startPageLoading({animate: true});

    $.ajax({
        url: BASE_URL + "/storyoflove/delete_image",
        type: 'POST',
        data: {
            id              : storyoflove_id,
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

