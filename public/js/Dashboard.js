Dashboard = {
    init: function ()
    {

    },

    deleteApplication: function (id)
    {
        var _this = this;
        this.confirmDeleteModal = $('#modalDelete');

        $("#modalDeleteLabel").html("Confirm delete application");

        // var cancel      = this.confirmDeleteModal.find('#modalDeleteCancelAction')[0];
        var del         = this.confirmDeleteModal.find('#modalDeleteConfirmation')[0];

        this.confirmDeleteModal.modal('show');

        del.onclick = function (e) {
            e.preventDefault();
            var data = {
                _token: window.Laravel.csrfToken,
                id: id
            };

            $.ajax({
                url: "/applications/"+id+"/deleteApp",
                // type: "DELETE",
                data: data,
                method: 'post',
                // dataType: "json",
                success: function () {
                    window.location.href="/";
                },
                error: function (data) {
                    Script.xmodal().setTitle("Something went wrong").setContent("Please, contact the system administrator").show();
                },
                complete: function () {
                    _this.confirmDeleteModal.modal('hide');
                }
            });
        };

    },
};