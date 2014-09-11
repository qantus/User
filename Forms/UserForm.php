<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\DropDownField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\ModelForm;
use Modules\User\Models\User;
use Modules\User\UserModule;

class UserForm extends ModelForm
{
    public function getFieldsets()
    {
        return [
            UserModule::t('Main information') => ['username', 'email', 'is_staff', 'is_superuser', 'is_active'],
            UserModule::t('Extra information') => ['groups', 'permissions'],
        ];
    }

    public function getFields()
    {
        return [
            'username' => [
                'class' => TextField::className(),
            ],
            'email' => [
                'class' => TextField::className(),
            ],
            'groups' => [
                'class' => DropDownField::className(),
            ],
            'permissions' => [
                'class' => DropDownField::className(),
            ],
            'is_staff' => [
                'class' => CheckboxField::className()
            ],
            'is_superuser' => [
                'class' => CheckboxField::className()
            ],
            'is_active' => [
                'class' => CheckboxField::className()
            ],
        ];
    }

    public function getModel()
    {
        return new User;
    }
}
