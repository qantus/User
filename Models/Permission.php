<?php

namespace Modules\User\Models;

use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\IntField;
use Mindy\Orm\Model;
use Modules\User\UserModule;

class Permission extends Model
{
    const TYPE_USER = 0;
    const TYPE_GROUP = 1;

    public function __toString()
    {
        return (string) $this->code;
    }

    public static function getFields()
    {
        return [
            "code" => [
                'class' => CharField::className(),
                'length' => 50,
                'verboseName' => UserModule::t("Permission code"),
            ],
            "name" => [
                'class' => CharField::className(),
                'length' => 200,
                'verboseName' => UserModule::t("Permission name"),
            ],
            "bizrule" => [
                'class' => CharField::className(),
                'length' => 255,
                'null' => true,
                'verboseName' => UserModule::t("Permission name")
            ],
            "module" => [
                'class' => CharField::className(),
                'length' => 30,
                'verboseName' => UserModule::t("Module")
            ],
            "is_locked" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is locked"),
            ],
            "is_auto" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is auto"),
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
            "is_global" => [
                'class' => BooleanField::className(),
                'verboseName' => UserModule::t("Is global"),
            ],
        ];
    }

    public function getTypes()
    {
        return [
            self::TYPE_USER => UserModule::t('User'),
            self::TYPE_GROUP => UserModule::t('Group'),
        ];
    }

    public function adminNames()
    {
        return [
            UserModule::t('Permissions'),
            UserModule::t('Create permission'),
            UserModule::t('Update permission: {name}', ["{name}" => $this->name])
        ];
    }
}
