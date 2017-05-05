$(document).ready(function () {
    appSteps.init();
});

var appSteps = {
   init: function ()
   {
       this.getSortables();
   },

    getSortables: function ()
    {
        this.steps = $('.step-sortable');
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
        console.log(this.steps);
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
            },
            error: function (data) {
                console.log('Error!');
            }
        });
        console.log(sequence);
    },
};