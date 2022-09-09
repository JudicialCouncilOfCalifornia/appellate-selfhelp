<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */


class Ressio_Logger implements IRessio_Logger
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    protected static $levels = array(
        'emergency' => 1,
        'alert' => 2,
        'critical' => 3,
        'error' => 4,
        'warning' => 5,
        'notice' => 6,
        'info' => 7,
        'debug' => 8
    );

    /**
     * @var int
     */
    protected $minLoggingLevel = 3;

    /**
     * @param $di Ressio_Di
     */
    public function setDI($di)
    {
        $this->minLoggingLevel = $di->config->logginglevel;
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, $context = null)
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Interesting events.
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, $context = null)
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, $context = null)
    {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, $context = null)
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, $context = null)
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * @param string $message
     * @param array $context
     */
    public function critical($message, $context = null)
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     * @param array $context
     */
    public function alert($message, $context = null)
    {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     */
    public function emergency($message, $context = null)
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, $context = null)
    {
        if (self::$levels[$level] > $this->minLoggingLevel) {
            return;
        }

        if (is_array($context) && count($context) > 0) {
            $message = $this->interpolate($message, $context);
            if (isset($context['exception']) && ($context['exception'] instanceof Exception)) {
                // @todo
            }
        }

        error_log("[" . date('d-m-Y H:i:s') . "] RESSIO : $level : $message");
    }

    /**
     * @param $message string
     * @param $context array
     * @return string
     */
    protected function interpolate($message, $context)
    {
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}