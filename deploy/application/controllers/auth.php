<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends My_Controller {

	public function login()
	{
		$data = array();

		// Forms
		$data['email'] = array(
										'name'        	=> 'email',
										'value'			=> ($this->input->post('email')) ? $this->input->post('email') : '',
										'placeholder'	=> 'email'
		);

		$data['password'] = array(
										'name'        	=> 'password',
										'value'			=> ($this->input->post('password')) ? $this->input->post('password') : '',
										'type'			=> 'password',
										'placeholder'	=> '****',
		);

		// Validaciones
		$this->form_validation->set_rules('email',		'email',		'required');
		$this->form_validation->set_rules('password',	'password',		'required');

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			$authModel = new auth_model();
			$authUser = $authModel->login(
											$this->input->post('email'),
											$this->input->post('password')
										);
										
			if ( $authUser )
			{
				$this->session->set_userdata( 'authUser', $authUser );
				
				redirect(base_url( 'index.php/stock'), 'location');
			}
		}


        $this->load->view( 'templates/nologin_head', $data );
		$this->load->view( 'auth_login' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function logout()
	{
		$this->session->set_userdata( 'authUser', 0 );
		
		redirect(base_url( 'index.php/auth/login'), 'location');
	}

}