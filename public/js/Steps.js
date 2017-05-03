$(document).ready(function () {
    Steps.init();
});

var Steps = {
    containers: {},
    action: 'create',

    init: function () {
        this.setTemplate();
        this.cfgContainers();

        if (this.action == 'edit')
        {
            this.moveEventsEmails();
        }
    },

    moveEventsEmails: function ()
    {
        var container = document.getElementById('eventsEmails');
        // var emails = container
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