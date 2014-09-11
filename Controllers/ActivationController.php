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
class ActivationController extends FrontendController
{
    public $defaultAction = 'activation';

	public function init()
	{
		parent::init();
		$user = Yii::app()->user;
		if($user->isGuest == false) {
			$this->redirect($user->getModel());
		}
	}

    public function actionActivation($key = null)
    {
        $model = $this->loadModel($key);

        if ($model){
            if ($model->isActivated()) {
                $this->setFlash('success', UserModule::t("You account is already activated"));
                $this->redirect($model->getAbsoluteUrl());
            } elseif ($model->isActivationKeyCorrect($key)) {
                //Если включена модерация
                if(Y::param('user.moderation')) {
                    $model->setStatusModeration();
                    $model->save();

                    $this->renderMessage(UserModule::t("User activation"), UserModule::t("You account is activated and now waiting to moderate."));
                } else {
                    $model->setStatusActivated();
                    $model->save();

                    $this->renderMessage(UserModule::t("User activation"), UserModule::t("You account is activated."));
                }
            } else {
                $this->renderMessage( UserModule::t("User activation"), UserModule::t("Incorrect activation URL.") );
            }
        }
    }

    protected function loadModel($key)
    {
        $errorTitle = UserModule::t("User activation");
        $errorMessage = UserModule::t("Incorrect activation URL.");

        if ($key == null)
            $this->renderMessage($errorTitle, $errorMessage);

        $model = User::model()->notsafe()->findByAttributes(array('activation_key' => $key));
        if($model === null)
            $this->renderMessage($errorTitle, $errorMessage);
        return $model;
    }

    protected function renderMessage($title, $message)
    {
        $this->render('message', array(
            'title' => $title,
            'message' => $message
        ));
    }

}