<?php
/**
 * @author Falaleev Maxim <max@studio107.com>
 * @link http://studio107.ru/
 * @copyright Copyright &copy; 2010-2012 Studio107
 * @license http://www.cms107.com/license/
 * @package modules.user.controllers
 * @since 1.1.1
 * @version 1.0
 *
 */
class RecoveryController extends ChangepasswordController
{
    public $defaultAction = 'recoverform';

	public $recoverModel = 'UserRecovery';

    public function init()
    {
        parent::init();

        if (!m::param('user.recovery')) {
            $this->redirect(Yii::app()->getModule('user')->getLoginUrl());
        }
    }

    public function actionRecoverform()
    {
        $model = new $this->recoverModel();
	    $form = $this->getForm($model);

	    $this->ajaxValidation($model, $this->model . '-form');

        if (isset($_POST[$this->recoverModel])) {
            $model->attributes = $_POST[$this->recoverModel];

            if ($model->validate()) {
                $user = User::model()->notsafe()->findbyPk($model->user_id);

	            $changepassword_url = $this->createAbsoluteUrl(implode(Yii::app()->controller->module->recoveryUrl), array(
	                "key" => $user->activation_key,
	                "email" => $user->email
                ));

	            Yii::app()->mail->send('user.recover', $user->email, array(
		            'sitename' => m::param('core.sitename'),
		            'changepassword_url' => $changepassword_url,
	            ));

                $this->setFlash('success', UserModule::t("Please check your email. An instructions was sent to your email address."));
                $this->render('success');
            }
        }

        $this->render('form', array(
	        'model' => $model,
	        'form' => $form
        ));
    }

    /**
     * Recovery password
     */
    public function actionRecovery($key)
    {
        $email = Yii::app()->request->getParam('email');
        if ($email !== null) {
            $find = User::model()->notsafe()->findByAttributes(array('email' => $email));

            if ($find !== null && $find->activation_key == $key) {

	            /**
	             * Устанавливаем статус активирован пользователю, так как после перехода по ссылке из письма
	             * он подтверждает свой email
	             */
	            $find->setStatusActivated();

	            /**
	             * Выводим форму на изменение пароля. Сам процесс изменения пароля
	             * происходит в $this->actionChangepassword()
	             */
	            $model = new $this->model();
	            $model->userId = $find->pk;

                $this->render('../changepassword/changepassword', array(
	                'model' => $model,
	                'form' => $this->getForm($model),
                ));
            } else {
                $this->setFlash('error', UserModule::t("Incorrect recovery link."));
                $this->redirect(Yii::app()->controller->module->recoveryUrl);
            }
        } else
	        $this->error(404);
    }
}
