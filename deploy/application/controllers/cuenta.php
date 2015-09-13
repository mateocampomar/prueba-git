<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuenta extends My_Controller {

	public function index()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/compras'), 'location');
			die;
		}

		$data = array();
		if ($this->modo == 'consignacion')
		{
			$data['menu_header_seleccionado']	= 'consignacion';
			$data['menu_seleccionado']			= 'resumen';
		}
		else
		{
			$data['menu_header_seleccionado']	= 'agolan';
			$data['menu_seleccionado']			= 'cuenta';
		}

		$crud = new grocery_CRUD();

        $crud->set_table( 'estado_cuenta' );
        
        $where = 'local_id = ' . $this->local;
        $crud->where($where);

		$crud->order_by('id', 'desc');

        $crud->columns( 'fecha', 'detalle', 'movimiento', 'saldo');

        $crud	->display_as( 'fecha','Fecha del Movimiento' );

		$crud->callback_column( 'movimiento',	array($this,'currencyColumn') );
		$crud->callback_column( 'saldo',		array($this,'currencyColumn') );

		$crud->set_lang_string('list_add', "+&nbsp;&nbsp;Nuevo Movimiento Manual");

        $crud->unset_delete();
        $crud->add_url('cuenta/nuevoMovimiento');
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();


        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'cuenta' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function nuevoMovimiento()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/recibo'), 'refresh');
			die;
		}
	
		$data = array();
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado'] 			= 'resumen';


		$data['detalle'] = array(
									'name'        	=> 'detalle',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
									'style'			=> 'width:100%;'
		);

		$data['total'] = array(
									'name'        	=> 'total',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('total')) ? $this->input->post('total') : '',
									'class'			=> 'numeric'
		);

		// Validaciones
		$this->form_validation->set_rules('detalle',		'Detalle',		'required');
		$this->form_validation->set_rules('total',			'Total',		'required');

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			// Generar Movimiento
			$cuentaModel = new cuenta_model();

				$arrayDatos = array(
					'local_id'			=> $this->local,
					'tipo_movimiento'	=> 'movimiento_manual',
					'detalle'			=> $this->input->post('detalle'),
					'relacion_obj'		=> 0,
					'movimiento'		=> $this->input->post('total')
				);
			
			if( $cuentaModel->nuevoRegistro('movimiento_manual', $arrayDatos) )
			{
				redirect(base_url( 'index.php/cuenta/'), 'location');
			}
		}

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'cuenta_nuevo' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}
}