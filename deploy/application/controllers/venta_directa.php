<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venta_directa extends My_Controller {

	public function index()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'venta_directa';

		$crud = new grocery_CRUD();

        $crud->set_table( 'ventas_directa' );

		$crud->order_by('id', 'desc');

        $crud->columns( 'facturaNumero', 'fechaManual', 'cliente', 'total', 'iva', 'total_iva_inc');

        $crud	->display_as( 'facturaNumero','Venta/Factura Número' )
        		->display_as( 'fechaManual', 'Fecha de Venta')
        		->display_as( 'total', 'Subtotal' )
        		->display_as( 'iva', 'IVA' )
        		->display_as( 'total_iva_inc', 'TOTAL' );
        		
        $crud->set_lang_string('list_add', "+ Nueva Venta Directa");
        
        $crud->callback_column( 'facturaNumero',	array($this,'facturaNumero') );
		$crud->callback_column( 'total',			array($this,'currencyColumn') );
		$crud->callback_column( 'iva',				array($this,'currencyColumn') );
		$crud->callback_column( 'total_iva_inc',	array($this,'currencyColumn') );

        $crud->unset_delete();
        if (!$this->local)
		{
        	$crud->unset_add();
        }
        else
        {
        	$crud->add_url(base_url('index.php/venta_directa/nuevaVenta'));
        }
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_proveedores', $data);
		$this->load->view( 'venta_directa_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function facturaNumero( $value, $row )
	{
		$toReturn = '<a href="' . base_url('index.php/venta_directa/verVenta/' . $row->id) . '">' . $row->facturaNumero . '</a>';
		
		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADA</span>';
		}
	
		return $toReturn;
	}

	public function nuevaVenta()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'venta_directa';
		
		$data['showDetalle']		= false;
	
		// Datos necesiarios para el form.
		$productoModel				= new Producto_model();
		$movimientosProductosModel	= new MovimientosProductos_model();

		$data['productos'] = array();
		$todosLosProductos = $productoModel->getProductosTodos(false, true, false, $this->local);
		foreach($todosLosProductos as $key => $producto)
		{
			$data['productos'][$producto->id] = array(
													'nombre'	=> $producto->nombre,
													'cantidad'	=> array(
															              'name'        => 'producto_' . $producto->id,
															              'maxlength'   => '2',
															              'size'		=> '2',
															              'style'		=> 'text-align:center;',
															              'autocomplete'=> 'off',
															              'class'		=> 'input_cantProd numeric',
															              'value'		=> ($this->input->post('producto_' . $producto->id)) ? $this->input->post('producto_' . $producto->id) : '',
															           ),
													'precio'	=> array(
															              'name'        => 'producto_precio_' . $producto->id,
															              'maxlength'   => '10',
															              'size'		=> '10',
															              'style'		=> 'text-align:right;',
															              'class'		=> 'pcioUnit numeric',
															              'value'		=> ($this->input->post('producto_precio_' . $producto->id)) ? $this->input->post('producto_precio_' . $producto->id) : $producto->precioVenta,
															              'autocomplete'=> 'off'
															           )
													);
		}

		$data['fecha'] 			= array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
										'autocomplete'	=> 'off'
		);

		$data['facturaNumero']	= array(
										'name'      	=> 'facturaNumero',
										'value'			=> $this->input->post('facturaNumero'),
										'autocomplete'	=> 'off',
										'class'			=> 'numeric'
		);

		$data['cliente']		= array(
										'name'      	=> 'cliente',
										'value'			=> $this->input->post('cliente'),
										'size'			=> 20,
										'style'			=> 'width:300px;'
		);
		
		$data['detalle'] = array(
										'name'        	=> 'detalle',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);
		
		$data['form_open']		= array('onsubmit'	=> "checkProductos()");

		// Validaciones
		$this->form_validation->set_rules('facturaNumero',		'Factura Número',		'required|numeric');
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',	'required');
		$this->form_validation->set_rules('cliente',			'Cliente',				'required');

		if ( $this->input->post('detalle') )
			$data['showDetalle']		= true;

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			// Validacion de que los productos están en stock.
			$totalConsignacionCantidad	= 0;

			foreach($todosLosProductos as $key => $producto)
			{
				$nuevaConsignacionCantidad	= $this->input->post('producto_' . $producto->id);
				
				$totalConsignacionCantidad += $nuevaConsignacionCantidad;
			}
			
			if (!$totalConsignacionCantidad)
			{
				flash_alert('Seleccioná la cantidad de al menos un producto.');
			}
			else
			{
	
				$venta_directaModel = new Ventadirecta_model();
				$ventaId = $venta_directaModel->nuevaVenta(
														$this->input->post('cliente'),
														$this->input->post('facturaNumero'),
														$this->input->post('fecha'),
														$this->input->post('detalle')
														);
				if ($ventaId)
				{
					$totalVenta		= 0;
					$cantidadVenta	= 0;
	
					$venta_directaProductosModel	= new Venta_directaProductos_model();
					
					// Para cada uno de los productos
					foreach($todosLosProductos as $key => $producto)
					{				
						// Si la cantidad es > 1
						if ($this->input->post('producto_' . $producto->id))
						{
							$cantidad = 0;
						
							// Si la cantidad es > 1
							if ($this->input->post('producto_' . $producto->id))
							{
								// Un registro por producto.
								for($i=1; $i<=$this->input->post('producto_' . $producto->id); $i++)
								{
									$cantidad		+= 1;
		
									$totalVenta 	+= $this->input->post('producto_precio_' . $producto->id);
									$cantidadVenta	+= 1;
								}
	
								$venta_directaProductosModel->insertVentaDirectaProducto(
																				$ventaId,
																				$producto->id,
																				$cantidad,
																				$this->input->post('producto_precio_' . $producto->id)
																		);
							}
						}
					}
					
					$venta_directaModel->updateTotal($ventaId, $totalVenta, $cantidadVenta);
	
					redirect(base_url( 'index.php/venta_directa/'), 'location');
				}
			}
		}

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'venta_directa_nueva' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}
	
	public function verVenta($idVenta)
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'venta_directa';
	
		$venta_directaModel = new Ventadirecta_model();
		
		$venta = $venta_directaModel->getVenta($idVenta);
		$data['venta'] = $venta;

		// Grocery Crud
		$crud = new grocery_CRUD();

        $crud->set_table( 'ventas_directa_productos' );
        
        $crud->where('venta_directa_id = ' . $idVenta);

		$crud->order_by('producto_id');
		
		$crud->set_relation( 'producto_id', 'producto', 'nombre');

        $crud->columns( 'producto_id', 'cantidad', 'producto_precio');
        
        $crud->callback_column( 'producto_precio',			array($this,'currencyColumn') );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'producto_precio','Precio Unitario' );

        $crud->unset_delete();
        $crud->unset_add();
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
		
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'venta_directa_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function anular($id)
	{
		$venta_directaModel = new Ventadirecta_model();

		if ($venta_directaModel->anular($id))
		{
			redirect(base_url( 'index.php/venta_directa/verVenta/' . $id), 'refresh');
		}

	}
}