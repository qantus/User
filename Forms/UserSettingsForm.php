<?php

/**
 * Created by Studio107.
 * Date: 15.04.13
 * Time: 12:31
 * All rights reserved.
 */
 
class UserSettingsForm extends MForm
{
	public function getFields()
	{
		return array(
			'moderation' => 'CheckboxField',
			'need_activation' => 'CheckboxField',
			'auto_login' => 'CheckboxField',
            'object_permissions' => 'CheckboxField',
			'username_min_length' => 'TextField',
			'password_min_length' => 'TextField',
			'login_duration' => 'TextField',
			'default_group' => array(
				'class' => 'Select2Field',
				'data' => $this->getModel()->getGroupList()
			),
		);
	}
}