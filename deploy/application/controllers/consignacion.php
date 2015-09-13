<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Consignacion extends My_Controller {

	public function index()
	{
		$data['menu_seleccionado']			= 'consignacion';
		$data['menu_header_seleccionado']	= 'consignacion';

		// Filtros
		$productosParaFiltro = array('Todos...');
		$movimientosProductosModel = new MovimientosProductos_model();
		foreach( $movimientosProductosModel->movimimientosConsignados($this->local) as $movimiento )
		{
			$productosParaFiltro[ $movimiento->producto_id ] = $movimiento->nombre;
		}
	
	    $crud = new grocery_CRUD();

	    $this->db->select('*, producto_id as producto_id_original');

		$where = 'recibo_id IS NULL';
		// AND movimientos_productos.status = 1

		if ($this->local && !isset( $this->filtros['local'] ) )	$where .= ' AND local_id = ' . $this->local;
		else													$where .= ' AND local_id IS NOT NULL';

		if ( is_array( $this->filtros ) && isset( $this->filtros['producto'] ) )
							$where .= ' AND producto_id = ' . $this->filtros['producto'];
		$crud->where($where);

        $crud->set_table( 'movimientos_productos' );

        $crud->set_relation( 'producto_id', 'producto', 'nombre');
        $crud->set_relation( 'local_id', 'local', '{nombre}');

		$this->db->order_by('producto_id desc, consignacion_id asc');

        $crud->columns( 'producto_id_original', 'producto_id', 'acciones', 'consignacion_id', 'local_id', 'consignacionPrecio', 'mp_comentario' );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'consignacion_id','Factura' )
        		->display_as( 'consignacionPrecio', 'Precio Consignado')
        		->display_as( 'producto_id_original', '')
        		->display_as( 'local_id', 'Local' )
        		->display_as( 'mp_comentario', 'Comentario' )
        ;
        
        $crud->set_lang_string('list_add', "+ Nueva Consignación");

		
		$crud	->callback_column( 'consignacion_id',		array($this,'consignacionColumn') )
				->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
				->callback_column( 'acciones',				array($this,'accionesColumn') )
				->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
				->callback_column( 'mp_comentario',	array($this,'comentarioMovimientosProductosColumn') )
		;

        $crud->unset_delete();
        if (!$this->local)		$crud->unset_add();
        else					$crud->add_url('consignacion/nueva');
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
        
        // Para el view
        $data['productosParaFiltro']	= $productosParaFiltro;
            
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu', $data );
		$this->load->view( 'consignacion' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function accionesColumn( $value, $row, $container=true )
	{
		$return = '';
	
		if ( $container )
			$return .= '<div class="acciones-indent" id="mov' . $row->id .'">';
	
		if (!$row->fechaVendido)
		{
			if (!$row->devolucion)
			{
				$return .= '| &nbsp;&nbsp;<a onclick="ajax_vendido(\'vendido\', ' . $row->id . ')" class="custom_action">Marcar como Vendido</a>';
				$return .= ' | <small><a href="' . base_url('index.php/consignacion/marcarDevolver/' . $row->id) . '">Devolver</a></small>';
			}
			else
			{
				$return .= '| &nbsp;&nbsp;<small><a href="' . base_url('index.php/consignacion/desmarcarDevolver/' . $row->id) . '">Cancelar Devolver</a></small>';
			}
		}
		else
		{
			$return .= '| &nbsp;&nbsp;<a onclick="ajax_vendido(\'desmarcarVendido\', ' . $row->id . ')" class="custom_action grey">Deshacer Vendido</a>';
		}

		if ( $container )
			$return .= '</div>';
		
		if ( $container )
		{
			return $return;
		}
		else
		{
	        $movimientosProductosModel = new MovimientosProductos_model();
			$recibosParaHacer	= $movimientosProductosModel->movimientosRecibosParaHacer($this->local);
	
			$json = array();

			$json['button']		= $return;
			
			if ( !count( $recibosParaHacer ) || !$this->local )
			{
				$json['recibos']	= '';
			}
			else
			{
				$json['recibos']	= '<a href="' . base_url('index.php/recibo/generarRecibo') . '" class="menu_alert_bubble">' . count( $recibosParaHacer ) . '</a>';
			}
				
			return json_encode( $json );
		}
	}
	
	public function consignacionColumn( $value, $row )
	{
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion ( $row->consignacion_id );
	
		return '<a href="' . base_url('index.php/consignacion/ver/' . $row->consignacion_id) . '">' . addZeros( $consignacion->facturaNumero ) . '</a> - <small>' . $consignacion->fechaManual . '</small>';
	}
	
	/**
	 * Marca el producto como venido.
	 */
	public function vendido($movimientoId)
	{
		$movimientosProductosModel = new MovimientosProductos_model();
		
		if ($movimientosProductosModel->marcarVendido($movimientoId, 'consignacion'))
		{
			echo $this->accionesColumn( $movimientoId, $movimientosProductosModel->getMovimiento($movimientoId), false );
		}
	}
	
	/**
	 * Desmarca el producto como vendido.
	 */
	public function desmarcarVendido($movimientoId)
	{
		$movimientosProductosModel = new MovimientosProductos_model();
		
		if ($movimientosProductosModel->desmarcarVendido($movimientoId))
		{
			echo $this->accionesColumn( $movimientoId, $movimientosProductosModel->getMovimiento($movimientoId), false );
		}
	}
	
	public function procesar( $consignacionId )
	{
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion($consignacionId);

		// Si no es nueva
		if ( $consignacion->status != 2 ) {
			flash_alert('La consignación no se encuentra en estado para procesar.');
		}
		else
		{
			$todosLosProductos = $consignacionModel->getProductosConsignacion( $consignacionId );
			
			$movimientosProductosModel	= new MovimientosProductos_model();
		
			// Validacion de que los productos están en stock.
			$stockOk					= true;

			// Chequeo de Stock.
			foreach($todosLosProductos as $key => $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $consignacion->deposito_id );
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
						$movimientosProductosModel->consignarMovimientos( $producto->producto_id, $consignacionId, $this->local, $producto->producto_precio, $consignacion->deposito_id );
					}
				}

				if ( !$consignacionModel->procesarRetiroConsignacion( $consignacionId ) )
				{
					flash_error('No se pudo actualizar el estado de la consignación.');
				}
			}
		}

		redirect(base_url( 'index.php/consignacion/ver/' . $consignacion->id ), 'location');
		die;
	}
	
	public function nueva()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/consignacion/listar/'), 'location');
			die;
		}
	
		$data = array();
		$data['menu_seleccionado']			= 'consignacion';
		$data['menu_header_seleccionado']	= 'consignacion';
		
		$data['showDetalle']		= false;
		$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : 0;

		// Datos necesiarios para el form.
		$productoModel				= new Producto_model();
		$movimientosProductosModel	= new MovimientosProductos_model();

		$data['productos'] = array();
		
		$productoModel->setPrecioWeb(false);
		$todosLosProductos = $productoModel->getProductosTodos(false, true, false);
		foreach($todosLosProductos as $key => $producto)
		{
			$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $data['selectedDeposito']);
			$movimientosProductosEnStock  = $movimientosConsignablesPorProducto[0];
		
			$disponiblesEnStock = $movimientosProductosEnStock->cantidad;
			
			if ( $disponiblesEnStock || $producto->status )
			{
				$data['productos'][$producto->id] = array(
											              'name'        => 'producto_' . $producto->id,
											              'id'			=> $producto->id,
											              'maxlength'   => '2',
											              'size'		=> '2',
											              'style'		=> 'text-align:center;',
											              'nombre'		=> $producto->nombre,
											              'precio'		=> $producto->precioVenta,
											              'class'		=> 'input_cantProd numeric',
											              'value'		=> ($this->input->post('producto_' . $producto->id)) ? $this->input->post('producto_' . $producto->id) : '',
											              'autocomplete'=> 'off'
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

		$data['detalle'] = array(
									'name'        	=> 'detalle',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);
		
		$data['form_open']		= array('onsubmit'	=> "checkProductos()");
		
		// Dropdown Deposito
		$depositoModel		= new Deposito_Model();
		$depositos			= $depositoModel->getDepositosTodos();
		$depositosOptions	= array(0 => '');
		foreach ($depositos as $deposito)
		{
			$depositosOptions[$deposito->id]		= $deposito->nombre;
		}
		$data['depositosOptions']	= $depositosOptions;

		// Validaciones
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',	'required');
		$this->form_validation->set_rules('deposito',			'Depósito',				'is_natural_no_zero');

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
			$stockOk					= true;
			$totalConsignacionCantidad	= 0;

			// Chequeo de Stock.
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
				$consignacionModel = new Consignacion_model();
				$consignacionId = $consignacionModel->nuevaConsignacion(
																		$this->local,
																		$this->input->post('facturaNumero'),
																		$this->input->post('fecha'),
																		$data['selectedDeposito'],
																		$this->input->post('detalle')
																		);
				
				// Si la consignación salió bien.
				if ($consignacionId)
				{
					$totalConsignacion		= 0;
					$cantidadConsignacion	= 0;
	
					$movimientosProductosModel	= new MovimientosProductos_model();
					$consignacionProductosModel	= new ConsignacionProductos_model();
					
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
	
								$totalConsignacion 		+= $producto->precioVenta;
								$cantidadConsignacion	+= 1;
							}
	
							$consignacionProductosModel->insertConsignacionProducto(
																						$consignacionId,
																						$producto->id,
																						$cantidad,
																						$producto->precioVenta
																					);	
						}
					}
					
					$consignacionModel->updateConsignacionTotal($consignacionId, $totalConsignacion, $cantidadConsignacion);
	
					redirect(base_url( 'index.php/consignacion/ver/' . $consignacionId), 'location');
				}
				else
				{
					flash_error('Mateo: Error! No se creó la consignación.');
				}
			}
		}

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu', $data );
		$this->load->view( 'consignacionNueva' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}

	public function ver($consignacionId, $imprimir=false)
	{
		$data = array();
		$data['menu_seleccionado']			= 'facturas';
		$data['menu_header_seleccionado']	= 'consignacion';
		$data['showDetalle']				= false;
		
		$stockOk = true;
		$movimientosProductosModel = new MovimientosProductos_model();
	
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion($consignacionId);
		
		// Local
		$localModel = new Local_model();
		$data['consignacionLocal'] = $localModel->getLocal( $consignacion->local );


		// Titulo
		$data['title']						= 'Campomar - Consignación ' . $consignacion->facturaNumero;


		$consignacionProductosModel = new ConsignacionProductos_model();
		$data['productosDeLaConsignacion'] = $consignacionProductosModel->getProductosPorConsignacion($consignacion->id);

		if ( $consignacion->status == 1 )
		{
			// La factura está procesada, los productos fueron retirados del stock.
			$crud = new grocery_CRUD();
	
			$this->db->select('*, COUNT(1) as cantidad, SUM(consignacionPrecio) as totalProducto, producto_id as producto_id_original' );
	
			$where = 'consignacion_id = ' . $consignacionId;		
			$crud->where($where);
	
	        $crud->set_table( 'movimientos_productos' );
	 
			$this->db->group_by(array(
										'producto_id'
										));
			
			$crud	->set_relation( 'producto_id', 'producto', 'nombre');
	
	        $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'custom', 'consignacionPrecio', 'totalProducto');
	
	        $crud	->display_as( 'producto_id','Producto' )
	        		->display_as( 'consignacionPrecio','Precio' )
	        		->display_as( 'custom','Estado' )
	        		->display_as( 'producto_id_original', '')
	        		->display_as( 'totalProducto','Importe' );
	        
	        $crud	->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
	        		->callback_column( 'totalProducto',			array($this,'currencyColumn') )
	        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
	        		->callback_column( 'custom',				array($this,'callback_estadoDelProducto') );
	    }
	    else
	    {
			/*********************************************
			/// La factura está anulada o no procesada ///
			*********************************************/

			// Form
			$data['facturaNumero']	= array(
											'name'      	=> 'facturaNumero',
											'value'			=> ( $this->input->post('facturaNumero') ) ? $this->input->post('facturaNumero') : ( ( $consignacion->facturaNumero ) ? $consignacion->facturaNumero : "" ),
											'autocomplete'	=> 'off'
			);

			// Detalle
			$data['detalle'] = array(
										'name'        	=> 'detalle',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : $consignacion->detalle,
			);

			// Dropdown Deposito
			$depositoModel		= new Deposito_Model();
			$depositos			= $depositoModel->getDepositosTodos();
			foreach ($depositos as $deposito)
			{
				$depositosOptions[$deposito->id]		= $deposito->nombre;
			}
			
			$data['depositosOptions']	= $depositosOptions;
			$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : $consignacion->deposito_id;


			// Validaciones
			$this->form_validation->set_rules('facturaNumero',		'Factura Número',		'required|numeric|is_unique_and_not_id[consignacion.facturaNumero.' . $consignacion->id . ']');
			$this->form_validation->set_rules('deposito',			'Depósito',				'is_natural_no_zero');
			

			// Detalle
			if ( $this->input->post('detalle') || $consignacion->detalle )
				$data['showDetalle']		= true;


			// Submit
			if ($this->form_validation->run() == FALSE)
			{
				// Not ok
			}
			else
			{
				$consignacionModel->updateConsignacionProcesar( $consignacionId, $this->input->post('facturaNumero'), $this->input->post('deposito'), $this->input->post('detalle') );

				redirect(base_url( 'index.php/consignacion/procesar/' . $consignacionId), 'location');
			}
			
			// Chequeo de Stock.
			$todosLosProductos = $consignacionModel->getProductosConsignacion( $consignacionId );
			foreach($todosLosProductos as $key => $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $consignacion->deposito_id );
				$movimientosConsignablesPorProducto = $movimientosConsignablesPorProducto[0];
				$movimientosProductosEnStock  = $movimientosConsignablesPorProducto->cantidad;

				if ( $producto->cantidad > $movimientosProductosEnStock )
				{
					$stockOk = false;
				}
			}
			
			$crud = new grocery_CRUD();
	
			$this->db->select('*, cantidad as cantidad_original');
	
	        $crud->set_table( 'consignacion_productos' );
	
			$crud->order_by('producto_id');
			
			$crud->where('consignacion_id = ' . $consignacionId );
			
			$crud->set_relation( 'producto_id', 'producto', 'nombre');
	
	        $crud->columns( 'producto_id', 'cantidad', 'subtotal');
	
	        $crud	->display_as( 'producto_id','Producto' )
	        		->display_as( 'cantidad','Cantidad' )
	        		->display_as( 'producto_precio','Precio' );
	        
	        $this->selectedDeposito = $consignacion->deposito_id;
	        
	        $crud->callback_column( 'subtotal',	array($this,'subtotalColumn') );
	        $crud->callback_column( 'cantidad',	array($this,'callback_cantidadStockColumn') );
	    }

        $crud->unset_delete();
        $crud->unset_add();
        $crud->unset_edit();


        // render crud
        $data['crud']				= $crud->render();
        $data['stockOk']			= $stockOk; 
        $data['consignacion']		= $consignacion;
        if ($imprimir)				$data['imprimir'] = true;


        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu', $data );
		$this->load->view( 'consignacion_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	protected function spare_email($str)
	{
	
	    // if first_item and second_item are equal
	    if(stristr($str, '@mywork.com') !== FALSE)
	    {
	    // success
	    return $str;
	    }
	    else
	    {
	    // set error message
	    $this->form_validation->set_message('spare_email', 'No match');
	
	    // return fail
	    return FALSE;
	    }
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
	
	public function callback_estadoDelProducto( $value, $row )
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		$reciboModel				= new recibo_model();
		$ncModel					= new nc_model();

		$movimientos = $movimientosProductosModel->getMovimientosPorConsignacion( $row->consignacion_id, $row->producto_id );
		
		$recibos			= array();
		$ncArray			= array();
		$productosSinRecibo = 0;
		$productosVendidos	= 0;
		
		foreach ($movimientos as $movimiento)
		{
			if ( isset( $recibos[$movimiento->recibo_id] ))
			{
				$recibos[$movimiento->recibo_id]['count']++;
			}
			else
			{
				if ( $movimiento->recibo_id )
				{		
					$recibo = $reciboModel->getRecibo( $movimiento->recibo_id );
					
					$recibos[$movimiento->recibo_id]['count']			= 1;
					$recibos[$movimiento->recibo_id]['reciboNumero']	= $recibo->reciboNumero;
				}
				else
				{
					if ( $movimiento->fechaVendido )
					{
						$productosVendidos++;
					}
					elseif ( $movimiento->nc_id )
					{
						if ( isset( $ncArray[$movimiento->nc_id] ) )
						{
							$ncArray[$movimiento->nc_id]['count']++;
						}
						else
						{
							$notadObj = $ncModel->getNc( $movimiento->nc_id );
							
							$ncArray[$movimiento->nc_id]['count']	= 1;
							$ncArray[$movimiento->nc_id]['ncNumero']	= $notadObj->ncNumero;
						}
					}
					else
					{
						$productosSinRecibo++;
					}
				}
			}
		}

		$toReturn = '';

		if ( $productosSinRecibo )
		{
			$toReturn .= '<small>x' . $productosSinRecibo . '</small><span class="textlabelConsignados" title="En Consignación">C</span><br /><br/>';
		}

		if ( $productosVendidos )
		{
			$toReturn .= '<small>x' . $productosVendidos . '</small><span class="textlabelVentasInformadas" title="Ventas Informadas">V</span><br /><br/>';
		}

		foreach ( $recibos as $reciboId => $recibo )
		{
			$toReturn .= '<small>x' . $recibo['count'] . '</small><span class="textlabelConRecibo" title="Vendidos">R<a href="' . base_url('index.php/recibo/verRecibo/') . '/' . $reciboId . '">' . addZeros( $recibo['reciboNumero'] ) . '</a></span><br /><br />';
		}

		foreach ( $ncArray as $ncId => $nc )
		{
			$toReturn .= '<small>x' . $nc['count'] . '</small><span class="textlabelNC" title="Nota de Crédito">NC<a href="' . base_url('index.php/nc/verNc/') . '/' . $ncId . '">' . addZeros( $nc['ncNumero'] ) . '</a></span><br /><br />';
		}
		
		return $toReturn;
	}

	public function subtotalColumn( $value, $row )
	{
		return '<div class="currency-right">' . currency_format( $row->cantidad_original * $row->producto_precio ) . '</div>';
	}
	
	public function listar()
	{
		$data = array();
		$data['menu_seleccionado']			= 'facturas';
		$data['menu_header_seleccionado']	= 'consignacion';
	
	    $crud = new grocery_CRUD();

		$where = '(facturaNumero <> 0 OR consignacion.status=2)';

		if ($this->local)
		{
			$where .= ' AND local = ' . $this->local;
		}
		
		$crud->where($where);


        $crud->set_table( 'consignacion' );

        $crud->set_relation( 'local', 'local', '{nombre}');

		$crud	->order_by('fechaManual', 'desc')
				->order_by('fechaAuto', 'desc');

        $crud->columns( 'facturaNumero', 'fechaManual', 'fechaProcesado', 'local', 'cantidad', 'total', 'iva', 'total_iva_inc');

        $crud	->display_as( 'facturaNumero','Factura Número' )
        		->display_as( 'fechaManual','Fecha' )
        		->display_as( 'local_id', 'Local' )
        		->display_as( 'total', 'Subtotal' )
        		->display_as( 'iva', 'IVA' )
        		->display_as( 'fechaProcesado', 'Fecha de Procesado')
        		->display_as( 'total_iva_inc', 'TOTAL' );
        
        $crud->set_lang_string('list_add', "Nueva Consignación");

		$crud	->callback_column( 'facturaNumero',	array($this,'facturaNumeroColumn') )
				->callback_column( 'total',			array($this,'currencyColumn') )
				->callback_column( 'iva',			array($this,'currencyColumn') )
				->callback_column( 'total_iva_inc',	array($this,'currencyColumn') )
				->callback_column( 'cantidad',		array($this,'callback_Cantidad') );

        $crud->unset_delete();
        if (!$this->local)
		{
        	$crud->unset_add();
        }
        else
        {
        	$crud->add_url(base_url('index.php/consignacion/nueva'));
        }
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
            
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu', $data );
		$this->load->view( 'consignacion_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function facturaNumeroColumn( $value, $row )
	{
		
		$facturaNumero = ( $row->facturaNumero ) ? addZeros( $row->facturaNumero ) : "s/n";
		
		$toReturn = '<a href="' . base_url('index.php/consignacion/ver/' . $row->id) . '">' . $facturaNumero . '</a>';
		
		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADA</span>';
		}
		
		return $toReturn;
	}
	
	public function callback_Cantidad( $value, $row )
	{
		if ( $row->status == 2 )
		{
			return '<span class="textlabelalert">SIN PROCESAR</span>';
		}
		elseif ( $row->status == 1 )
		{
			$toReturn = ' ';
	
			$movimientosProductosModel	= new MovimientosProductos_model();
			$movimientos = $movimientosProductosModel->getMovimientosPorConsignacion( $row->id );
			
			$productosSinRecibo = 0;
			$productosVendidos	= 0;
			$productosConRecibo	= 0;
			$productosConNc		= 0;
			
			foreach ($movimientos as $movimiento)
			{
				if ( !$movimiento->recibo_id )
				{
					if ( $movimiento->fechaVendido )
					{
						$productosVendidos++;
					}
					elseif ( $movimiento->nc_id )
					{
						$productosConNc++;
					}
					else
					{
						$productosSinRecibo++;
					}
				}
				else
				{
					$productosConRecibo++;
				}
			}
			
			if ( $productosSinRecibo )
			{
				$toReturn .= '<a href="' . base_url( 'index.php/consignacion/') . '" class="textlabelConsignados" title="Productos en Consignaci&oacute;n">' . $productosSinRecibo .'</a> +';
			}
	
			if ( $productosVendidos )
			{
				$toReturn .= '<a href="' . base_url( 'index.php/recibo/generarRecibo/') . '" class="textlabelVentasInformadas" title="Ventas Informadas">' . $productosVendidos .'</a> +';
			}
	
			if ( $productosConNc )
			{
				$toReturn .= '<a href="' . base_url( 'index.php/nc/') . '" class="textlabelNC" title="Nota de Crédito">' . $productosConNc .'</a> +';
			}
			
			if ( $productosConRecibo )
			{
				$toReturn .= '<span class="textlabelConRecibo" title="Vendidos">' . $productosConRecibo .'</span>';
			}
			else
			{
				$toReturn = substr( $toReturn, 0, -1);
			}
	
			$toReturn .= ' = <strong>' . $value . '</strong>';
			
			return $toReturn;
		}
		else
		{
			return '<span style="color:grey; text-decoration: line-through;">' . $value . '</span>';
		}
	}

	public function cambiarNumeroConsignacion($consignacionId, $nuevoNumeroConsignacion)
	{
		$consignacionModel = new Consignacion_model();
		
		// Chequeo a ver si ya hay una consignacion/factura con ese número.
		$consignacionConMismoNumero = $consignacionModel->getConsignacionByNumero($nuevoNumeroConsignacion);
		
		if ($consignacionConMismoNumero)
		{
			flash_alert('Ya hay una consignación con ese número.');
		}
		else
		{
			$consignacionModel->editConsignacionNumero($consignacionId, $nuevoNumeroConsignacion);
		}
		
		redirect(base_url( 'index.php/consignacion/ver/' . $consignacionId), 'refresh');
	}

	public function anularConsignacion($consignacionId)
	{
		$consignacionModel = new Consignacion_model();

		if ( $consignacionModel->esAnulable($consignacionId) )
		{
			if ($consignacionModel->anularConsignacion($consignacionId))
			{
			}
		}
		else
		{
			flash_alert('La factura no se puede anular porque hay productos vendidos.');
		}

		redirect(base_url( 'index.php/consignacion/ver/' . $consignacionId), 'refresh');
	}

	public function marcarDevolver($movimientoId)
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		if ($movimientosProductosModel->marcarDevolver($movimientoId))
		{
			redirect(base_url( 'index.php/consignacion'), 'refresh');
		}
	}

	public function desmarcarDevolver($movimientoId)
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		if ($movimientosProductosModel->desmarcarDevolver($movimientoId))
		{
			redirect(base_url( 'index.php/consignacion'), 'refresh');
		}
	}

	public function setFiltrosParaConsignacion()
	{
		$this->set_filtros( 'consignacion/index', $this->uri->uri_to_assoc() );
		
		redirect(base_url( 'index.php/consignacion/index' ), 'location');
	}
	
	public function facturaOficial( $consignacionId )
	{
		$data = array();

		// Models
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion($consignacionId);
		
		$localModel = new Local_model();
		$local = $localModel->getLocal( $consignacion->local );

		$consignacionProductosModel = new ConsignacionProductos_model();
		$consignacionProductos = $consignacionProductosModel->getProductosPorConsignacion( $consignacion->id );


		// Library
		$this->load->library('fpdf');
		$pdf	= new FPDF();


		// View
		$data['pdf']					= $pdf;
		$data['consignacion']			= $consignacion;
		$data['local']					= $local;
		$data['consignacionProductos']	= $consignacionProductos;

		$this->load->view( 'pdf/consignacion/factura-oficial' , $data );
	}
}