<?php

use Mindy\Form\Fields\PasswordField;
use Mindy\Form\ModelForm;

class UserChangePasswordForm extends ModelForm
{
    public function getFields()
    {
        return [
            'password' => [
                'class' => PasswordField::className(),
                'label' => UserModule::t("Password"),
            ],

            // TODO validation equalTo `password`
            'verifyPassword' => [
                'class' => PasswordField::className(),
                'label' => UserModule::t("Retype Password"),
            ],
        ];
    }

    public function getModel()
    {
        return new User();
    }
}
