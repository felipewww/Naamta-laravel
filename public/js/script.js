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

    Random: function (end, start)
    {
        if ( start == undefined ) { start = 1; }
        if ( end == undefined ) { end = 100; }

        return Math.floor(Math.random() * end) + start;
    },
};