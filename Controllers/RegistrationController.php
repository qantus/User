<?php

/**
 * @TODO: copyright
 */
class RegistrationController extends FrontendController
{
    public $defaultAction = 'registration';

    public $model = 'UserRegistration';

    public function allowedActions()
    {
        return array(
            'registration'
        );
    }

    /*
    public function init()
    {
        parent::init();
        if (!m::param('user.registration'))
            $this->redirect(Yii::app()->getModule('user')->loginUrl);

        if (Yii::app()->user->id)
            $this->redirect(Yii::app()->controller->module->profileUrl);
    }
    */

    /**
     * Registration user
     * @return mixed html render form
     */
    public function actionRegistration()
    {
        $model = new $this->model();
        $profile = new UserProfile();
        $profile->regMode = true;

        // ajax validator
        $this->ajaxValidation(array($model, $profile), $this->model . '-form');

        if (isset($_POST[$this->model])) {

            $model->attributes = $_POST[$this->model];
            $profile->attributes = m::request()->getParam('Profile', array());

            if ($model->validate() && $profile->validate()) {
                $soucePassword = $model->password;

                if ($model->save()) {
                    $profile->user_id = $model->id;
                    $profile->save();

                    // Проверяем нужно ли подтверждение пользователя по email и активация его
                    if (m::param('user.need_activation')) {
                        $flashMessage = UserModule::t('You have successfully signed up for the site. You an e-mail sent instructions on how to activate your account.');

	                    $this->redirect(array('success'));
                    } else {
                        // Автоматически авторизуем пользователя на сайте и отправляем ему письмо об успешной регистрации
                        $user = Yii::app()->user;

                        if (m::param('user.auto_login')) {
                            $identity = new UserIdentity($model->email, $soucePassword);
                            $identity->authenticate();

                            $user->login($identity, UserHelper::getLoginDuration());

                            $user->setFlash('success', UserModule::t("You have successfully signed up for the site."));

	                        $this->redirect(array('success'));
                        } else {
                            $user->setFlash('success', 'You have successfully signed up for the site. Please log.');

                            $this->redirect(array('//user/login/login'));
                        }
                    }
                }
            }
        }

	    $form = $this->getForm($model);

        $this->render('registration', array(
	        'model' => $model,
	        'profile' => $profile,
	        'fields' => $profile->getFields(),
	        'form' => $form
        ));
    }

	public function actionSuccess()
	{
		$this->render('success');
	}
}