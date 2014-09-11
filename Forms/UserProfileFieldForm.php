<?php

/**
 * Created by Studio107.
 * Date: 14.04.13
 * Time: 17:14
 * All rights reserved.
 */
 
class UserProfileFieldForm extends CustomFieldsForm
{
	public function getFields()
	{
		return array_merge(array(
			'group_id' => array(
				'class' => 'Select2Field',
				'data' => CHtml::listData(UserGroup::model()->findAll(), 'id', 'name'),
			)
		), parent::getFields());
	}
}