<?php
/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 09/12/14 21:04
 */

namespace Modules\User\Components\HashStrategy;

/**
 * Interface IHashStrategy
 * @package Modules\User\Components\HashStrategy
 */
interface IHashStrategy
{
    /**
     * @return string random
     */
    public function generateSalt();

    /**
     * @param $password string
     * @return string
     */
    public function hashPassword($password);

    /**
     * @param $password string
     * @param $hash string
     * @return string
     */
    public function verifyPassword($password, $hash);
}