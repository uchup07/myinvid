/**
 * Created by Barind on 6/8/17.
 */
var ComponentsBootstrapMaxlength = function () {

    var handleBootstrapMaxlength = function() {
        $('.maxlength').maxlength({
            limitReachedClass: "label label-danger",
        })


        $('.maxlength_textarea').maxlength({
            limitReachedClass: "label label-danger",
            warningClass: "label label-success",
            alwaysShow: true
        });

    }

    return {
        //main function to initiate the module
        init: function () {
            handleBootstrapMaxlength();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsBootstrapMaxlength.init();
});

