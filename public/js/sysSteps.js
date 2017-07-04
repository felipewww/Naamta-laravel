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
                dataType: 'json',
                success: function (data) {
                    // console.log('Success', data);
                    $(element).closest('.step-sortable').remove();
                    _this.save();
                },
                error: function (data) {
                    console.log('Error', data);
                },
                complete: function () {
                    _this.confirmDeleteModal.modal('hide');
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
        var title, content;
        var sequence = { _token: window.Laravel.csrfToken, ids: [], toInactive: [] };
        $('#sortables .step-sortable').each(function (e) {
            $this = $(this);
            sequence.ids.push( $(this).attr('data-stepid') );
        });

        var inactives = $('#not-sortables .step-sortable').each(function (e) {
            $this = $(this);
            sequence.toInactive.push( $(this).attr('data-stepid') );
        });

        $.ajax({
            url: '/steps/saveDefaultStepsPosition',
            method: 'POST',
            data: sequence,
            success: function (data) {
                alert(data);
                _this.safeLeave.setStatus(true);
                title   = 'Success';
                content = 'Steps has been saved!';
                Script.xmodal().setTitle(title).setContent(content).setHeader('alert-success').show();
            },
            error: function (data){
                title = 'Error during send this form';
                content = 'Please, contact the system administrator.';
                Script.xmodal().setTitle(title).setContent(content).setHeader('alert-danger').show();
            }
        });
    },
    
    // changeStatus: function (e) {
    //     var currentStatus = $(e).attr('data-status');
    //     var parent = $(e).closest('.step-sortable');
    //
    //     this.safeLeave.setStatus(false);
    //
    //     if ( currentStatus == 1 )
    //     {
    //         $(e).html('Activate');
    //         $(e).removeClass('btn-default');
    //         $(e).addClass('btn-success');
    //         $(e).attr('data-status', 0);
    //         $(this.containerInactives).append(parent);
    //     }
    //     else if(currentStatus == 0)
    //     {
    //         $(e).html('Inactivate');
    //         $(e).removeClass('btn-success');
    //         $(e).addClass('btn-default');
    //         $(e).attr('data-status', 1);
    //         $(this.container).append(parent);
    //     }
    // },

    confirmDelete: function (e, stepID)
    {
        $modal = this.confirmDeleteModal.modal('show');
        this.confirmDelete.boxSelected  = e;
        this.confirmDelete.idSelected   = stepID;
    }
};