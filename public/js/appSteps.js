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
        var header, title, content;

        $('.step-sortable').each(function (e) {
            $this = $(this);
            sequence.ids.push( $(this).attr('data-stepid') );
        });

        $.ajax({
            url: 'saveStepsPosition',
            method: 'POST',
            data: sequence,
            success: function (data) {
                _this.safeLeave.setStatus(true);
                var response = JSON.parse(data);
                title   = response.title;
                content = response.message;
                header  = response.header;
                Script.xmodal().setTitle(title).setContent(content).setHeader(header).show();
            },
            error: function (data) {
                var response = JSON.parse(data);
                title = response.title;
                content = response.message;
                header  = response.header;
                Script.xmodal().setTitle(title).setContent(content).setHeader(header).show();
            }
        });
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

                var title, content;
                if(data.reqStatus == 'disallowed'){
                    title   = 'Action not allowed';
                    content = 'You cannot change this step status because the application is already approved and released to client';
                    Script.xmodal().setTitle(title).setContent(content).setHeader('alert-danger').show();
                    return false;
                }

                if (data.approved) {
                    $(e).attr('data-currentstatus', data.newStatus);
                    if (data.newStatus == '1')
                    {
                        $(e).removeClass('btn-save');
                        $(e).addClass('btn-danger');
                        $(e).find('> i').first().removeClass('fa-check');
                        $(e).find('> i').first().addClass('fa-ban');
                    }
                    else
                    {
                        $(e).removeClass('btn-danger');
                        $(e).addClass('btn-save');
                        $(e).find('> i').first().removeClass('fa-ban');
                        $(e).find('> i').first().addClass('fa-check');
                    }
                }
                else
                {
                    title   = 'Action not allowed';
                    content = 'You cannot approve this step because it has no form or screen related. Please, click on edit button and fill correctly settings before active.';
                    Script.xmodal().setTitle(title).setContent(content).setHeader('alert-danger').show();
                }
            },
            error: function (data) {
                // console.log('Error!', data);
            }
        });
    }
};