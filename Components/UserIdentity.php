<?php

namespace Modules\User\Components;

use Mindy\Base\UserIdentity as DeprecatedUserIdentity;
use Mindy\Helper\Password;
use Mindy\Orm\Model;
use Modules\User\Models\User;

class UserIdentity extends DeprecatedUserIdentity
{
    private $_model;

    /**
     * @var \Mindy\Orm\Model user model
     */
    protected $user;

    const ERROR_EMAIL_INVALID = 3;
    const ERROR_INACTIVE = 4;

    /**
     * Авторизация пользователей.
     * @return boolean если пользователь успешно авторизовался
     */
    public function authenticate()
    {
        $model = User::objects()->filter(
            [strpos($this->username, "@") ? 'email' : 'username' => $this->username]
        )->get();

        if ($model === null) {
            if (strpos($this->username, "@")) {
                $this->errorCode = self::ERROR_EMAIL_INVALID;
            } else {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            }
        } else if (!$model->is_active) {
            $this->errorCode = self::ERROR_INACTIVE;
        } else if (!Password::verifyPassword($this->password, $model->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;

            $this->setModel($model);
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->getModel()->pk;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function updateUserById($id)
    {
        $user = User::objects()->filter(['pk' => $id])->get();
        if ($user) {
            $this->setModel($user);
        }
    }

    public function setModel(Model $model)
    {
        $this->_model = $model;
        return $this;
    }
}
