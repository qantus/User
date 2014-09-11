<?php

/**
 * Created by Studio107.
 * Date: 14.04.13
 * Time: 18:31
 * All rights reserved.
 */
 
class MChangepasswordAction extends CAction
{
	public $model;

	public function run($id = null)
	{
		$controller = $this->getController();

		if($id===null)
			$id = Yii::app()->user->id;

		$model = new $this->model();
		$user = User::model()->notsafe()->findByPk($id);
		if($user===null)
			$controller->error(404);

		$model->userId = $id;

		$controller->ajaxValidation($model, $this->model . '-form');

		if (isset($_POST[$this->model])) {
			$model->attributes = $_POST[$this->model];

			if ($model->validate()) {
				$user = User::model()->notsafe()->findbyPk($id);
				$user->setPassword($model->password);
				$user->changeActivationKey();
				$user->save(false, array('password', 'salt', 'activation_key'));

				if(Yii::app()->request->isAjaxRequest) {
					$controller->responseJson(array(
						'status' => 'success',
						'title' => CoreModule::t('Success')
					));
				} else {
					$controller->setFlash('success', UserModule::t("New password is saved."));

					$controller->redirect(Yii::app()->user->getReturnUrl());
				}
			}
		}

		$form = $controller->getForm($model);

		$controller->renderJsonMaybe('changepassword', array(
			'model' => $model,
			'form' => $form,
		));
	}
}