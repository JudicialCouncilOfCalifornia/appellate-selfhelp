"use strict";

(function (window, document, script_list, requestAnimationFrame, destination, writeBuffer) {

    if (document.readyState === "complete") {
        loadAll();
    } else if (window.addEventListener) {
        window.addEventListener("load", loadAll);
    } else { // IE
        window.attachEvent("onload", loadAll);
    }

    function loadAll(i, source, collection) {
        // LOAD JAVASCRIPTS
        collection = document.getElementsByTagName("script");
        for (i = 0; source = collection[i++];) {
            if (source.type === "text/ress") {
                script_list.push(source);
            }
        }

        writeBuffer = "";
        document.write = function (str) {
            writeBuffer += str;
        };
        document.writeln = function (str) {
            writeBuffer += str + "\n";
        };
        loadNextJavascript();
    }

    function loadNextJavascript(source, src, parent, p, child) {

        if (writeBuffer) {
            p = document.createElement("p");
            p.innerHTML = writeBuffer;
            source = destination.nextSibling;
            for (; (child = p.firstChild);) {
                destination.parentNode.insertBefore(child, source);
            }
            writeBuffer = "";
        }

        if ((source = script_list.shift())) {
            destination = document.createElement("script");
            for (p = 0; child = source.attributes[p++];) {
                destination.setAttribute(child.nodeName, child.nodeValue);
            }
            destination.type = "text/javascript";

            if ((src = source.getAttribute("ress-src"))) {
                destination.onload = destination.onerror = destination.onreadystatechange = function () {
                    if (destination.onload && (!destination.readyState || destination.readyState === "loaded" || destination.readyState === "complete")) {
                        destination.onload = destination.onerror = destination.onreadystatechange = null;
                        setTimeout(loadNextJavascript);
                    }
                };
                destination.src = src;
            } else {
                src = source.text || source.textContent || source.innerHTML;
                if (destination.text === "") { // HTML5 property
                    destination.text = src;
                } else { // Legacy browsers
                    destination.appendChild(document.createTextNode(src));
                }
                setTimeout(loadNextJavascript);
            }

            parent = source.parentNode;
            parent.insertBefore(destination, source);
            parent.removeChild(source);
        }
    }

})(window, document, []);
