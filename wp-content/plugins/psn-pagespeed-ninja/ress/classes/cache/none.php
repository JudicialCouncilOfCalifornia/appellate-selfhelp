<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Cache_None implements IRessio_Cache
{
    /**
     * @param $deps string|array
     * @param $suffix string
     * @return string
     */
    public function id($deps, $suffix = '')
    {
        if (is_array($deps)) {
            $deps = implode("\0", $deps);
        }
        return sha1($deps) . '_' . $suffix;
    }

    /**
     * @param $id string
     * @return string|bool
     */
    public function getOrLock($id)
    {
        return true;
    }

    /**
     * @param $id string
     * @return bool
     */
    public function lock($id)
    {
        return true;
    }

    /**
     * @param $id string
     * @param $data string
     * @return bool
     */
    public function storeAndUnlock($id, $data)
    {
        return true;
    }

    /**
     * @param $id string
     * @return bool
     */
    public function delete($id)
    {
        return true;
    }
}

