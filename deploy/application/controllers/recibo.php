<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recibo extends My_Controller {

	public function index()
	{	
		$data = array();
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'recibo';

		$crud = new grocery_CRUD();

        $crud->set_table( 'recibo' );
        
        // where
        if ($this->local)
        	$crud->where('local_id', $this->local);

		$crud->order_by('reciboNumero');
		
		$crud->set_relation( 'local_id', 'local', '{nombre}');

        $crud->columns( 'reciboNumero', 'local_id', 'detalle', 'fechaManual', 'fechaAuto', 'fechaCobro', 'total');

        $crud	->display_as( 'reciboNumero','Recibo Número' )
        		->display_as( 'fechaManual','Fecha' )
        		->display_as( 'local_id', 'Local')
        		->display_as( 'fechaCobro', 'Fecha de Cobrado' )
        		->display_as( 'fechaAuto', 'Fecha de Creación');
        
        $crud	->callback_column( 'reciboNumero',		array($this,'reciboNumeroColumn') )
				->callback_column( 'total',				array($this,'currencyColumn') );
        
        $crud->set_lang_string('list_add', "+ Nuevo Recibo");

        $crud->unset_delete();
		if (!$this->local)
		{
        	$crud->unset_add();
        }
        else
        {
        	$crud->add_url('recibo/generarRecibo');
        }
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'recibo_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function reciboNumeroColumn( $value, $row )
	{
		$toReturn = '<a href="' . base_url('index.php/recibo/verRecibo/' . $row->id) . '">' . addZeros( $row->reciboNumero ) . '</a>';

		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADO</span>';
		}
		elseif ( $row->status == 1 )
		{
			$toReturn .= '<span class="textlabelalert">SIN COBRAR</span>';
		}
		
		return $toReturn;
	}
	
	public function verRecibo($idRecibo)
	{
												$data								= array();
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
												$data['menu_seleccionado']			= 'recibo';
	
		$reciboModel = new recibo_model();
		
		$recibo = $reciboModel->getRecibo($idRecibo);
		$data['recibo'] = $recibo;

		// Local
		$localModel = new Local_model();
		$data['reciboLocal'] = $localModel->getLocal( $recibo->local_id );

		$reciboProductosModel = new ReciboProductos_model();
		$data['productosDelRecibo'] = $reciboProductosModel->getProductosPorRecibo($recibo->id);
		$totalCantidadProductos		= 0;

		// Grocery Crud
		if ( count($data['productosDelRecibo']) )
		{
			foreach ( $data['productosDelRecibo'] as $producto )
			{
				$totalCantidadProductos += $producto->cantidad;
			}
			$data['totalCantidadProductos'] =	$totalCantidadProductos;
		
			$crud = new grocery_CRUD();

			if ( !$recibo->status )
			{
				$this->db->select('*, producto_precio as producto_precio_original');
		
		        $crud->set_table( 'recibo_productos' );
		        
		        $crud->where('recibo_id = ' . $idRecibo);
		
				$crud->order_by('producto_id');
				
				$crud->set_relation( 'producto_id', 'producto', 'nombre');
		
		        $crud->columns( 'producto_id', 'cantidad', 'producto_precio', 'importe');
		        
		        $crud	->callback_column( 'importe',				array($this,'callback_importe') )
		        		->callback_column( 'producto_precio',		array($this,'currencyColumn') );
		
		        $crud	->display_as( 'producto_id','Producto' )
		        		->display_as( 'producto_precio','Precio' );
		    }
		    else
		    {
    			// La factura está activa
				$crud = new grocery_CRUD();
		
				$this->db->select('*, COUNT(1) as cantidad, SUM(vendidoPrecio) as totalProducto, producto_id as producto_id_original' );
		
				$where = 'recibo_id = ' . $recibo->id;		
				$crud->where($where);
		
		        $crud->set_table( 'movimientos_productos' );
		 
				$this->db->group_by(array(
											'producto_id',
											'vendidoPrecio'
											));
				
				$crud	->set_relation( 'producto_id', 'producto', 'nombre');
		
		        $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'custom', 'vendidoPrecio', 'totalProducto');
		
		        $crud	->display_as( 'producto_id','Producto' )
		        		->display_as( 'vendidoPrecio','Precio' )
		        		->display_as( 'custom','Factura / Consignación' )
		        		->display_as( 'producto_id_original','' )
		        		->display_as( 'totalProducto','Importe' );
		        
		        $crud	->callback_column( 'vendidoPrecio',		array($this,'currencyColumn') )
		        		->callback_column( 'totalProducto',		array($this,'currencyColumn') )
		        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
		        		->callback_column( 'cantidad',			array($this,'cantidadColumn') )
		        		->callback_column( 'custom',			array($this,'callback_factura') );
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
		$this->load->view( 'recibo_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function callback_importe( $value, $row )
	{
		return '<div class="currency-right">' . currency_format( $row->cantidad * $row->producto_precio_original ) . '</div>';
	}
	
	public function callback_factura( $value, $row )
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		$movimientos = $movimientosProductosModel->getMovimientosPorRecibo( $row->recibo_id, false, $row->producto_id );
		
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

	public function generarRecibo()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/recibo'), 'refresh');
			die;
		}
	
		$data = array();
		if ($this->modo == 'consignacion')		$data['menu_header_seleccionado']	= 'consignacion';
		else									$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado'] 			= 'recibo';
		
		$data['showDetalle']		= true;
		
		if ( !$this->input->post('detalle') )
			$data['showDetalle']		= false;

	
	    $movimientosProductosModel = new MovimientosProductos_model();
		$data['paraHacerRecibo'] = $movimientosProductosModel->movimientosRecibosParaHacer($this->local);

		$sumaRecibosParaHacer = $movimientosProductosModel->sumaRecibosParaHacer( $data['paraHacerRecibo'] );

		if (count($data['paraHacerRecibo']))
		{
			$data['totalRecibos']		= $sumaRecibosParaHacer['totalRecibos'];
			$data['cantidadRecibos']	= $sumaRecibosParaHacer['cantidadRecibos'];
		}
	
		// Form
		$data['fecha'] = array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'autocomplete'	=> 'off',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
		);

		$data['reciboNumero'] = array(
										'name'        	=> 'reciboNumero',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('reciboNumero')) ? $this->input->post('reciboNumero') : '',
										'class'			=> 'numeric'
		);

		$data['detalle'] = array(
									'name'        	=> 'detalle',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);

		if (!count($data['paraHacerRecibo']))
		{

			$data['total'] = array(
										'name'        	=> 'total',
										'autocomplete'	=> 'off',
										'value'			=> ($this->input->post('total')) ? $this->input->post('total') : '',
										'class'			=> 'numeric'
			);
		}

		// Validaciones
		$this->form_validation->set_rules('reciboNumero',		'Recibo Número',		'required|is_unique[recibo.reciboNumero]');
		$this->form_validation->set_rules('fecha',				'Fecha del Recibo',		'required');
		if (!count($data['paraHacerRecibo']))
		{
			$this->form_validation->set_rules('detalle',		'Detalle',		'required');
			$this->form_validation->set_rules('total',			'Total',		'required');
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
			// Totales
			if (count($data['paraHacerRecibo']))
			{
				$detalle	= '';
				$total		= $data['totalRecibos'];
			}
			else
			{
				$total		= $this->input->post('total');
			}
			
			$detalle	= $this->input->post('detalle');
		
			// Generar Recibo
			$reciboModel = new recibo_model();
			$reciboId = $reciboModel->nuevoRecibo(
													$this->local,
													$this->input->post('reciboNumero'),
													datepicker_to_mysql($this->input->post('fecha')),
													$total,
													$detalle
												);

			// Movimientos relacionados con el recibo.
			if ($reciboId)
			{
				if (count($data['paraHacerRecibo']))
				{
					$movimientosProductosModel = new MovimientosProductos_model();
					$reciboProductosModel = new ReciboProductos_model();
					
					$reciboArray = array();
					
					foreach($data['paraHacerRecibo'] as $movimiento)
					{
						$movimientosProductosModel->updateRecibo($movimiento->id, $reciboId);
						
						if (isset($reciboArray[$movimiento->producto_id]))
						{
							$reciboArray[$movimiento->producto_id]['cantidad']		+= 1;
						}
						else
						{
							$reciboArray[$movimiento->producto_id]['cantidad']		= 1;
							$reciboArray[$movimiento->producto_id]['vendidoPrecio'] = $movimiento->vendidoPrecio;
						}
					}
					
					foreach($reciboArray as $productoId => $registroRecibo)
					{
						$reciboProductosModel->insertReciboProducto(
													$reciboId,
													$productoId,
													$registroRecibo['cantidad'],
													$registroRecibo['vendidoPrecio']
												);
					}
				}

				if ($this->modo == 'consignacion')
					redirect(base_url( 'index.php/recibo/verRecibo/' . $reciboId), 'location');
				else
					redirect(base_url( 'index.php/recibo/' ), 'location');

			}
		}

		if (count($data['paraHacerRecibo']))
		{

		    $crud = new grocery_CRUD();
	
			$this->db->select('*, SUM(vendidoPrecio) as totalFactura' );
	
	        $crud->set_table( 'movimientos_productos' );
	        
	        // [TODO] Se podría traer el where desde el modelo así si cambia no hay que modificar dos cosas. La lógica se repite bastante parecida en MovimientosProductos_model->getMovimientosVendidosSinReciboPorFactura()
	        $where = "fechaVendido IS NOT NULL AND recibo_id IS NULL AND local_id = " . $this->local;
	        $crud->where($where);
	
	        $crud->set_relation( 'consignacion_id', 'consignacion', '{facturaNumero}');
	        
	        $this->db->group_by("consignacion_id"); 
	
			$crud->order_by('producto_id');
	
	        $crud->columns( 'facturaNumero', 'custom', 'totalFactura');
	        
	        $crud	->callback_column( 'totalFactura',		array($this,'masIvaColumn') )
	        		->callback_column( 'facturaNumero',		array($this,'linkAFactura') )
	        		->callback_column( 'custom',			array($this,'detalleDeLosProductos') );
	        
	        $crud	->display_as( 'consignacion_id','Factura' )
	        		->display_as( 'custom','Detalle de los productos' )
	        		->display_as( 'totalFactura','Debitar de la Factura' );
	
	        $crud->unset_delete();
	        $crud->unset_add();
	        $crud->unset_edit();
	
	        // render crud
	        $data['crud'] = $crud->render();
	    }

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'consignacion')		$this->load->view( 'templates/menu', $data );
        else									$this->load->view( 'templates/menu_proveedores', $data );
		$this->load->view( 'generarRecibo' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}
	
	public function detalleDeLosProductos( $value, $row )
	{
		$movimientosProductosModel = new MovimientosProductos_model();
		$productos = $movimientosProductosModel->getMovimientosVendidosSinReciboPorFactura( $row->consignacion_id );
		
		$return = '<span style="font-size:9pt;">';
		
		foreach ($productos as $producto)
		{
			$return .= $producto->nombre . '&nbsp;&nbsp;&nbsp;<span style="color:#999;font-size:7pt;">$' . currency_format( $producto->vendidoPrecio ) . ' + iva</span><br/>';
		}
		
		$return .= '</span>';
		
		return $return;
	}

	public function linkAFactura( $value, $row )
	{
		return '<a href="' . base_url('index.php/consignacion/ver/' . $row->consignacion_id) . '">' . addZeros( $value ) . '</a>';
	}
	
	public function cambiarNumeroRecibo($reciboId, $nuevoNumeroRecibo=false)
	{
		if (!$nuevoNumeroRecibo)
		{
			flash_alert('Ingresá un número de recibo nuevo.');
		}
		else
		{
			$reciboModel = new Recibo_model();
			
			// Chequeo a ver si ya hay un recibo con ese número
			$reciboConMismoNumero = $reciboModel->getReciboByNumero($nuevoNumeroRecibo);
			
			if ($reciboConMismoNumero)
			{
				flash_alert('Ya hay un recibo con ese número.');
			}
			else
			{
				$reciboModel->editReciboNumero($reciboId, $nuevoNumeroRecibo);
			}
		}
		
		redirect(base_url( 'index.php/recibo/verRecibo/' . $reciboId), 'refresh');
	}

	public function anularRecibo($reciboId)
	{
		$reciboModel = new Recibo_model();
		if ($reciboModel->anularRecibo($reciboId))
		{
			redirect(base_url( 'index.php/recibo/verRecibo/' . $reciboId), 'refresh');
		}
	}

	public function cobrarRecibo($reciboId)
	{
		$reciboModel = new Recibo_model();
		if ($reciboModel->cobrarRecibo($reciboId))
		{
			redirect(base_url( 'index.php/recibo/verRecibo/' . $reciboId), 'refresh');
		}
	}
}