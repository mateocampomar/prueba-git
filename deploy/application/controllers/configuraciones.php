<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configuraciones extends My_Controller {

	public function index()
	{
		$data['menu_header_seleccionado']	= 'configuraciones';

		// Form
		$data['fecha'] 			= array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : mysql_to_datepicker(cierreDeEjercicio()),
										'autocomplete'	=> 'off'
		);
		
		// Validaciones
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',	'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			flash_ok('Configuraciones Guardadas.');
		
			$this->session->set_userdata( 'cierre_de_ejercicio', datepicker_to_mysql($this->input->post('fecha')) );
		}

        $this->load->view( 'templates/head', $data );
		$this->load->view( 'config/index' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
}