<?php

namespace Modules\User\Models;

use Mindy\Helper\Password;
use Mindy\Orm\Manager;

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
 * @date 24/04/14.04.2014 21:05
 */
class UserManager extends Manager
{
    /**
     * Create not privileged user
     * @param $username
     * @param $password
     * @param $email
     * @param array $extra
     * @return array|\Mindy\Orm\Model Errors or created model
     */
    public function createUser($username, $password, $email, array $extra = [])
    {
        $model = $this->getModel();
        $model->setAttributes(array_merge([
            'username' => $username,
            'email' => $email,
            'password' => Password::hashPassword($password),
            'activation_key' => $this->generateActivationKey()
        ], $extra));

        if ($model->save()) {
            return $model;
        } else {
            return $model->getErrors();
        }
    }

    public function setPassword($password)
    {
        return $this->getModel()->setAttributes([
            'password' => Password::hashPassword($password)
        ])->save(['password']);
    }

    public function createSuperUser($username, $password, $email, array $extra = [])
    {
        return $this->createUser($username, $password, $email, array_merge($extra, [
            'is_superuser' => true,
            'is_active' => true,
            'is_staff' => true
        ]));
    }

    public function createStaffUser($username, $password, $email, array $extra = [])
    {
        return $this->createUser($username, $password, $email, array_merge($extra, [
            'is_staff' => true
        ]));
    }

    public function generateActivationKey()
    {
        return substr(md5(Password::generateSalt()), 0, 10);
    }

    public function active()
    {
        return $this->filter(['is_active' => true]);
    }
}
