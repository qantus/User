<?php

namespace Modules\User\Admin;

use Modules\Admin\Components\ModelAdmin;
use Modules\User\Models\UserGroup;
use Modules\User\Forms\UserGroupForm;
use Modules\User\UserModule;

class UserGroupAdmin extends ModelAdmin
{
    public function getColumns()
    {
        return ['id', 'name', 'is_locked', 'is_visible'];
    }

    public function getCreateForm()
    {
        return UserGroupForm::className();
    }

    public function getModel()
    {
        return new UserGroup;
    }

    public function getVerboseName()
    {
        return UserModule::t('group');
    }

    public function getVerboseNamePlural()
    {
        return UserModule::t('groups');
    }
}
