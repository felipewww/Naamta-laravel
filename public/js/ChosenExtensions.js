$(document).ready(function () {
    ChosenExtensions.init();
});

ChosenExtensions = {
    init: function () {
        $('.chosen-select').chosen();
        //alert('init chosenExt');
    }
};