<?php

/**
 * Created by Studio107.
 * Date: 25.04.13
 * Time: 12:10
 * All rights reserved.
 */
 
class GroupController extends FrontendController
{
	public $model = 'UserGroup';

	public function actionChange()
	{
		$model = new $this->model('search');
		$model->unsetAttributes();

		if(isset($_GET[$this->model]))
			$model->attributes = $_GET[$this->model];

		$this->render('change', array(
			'model' => $model,
		));
	}

	public function actionSelect($id)
	{
		$group = $this->getModel($this->model, $id);

		$balance = Yii::app()->balance;
		if($balance->hasOrError($group->price, $this->createUrl('change'))) {
			$balance->down($group->price, UserModule::t('Change user group'));

			$model = Yii::app()->user->getModel();
			if($model->setGroup($group)) {
				$this->setFlash('success', UserModule::t('Group successfully changed'));
				$this->redirect($model);
			} else {
				$this->redirect(array('change'));
			}
		}
	}
}