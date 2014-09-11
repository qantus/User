<?php

/**
 * Created by Studio107.
 * Date: 27.04.13
 * Time: 14:35
 * All rights reserved.
 */
 
class UserRecoveryForm extends MForm
{
	public function getFields()
	{
		return array(
			'login_or_email' => 'TextField'
		);
	}
}