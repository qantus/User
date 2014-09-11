<?php

namespace Modules\User\Models;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\HasManyField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\ManyToManyField;
use Mindy\Orm\Model;
use Modules\User\Components\Permissions\PermissionManager;
use Modules\User\UserModule;

class UserGroup extends Model
{
    public static function getFields()
    {
        return [
            "name" => [
                'class' => CharField::className(),
                'verboseName' => UserModule::t("Name"),
            ],
            "description" => [
                'class' => TextField::className(),
                'verboseName' => UserModule::t("Description"),
            ],
            "is_locked" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is locked"),
            ],
            "is_visible" => [
                'class' => BooleanField::className(),
                'default' => true,
                'verboseName' => UserModule::t("Is visible"),
            ],
            "is_default" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is default"),
            ],
            'permissions' => [
                'class' => ManyToManyField::className(),
                'modelClass' => Permission::className(),
                'through' => UserGroupPermission::className(),
                'verboseName' => UserModule::t("Permissions"),
            ],
            'users' => [
                'class' => ManyToManyField::className(),
                'modelClass' => User::className(),
                'verboseName' => UserModule::t("Users"),
            ]
        ];
    }

    public function __toString()
    {
        return (string)$this->name;
    }
}

//class UserGroup extends MActiveRecord
//{
//    public function behaviors()
//    {
//        return array(
//            'MPermissionBehavior' => array(
//                'class' => 'user.behaviors.MPermissionBehavior',
//                'type' => MPermissionManager::TYPE_GROUP
//            )
//        );
//    }
//}
