<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_Cache
{
    /**
     * @param $deps string|array
     * @param $suffix string
     * @return string
     */
    public function id($deps, $suffix = '');

    /**
     * @param $id string
     * @return string|bool
     */
    public function getOrLock($id);

    /**
     * @param $id string
     * @return bool
     */
    public function lock($id);

    /**
     * @param $id string
     * @param $data string
     * @return bool
     */
    public function storeAndUnlock($id, $data);

    /**
     * @param $id string
     * @return bool
     */
    public function delete($id);
}

