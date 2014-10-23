<?php

namespace Modules\User\Admin;

use Mindy\Orm\Model;
use Modules\Admin\Components\ModelAdmin;
use Modules\User\Forms\UserForm;
use Modules\User\Models\User;

class UserAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    public $actionsTemplate = 'admin/user/_actions.html';
    /**
     * @var string
     */
    public $updateTemplate = 'admin/user/update.html';

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

    public function getInfoFields(Model $model)
    {
        return ['pk', 'username', 'email', 'is_staff', 'is_superuser', 'is_active', 'last_login'];
    }

    public function getCreateForm()
    {
        return UserForm::className();
    }

    public function getModel()
    {
        return new User;
    }
}
