(function (document, window, deferjsList, jsnode, addEventListener, removeEventListener) {

    window.ress_js = deferjsList.push.bind(deferjsList);
    addEventListener = window.addEventListener;
    removeEventListener = window.removeEventListener;

    function loadAll_unbind() {
        removeEventListener("scroll", loadAll_unbind);
        removeEventListener("mouseover", loadAll_unbind);
        removeEventListener("touchstart", loadAll_unbind);
        loadAll();
    }

    function loadAll(i) {
        jsnode = document.getElementsByTagName("script")[i = 0];
        window.ress_js = loadJs;
        for (; deferjsList[i];) {
            loadJs(deferjsList[i++]);
        }
        deferjsList = [];
    }

    function loadJs(src, js) {
        if (!document.getElementById(src)) {
            js = document.createElement("script");
            js.src = js.id = src;
            js.async = true;
            jsnode.parentNode.insertBefore(js, jsnode);
        }
    }

    if (!addEventListener || document.readyState === "complete") {
        loadAll();
    } else {
        addEventListener("load", function () {
            setTimeout(loadAll_unbind, 5500);
        });
        addEventListener("scroll", loadAll_unbind);
        addEventListener("mousemove", loadAll_unbind);
        addEventListener("touchstart", loadAll_unbind, {passive: true});
    }

})(document, window, []);