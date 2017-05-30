$(document).ready(function () {
    workflow.init();

});

workflow = {
    init: function ()
    {
        _this = this;
    },

    sendForm: function () {
        _this = this;

        var sequence = { _token: window.Laravel.csrfToken, id: $('input[name=stepId]').val(), form_json: toJson() };
        $.ajax({
            url: '/workflow/saveStepForm',
            method: 'POST',
            data: sequence,
            success: function (data) {
                console.log('Success!');
                window.location.href = window.location.protocol + "//" + window.location.hostname;
            },
            error: function (data) {
                console.log('Error!');
            }
        });
    },

    sendApproval: function (status, stepId, form) {
        _this = this;
        var sequence = { _token: window.Laravel.csrfToken, id:stepId, status: status, form : form };
        $.ajax({
            url: '/workflow/saveApproval',
            method: 'POST',
            data: sequence,
            success: function (data) {
               window.location.href = window.location.protocol + "//" + window.location.hostname;
            },
            error: function (data) {
                console.log(data)
                console.log('Error!');
            }
        });
    }
};