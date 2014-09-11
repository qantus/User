<?php

namespace Modules\User\Models;

use Modules\User\Components\PermissionTrait;

class User extends UserBase
{
    public function getAbsoluteUrl()
    {
        return $this->reverse('user.view', ['id' => $this->pk]);
    }
}
