<?php

namespace Modules\User\Api;

use Modules\Api\Components\Api;
use Modules\User\Models\User;

/**
 * Class UserApi
 * @package Modules\User
 */
class UserApi extends Api
{
    /**
     * @return array
     */
    public function getAllowedFields()
    {
        return ['id', 'username', 'email', 'is_active', 'profile', 'last_login'];
    }

    /**
     * @return \Mindy\Orm\Model
     */
    public function getQuerySet()
    {
        return $this->getModel()->objects();
    }

    public function auth()
    {
        if (isset($_POST['username']) && isset($_POST['key'])) {
            $user = User::objects()->filter([
                'key__key' => $_POST['key'],
                'username' => $_POST['username']
            ])->get();

            if ($user !== null) {
                return [
                    'status' => true,
                    'session' => $user->session
                ];
            }
        }
        return [
            'status' => false
        ];
    }

    /**
     * @return \Mindy\Orm\Model|\Mindy\Orm\TreeModel
     */
    public function getModel()
    {
        return new User;
    }
}
