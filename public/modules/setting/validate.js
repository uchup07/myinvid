var FormValidation = function () {

    // FORM VALIDATION //
    var handleFormSetting = function() {

            var form = $('#form_setting');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    project_name: {
                        required: true
                    },
                    keywords: {
                        required: true
                    },
                    email: {
                        required: true,
                        email : true
                    },
                    description: {
                        required: true
                    },
                    visi: {
                        required: true
                    },
                    misi: {
                        required: true
                    },
                    address: {
                        required: true
                    }
                },
                message: {
                    project_name: {
                        required: "Project name required"
                    },
                    keywords: {
                        required: "Keywords required"
                    },
                    email: {
                        required: "Email required",
                        email : "Format Email is wrong"
                    },
                    description: {
                        required: "Description required"
                    },
                    visi: {
                        required: "Visi required"
                    },
                    misi: {
                        required: "Misi required"
                    },
                    address: {
                        required: "Address required"
                    }

                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var cont = $(element).parent('.input-group');
                    if (cont.size() > 0) {
                        cont.after(error);
                    } else {
                        element.after(error);
                    }
                },

                highlight: function (element) { // hightlight error inputs

                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form.submit(); // submit the form
                }
            });
    }


    // FORM VALIDATION //

    return {
        //main function to initiate the module
        init: function () {

            handleFormSetting(); // Setting

        }

    };

}();

jQuery(document).ready(function() {
    FormValidation.init();
});
