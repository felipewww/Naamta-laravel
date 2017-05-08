$(document).ready(function () {
    appSteps.init();
});

var appSteps = {
    appID: null,
    
    init: function ()
    {
        this.safeLeave = new Script.safeLeave().start();
        this.getSortables();
    },

    getSortables: function ()
    {
        _this = this;
        this.steps = $('.step-sortable');
        this.container = $('#sortables').sortable({
            stop: function (e, ui)
            {
                _this.safeLeave.setStatus(false);
            }
        });
    },

    save: function () {
        _this = this;
        var sequence = { _token: window.Laravel.csrfToken, ids: [] };
        $('.step-sortable').each(function (e) {
            $this = $(this);
            sequence.ids.push( $(this).attr('data-stepid') );
        });
        
        $.ajax({
            url: 'saveStepsPosition',
            method: 'POST',
            data: sequence,
            success: function (data) {
                console.log('Success!');
                _this.safeLeave.setStatus(true);
            },
            error: function (data) {
                console.log('Error!');
            }
        });
        console.log(sequence);
    },

    changeStatus: function (e, stepID)
    {
        var currentStatus = e.getAttribute('data-currentstatus');

        var data = {
            id: stepID,
            currentStatus: currentStatus,
            _token: window.Laravel.csrfToken
        };

        console.log('data', data);

        $.ajax({
            url: 'changeStepStatus',
            method: 'post',
            data: data,
            dataType: 'json',
            success: function (data) {

                $(e).attr('data-currentstatus', data.newStatus);
                if (data.newStatus)
                {
                    $(e).removeClass('btn-save');
                    $(e).addClass('btn-custom');
                    $(e).find('> i').first().removeClass('fa-check');
                    $(e).find('> i').first().addClass('fa-ban');
                }
                else
                {
                    $(e).removeClass('btn-custom');
                    $(e).addClass('btn-save');
                    $(e).find('> i').first().removeClass('fa-ban');
                    $(e).find('> i').first().addClass('fa-check');
                }
            },
            error: function (data) {
                // console.log('Error!', data);
            }
        });
    }
};