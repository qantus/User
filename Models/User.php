<?php

namespace Modules\User\Models;

use Modules\User\Components\PermissionTrait;

/**
 * Class User
 * @package Modules\User
 * @method static \Modules\User\Models\UserManager objects($instance = null)
 */
class User extends UserBase
{
    public function getAbsoluteUrl()
    {
        return $this->reverse('user.view', ['id' => $this->pk]);
    }
}
