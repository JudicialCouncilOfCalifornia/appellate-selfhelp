/*! Lazy Load XT v2.0.0 2019-05-12
 * http://ressio.github.io/lazy-load-xt
 * (C) 2013-2019 RESSIO
 * Licensed under MIT */

(function (window) {
    window.lazyLoadXT.updateEvent += ' lazyUpdate';
    window.lazyLoadXT.loadEvent += ' lazyReload';
    (window.jQuery || window.Zepto || window.$)(window).on('collapsibleexpand filterablefilter pagechange panelopen popupafteropen tabsactivate', function () {
        window.lazyLoadXT.triggerEvent('Update', window);
    }).on('pageshow pagecontainershow', function () {
        window.lazyLoadXT.triggerEvent('Reload', window);
    });
})(window);
