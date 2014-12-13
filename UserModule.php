<?php

namespace Modules\User;

use Mindy\Base\Mindy;
use Mindy\Base\Module;
use Mindy\Helper\Console;
use Modules\Core\CoreModule;

/**
 * Class UserModule
 * @package Modules\User
 */
class UserModule extends Module
{
    /**
     * @var \Modules\User\Models\Profile
     */
    public $profileModelClass = '\Modules\User\Models\Profile';
    /**
     * @var \Modules\User\Forms\ProfileForm
     */
    public $profileFormClass = '\Modules\User\Forms\ProfileForm';
    /**
     * @var array
     */
    public $config = [];
    /**
     * @var int Remember Me Time (seconds), defalt = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days
    /**
     * @var string the name of the user model class.
     */
    public $userClass = 'User';
    /**
     * @var string
     */
    public $loginUrl = 'user.login';
    /**
     * @var int 3600 * 24 * $days
     */
    public $loginDuration = 2592000;
    /**
     * @var bool
     */
    public $userList = true;
    /**
     * @var bool
     */    
    public $sendUserCreateMail = true;
    /**
     * @var bool
     */
    public $sendUserCreateRawMail = false;

    public static function preConfigure()
    {
        $app = Mindy::app();
        $tpl = $app->template;
        $tpl->addHelper('gravatar', function ($user, $size = 80) {
            $email = $user->email;
            $default = "http://placehold.it/" . $size . "x" . $size;
            return "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size;
        });
        $tpl->addHelper('login_form', ['\Modules\User\Helpers\UserHelper', 'render']);

        $signal = $app->signal;

        if ($this->sendUserCreateRawMail) {
            $signal->handler('\Modules\User\Models\UserBase', 'createUser', function ($user) use ($app) {
                $app->mail->fromCode('user.registration', $this->email->getValue(), [
                    'data' => $model,
                    'site' => $app->getModule('Sites')->getSite(),
                    'activation_link' => $app->request->http->absoluteUrl($app->urlManager->reverse('user.registration_activation', [
                            'key' => $model->activation_key
                        ]))
                ]);
            });
        }
        
        if ($this->sendUserCreateRawMail) {
            $signal->handler('\Modules\User\Models\UserBase', 'createRawUser', function ($user, $password) {
                if (!Console::isCli() && $user->email) {
                    Mindy::app()->mail->fromCode('user.create_raw_user', $user->email, [
                        'user' => $user,
                        'password' => $password
                    ]);
                }
            });
        }
    }

    public function getVersion()
    {
        return '1.0';
    }

    public function getName()
    {
        return self::t('Users');
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
                ],
                [
                    'code' => 'GroupAdmin',
                    'name' => self::t('Groups'),
                    'adminClass' => 'GroupAdmin',
                ],
                [
                    'code' => 'PermissionAdmin',
                    'name' => self::t('Permissions'),
                    'adminClass' => 'PermissionAdmin',
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
        return Mindy::app()->urlManager->reverse($this->loginUrl);
    }
}
