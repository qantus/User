<?php

namespace Modules\User\Models;

use Mindy\Base\Mindy;
use Mindy\Helper\Params;
use Mindy\Orm\Fields\HasManyField;
use Mindy\Orm\Fields\PasswordField;
use Modules\User\Components\Permissions\PermissionManager;
use Modules\User\Components\PermissionTrait;
use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\EmailField;
use Mindy\Orm\Fields\ForeignField;
use Mindy\Orm\Fields\IntField;
use Mindy\Orm\Fields\ManyToManyField;
use Mindy\Orm\Model;
use Modules\User\UserModule;

abstract class UserBase extends Model
{
    use PermissionTrait;

    const GUEST_ID = -1;

    public static function getFields()
    {
        return [
            "username" => [
                'class' => CharField::className(),
                'verboseName' => UserModule::t("Username"),
                'unique' => true
            ],
            "email" => [
                'class' => EmailField::className(),
                'verboseName' => UserModule::t("Email"),
                'unique' => true
            ],
            "password" => [
                'class' => PasswordField::className(),
                'null' => true,
                'verboseName' => UserModule::t("Password"),
            ],
            "activation_key" => [
                'class' => CharField::className(),
                'null' => true,
                'verboseName' => UserModule::t("Activation key"),
            ],
            "is_active" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is active"),
            ],
            "is_staff" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is staff"),
            ],
            "is_superuser" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is superuser"),
            ],
            'profile' => [
                'class' => ForeignField::className(),
                'modelClass' => UserProfile::className(),
                'null' => true,
                'verboseName' => UserModule::t("User profile"),
            ],
            'last_login' => [
                'class' => IntField::className(),
                'null' => true,
                'verboseName' => UserModule::t("Last login"),
            ],
            'groups' => [
                'class' => ManyToManyField::className(),
                'modelClass' => UserGroup::className(),
                'verboseName' => UserModule::t("Groups"),
            ],
            'permissions' => [
                'class' => ManyToManyField::className(),
                'modelClass' => Permission::className(),
                'through' => UserPermission::className(),
                'verboseName' => UserModule::t("Permissions"),
            ],
        ];
    }

    public function __toString()
    {
        return (string)$this->username;
    }

    public function adminNames()
    {
        return array(
            UserModule::t('Users'),
            UserModule::t('Create user'),
            UserModule::t('Update user')
        );
    }

    public function getIsGuest()
    {
        return $this->pk == self::GUEST_ID;
    }

    public function notifyRegistration()
    {
        Mindy::app()->mail->send('user.registration', $this->email, array(
            'username' => $this->username,
            'sitename' => Params::get('core.sitename'),
            'activation_url' => Mindy::app()->createAbsoluteUrl('//user/activation/activation', array('key' => $this->activation_key)),
        ));
    }

    public function getPermissionList()
    {
        return Mindy::app()->authManager->getPermIdArray();
    }

    /**
     * @param null $instance
     * @return \Mindy\Orm\Manager|UserManager
     */
    public static function objectsManager($instance = null)
    {
        $className = get_called_class();
        return new UserManager($instance ? $instance : new $className);
    }

    public function beforeSave($owner, $isNew)
    {
        if($isNew) {
            $owner->activation_key = substr(md5(time() . $owner->username . $owner->pk), 0, 10);
        }
    }
}
