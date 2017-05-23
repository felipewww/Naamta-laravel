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

    sendApproval: function (status, stepId) {
        _this = this;

        var sequence = { _token: window.Laravel.csrfToken, id:stepId, status: status };
        $.ajax({
            // url: '/workflow/saveApproval',
            url: '/workflow/saveApproval',
            method: 'POST',
            data: sequence,
            dataType: 'json',
            success: function (data) {
                if (data.status) {
                    location.reload();
                }
                console.log('Success!');
            },
            error: function (data) {
                console.log('Error!');
            }
        });
    }
};