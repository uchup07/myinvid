var FormValidation = function () {

    // FORM VALIDATION //
    var handleFormInfo = function() {

            var form = $('#form_wedding_information');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    boy_name: {
                        required: true
                    },
                    boy_nick_name: {
                        required: true
                    },
                    boy_email: {
                        required: true,
                        email:true
                    },
                    boy_dob: {
                        required: true
                    },
                    girl_name: {
                        required: true
                    },
                    girl_nick_name: {
                        required: true
                    },
                    girl_email: {
                        required: true,
                        email:true
                    },
                    girl_dob: {
                        required: true
                    },
                    ceremony_start_date: {
                        required: true
                    },
                    ceremony_start_time: {
                        required: true
                    },
                    ceremony_end_date: {
                        required: true
                    },
                    ceremony_end_time: {
                        required: true
                    },
                    ceremony_place: {
                        required: true
                    },
                    wedding_start_date: {
                        required: true
                    },
                    wedding_start_time: {
                        required: true
                    },
                    wedding_end_date: {
                        required: true
                    },
                    wedding_end_time: {
                        required: true
                    },
                    wedding_place: {
                        required: true
                    }
                },
                messages: {
                    boy_name: {
                        required: "Nama Lengkap Pria wajib diisi"
                    },
                    boy_nick_name: {
                        required: "Nama Panggilan Pria wajib diisi"
                    },
                    boy_email: {
                        required: "Email Pria wajib diisi",
                        email: "Format Email Pria salah"
                    },
                    boy_dob: {
                        required: "Tanggal Lahir Pria wajib diisi"
                    },
                    girl_name: {
                        required: "Nama Lengkap Wanita wajib diisi"
                    },
                    girl_nick_name: {
                        required: "Nama Panggilan Wanita wajib diisi"
                    },
                    girl_email: {
                        required: "Email Wanita wajib diisi",
                        email: "Format Email Wanita salah"
                    },
                    girl_dob: {
                        required: "Tanggal Lahir Wanita wajib diisi"
                    },
                    ceremony_start_date: {
                        required: "Tanggal mulai wajib diisi"
                    },
                    ceremony_start_time: {
                        required: "Waktu mulai wajib diisi"
                    },
                    ceremony_end_date: {
                        required: "Tanggal berakhir wajib diisi"
                    },
                    ceremony_end_time: {
                        required: "Waktu berakhir wajib diisi"
                    },
                    ceremony_place: {
                        required: "Tempat wajib diisi"
                    },
                    wedding_start_date: {
                        required: "Tanggal mulai wajib diisi"
                    },
                    wedding_start_time: {
                        required: "Waktu mulai wajib diisi"
                    },
                    wedding_end_date: {
                        required: "Tanggal berakhir wajib diisi"
                    },
                    wedding_end_time: {
                        required: "Waktu berakhir wajib diisi"
                    },
                    wedding_place: {
                        required: "Tempat wajib diisi"
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

    var handleFormAvatar = function() {

            var form = $('#form_avatar');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    avatar: {
                        required: true
                    }
                },
                message: {
                    avatar: {
                        required: "Avatar required"
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
                    loading();
                    form.submit(); // submit the form
                }

            });
    }

    // FORM VALIDATION //

    return {
        //main function to initiate the module
        init: function () {

            handleFormInfo(); // Member Info
            handleFormAvatar(); // Avatar

        }

    };

}();

jQuery(document).ready(function() {
    FormValidation.init();
});
