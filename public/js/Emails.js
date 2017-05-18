$(document).ready(function () {
    Emails.init();
});

var Emails = {
    quill: null,
    quillValidated: false,

    init: function () 
    {
        this.quillEditor();
        this.sendQuillContent();
    },

    /**
    * Get quill content and copy it to textarea, before submit.
    * */
    sendQuillContent: function ()
    {
        var _this       = this;
        var submit      = document.getElementById('sendform');
        var textarea    = document.getElementById('text-hidden');

        submit.addEventListener('click', function (e) {
            if (!_this.quillValidated) {
                e.preventDefault();
            }

            textarea.innerHTML = _this.quill.container.firstChild.innerHTML;

            _this.quillValidated = true;
            this.click();
        })
    },

    /**
     * Setup quillEditor
     * */
    quillEditor: function () 
    {
        this.quill = new Quill('#text', 
            {
                modules: {
                    toolbar: [
                        [
                            { header: [1, 2, false] }
                        ],
                        ['bold', 'italic', 'underline'],
                        ['image', 'code-block']
                    ]
                },
                theme: 'snow'
        });
    }
};