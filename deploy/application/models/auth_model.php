<?php
class Auth_model extends My_Model {
	
	public function login($username, $password)
	{
		switch($username)
		{
			case 'ppooww@gmail.com':
				$authUser['db_pass']= 'FOL711';
				$authUser['email']	= 'ppooww@gmail.com';
				$authUser['id']		= 1;
				break;
			case 'mateoc@gmail.com':
				$authUser['db_pass']= 'HTL2514';
				$authUser['email']	= 'mateoc@gmail.com';
				$authUser['id']		= 2;
				break;
			case 'aguscampomar@gmail.com':
				$authUser['db_pass']= '123456';
				$authUser['email']	= 'aguscampomar@gmail.com';
				$authUser['id']		= 3;
				break;
			case 'alicia@gmail.com':
				$authUser['db_pass']= '123456';
				$authUser['email']	= 'alicia@gmail.com';
				$authUser['id']		= 4;
				break;
			default:
				return false;
			break;
		}
		
		if ($authUser['db_pass'] == $password)
		{
			return $authUser;
		}
		else
		{
			return false;
		}
	}
}