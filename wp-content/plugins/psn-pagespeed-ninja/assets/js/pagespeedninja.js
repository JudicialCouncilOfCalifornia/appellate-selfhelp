'use strict';
(function () {

    var pagespeed_api = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed';

    var tab_name,
        newUrl = null,
        newAjax = false,
        origScore = {},
        psnScore = {};

    var stored_items = [
        'pagespeed_desktop_orig',
        'pagespeed_mobile_orig',
        'pagespeed_usability_orig',
        'pagespeed_desktop',
        'pagespeed_mobile',
        'pagespeed_usability'
    ];

    function streamoptimizer_check() {
        if (jQuery('.streamoptimizer').val() === 'stream') {
            jQuery('.streamdisabled').attr('disabled', true);
        } else {
            jQuery('.streamdisabled').removeAttr('disabled');
        }
    }

    function updatePSIScore(score, span) {
        var className;
        if (score === '') {
            className = 'gps_unknown';
        } else if (score < 65) {
            className = 'gps_error';
        } else if (score < 85) {
            className = 'gps_warning';
        } else {
            className = 'gps_excellent';
        }
        span.innerHTML = score;
        jQuery(span)
            .removeClass('gps_loading gps_unknown gps_error gps_warning gps_excellent')
            .addClass(className);
    }

    function rearrangeItems(ruleResults, prefix, group) {
        var passedList = [],
            considerList = [],
            shouldFixList = [],
            rule;

        for (rule in ruleResults) {
            if (ruleResults[rule].groups[0] !== group) {
                continue;
            }
            var div = document.getElementById(prefix + '_' + rule);
            if (div) {
                var score = ruleResults[rule].ruleImpact;
                div.setAttribute('data-score', score);
                if (score === 0.0) {
                    // passed
                    passedList.push(div);
                } else if (score < 10.0) {
                    // consider
                    considerList.push(div);
                } else {
                    // should
                    shouldFixList.push(div);
                }

                if (origScore[prefix]) {
                    var orig = origScore[prefix][rule].ruleImpact,
                        warnLevel = 1;
                    if (score === 0.0 || (score < orig && score < 10.0)) {
                        warnLevel = 0;
                    } else if (score >= orig + 0.05 && score >= 10.0) {
                        warnLevel = 2;
                    }
                    jQuery(div).find('input:checked')
                        .toggleClass('psiwarn', warnLevel === 1)
                        .toggleClass('psierror', warnLevel === 2);
                }
            }
        }

        function comparator(a, b) {
            var a_score = parseFloat(a.getAttribute('data-score')),
                b_score = parseFloat(b.getAttribute('data-score'));
            return ((a_score < b_score) ? 1 : ((a_score > b_score) ? -1 : 0));
        }

        considerList.sort(comparator);
        shouldFixList.sort(comparator);

        jQuery('#' + prefix + '-passed').append(passedList).toggleClass('hide', !passedList.length);
        jQuery('#' + prefix + '-consider-fixing').append(considerList).toggleClass('hide', !considerList.length);
        jQuery('#' + prefix + '-should-fix').append(shouldFixList).toggleClass('hide', !shouldFixList.length);
    }

    function loadPageSpeedCached() {
        var result = true,
            psn_cache_stamp = window.psn_cache_timestamp || 'x',
            prev_stamp = parseInt(window.localStorage.getItem('psn_cache_timestamp')),
            prev_time = parseInt(window.localStorage.getItem('psn_result_time')),
            cached_scores;

        origScore = JSON.parse(window.localStorage.getItem('psn_result_origscores')) || {};
        cached_scores = JSON.parse(window.localStorage.getItem('psn_result_psnscores'));

        if (prev_stamp !== psn_cache_stamp || prev_time < new Date().getTime() - 15 * 60 * 1000) {
            result = false;
        }

        for (var i = 0; i < stored_items.length; i++) {
            var key = stored_items[i],
                score = window.localStorage.getItem(key);
            if (score === null || score === '') {
                result = false;
            } else {
                updatePSIScore(score, document.getElementById(key));
            }
        }

        if (cached_scores !== null) {
            if ('desktop' in cached_scores) {
                rearrangeItems(cached_scores['desktop'], 'desktop', 'SPEED');
            } else {
                result = false;
            }
            if ('mobile' in cached_scores) {
                rearrangeItems(cached_scores['mobile'], 'mobile', 'SPEED');
                rearrangeItems(cached_scores['mobile'], 'usability', 'USABILITY');
            } else {
                result = false;
            }
        } else {
            result = false;
        }

        return result;
    }

    function savePageSpeedCached() {
        if (!window.localStorage) {
            return;
        }

        window.localStorage.setItem('psn_cache_timestamp', window.psn_cache_timestamp);
        window.localStorage.setItem('psn_result_time', new Date().getTime().toString());
        window.localStorage.setItem('psn_result_psnscores', JSON.stringify(psnScore));
        window.localStorage.setItem('psn_result_origscores', JSON.stringify(origScore));

        for (var i = 0; i < stored_items.length; i++) {
            var key = stored_items[i];
            window.localStorage.setItem(key, document.getElementById(key).innerText);
        }
    }

    function loadPageSpeed() {
        var url = location.href.split('/').slice(0, -2).join('/') + '/';
        var url_orig = url + '?pagespeedninja=no';
        var url_random = url + '?pagespeedninja=' + Math.random();

        jQuery('#pagespeed_desktop_orig,#pagespeed_mobile_orig,#pagespeed_usability_orig,' +
            '#pagespeed_desktop,#pagespeed_mobile,#pagespeed_usability').addClass('gps_loading');

        jQuery.when(
            jQuery.get(pagespeed_api, {strategy: 'desktop', url: url_orig}).done(function (response) {
                var score = '';
                try {
                    score = response.ruleGroups.SPEED.score;
                    origScore['desktop'] = response.formattedResults.ruleResults;
                } catch (e) {
                    console.log(e);
                    origScore['desktop'] = null;
                }
                updatePSIScore(score, document.getElementById('pagespeed_desktop_orig'));
            }),

            jQuery.get(pagespeed_api, {strategy: 'mobile', url: url_orig}).done(function (response) {
                var score1 = '',
                    score2 = '';
                try {
                    score1 = response.ruleGroups.SPEED.score;
                    score2 = response.ruleGroups.USABILITY.score;
                    origScore['mobile'] = response.formattedResults.ruleResults;
                    origScore['usability'] = response.formattedResults.ruleResults;
                } catch (e) {
                    console.log(e);
                    origScore['mobile'] = null;
                    origScore['usability'] = null;
                }
                updatePSIScore(score1, document.getElementById('pagespeed_mobile_orig'));
                updatePSIScore(score2, document.getElementById('pagespeed_usability_orig'));
            }),

            // generate optimized assets
            jQuery.get(url, {pagespeedninja: 'desktop'}).then(function () {
                return jQuery.get(url, {pagespeedninja: 'mobile'});
            })
        ).always(function () {

            jQuery.get(pagespeed_api, {strategy: 'desktop', url: url_random}).done(function (response) {
                var score = '';
                try {
                    score = response.ruleGroups.SPEED.score;
                    psnScore['desktop'] = response.formattedResults.ruleResults;
                    rearrangeItems(response.formattedResults.ruleResults, 'desktop', 'SPEED');
                } catch (e) {
                    console.log(e);
                }
                updatePSIScore(score, document.getElementById('pagespeed_desktop'));
            }).always(function () {

                jQuery.get(pagespeed_api, {strategy: 'mobile', url: url_random}).done(function (response) {
                    var score1 = '',
                        score2 = '';
                    try {
                        score1 = response.ruleGroups.SPEED.score;
                        score2 = response.ruleGroups.USABILITY.score;
                        psnScore['mobile'] = response.formattedResults.ruleResults;
                        rearrangeItems(response.formattedResults.ruleResults, 'mobile', 'SPEED');
                        rearrangeItems(response.formattedResults.ruleResults, 'usability', 'USABILITY');
                    } catch (e) {
                        console.log(e);
                    }
                    updatePSIScore(score1, document.getElementById('pagespeed_mobile'));
                    updatePSIScore(score2, document.getElementById('pagespeed_usability'));
                }).always(function () {
                    savePageSpeedCached();
                });

            });
        });
    }

    function getQueryParameterByName(name) {
        var match = (new RegExp('[?&]' + name + '=([^&]*)')).exec(location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }

    function populateCheckboxes() {
        jQuery('#pagespeedninja_form').children('input[type=hidden]').each(function () {
            var id = this.id.split('_');
            if (id.length === 4) {
                var section = id[3];
                var checked = this.value === '1';
                var prefixes = ['mobile', 'desktop', 'usability'];
                this.initstate = checked;
                for (var i = 0; i < prefixes.length; i++) {
                    var prefix = prefixes[i];
                    var element = document.getElementById('pagespeedninja_config_' + prefix + '_' + section);
                    if (element) {
                        element.checked = checked;
                        if (checked) {
                            element.parentNode.className += ' show';
                        }
                    }
                }
            }
        });
    }

    function basicLoadATFCSS() {
        if (tab_name === 'basic') {
            var $enabled = jQuery('#pagespeedninja_config_psi_MinimizeRenderBlockingResources');
            if ($enabled.length && $enabled.val() === '1') {
                var $css_abovethefoldstyle = jQuery('#pagespeedninja_config_css_abovethefoldstyle');
                if ($css_abovethefoldstyle.length && $css_abovethefoldstyle.val() === '') {
                    var local = (document.getElementById('pagespeedninja_config_css_abovethefoldlocal').value !== '0');
                    autoGenerateATF('pagespeedninja_config_css_abovethefoldstyle', local);
                }
            }
        }
    }

    jQuery(document).ready(function () {
        populateCheckboxes();

        streamoptimizer_check();
        jQuery('.streamoptimizer').on('change', streamoptimizer_check);

        var $psn = jQuery('#pagespeedninja'),
            $form = jQuery('#pagespeedninja_form'),
            $thickboxes = jQuery('#pagespeedninja .gps_result_new > a.thickbox'),
            base_url = location.href.split('/').slice(0, -2).join('/') + '/',
            plugin_name = getQueryParameterByName('page');

        tab_name = getQueryParameterByName('tab') || 'basic';

        $psn.find('a.save').addClass('disabled');
        $form.areYouSure({
            fieldSelector: 'input:not(input[type=submit]):not(input[type=button]),select,textarea'
        }).on('dirty.areYouSure', function () {
            $psn.find('a.save').removeClass('disabled');
        }).on('clean.areYouSure', function () {
            $psn.find('a.save').addClass('disabled');
        });

        $psn.find('a.save').on('click', function () {
            if (!jQuery(this).hasClass('disabled')) {
                jQuery('#pagespeedninja_form').removeClass('dirty').submit();
                //document.getElementById('pagespeedninja_form').submit();
            }
        });
        $psn.find('a.advanced').on('click', function (e) {
            e.stopPropagation();
            location.href = '?page=' + plugin_name + '&tab=advanced';
        });
        $psn.find('a.basic').on('click', function (e) {
            e.stopPropagation();
            location.href = '?page=' + plugin_name;
        });

        $psn.on('change', 'input[type=checkbox]', function () {
            var id = this.id.split('_');
            if (id.length === 4) {
                var thisprefix = id[2];
                var section = id[3];
                var checked = this.checked;

                var element = document.getElementById('pagespeedninja_config_psi_' + section);
                if (element) {
                    element.value = checked ? '1' : '0';
                }

                var prefixes = ['mobile', 'desktop', 'usability'];
                for (var i = 0; i < prefixes.length; i++) {
                    var prefix = prefixes[i];
                    if (prefix !== thisprefix) {
                        element = document.getElementById('pagespeedninja_config_' + prefix + '_' + section);
                        if (element) {
                            element.checked = checked;
                        }
                    }
                }
                if (section === 'MinimizeRenderBlockingResources') {
                    basicLoadATFCSS();
                }
                $form.trigger('checkform.areYouSure');

                var data = $form.serialize();
                data += '&action=pagespeedninja_key';
                jQuery.post(ajaxurl, data, function (key) {
                    var url = base_url + '?pagespeedninja=test&pagespeedninjakey=' + key;
                    newUrl = url;
                    $thickboxes.attr('href', url + '&TB_iframe');
                });
                jQuery('.gps_result_new').removeClass('hide');
                jQuery('#pagespeed_desktop_new, #pagespeed_mobile_new, #pagespeed_usability_new')
                    .html('&nbsp;')
                    .removeClass('gps_error gps_warning gps_success')
                    .addClass('gps_loading');
            }
        });

        $psn.on('click', '.expando', function () {
            var $this = jQuery(this);
            $this.toggleClass('open');
            $this.parent().next().toggleClass('show', $this.hasClass('open'));
        });

        $psn.on('click', '.expando+.title', function () {
            var $this = jQuery(this).prev();
            $this.toggleClass('open');
            $this.parent().next().toggleClass('show', $this.hasClass('open'));
        });

        jQuery('#psn_excludejs').on('change', 'input[type=checkbox]', function () {
            updateExcludeList(this, '#pagespeedninja_config_js_excludelist');
        });

        jQuery('#psn_excludecss').on('change', 'input[type=checkbox]', function () {
            updateExcludeList(this, '#pagespeedninja_config_css_excludelist');
        });

        function updateCachesize(type) {
            jQuery.post(ajaxurl, {'action': 'pagespeedninja_get_cache_size', 'type': type}, function (response) {
                jQuery('#psn_cachesize_' + type + '_size').text(response.size);
                jQuery('#psn_cachesize_' + type + '_files').text(response.files);
            });
        }

        jQuery('#do_clear_images').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_images'}, function () {
                $el.removeAttr('disabled');
                updateCachesize('image');
            });
        });
        jQuery('#do_clear_cache_expired').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_cache_expired'}, function () {
                $el.removeAttr('disabled');
                updateCachesize('static');
                updateCachesize('ress');
            });
        });
        jQuery('#do_clear_cache_all').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_cache_all'}, function () {
                $el.removeAttr('disabled');
                updateCachesize('static');
                updateCachesize('ress');
            });
        });
        jQuery('#do_clear_pagecache_expired').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_pagecache_expired'}, function () {
                $el.removeAttr('disabled');
                updateCachesize('page');
            });
        });
        jQuery('#do_clear_pagecache_all').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_pagecache_all'}, function () {
                $el.removeAttr('disabled');
                updateCachesize('page');
            });
        });
        jQuery('#do_clear_amddcache').on('click', function () {
            var $el = jQuery(this);
            $el.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, {action: 'pagespeedninja_clear_amddcache'}, function () {
                $el.removeAttr('disabled');
            });
        });

        updateCachesize('image');
        updateCachesize('static');
        updateCachesize('ress');
        updateCachesize('page');

        if (jQuery('#pagespeed_desktop_orig').length) {
            if (!window.localStorage) {
                loadPageSpeed();
            } else {
                if (!loadPageSpeedCached()) {
                    loadPageSpeed();
                }
            }
            setInterval(getNewScores, 200);
        } else {
            detectPreset();
        }

        basicLoadATFCSS();
    });

    function getNewScores() {
        if (newUrl === null || newAjax) {
            return;
        }

        // loading of new scores
        var url = newUrl;

        newAjax = true;
        newUrl = null;

        jQuery
            .get(url)
            .then(function () {
                if (newUrl !== null) {
                    return;
                }

                jQuery.get(pagespeed_api, {strategy: 'desktop', url: url}).done(function (response) {
                    if (newUrl !== null) {
                        return;
                    }
                    try {
                        updatePSIScore(response.ruleGroups.SPEED.score, document.getElementById('pagespeed_desktop_new'));
                    } catch (e) {
                        console.log(e);
                    }
                });

                jQuery.get(pagespeed_api, {strategy: 'mobile', url: url}).done(function (response) {
                    if (newUrl !== null) {
                        return;
                    }
                    try {
                        updatePSIScore(response.ruleGroups.SPEED.score, document.getElementById('pagespeed_mobile_new'));
                        updatePSIScore(response.ruleGroups.USABILITY.score, document.getElementById('pagespeed_usability_new'));
                    } catch (e) {
                        console.log(e);
                    }
                });
            })
            .always(function () {
                newAjax = false;
            });
    }

    function updateExcludeList(checkbox, target) {
        var url = jQuery.trim(jQuery(checkbox).closest('tr').children(':first').text()),
            $el = jQuery(target),
            list = jQuery.trim($el.val()).split('\n'),
            index = -1;
        for (var i = 0; i < list.length; i++) {
            if (list[i] === url) {
                index = i;
                break;
            }
        }
        if (checkbox.checked) {
            if (index === -1) {
                list.push(url);
            }
        } else {
            if (index > -1) {
                list.splice(index, 1);
            }
        }
        $el.val(jQuery.trim(list.join('\n'))).trigger('change');
    }

    function detectPreset() {
        if (!window.pagespeedninja_presets) {
            return;
        }
        for (var preset in pagespeedninja_presets) {
            var match = true;
            for (var option in pagespeedninja_presets[preset]) {
                var $els = jQuery('input[name="pagespeedninja_config[' + option + ']"]:not([type=hidden]),select[name="pagespeedninja_config[' + option + ']"]');
                if ($els.length) {
                    $els.each(function () {
                        var $el = jQuery(this),
                            value = $el.val();
                        switch (this.nodeName) {
                            case 'INPUT':
                                if ($el.attr('type') === 'checkbox') {
                                    value = +$el.prop('checked'); // convert boolean to integer
                                } else if ($el.attr('type') === 'radio' && !$el.prop('checked')) {
                                    return;
                                }
                                break;
                            case 'SELECT':
                                break;
                            default:
                                console.log('Unknown node: ' + this.nodeName);
                                return;
                        }
                        if (value != pagespeedninja_presets[preset][option]) {
                            match = false;
                            return false;
                        }
                    });
                } else {
                    console.log('Option not found: ' + option);
                }
            }
            if (match === true) {
                jQuery('#pagespeedninja_preset_' + preset).prop('checked', true);
                return;
            }
        }
        jQuery('#pagespeedninja_preset_custom').prop('checked', true);
    }

    window.pagespeedninjaLoadPreset = function (preset) {
        if (preset === '') {
            document.getElementById('pagespeedninja_form').reset();
            return;
        }
        if (!(preset in pagespeedninja_presets)) {
            return;
        }
        for (var option in pagespeedninja_presets[preset]) {
            jQuery('input[name="pagespeedninja_config[' + option + ']"]:not([type=hidden]),select[name="pagespeedninja_config[' + option + ']"]').each(function () {
                var $el = jQuery(this);
                switch (this.nodeName) {
                    case 'INPUT':
                        if ($el.attr('type') === 'checkbox') {
                            $el.prop('checked', !!pagespeedninja_presets[preset][option]);
                        } else if ($el.attr('type') === 'radio') {
                            $el.prop('checked', pagespeedninja_presets[preset][option] == $el.val());
                        } else {
                            $el.val(pagespeedninja_presets[preset][option]);
                        }
                        break;
                    case 'SELECT':
                        $el.val(pagespeedninja_presets[preset][option]);
                        break;
                }
            });
        }
        jQuery('#pagespeedninja_form').trigger('checkform.areYouSure');
    };

    function setATFText(id, content) {
        jQuery('#' + id).removeAttr('disabled').val(content);
        jQuery('#pagespeedninja_form').trigger('checkform.areYouSure');
        if (tab_name === 'basic') {
            jQuery('#pagespeedninja_atfcss_notice').removeClass('hidden');
        }
    }

    function updateATFExternal(id, url) {
        jQuery.ajax({
            url: 'https://pagespeed.ninja/api/getcss',
            data: {
                url: url
            },
            success: function (content) {
                setATFText(id, content);
            },
            cache: false
        });
    }

    function updateATFInternal(id, url) {
        getATFCSS(url, function (content) {
            setATFText(id, content);
        });
    }

    window.autoGenerateATF = function (id, local) {
        jQuery('#' + id).attr('disabled', 'disabled');
        var url = location.href.split('/wp-admin/')[0] + '/?pagespeedninja=no';
        // get current locality state
        if (local) {
            updateATFInternal(id, url);
        } else {
            updateATFExternal(id, url);
        }
    };

})();
