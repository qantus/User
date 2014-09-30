<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\PasswordField;
use Mindy\Form\ModelForm;
use Mindy\Form\Validator\MinLengthValidator;
use Modules\User\Models\User;
use Modules\User\UserModule;

class ChangePasswordForm extends ModelForm
{
    public $exclude = [
        'username', 'email', 'is_staff', 'is_superuser', 'is_active', 'activation_key',
        'profile', 'last_login', 'groups', 'permissions', 'users_set', 'password'
    ];

    public function getFields()
    {
        return [
            'password_create' => [
                'class' => PasswordField::className(),
                'validators' => [
                    new MinLengthValidator(6)
                ],
                'label' => UserModule::t('Password')
            ],
            'password_repeat' => [
                'class' => PasswordField::className(),
                'validators' => [
                    new MinLengthValidator(6)
                ],
                'label' => UserModule::t('Password repeat')
            ]
        ];
    }

    public function cleanPassword_repeat($value)
    {
        if ($this->password_create === $value) {
            return $value;
        } else {
            $this->addError('password_repeat', 'Incorrect password repeat');
        }
        return null;
    }

    public function getModel()
    {
        return new User;
    }

    public function save()
    {
        return $this->getInstance()->objects()->setPassword($this->password_create);
    }
}
