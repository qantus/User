<?php

namespace Modules\User\Admin;

use Modules\Admin\Components\ModelAdmin;
use Modules\User\Forms\UserForm;
use Modules\User\Models\User;
use Modules\User\UserModule;

class UserAdmin extends ModelAdmin
{
    public function getColumns()
    {
        return [
            'id',
            'username',
            'email',
            'is_staff',
            'is_superuser',
        ];
    }

    public function getCreateForm()
    {
        return UserForm::className();
    }

    public function getModel()
    {
        return new User;
    }

    public function getVerboseName()
    {
        return UserModule::t('user');
    }

    public function getVerboseNamePlural()
    {
        return UserModule::t('users');
    }
}
