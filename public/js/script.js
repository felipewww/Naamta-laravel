$(document).ready(function () {
    Script.init();
});

Script = {
    init: function () {
        
    },

    safeLeave: function ()
    {
        console.log('here');
        var _this = Script.safeLeave;

        _this.elements = ['a'];
        //_this.status = false; //false means cannot change page without warning user
        
        _this.setElements = function ()
        {
            //todo
        };
        
        _this.setStatus = function (status) {
            _this.status = status;
        };

        _this.start = function (status)
        {
            if (!status) { status = true; } //true permit to continue without verification
            _this.status = status;
            _this.$modal = $('#stepModalConfirm');

            var $modal = _this.$modal;

            $modal.on('show.bs.modal', function (e) {
                var stay    = $modal.find('#stepModalStay')[0];
                var leave   = $modal.find('#stepModalLeave')[0];

                if (!_this.modalButtonsSetted)
                {
                    $(stay).on('click', function () {
                        _this.reset();
                    });

                    $(leave).on('click', function () {
                        _this.status = true;

                        if (_this.elementClicked == 'F5') {
                            window.location.reload(); //force F5
                        }else{
                            _this.elementClicked.click(); //go to link --force
                        }
                    });

                    _this.modalButtonsSetted = true;
                }
            });

            document.onkeydown  = fkey;
            document.onkeypress = fkey;
            document.onkeyup    = fkey;

            function fkey(e){

                if (e.keyCode == 116) {
                    if (!_this.status) {
                        e.preventDefault();
                        _this.confirm(e, 'F5');
                    }
                }
            }

            for(var idx in _this.elements)
            {
                var e = _this.elements[idx];

                $(e).each(function () {
                    $(this).on('click', function (e) {
                        if (!_this.status)
                        {
                            e.preventDefault();
                            _this.confirm(e, $(this)[0]);
                        }
                    })
                });
            }

            return _this;
        };

        _this.confirm = function (e, element)
        {
            _this.elementClicked = element;
            _this.$modal.modal('show');
        };

        _this.reset = function ()
        {
            _this.elementClicked = null;
        };

        return _this;
    },

    createElement: function (element, innerHTML, attrs, styles)
    {
        if (typeof innerHTML == 'object') {
            attrs = innerHTML;
            innerHTML = '';
        }

        if (typeof attrs    != 'object' ) { attrs = {} }
        if (typeof styles   != 'object' ) { styles = {} }

        var e = document.createElement(element);
        e.innerHTML = innerHTML;

        for(attr in attrs)
        {
            if (attr == 'onclick' && typeof attrs[attr] == 'function') {
                e.addEventListener('click', function (ev) {
                    return ev;
                }(attrs[attr]))
            }else{
                e.setAttribute(attr, attrs[attr]);
            }
        }

        for(css in styles)
        {
            e.style[css] = styles[css];
        }

        return e;
    },

    /**
     * Instantiate it as new() will generate random numbers between two numbers without repeat
     * */
    _Random: function (end, start)
    {
        return function (end, start) {

            if (!this.generateds) {
                this.generateds = [];
            }

            if ( start == undefined ) { start = 1; }
            if ( end == undefined ) { end = 100; }

            var current = Script.Random(end, start);

            if ( this.generateds.length >= ((end-start)+1) ) {
                console.log('Número máximo de randons atingido', this.generateds);
                end = end+10;
            }

            console.log(':: ', this.generateds);

            if ( this.generateds.indexOf(current) >= 0 ) {
                console.log('já existe '+current+'. Tentar criar outro.');
                console.log(this);
                return this._Random(end, start)
            }

            this.generateds.push(current);
            return current;
        }
    },

    Random: function (end, start)
    {
        if ( start == undefined ) { start = 1; }
        if ( end == undefined ) { end = 100; }

        return Math.floor(Math.random() * end) + start;
    }
};