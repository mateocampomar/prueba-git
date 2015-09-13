<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nc extends My_Controller {

	public function index()
	{	
		$data = array();
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado'] = 'nc';

		$crud = new grocery_CRUD();

        $crud->set_table( 'nota_de_credito' );
        
        // where
        if ($this->local)
        	$crud->where('local_id', $this->local);

		$crud->order_by('ncNumero');
		
		$crud->set_relation( 'local_id', 'local', '{nombre}');

        $crud->columns( 'ncNumero', 'local_id', 'fechaManual', 'fechaAuto', 'cantidad', 'total');

        $crud	->display_as( 'ncNumero','Nota de Crédito #' )
        		->display_as( 'fechaManual','Fecha' )
        		->display_as( 'local_id', 'Local')
        		->display_as( 'fechaAuto', 'Fecha de Creación');
        
        $crud->set_lang_string('list_add', "+ Nueva Nota de Crédito");
        
        $crud->callback_column( 'ncNumero',		array($this,'ncNumeroColumn') );
        $crud->callback_column( 'total',			array($this,'currencyColumn') );

        $crud->unset_delete();

        if (!$this->local)
		{	$crud->unset_add(); }
        else
        {	$crud->add_url('nc/nuevaNc'); }

        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'nc_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function ncNumeroColumn( $value, $row )
	{
		$toReturn = '<a href="' . base_url('index.php/nc/verNc/' . $row->id) . '">' . addZeros( $row->ncNumero ) . '</a>';

		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADO</span>';
		}
		
		return $toReturn;
	}

	public function nuevaNc()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/nc'), 'refresh');
			die;
		}
		
		$movimientosProductosModel				= new MovimientosProductos_model();
		$productosConDevolucion					= $movimientosProductosModel->movimientosNcParaHacer($this->local);
		//$movimientosCambioPrecioConsignacion	= $movimientosProductosModel->movimientosCambioPrecioConsignacion($this->local);

		$data = array();
		$data['menu_seleccionado']			= 'nc';
		if ($this->modo == 'consignacion')	$data['menu_header_seleccionado']	= 'consignacion';
		else								$data['menu_header_seleccionado']	= 'agolan';

		$data['productosConDevolucion']					= $productosConDevolucion;
		//$data['movimientosCambioPrecioConsignacion']	= $movimientosCambioPrecioConsignacion;
		
		$data['showDetalle']		= true;

		if (count($productosConDevolucion))
		{
			$data['totalNc']	= 0;
			$data['cantidadNc']	= 0;
			foreach($data['productosConDevolucion'] as $movimiento)
			{
				$iva = calcularIVA($movimiento->consignacionPrecio);
			
				$data['totalNc']		+= $iva['total_iva_inc'];
				$data['cantidadNc']		+= 1;
			}
			
			$data['showDetalle']		= false;
			
			
			// Dropdown Deposito
			$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : 0;

			$depositoModel		= new Deposito_Model();
			$depositos			= $depositoModel->getDepositosTodos();
			$depositosOptions	= array(0 => '');
			foreach ($depositos as $deposito)
			{
				$depositosOptions[$deposito->id]		= $deposito->nombre;
			}
			$data['depositosOptions']	= $depositosOptions;
		}
	
		// Form
		$data['fecha'] = array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'autocomplete'	=> 'off',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
		);

		$data['ncNumero'] = array(
										'name'        	=> 'ncNumero',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('ncNumero')) ? $this->input->post('ncNumero') : '',
										'class'			=> 'numeric'
		);

		$data['detalle'] = array(
										'name'        	=> 'detalle',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);

		$data['total'] = array(
										'name'        	=> 'total',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('total')) ? $this->input->post('total') : '',
										'class'			=> 'numeric'
		);


		// Validaciones
		$this->form_validation->set_rules('ncNumero',		'Número',		'required|is_unique[nota_de_credito.ncNumero]');
		$this->form_validation->set_rules('fecha',			'Fecha',		'required');
		if (!count($productosConDevolucion))
		{
			$this->form_validation->set_rules('detalle',		'Detalle',		'required');
			$this->form_validation->set_rules('total',			'Total',		'required');
		}
		else
		{
			$this->form_validation->set_rules('deposito',		'Depósito',		'is_natural_no_zero');
		}

		if ( $this->input->post('detalle') )
			$data['showDetalle']		= true;

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			if (count($productosConDevolucion))
			{
				$movimientosProductosModel = new MovimientosProductos_model();
				$ncProductosModel = new NcProductos_model();
				
				$totalProductos = 0;
				
				$ncArray 						= array();
				$nuevosMovimientosCreadosPorNC	= array();
				
				foreach( $productosConDevolucion as $movimiento )
				{
					$nuevosMovimientosCreadosPorNC[] = $movimientosProductosModel->devolver($movimiento->id, $data['selectedDeposito']);
					
					if (isset($ncArray[$movimiento->producto_id]))
					{
						$ncArray[$movimiento->producto_id]['cantidad']		+= 1;
					}
					else
					{
						$ncArray[$movimiento->producto_id]['cantidad']		= 1;
						$ncArray[$movimiento->producto_id]['consignacionPrecio'] = $movimiento->consignacionPrecio;
					}
					
					$totalProductos += $movimiento->consignacionPrecio;
				}

				$total		= $totalProductos * $this->config->item('iva');
			}
			else
			{
				$total		= $this->input->post('total');
			}
			
			$detalle	= $this->input->post('detalle');

			$ncModel = new nc_model();
			$ncId = $ncModel->nuevaNc(
										$this->local,
										$this->input->post('ncNumero'),
										datepicker_to_mysql($this->input->post('fecha')),
										( !empty($data['selectedDeposito']) ) ? $data['selectedDeposito'] : null,
										$detalle,
										$total
									);

			if($ncId)
			{
				if (count($productosConDevolucion))
				{
					foreach($ncArray as $productoId => $registroNc)
					{
						$ncProductosModel->insertNcProducto(
													$ncId,
													$productoId,
													$registroNc['cantidad'],
													$registroNc['consignacionPrecio']
												);
					}
				}

				if ( !empty($nuevosMovimientosCreadosPorNC) && count($nuevosMovimientosCreadosPorNC))
				{				
					foreach($nuevosMovimientosCreadosPorNC as $movimiento_id)
					{
						$movimientosProductosModel->actualizar_NCId( $movimiento_id, $ncId );
					}
				}

				redirect(base_url( 'index.php/nc/verNc/' . $ncId), 'location');
			}
		}
		
		if (count($productosConDevolucion))
		{
			$crud = new grocery_CRUD();
	
			$this->db->select('*, producto_id as producto_id_original');
	
	        $crud->set_table( 'movimientos_productos' );
	        
	        // [TODO] Se podría traer el where desde el modelo así si cambia no hay que modificar dos cosas.
	        $where = "devolucion IS NOT NULL AND local_id = " . $this->local;
	        $crud->where($where);
	
	        $crud->set_relation( 'producto_id', 'producto', '{nombre}');
	
			$crud->order_by('producto_id');
			
			$crud	->display_as( 'consignacionPrecio','Precio Unitario (iva no inc)' )
					->display_as( 'producto_id_original','' );
	
	        $crud->columns( 'producto_id_original', 'producto_id', 'consignacionPrecio');
	        
	        $crud	->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
	        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') );
	
	        $crud->unset_delete();
	        $crud->unset_add();
	        $crud->unset_edit();
	
	        // render crud
	        $data['crud'] = $crud->render();
		}

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'nc_nuevo' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}

	public function verNc($ncId)
	{
		$data = array();
		$data['menu_seleccionado'] = 'nc';
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
	
		$ncModel			= new nc_model();
		$ncProductosModel	= new NcProductos_model();
		
		// Nota de Crédito
		$nc = $ncModel->getNc($ncId);
		$data['nc'] = $nc;
		
		// Local
		$localModel = new Local_model();
		$data['ncLocal'] = $localModel->getLocal( $nc->local_id );
		
		$productosDeLaNc = $ncProductosModel->getProductosPorNc($ncId);
		$data['productosDeLaNc']	= $productosDeLaNc;
		$totalCantidadProductos		= 0;

		// Las notas de créditos sin productos asociados son hechas SOLAMENTE a proveedores.
		if (count($productosDeLaNc))
		{
			foreach ( $productosDeLaNc as $producto )
			{
				$totalCantidadProductos += $producto->cantidad;
			}
			$data['totalCantidadProductos'] =	$totalCantidadProductos;
		
			if ( $nc->status )
		    {
    			// La factura está activa
				$crud = new grocery_CRUD();
		
				$this->db->select('*, COUNT(1) as cantidad, SUM(consignacionPrecio) as totalProducto, producto_id as producto_id_original' );
		
				$where = 'nc_id = ' . $ncId;		
				$crud->where($where);
		
		        $crud->set_table( 'movimientos_productos' );
		 
				$this->db->group_by(array(
											'producto_id',
											'consignacionPrecio'
											));
				
				$crud	->set_relation( 'producto_id', 'producto', 'nombre');
		
		        $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'custom', 'consignacionPrecio', 'totalProducto');
		
		        $crud	->display_as( 'producto_id','Producto' )
		        		->display_as( 'consignacionPrecio','Precio' )
		        		->display_as( 'custom','Factura / Consignación' )
		        		->display_as( 'producto_id_original','' )
		        		->display_as( 'totalProducto','Importe' );
		        
		        $crud	->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
		        		->callback_column( 'totalProducto',			array($this,'currencyColumn') )
		        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
		        		->callback_column( 'cantidad',				array($this,'cantidadColumn') )
		        		->callback_column( 'custom',				array($this,'callback_factura') );
			}
			else
			{
		
				// Grocery Crud
				$crud = new grocery_CRUD();
			
			    $crud->set_table( 'nc_productos' );
			    
			    $this->db->select('*, producto_id as producto_id_original' );
			    
			    $crud->where('nc_id = ' . $ncId);
			
				$crud->order_by('producto_id');
				
				$crud->set_relation( 'producto_id', 'producto', 'nombre');
			
			    $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'producto_precio');
			    
			    $crud	->callback_column( 'producto_precio',		array($this,'masIvaColumn') )
			    		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') );
			
			    $crud	->display_as( 'producto_id','Producto' )
			    		->display_as( 'producto_id_original', '')
			    		->display_as( 'producto_precio','Precio (IVA inc)' );
			}
		
		    $crud->unset_delete();
		    $crud->unset_add();
		    $crud->unset_edit();
		
		    // render crud
		    $data['crud'] = $crud->render();
		}
		
        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'nc_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function callback_factura( $value, $row )
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		$movimientos = $movimientosProductosModel->getMovimientosPorNc( $row->nc_id, false, $row->producto_id );
		
		$consignaciones = array();
		
		foreach ($movimientos as $movimiento)
		{
			if ( isset( $consignaciones[$movimiento->consignacion_id] ))
			{
				$consignaciones[$movimiento->consignacion_id]['count']++;
			}
			else
			{
				$consignacionModel = new Consignacion_model();
				$consignacion = $consignacionModel->getConsignacion( $movimiento->consignacion_id );
						
				$consignaciones[$movimiento->consignacion_id]['count']			= 1;
				$consignaciones[$movimiento->consignacion_id]['consignacionNumero']	= $consignacion->facturaNumero;
			}
		}

		$toReturn = '';
	
		foreach ( $consignaciones as $consignacionId => $consignacion )
		{
			$toReturn .= '<small>x' . $consignacion['count'] . '</small> <a href="' . base_url('index.php/consignacion/ver/') . '/' . $consignacionId . '">' . addZeros( $consignacion['consignacionNumero'] ) . '</a><br />';
		}
		
		return $toReturn;
	}
	
	public function anularNc($ncId)
	{
		$ncModel = new nc_model();

		$ncModel->anularNc($ncId);

		redirect(base_url( 'index.php/nc/verNc/' . $ncId), 'refresh');
	}
}