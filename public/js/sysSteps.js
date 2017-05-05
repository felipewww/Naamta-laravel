$(document).ready(function () {
    sysSteps.init();
});

sysSteps = {
    init: function ()
    {
        (function () {
            [].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
                new CBPFWTabs(el);
            });
        })();

        $('.btn-danger.btn-circle').on('click',function(){
            $(this).parent().parent().parent().parent().parent().remove();
        });

        this.containerInactives = $('#not-sortables');
        //this.containerActives = $('#sortables');
        this.getSortables();
    },

    getSortables: function ()
    {
        this.container = $('#sortables').sortable({
            stop: function (e, ui)
            {
                console.log("e",e);
                console.log("ui",ui);
                console.log("\n\n");
                console.log(">",ui.item);
            }
        });
    },

    save: function () {
        var sequence = { _token: window.Laravel.csrfToken, ids: [], toInactive: [] };
        $('#sortables .step-sortable').each(function (e) {
            $this = $(this);
            sequence.ids.push( $(this).attr('data-stepid') );
        });

        var inactives = $('#not-sortables .step-sortable').each(function (e) {
            $this = $(this);
            sequence.toInactive.push( $(this).attr('data-stepid') );
        });

        console.log(sequence);
        // return false;

        $.ajax({
            url: '/steps/saveDefaultStepsPosition',
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
    },
    
    changeStatus: function (e) {
        var currentStatus = $(e).attr('data-status');
        var parent = $(e).closest('.step-sortable');
        console.log(currentStatus);

        if ( currentStatus == 1 )
        {
            $(e).html('Activate');
            $(e).removeClass('btn-danger');
            $(e).addClass('btn-def');
            $(e).attr('data-status', 0);
            $(this.containerInactives).append(parent);
        }
        else if(currentStatus == 0)
        {
            $(e).html('Inactivate');
            $(e).removeClass('btn-def');
            $(e).addClass('btn-danger');
            $(e).attr('data-status', 1);
            $(this.container).append(parent);
        }
    }
};