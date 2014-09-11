<?php

/**
 * Created by Studio107.
 * Date: 19.04.13
 * Time: 11:52
 * All rights reserved.
 */
 
class UserRegistrationForm extends MForm
{
	public function getFields()
	{
		return array(
			'username' => 'TextField',
			'email' => 'TextField',
			'password' => 'PasswordField',
			'verifyPassword' => 'PasswordField',
		);
	}
}