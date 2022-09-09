<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_Logger
{
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, $context = null);

    /**
     * Interesting events.
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, $context = null);

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, $context = null);

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, $context = null);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, $context = null);

    /**
     * Critical conditions.
     *
     * @param string $message
     * @param array $context
     */
    public function critical($message, $context = null);

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     * @param array $context
     */
    public function alert($message, $context = null);

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     */
    public function emergency($message, $context = null);

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, $context = null);
}
