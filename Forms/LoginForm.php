<?php

namespace Modules\User\Forms;

use Mindy\Base\Mindy;
use Mindy\Form\Fields\CheckboxField;
use Mindy\Form\Fields\PasswordField;
use Mindy\Form\Fields\TextField;
use Mindy\Form\Form;
use Modules\User\Components\UserIdentity;
use Modules\User\UserModule;

class LoginForm extends Form
{
    private $_identity;

    public function getFields()
    {
        return [
            'username' => [
                'class' => TextField::className(),
                'label' => UserModule::t('Username'),
                'html' => [
                    'placeholder' => UserModule::t('Username')
                ],
            ],
            'password' => [
                'class' => PasswordField::className(),
                'label' => UserModule::t('Password'),
                'html' => [
                    'placeholder' => UserModule::t('Password')
                ]
            ],
            'rememberMe' => [
                'class' => CheckboxField::className(),
                'label' => UserModule::t('Remember me')
            ]
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
                case UserIdentity::ERROR_INACTIVE:
                    $this->addError("username", UserModule::t("Account not active. Please activate your account."));
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
