<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\DropDownField;
use Mindy\Form\Fields\TextAreaField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\ModelForm;
use Modules\User\Models\UserGroup;

/**
 * Created by Studio107.
 * Date: 14.04.13
 * Time: 16:55
 * All rights reserved.
 */
class UserGroupForm extends ModelForm
{
    public function getFields()
    {
        return [
            'name' => TextField::className(),
            'is_visible' => CheckboxField::className(),
            'is_locked' => CheckboxField::className(),
            'permissions' => [
                'class' => DropDownField::className(),
            ],
            'description' => TextAreaField::className()
        ];
    }

    public function getModel()
    {
        return new UserGroup;
    }
}
