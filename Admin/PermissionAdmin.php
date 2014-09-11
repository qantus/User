<?php

namespace Modules\User\Admin;

use Modules\Admin\Components\ModelAdmin;
use Modules\User\Forms\PermissionForm;
use Modules\User\Models\Permission;
use Modules\User\UserModule;

class PermissionAdmin extends ModelAdmin
{
    public function getColumns()
    {
        return [
            'id',
            'code',
            'name',
            'is_auto',
            'is_global',
            'is_locked',
            'type',
            'bizrule',
            'module'
        ];
    }

    public function getCreateForm()
    {
        return PermissionForm::className();
    }

    public function getModel()
    {
        return new Permission;
    }

    public function getVerboseName()
    {
        return UserModule::t('permission');
    }

    public function getVerboseNamePlural()
    {
        return UserModule::t('permissions');
    }
}

