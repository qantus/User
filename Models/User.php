<?php

namespace Modules\User\Models;

use Mindy\Orm\Fields\HasManyField;
use Modules\User\Components\PermissionTrait;

/**
 * Class User
 * @package Modules\User
 * @method static \Modules\User\Models\UserManager objects($instance = null)
 */
class User extends UserBase
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'keys' => [
                'class' => HasManyField::className(),
                'modelClass' => Key::className(),
                'editable' => false
            ]
        ]);
    }

    public function getAbsoluteUrl()
    {
        return $this->reverse('user.view', ['username' => $this->username]);
    }
}
