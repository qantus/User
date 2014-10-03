<?php

namespace Modules\User\Models;

use Modules\User\Components\PermissionTrait;
use Modules\User\UserModule;

/**
 * Class User
 * @package Modules\User
 * @method static \Modules\User\Models\UserManager objects($instance = null)
 */
class User extends UserBase
{
    protected $_isLogin = false;

    public function getAbsoluteUrl()
    {
        return $this->reverse('user.view', ['id' => $this->pk]);
    }

    public function save(array $fields = [])
    {
        if ($fields == ['last_login']) {
            $this->_isLogin = true;
        }
        parent::save($fields);
    }

    public function afterSave($owner, $isNew)
    {
        if ($this->_isLogin) {
            $this->recordAction(UserModule::t('User [[{url}|{name}]] logged in', [
                '{url}' => $owner->getAbsoluteUrl(),
                '{name}' => (string) $owner
            ]));
            $this->_isLogin = false;
        }else{
            parent::afterSave($owner, $isNew);
        }
    }
}
