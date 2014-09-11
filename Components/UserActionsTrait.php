<?php
/**
 * 
 *
 * All rights reserved.
 * 
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 16/07/14.07.2014 13:34
 */

namespace Modules\User\Components;


use Modules\Core\Components\UserLog;

trait UserActionsTrait
{
    public function recordAction($message)
    {
        if(YII_TEST === false) {
            return UserLog::log($message);
        }
    }
}
