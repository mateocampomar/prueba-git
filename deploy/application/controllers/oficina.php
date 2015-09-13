<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oficina extends My_Controller {

	public function index( $filter='' )
	{	
		$data = array();
		$data['filter']		= $filter;

		$data['menu_header_seleccionado']	= 'oficina';
		
		if ($filter == 'web')		$data['menu_seleccionado']			= 'web';
		else						$data['menu_seleccionado']			= 'ventas';

		$crud = new grocery_CRUD();

        $crud->set_table( 'ventas_oficina' );

		if ($filter == 'web')
		{		
			if ( $this->input->post('filter_web') )	$this->session->set_userdata( 'filter_web', $this->input->post('filter_web'));
		
			if ( $this->session->userdata( 'filter_web') )		$filter_web = $this->session->userdata( 'filter_web');
			else												$filter_web	= 'nuevo';

			$crud->where("tipo_venta = 'web'");
			//$crud->where("estado_web = '" . $filter_web . "'");
			
			$data['filter_web']	= $filter_web;
			
			$crud->columns( 'facturaNumero', 'fechaManual', 'cliente', 'estado_web', 'total');
			
			$crud->display_as( 'total', 'Total' );
			
			$crud->unset_add();
		}
		else
		{
			$crud->where("tipo_venta != 'web'");

			$crud->columns( 'facturaNumero', 'fechaManual', 'fechaProcesado', 'cliente', 'cantidad', 'total', 'iva', 'total_iva_inc');
			
			$crud	->display_as( 'total', 'Subtotal' )
					->display_as( 'fechaProcesado', 'Fecha de Procesado');
			
			$crud->add_url(base_url('index.php/oficina/nuevaVenta'));
		}

		$crud->order_by('id', 'desc');

        $crud	->display_as( 'facturaNumero','Venta/Factura Número' )
        		->display_as( 'fechaManual', 'Fecha de Venta')
        		->display_as( 'iva', 'IVA' )
        		->display_as( 'total_iva_inc', 'TOTAL' );
        
        $crud	->callback_column( 'facturaNumero',	array($this,'facturaNumero') )
        		->callback_column( 'cantidad',		array($this,'cantidadColumn') )
				->callback_column( 'total',			array($this,'currencyColumn') )
				->callback_column( 'iva',			array($this,'currencyColumn') )
				->callback_column( 'total_iva_inc',	array($this,'currencyColumn') );
		
		$crud->set_lang_string('list_add', "+ Nueva Venta Oficina");

        $crud->unset_delete();
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'oficina_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
    public function cantidadColumn( $value, $row )
	{
		$sinProcesar = '';

		if ( $row->status == 2 )
		{
			$sinProcesar = ' <span class="textlabelalert">SIN PROCESAR</span>';
		}	
	
		return '<div class="cantidad">x ' . $value . $sinProcesar . '</div>';
	}

	public function facturaNumero( $value, $row )
	{
		if ( $row->facturaNumero == 0 )
		{
			$fn = 's/n';
		}
		else
		{
			$fn = ( $row->facturaNumero != '00000' ) ? addZeros( $row->facturaNumero ) : 'Web: ' . addZeros( $row->id, 10 );
		}
	
		$toReturn = '<a href="' . base_url('index.php/oficina/verVenta/' . $row->id) . '">' . $fn . '</a>';
		
		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADA</span>';
		}
	
		return $toReturn;
	}

	public function nuevaVenta()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'ventas';
		
		$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : 0;
	
		// Datos necesiarios para el form.
		$productoModel				= new Producto_model();
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		$productoModel->setPrecioWeb(false);
		$todosLosProductos = $productoModel->getProductosTodos(false, false, true);

		$data['productos'] = array();
		foreach($todosLosProductos as $key => $producto)
		{
			$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $data['selectedDeposito']);
			$movimientosProductosEnStock  = $movimientosConsignablesPorProducto[0];
		
			$disponiblesEnStock = $movimientosProductosEnStock->cantidad;
			
			if ( $disponiblesEnStock || $producto->status )
			{
				$data['productos'][$producto->id] = array(
														'nombre'	=> $producto->nombre,
														'id'		=> $producto->id,
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
																              'class'		=> 'pcioUnit numeric',
																              'style'		=> 'text-align:right;',
																              'value'		=> ($this->input->post('producto_precio_' . $producto->id)) ? $this->input->post('producto_precio_' . $producto->id) : $producto->precioOficina,
																              'autocomplete'=> 'off'
																           )
														);
			}
			
			$data['productos_consignables'][$producto->id] = $disponiblesEnStock;
		}

		$data['fecha'] 			= array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
										'autocomplete'	=> 'off'
		);

		$data['cliente']		= array(
										'name'      	=> 'cliente',
										'value'			=> $this->input->post('cliente'),
										'size'			=> 20,
										'style'			=> 'width:300px;'
		);


		$data['rut']			= array(
										'name'      	=> 'rut',
										'value'			=> $this->input->post('rut'),
										'size'			=> 15,
										'placeholder'	=> '(opcional)',
										'style'			=> 'width:250px;'
		);


		$data['direccion']		= array(
										'name'      	=> 'direccion',
										'value'			=> $this->input->post('direccion'),
										'size'			=> 50,
										'placeholder'	=> '(opcional)',
										'style'			=> 'width:250px;'
		);

		$data['detalle'] = array(
									'name'        	=> 'detalle',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);

		// Dropdown Deposito
		$depositoModel		= new Deposito_Model();
		$depositos			= $depositoModel->getDepositosTodos();
		$depositosOptions	= array(0 => '');
		foreach ($depositos as $deposito)
		{
			$depositosOptions[$deposito->id]		= $deposito->nombre;
		}
		$data['depositosOptions']	= $depositosOptions;

		if ( $this->input->post('detalle') )
			$data['showDetalle']		= true;
		else
			$data['showDetalle']		= false;

		
		$data['form_open']		= array('onsubmit'	=> "checkProductos()");

		// Validaciones
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',	'required');
		$this->form_validation->set_rules('cliente',			'Cliente',				'required');
		$this->form_validation->set_rules('deposito',			'Depósito',				'is_natural_no_zero');

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			// Validacion de que los productos están en stock.
			$stockOk					= true;
			$totalConsignacionCantidad	= 0;

			foreach($todosLosProductos as $key => $producto)
			{
				$nuevaConsignacionCantidad	= $this->input->post('producto_' . $producto->id);
				$consignablesCantidad		= $data['productos_consignables'][$producto->id];
				
				$totalConsignacionCantidad += $nuevaConsignacionCantidad;

				if ($nuevaConsignacionCantidad > $consignablesCantidad)
				{
					flash_alert('Hay por lo menos un producto que supera el stock.');
				}	
			}
			
			if (!$totalConsignacionCantidad)
			{
				flash_alert('Seleccioná la cantidad de al menos un producto.');
			}
		
			if ($stockOk && $totalConsignacionCantidad)
			{

				$oficinaModel = new Oficina_model();
				$ventaId = $oficinaModel->nuevaVenta(
														$this->input->post('cliente'),
														$this->input->post('facturaNumero'),
														$this->input->post('fecha'),
														$this->input->post('detalle'),
														'',
														$this->input->post('rut'),
														$this->input->post('direccion')
														);
	
				if ($ventaId)
				{
					$totalVenta		= 0;
					$cantidadVenta	= 0;
	
					$movimientosProductosModel	= new MovimientosProductos_model();
					$oficinaProductosModel		= new Venta_oficinaProductos_model();
					
					// Para cada uno de los productos
					foreach($todosLosProductos as $key => $producto)
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
	
							$oficinaProductosModel->insertVentaOficinaProducto(
																			$ventaId,
																			$producto->id,
																			$cantidad,
																			$this->input->post('producto_precio_' . $producto->id)
																		);
						}
					}
					
					$oficinaModel->updateTotal($ventaId, $totalVenta, $cantidadVenta, $data['selectedDeposito']);
	
					redirect(base_url( 'index.php/oficina/verVenta/' . $ventaId), 'location');
				}
			}
		}

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_oficina', $data );
		$this->load->view( 'oficina_nueva' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}
	
	public function verVenta($idVenta)
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['showDetalle']				= false;
		$stockOk							= true;
	
		$oficinaModel = new Oficina_model();
		$movimientosProductosModel = new MovimientosProductos_model();
		
		$venta = $oficinaModel->getVenta($idVenta);
		$data['venta'] = $venta;

		
		if ($venta->tipo_venta == 'web')	$data['menu_seleccionado']			= 'web';
		else								$data['menu_seleccionado']			= 'ventas';

		if ( $venta->status == 1 )
		{
			// Grocery Crud
			$crud = new grocery_CRUD();
			
			$this->db->select('*, producto_id as producto_id_original, producto_precio as producto_precio_original, cantidad as cantidad_original');
	
	        $crud->set_table( 'ventas_oficina_productos' );
	        
	        $crud->where('venta_oficina_id = ' . $idVenta);
	
			$crud->order_by('producto_id');
			
			$crud->set_relation( 'producto_id', 'producto', 'nombre');
	
	        $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'producto_precio', 'subtotal');
	        
	        $crud	->callback_column( 'producto_precio',		array($this,'currencyColumn') )
	        		->callback_column( 'cantidad',				array($this,'cantidadColumn') )
	        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
	        		->callback_column( 'subtotal',				array($this,'subtotalColumn') );
	
	        $crud	->display_as( 'producto_id','Producto' )
	        		->display_as( 'producto_precio','Precio' )
	        		->display_as( 'producto_id_original','' )
	        		->display_as( 'subtotal','Importe' );
	    }
	    else
	    {
		    // La factura está anulada o no procesada
			// Chequeo de Stock.
			$oficinaProductosModel		= new Venta_oficinaProductos_model();
			$todosLosProductos = $oficinaProductosModel->getProductosPorVenta( $idVenta );
			foreach($todosLosProductos as $key => $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $venta->deposito_id );
				$movimientosConsignablesPorProducto = $movimientosConsignablesPorProducto[0];
				$movimientosProductosEnStock  = $movimientosConsignablesPorProducto->cantidad;

				if ( $producto->cantidad > $movimientosProductosEnStock )
				{
					$stockOk = false;
				}
			}

			// Form
			$data['facturaNumero']	= array(
											'name'      	=> 'facturaNumero',
											'value'			=> ( $this->input->post('facturaNumero') ) ? $this->input->post('facturaNumero') : ( ( $venta->facturaNumero ) ? $venta->facturaNumero : "" ),
											'autocomplete'	=> 'off'
			);

			// Detalle
			$data['detalle'] = array(
										'name'        	=> 'detalle',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : $venta->detalle,
			);

			if ( $this->input->post('detalle') || $venta->detalle )
				$data['showDetalle']		= true;

			// Dropdown Deposito
			$depositoModel		= new Deposito_Model();
			$depositos			= $depositoModel->getDepositosTodos();
			foreach ($depositos as $deposito)
			{
				$depositosOptions[$deposito->id]		= $deposito->nombre;
			}
			
			$data['depositosOptions']	= $depositosOptions;
			$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : $venta->deposito_id;


			// Validaciones
			$this->form_validation->set_rules('facturaNumero',		'Factura Número',		'required|numeric|is_unique_and_not_id[consignacion.facturaNumero.' . $idVenta . ']');


			// Submit
			if ($this->form_validation->run() == FALSE)
			{
				// Not ok
			}
			else
			{
				$oficinaModel->updateVentaOficinaProcesar( $idVenta, $this->input->post('facturaNumero'), $this->input->post('deposito'), $this->input->post('detalle') );

				redirect(base_url( 'index.php/oficina/procesar/' . $idVenta), 'location');
			}


			// Grocery Crud
			$crud = new grocery_CRUD();
	
	        $crud->set_table( 'ventas_oficina_productos' );
	        
	        $crud->where('venta_oficina_id = ' . $idVenta);
	
			$crud->order_by('producto_id');
			
			$crud->set_relation( 'producto_id', 'producto', 'nombre');
	
	        $crud->columns( 'producto_id', 'cantidad', 'producto_precio');
	        
	        $this->selectedDeposito = $venta->deposito_id;
	        
	        $crud->callback_column( 'producto_precio',		array($this,'currencyColumn') );
	        $crud->callback_column( 'cantidad',				array($this,'callback_cantidadStockColumn') );
	
	        $crud	->display_as( 'producto_id','Producto' )
	        		->display_as( 'producto_precio','Precio Unitario' );
	    }

	    $crud->unset_delete();
	    $crud->unset_add();
	    $crud->unset_edit();
	
	    $data['crud'] = $crud->render();

		$data['stockOk']	= $stockOk; 
		
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data );
		$this->load->view( 'oficina_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function callback_cantidadStockColumn( $value, $row )
	{
		$toReturn					= '<div style="text-align:center;">';
		$movimientosProductosModel	= new MovimientosProductos_model();	
		
		
		$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto( $row->producto_id, $this->selectedDeposito );
	
		if ( $movimientosConsignablesPorProducto[0]->cantidad < $row->cantidad )
		{
			$toReturn .= '<strong style="color:red;">';
			$toReturn .= $row->cantidad . '<br/>';
			$toReturn .= '<span style="font-size:smaller; font-weight:normal;">( Faltan ' . ( $row->cantidad - $movimientosConsignablesPorProducto[0]->cantidad ) . ' producto/s )</span>';
			$toReturn .= '</strong>';
		}
		else
		{
			$toReturn .= $row->cantidad;
		}
		
		$toReturn .= '</div>';
		
		return $toReturn;
	}

	public function subtotalColumn( $value, $row )
	{
		return  '<div class="currency-right">' . currency_format( $row->cantidad_original * $row->producto_precio_original ) . "</div>";
	}

	public function anular($id)
	{
		$oficinaModel = new Oficina_model();

		if ($oficinaModel->anular($id))
		{
			redirect(base_url( 'index.php/oficina/verVenta/' . $id), 'refresh');
		}

	}
	
	public function avanzarPedido( $ventaId, $param2=null )
	{
	
		$productoModel				= new Producto_model();
		$movimientosProductosModel	= new MovimientosProductos_model();
		$oficinaModel				= new Oficina_model();

		$venta = $oficinaModel->getVenta( $ventaId );
		
		if ( $venta->tipo_venta == 'web' )
		{
			if ( $venta->estado_web == 'en-proceso' )
			{
				$depositoId = $param2;
			
				$oficinaProductosModel		= new Venta_oficinaProductos_model();
	
				// Validacion de que los productos están en stock.
				$stockOk					= true;
				
				foreach( $oficinaProductosModel->getProductosPorVenta( $ventaId ) as $key => $producto )
				{
					$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $depositoId);
					$movimientosProductosEnStock  = $movimientosConsignablesPorProducto[0];
			
					$disponiblesEnStock = $movimientosProductosEnStock->cantidad;
					
					if ($producto->cantidad > $disponiblesEnStock)
					{
						flash_alert('Hay por lo menos un producto que supera el stock.');
						$stockOk = false;
					}
				}
				
				if ($stockOk)
				{
					foreach( $oficinaProductosModel->getProductosPorVenta( $ventaId ) as $key => $producto )
					{
						// Un registro por producto.
						for($i=1; $i<=$producto->cantidad; $i++)
						{
							$movimientosProductosModel->venderMovimientos($producto->producto_id, $ventaId, $producto->producto_precio, $depositoId );
						}
					}
					
					$oficinaModel->anvanzarSent( $ventaId, $depositoId );
				}
			}
			elseif ( $venta->estado_web == 'confirmed' )
			{
				$oficinaModel->anvanzarEnProceso( $ventaId );
			}
			
			redirect(base_url( 'index.php/oficina/verVenta/' . $ventaId), 'location');
		}
	}

	public function procesar( $idVenta )
	{
		$oficinaModel = new Oficina_model();
		$venta = $oficinaModel->getVenta( $idVenta );
		
		// Si no es nueva
		if ( $venta->status != 2 ) {
			flash_alert('La Venta Oficina no se encuentra en estado para procesar.');
		}
		else
		{
			$movimientosProductosModel	= new MovimientosProductos_model();

			$oficinaProductosModel		= new Venta_oficinaProductos_model();
			$todosLosProductos = $oficinaProductosModel->getProductosPorVenta( $idVenta );
		
			// Validacion de que los productos están en stock.
			$stockOk					= true;

			// Chequeo de Stock.
			foreach($todosLosProductos as $key => $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $venta->deposito_id );
				$movimientosConsignablesPorProducto = $movimientosConsignablesPorProducto[0];
				$movimientosProductosEnStock  = $movimientosConsignablesPorProducto->cantidad;

				if ( $producto->cantidad > $movimientosProductosEnStock )
				{
					flash_alert('Hay por lo menos un producto que supera el stock.');
					$stockOk = false;
				}
			}

			// Movimientos en stock
			if ($stockOk)
			{
				foreach($todosLosProductos as $key => $producto)
				{
					for ( $i=1; $i <= $producto->cantidad; $i++)
					{
						$movimientosProductosModel->venderMovimientos($producto->producto_id, $idVenta, $producto->producto_precio, $venta->deposito_id );
					}
				}

				if ( !$oficinaModel->procesarRetiroStockVenta( $idVenta ) )
				{
					flash_error('No se pudieron actualizar el estado de la venta oficina.');
				}
			}
		}

		redirect(base_url( 'index.php/oficina/verVenta/' . $venta->id ), 'location');
		die;
	}

	public function facturaOficial( $oficinaId )
	{
		$data = array();

		// Models
		$oficinaModel	= new Oficina_model();
		$oficina		= $oficinaModel->getVenta( $oficinaId );

		$oficinaProductosModel	= new Venta_oficinaProductos_model();
		$oficinaProductos		= $oficinaProductosModel->getProductosPorVenta( $oficinaId );


		// Library
		$this->load->library('fpdf');
		$pdf	= new FPDF();


		// View
		$data['pdf']				= $pdf;
		$data['oficina']			= $oficina;
		$data['oficinaProductos']	= $oficinaProductos;

		$this->load->view( 'pdf/oficina/factura-oficial' , $data );
	}

	public function cambiarNumero( $oficinaId, $nuevoNumero )
	{
		$oficinaModel	= new Oficina_model();
		
		// Chequeo a ver si ya hay una factura con ese número.
		$facturaConMismoNumero = $oficinaModel->getOficinaByNumero( $nuevoNumero );
		
		if ( $facturaConMismoNumero )
		{
			flash_alert( 'Ya hay una Venta Oficina con el mismo número.' );
		}
		else
		{
			$oficinaModel->editFacturaNumero( $oficinaId, $nuevoNumero );
		}
		
		redirect( base_url( 'index.php/oficina/verVenta/' . $oficinaId ), 'refresh');
	}
}