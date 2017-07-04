$(document).ready(function () {
    Steps.init();
});

var Steps = {
    containers: {},
    action: 'create',
    from: null,

    init: function () {
        this.setTemplate();
        this.cfgContainers();

        if (this.action == 'edit')
        {
            this.moveEventsEmails();
        }
            this.showSelectsApprovalForm();
    },

    moveEventsEmails: function ()
    {
        var container   = document.getElementById('eventsEmails');
        var e_success   = container.getElementsByClassName('mail-component-success');
        var e_rejected  = container.getElementsByClassName('mail-component-rejected');

        $(e_success).find('> select').each(function () {
            $(this).chosen();
        });

        $(e_rejected).find('> select').each(function () {
            $(this).chosen();
        });

        var selects = {
            success: e_success,
            rejected: e_rejected
        };

        for(var idx in selects)
        {
            var obj = selects[idx];

            while (obj.length > 0)
            {
                var element = obj[0];
                if (element) {

                    (function (element) {
                        var btnDelete = element.getElementsByClassName('delete_component')[0];
                        btnDelete.onclick=function () {
                            element.parentNode.removeChild(element);
                        };
                    })(element);
                    this.containers[idx].appendChild(element);
                }
            }
        }
    },

    showSelectsApprovalForm: function ()
    {
        // var _this = this;
        var radios = $('[name*="morphs_from"]');

        $(radios).each(function () {
            var e = $(this)[0];

            if (e.checked) { Steps.__showSelect(e.value); }

            e.addEventListener('change', function () {
                Steps.__showSelect(this.value);
            });
        });
    },
    
    __showSelect: function (value) 
    {
        var selectContainer = 'list_';

        var all = $('.select_list');

        all.each(function () {
            $(this).find('select').first().removeAttr('required');
            $(this).find('select').first().removeAttr('name');
        });

        all.hide();
        switch (value)
        {
            case "App\\Models\\FormTemplate":
                selectContainer += 'forms';
                this.__showEmailsSelectForm();
                break;

            case "App\\Models\\Approval":
                selectContainer += 'approvals';
                this.__showEmailsSelectApproval();
                break;
        }

        /**
         * The "form select" is showed only when editing a cloned step
         * */
        // if (value == "App\\Models\\FormTemplate" && this.action == 'edit' && this.from != 'clone') {
        if (value == 'App\\Models\\FormTemplate' && this.action == 'edit' && (this.from != 'application' && this.from != 'dev' && this.from != 'local')) {
            console.log(this.action, this.from);
            return false;
        }

        selectContainer = $('#'+selectContainer);
        selectContainer.show();

        selectContainer.find('select').first().attr('required','required');
        selectContainer.find('select').first().attr('name','morphs_item[]');


    },

    __showEmailsSelectApproval: function ()
    {
        // $('#show-emails-form').hide();
        // $('#show-approval').show();
        this.containers.rejected.style.display="block";
        this.containers.success.getElementsByTagName('h4')[0].innerHTML = "On Step Success";
    },

    __showEmailsSelectForm: function ()
    {
        this.containers.rejected.style.display="none";
        this.containers.success.getElementsByTagName('h4')[0].innerHTML = "On forms submitted";
        // $('#show-emails-form').show();
        // $('#show-approval').hide();
    },

    setTemplate: function () {
        this.template = document.getElementById('mail-component');
        this.template.parentNode.removeChild(this.template);

        this.template.removeAttribute('id');
        this.template.style.visibility='visible';

        this.containers.success     = document.getElementById('emails_success');
        this.containers.rejected    = document.getElementById('emails_rejected');
    },

    getTemplate: function (name)
    {
        var clone = this.template.cloneNode(true);
        clone.setAttribute('class', 'selects');
        var selects = $(clone).find('select');

        var btnDelete = clone.getElementsByClassName('delete_component')[0];
        btnDelete.onclick=function () {
            clone.parentNode.removeChild(clone);
        };

        var i = 0;
        
        while (i < selects.length)
        {
            var slt = selects[i];
            slt.setAttribute('class', 'chosen-select');
            slt.setAttribute('name', name+'['+i+'][]');
            i++;
        }
        
        return clone;
    },

    cfgContainers: function ()
    {
        for(var idx in this.containers)
        {
            var container = this.containers[idx];
            var button = container.getElementsByClassName('button_add_mail')[0];

            this._cfgButton(button, container);
        }
    },

    _cfgButton: function (btn, container)
    {
        var _this = this;

        btn._Random = new Script._Random;
        btn.addEventListener('click', function () {
            var name = container.getAttribute('id') + '[' + btn._Random(100,1) + ']';
            container.appendChild(_this.getTemplate(name));
            ChosenExtensions.init();
        })
    }
};