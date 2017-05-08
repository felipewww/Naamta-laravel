$(document).ready(function () {
    sysSteps.init();
});

sysSteps = {
    init: function ()
    {
        _this = this;
        this.safeLeave = new Script.safeLeave().start();

        (function () {
            [].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
                new CBPFWTabs(el);
            });
        })();

        // $('.btn-danger.btn-circle').on('click',function(){
        //     $(this).parent().parent().parent().parent().parent().remove();
        // });

        this.confirmDeleteModal = $('#deleteStepModalConfirm');
        var cancel      = this.confirmDeleteModal.find('#stepModalCancel')[0];
        var del        = this.confirmDeleteModal.find('#stepModalDelete')[0];

        $(cancel).on('click', function () {
            _this.confirmDelete.boxSelected     = null;
            _this.confirmDelete.idSelected      = null;
        });

        $(del).on('click', function () {
            var element = _this.confirmDelete.boxSelected;

            var data = {
                _token: window.Laravel.csrfToken,
                id: _this.confirmDelete.idSelected
            };
            
            $.ajax({
                url: 'step/delete',
                method: 'post',
                data: data,
                // dataType: 'json',
                success: function (data) {
                    console.log('Success', data);
                    $(element).closest('.step-sortable').remove();
                },
                error: function (data) {
                    console.log('Error', data);
                }
            });
        });

        this.containerInactives = $('#not-sortables');
        //this.containerActives = $('#sortables');
        this.getSortables();
    },

    getSortables: function ()
    {
        _this = this;
        this.container = $('#sortables').sortable({
            stop: function (e, ui)
            {
                _this.safeLeave.setStatus(false);
            }
        });
    },

    save: function () {
        _this = this;

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
                _this.safeLeave.setStatus(true);
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

        this.safeLeave.setStatus(false);

        if ( currentStatus == 1 )
        {
            $(e).html('Activate');
            $(e).removeClass('btn-custom');
            $(e).addClass('btn-def');
            $(e).attr('data-status', 0);
            $(this.containerInactives).append(parent);
        }
        else if(currentStatus == 0)
        {
            $(e).html('Inactivate');
            $(e).removeClass('btn-def');
            $(e).addClass('btn-custom');
            $(e).attr('data-status', 1);
            $(this.container).append(parent);
        }
    },

    confirmDelete: function (e, stepID)
    {
        $modal = this.confirmDeleteModal.modal('show');
        this.confirmDelete.boxSelected  = e;
        this.confirmDelete.idSelected   = stepID;
    }
};