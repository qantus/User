<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 09/12/14 21:03
 */

namespace Modules\User\Components\HashStrategy;

use Mindy\Helper\Password;

class BlueFishStrategy implements IHashStrategy
{
    /**
     * @return string random
     */
    public function generateSalt()
    {
        return Password::generateSalt();
    }

    /**
     * @param $password string
     * @return string
     */
    public function hashPassword($password)
    {
        return Password::hashPassword($password);
    }

    /**
     * @param $password1 string
     * @param $password2 string
     * @return string
     */
    public function verifyPassword($password, $hash)
    {
        return Password::verifyPassword($password, $hash);
    }
}
