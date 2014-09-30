<?php

namespace Modules\User\Forms;

use Mindy\Base\Mindy;
use Mindy\Form\Fields\PasswordField;
use Mindy\Form\ModelForm;
use Mindy\Form\Fields\CharField;
use Mindy\Form\Fields\EmailField;
use Mindy\Form\Validator\MinLengthValidator;
use Modules\User\Models\User;
use Modules\User\UserModule;

class RegistrationForm extends ModelForm
{
    public $exclude = [
        'users_set',
        'permissions',
        'groups',
        'last_login',
        'is_active',
        'is_staff',
        'is_superuser',
        'profile',
        'activation_key'
    ];

    public function getFields()
    {
        return [
            'username' => [
                'class' => CharField::className(),
            ],
            'email' => [
                'class' => EmailField::className(),
            ],
            'password' => [
                'class' => PasswordField::className(),
                'validators' => [
                    new MinLengthValidator(6)
                ],
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
        if($this->password === $value) {
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
        $model = User::objects()->createUser($this->username, $this->password, $this->email);
        if($model->hasErrors() === false) {
            Mindy::app()->mail->fromCode('user.registration', $this->email, [
                'data' => $this->cleanedData
            ]);
            return $model;
        }
        return false;
    }
}