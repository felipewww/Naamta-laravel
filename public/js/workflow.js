$(document).ready(function () {
    workflow.init();

});

workflow = {
    init: function ()
    {
        _this = this;
        createTabs($('input[name=containers]').val(), true);
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
            },
            error: function (data) {
                console.log('Error!');
            }
        });
        console.log(sequence);
    }
};