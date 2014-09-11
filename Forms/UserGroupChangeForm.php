<?php

/**
 * Created by Studio107.
 * Date: 25.04.13
 * Time: 12:12
 * All rights reserved.
 */
 
class UserGroupChangeForm extends MForm
{
	public function getFields()
	{
		return array(
			'id' => array(
				'class' => 'RadiolistField',
				'data' => CHtml::listData($this->model->findAll(), 'id', 'name')
			)
		);
	}
}