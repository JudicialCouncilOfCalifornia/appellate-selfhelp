(function (window, document) {
    function check() {
        var links = document.getElementsByTagName('link'),
            i = 0,
            link;
        for (; i < links.length; i++) {
            link = links[i];
            if ((link.rel === 'preload' && link.getAttribute('as') === 'style')
                || (link.rel === 'stylesheet' && link.media === 'only x')) {
                return setTimeout(check, 100);
            }
        }
        i = document.createEvent('HTMLEvents');
        i.initEvent('resize', true, false);
        window.dispatchEvent(i);
    }
    setTimeout(check, 100);
}(window, document));