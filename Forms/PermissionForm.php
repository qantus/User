<?php

namespace Modules\User\Forms;

use Mindy\Base\Mindy;
use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\DropDownField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\ModelForm;
use Modules\User\Models\Permission;

class PermissionForm extends ModelForm
{
    public function getFields()
    {
        $model = $this->getModel();

        $fields = [
            'name' => [
                'class' => TextField::className()
            ],
            'code' => [
                'class' => TextField::className()
            ],
            'module' => [
                'class' => TextField::className()
            ],
        ];

        $user = Mindy::app()->user;

        if ($user && $user->is_superuser && $model->is_auto == 0) {
            $fields['is_locked'] = ['class' => CheckboxField::className()];
        }

        if ($user && $user->is_superuser) {
            $fields['is_global'] = ['class' => CheckboxField::className()];
        }

        return $fields;
    }

    public function getModel()
    {
        return new Permission;
    }
}
