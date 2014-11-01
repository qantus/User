<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\DropDownField;
use Mindy\Form\Fields\TextAreaField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\ModelForm;
use Modules\User\Models\Group;
use Modules\User\UserModule;

/**
 * Class GroupForm
 * @package Modules\User
 */
class GroupForm extends ModelForm
{
    public function getFieldsets()
    {
        return [
            UserModule::t('Main information') => ['name', 'description'],
            UserModule::t('Settings') => ['is_visible', 'is_locked'],
            UserModule::t('Permissions') => ['permissions'],
        ];
    }

    public function getFields()
    {
        return [
            'name' => TextField::className(),
            'description' => TextAreaField::className(),
            'is_visible' => CheckboxField::className(),
            'is_locked' => CheckboxField::className(),
            'permissions' => [
                'class' => DropDownField::className(),
            ],
        ];
    }

    public function getModel()
    {
        return new Group;
    }
}
