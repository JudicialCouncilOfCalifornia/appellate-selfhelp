<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin
{
    /** @var Ressio_DI */
    protected $di;
    /** @var Ressio_Config */
    protected $config;

    /** @var stdClass */
    public $params;

    /**
     * @param $di Ressio_DI
     * @param $params stdClass|null
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $this->di = $di;
        $this->config = $di->config;
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getEventPriorities()
    {
        return array();
    }

    /**
     * @param string $filename
     * @param stdClass|null $override
     * @return stdClass
     */
    protected function loadConfig($filename, $override)
    {
        if (!is_file($filename)) {
            return $override;
        }
        $params = json_decode(file_get_contents($filename));
        if ($override !== null) {
            /** @var stdClass $override */
            foreach ($override as $key => $value) {
                if (isset($params->$key)) {
                    $params->$key = $value;
                }
            }
        }
        return $params;
    }
}