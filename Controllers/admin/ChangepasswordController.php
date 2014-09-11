<?php

class ChangepasswordController extends BackendController
{
    public $defaultAction = 'changepassword';

	/**
	 * @var UserChangePassword
	 */
	public $model = 'UserChangePassword';

	public function actions()
	{
		return array(
			'changepassword' => array(
				'class' => 'user.components.actions.MChangepasswordAction',
				'model' => $this->model,
			)
		);
	}
}