<?php

class PagespeedNinja_i18n {
    /**@var string $domain The domain identifier for this plugin. */
    private $domain;

    public function load_plugin_textdomain() {
        load_plugin_textdomain( $this->domain );
    }

    /**
     * @param    string $domain The domain that represents the locale of this plugin.
     */
    public function set_domain( $domain ) {
        $this->domain = $domain;
    }
}
