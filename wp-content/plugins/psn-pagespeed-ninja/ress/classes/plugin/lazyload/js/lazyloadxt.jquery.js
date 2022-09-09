/*! Lazy Load XT v2.0.0 2019-05-12
 * http://ressio.github.io/lazy-load-xt
 * (C) 2013-2019 RESSIO
 * Licensed under MIT */

(function ($, window) {
    var _lazyLoadXT = window.lazyLoadXT;
    _lazyLoadXT.extend(window.lazyLoadXT, $.lazyLoadXT);

    $.fn.lazyLoadXT = function (overrides) {
        return this.each(function (index, el) {
            if (el === window) {
                _lazyLoadXT(overrides);
            } else {
                var params = overrides;
                params.selector = el;
                _lazyLoadXT(params);
            }
        });

    };
})(window.jQuery || window.Zepto || window.$, window);
