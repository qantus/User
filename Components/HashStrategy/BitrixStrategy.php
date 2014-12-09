<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 09/12/14 21:09
 */

namespace Modules\User\Components\HashStrategy;

use Mindy\Helper\Password;

class BitrixStrategy implements IHashStrategy
{
    /**
     * @return string random
     */
    public function generateSalt()
    {
        return Password::generateSalt(8);
    }

    /**
     * @param $password string
     * @return string
     */
    public function hashPassword($password)
    {
        $salt = $this->generateSalt();
        return $salt . md5($salt . $password);
    }

    /**
     * @param $password string
     * @param $hash string
     * @return string
     */
    public function verifyPassword($password, $hash)
    {
        $salt = substr($hash, 0, 8);
        $hash = substr($hash, 8);
        return $salt . $hash == $salt . md5($salt . $password);
    }
}
 