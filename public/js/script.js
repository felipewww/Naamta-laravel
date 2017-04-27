$(document).ready(function () {
    Script.init();
});

Script = {
    init: function () {
        
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