/*
 * The code is based on
 * https://github.com/filamentgroup/loadCSS/blob/master/src/cssrelpreload.js
 */
(function (window, document) {
    try {
        if (document.createElement('link').relList.supports('preload')) {
            return;
        }
    } catch (e) {
    }

    function preloadPolyfill() {
        var links = document.getElementsByTagName('link'),
            i = 0,
            link;
        for (; i < links.length; i++) {
            link = links[i];
            if (link.rel==='preload' && link.getAttribute('as')==='style') {
                loadCSS(link.href, link, link.media);
                link.rel = null;
            }
        }
    }

    function loadCSS(href, before, media) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = 'only x';

        function onLoad() {
            if (link.addEventListener) {
                link.removeEventListener('load', onLoad);
            }
            link.media = media || 'all';
        }

        if (link.addEventListener) {
            link.addEventListener('load', onLoad);
        }

        (function ready() {
            if (document.body) {
                before.parentNode.insertBefore(link, before);
            } else {
                setTimeout(ready);
            }
        })();

        (function checkCSSLoaded() {
            var sheets = document.styleSheets,
                i = sheets.length;
            while (i--) {
                if (sheets[i].href === link.href) {
                    return onLoad();
                }
            }
            setTimeout(checkCSSLoaded);
        })();
    }

    var run = setInterval(preloadPolyfill, 300);

    function clear() {
        preloadPolyfill();
        clearInterval(run);
    }

    if (window.addEventListener) {
        window.addEventListener('load', clear);
    }
    if (window.attachEvent) {
        window.attachEvent('onload', clear);
    }

    preloadPolyfill();

}(window, document));