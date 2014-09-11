<?php

namespace Modules\User\Forms;

use Mindy\Base\Mindy;
use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\PasswordField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\Form;
use Modules\User\Components\UserIdentity;
use Modules\User\UserModule;

/**
 * Created by Studio107.
 * Date: 20.03.13
 * Time: 15:04
 * All rights reserved.
 */
class UserLoginForm extends Form
{
    private $_identity;

    public function getFields()
    {
        return [
            'username' => [
                'class' => TextField::className(),
                'html' => [
                    'placeholder' => UserModule::t('Username or email')
                ],
            ],
            'password' => [
                'class' => PasswordField::className(),
                'html' => [
                    'placeholder' => UserModule::t('Password')
                ]
            ],
            'rememberMe' => CheckboxField::className()
        ];
    }

    public function isValid()
    {
        parent::isValid();
        $this->authenticate();
        return $this->hasErrors() === false;
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate()
    {
        if($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
        }

        if (!$this->_identity->authenticate()) {
            switch ($this->_identity->errorCode) {
                case UserIdentity::ERROR_EMAIL_INVALID:
                    $this->addError("username", UserModule::t("Email is incorrect."));
                    break;
                case UserIdentity::ERROR_USERNAME_INVALID:
                    $this->addError("username", UserModule::t("Username is incorrect."));
                    break;
                case UserIdentity::ERROR_PASSWORD_INVALID:
                    $this->addError("password", UserModule::t("Password is incorrect."));
                    break;
            }
        }
    }

    public function login()
    {
        $loginDuration = $this->rememberMe ? Mindy::app()->getModule('user')->loginDuration : 3600 * 24 * 1;
        return Mindy::app()->auth->login($this->_identity->getModel(), $loginDuration);
    }
}
