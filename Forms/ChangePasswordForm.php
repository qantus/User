<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\PasswordField;
use Mindy\Form\Form;
use Mindy\Validation\MinLengthValidator;
use Modules\User\Models\User;
use Modules\User\UserModule;

/**
 * Class ChangePasswordForm
 * @package Modules\User
 */
class ChangePasswordForm extends Form
{
    private $_model;

    public function setModel(User $model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

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
        if ($this->password_create->getValue() === $value) {
            return $value;
        } else {
            $this->addError('password_repeat', 'Incorrect password repeat');
        }
        return null;
    }

    public function save()
    {
        return $this->getModel()->objects()->setPassword($this->password_create);
    }
}
