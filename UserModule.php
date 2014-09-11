<?php

namespace Modules\User;

use Mindy\Base\Mindy;
use Mindy\Base\Module;
use Modules\Core\CoreModule;

class UserModule extends Module
{
    public $defaultController = 'users';

    /**
     * @var array
     */
    public $config = [];

    /**
     * @var int
     * @desc Remember Me Time (seconds), defalt = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days

    /**
     * @property string the name of the user model class.
     */
    public $userClass = 'User';

    public $loginUrl = 'user.login';

    public $returnUrl = 'user.profile';

    // 3600 * 24 * $days
    public $loginDuration = 2592000;

    public function getVersion()
    {
        return '1.0';
    }

    public function getMenu()
    {
        return [
            'name' => $this->getName(),
            'items' => [
                [
                    'code' => 'UserAdmin',
                    'name' => self::t('Users'),
                    'adminClass' => 'UserAdmin',
                    'icon' => 'icon-user'
                ],
                [
                    'code' => 'UserGroupAdmin',
                    'name' => self::t('Groups'),
                    'adminClass' => 'UserGroupAdmin',
                    'icon' => 'icon-group'
                ],
                [
                    'code' => 'PermissionAdmin',
                    'name' => self::t('Permissions'),
                    'adminClass' => 'PermissionAdmin',
                    'icon' => 'icon-key'
                ]
            ]
        ];
    }

    /**
     * Return array of mail templates and his variables
     * @return array
     */
    public function getMailTemplates()
    {
        return [
            'registration' => [
                'username' => UserModule::t('Username'),
                'activation_url' => UserModule::t('Url with activation key'),
                'sitename' => CoreModule::t('Site name')
            ],
            'recovery' => [
                'recover_url' => UserModule::t('Url with link to recover password'),
            ],
            'changepassword' => [
                'changepassword_url' => UserModule::t('Url with link to change password'),
            ],
            'activation' => [],
        ];
    }

    public function getLoginUrl()
    {
        return Mindy::app()->urlManager->createUrl($this->loginUrl);
    }

    public function getReturnUrl()
    {
        return Mindy::app()->urlManager->createUrl($this->returnUrl);
    }
}
