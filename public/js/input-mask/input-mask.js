/**
 * Created by Barind on 6/8/17.
 */

var FormInputMask = function () {

    var handleInputMasks = function () {

        $(".mask_ktp").inputmask({
            "mask":    "9999999999999999",
            placeholder: "" // remove underscores
        });

        $(".mask_zipcode").inputmask({
            "mask":    "99999",
            placeholder: "" // remove underscores
        });

        $(".mask_phone").inputmask({
            "mask":    "999999999999999",
            placeholder: "" // remove underscores
        });

        $(".date").inputmask({
            "mask":    "dd-mm-yyyy"
        });

        $(".rp").inputmask("99.999.999",{
            numericInput: true,
            rightAlignNumerics: false,
        });


        $(".discount").inputmask("999",{
            numericInput: true,
            rightAlignNumerics: false,
        });

        $(".mask_swiftcode").inputmask({
            "mask":    "AAAA-AA-**-***",
            placeholder: "____-__-__-___" // FORMAT //
        });

        $(".rupiah").inputmask("Rp 999.999.999",{
            numericInput: true,
            rightAlignNumerics: false,
        });

        $(".mobile").inputmask("+62 9999999999999",{
            placeholder: "" // remove underscores
        });

    }


    return {
        //main function to initiate the module
        init: function () {
            handleInputMasks();
        }
    };

}();

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {
        FormInputMask.init(); // init metronic core componets
    });
}
