<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Traslado extends My_Controller {

	public function index()
	{
		$data	= array();

		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'traslado';

		$crud = new grocery_CRUD();

        $crud->set_table( 'traslado' );

        $crud	->set_relation( 'depositoOrigen', 'deposito', '{nombre}')
        		->set_relation( 'depositoDestino', 'deposito', '{nombre}')
        ;

		$this->db->order_by('id desc');

        $crud->columns( 'id', 'fechaManual', 'depositoOrigen', 'depositoDestino', 'cantidad', 'detalle');

        $crud	->display_as( 'fechaManual',		'Fecha' )
       			->display_as( 'id',					'#' )
        		->display_as( 'depositoOrigen',		'Depósito de Origen' )
        		->display_as( 'depositoDestino',	'Depósito de Destino')
        ;

		$crud	->callback_column( 'id',			array($this,'trasladoIdColumn') )
				->callback_column( 'cantidad',		array($this,'cantidadColumn') )
		;

		$crud->set_lang_string('list_add', "+ Nuevo Traslado");

        $crud->unset_delete();
        $crud->add_url('traslado/nuevo');
        $crud->unset_edit();

        // Render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'traslado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function trasladoIdColumn( $value, $row )
	{
		return '<a href="' . base_url( 'index.php/traslado/ver/' . $value ) . '">' . addZeros( $value ) . '</a>';
	}

	public function nuevo()
	{
		$data = array();

		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'traslado';
		
		$selectedDepositoOrigen		= $this->input->post('deposito_origen') ? $this->input->post('deposito_origen') : 0;
		$selectedDepositoDestino	= $this->input->post('deposito_destino') ? $this->input->post('deposito_destino') : 0;

		$productoModel				= new Producto_model();
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		$productosEnStockArray = array();
		
		$productoModel->setPrecioWeb(false);
		$todosLosProductos = $productoModel->getProductosTodos();

		$data['productos'] = array();
		foreach($todosLosProductos as $key => $producto)
		{
			$movimientosEnStockPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $selectedDepositoOrigen );
			$movimientosProductosEnStock  = $movimientosEnStockPorProducto[0];
		
			$disponiblesEnStock = $movimientosProductosEnStock->cantidad;
			
			$productosEnStockArray[$producto->id] = $disponiblesEnStock;
			
			if ( $disponiblesEnStock || $producto->status )
			{
				$data['productos'][$producto->id] = array(
														'nombre'	=> $producto->nombre,
														'id'		=> $producto->id,
														'cantidad'	=> array(
																              'name'        => 'producto_' . $producto->id,
																              'maxlength'   => '3',
																              'size'		=> '2',
																              'style'		=> 'text-align:center;',
																              'autocomplete'=> 'off',
																              'class'		=> 'input_cantProd numeric',
																              'value'		=> ($this->input->post('producto_' . $producto->id)) ? $this->input->post('producto_' . $producto->id) : '',
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

		// Validaciones
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',	'required');
		$this->form_validation->set_rules('deposito_origen',	'Depósito de Origen',	'required|is_natural_no_zero');
		$this->form_validation->set_rules('deposito_destino',	'Depósito de Destino',	'required|is_natural_no_zero');

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			// Validacion de que los productos están en stock.
			$stockOk		= true;
			$mismoDeposito	= false;
			$cantidadTotal	= 0;

			// Chequeo de stock.
			foreach($todosLosProductos as $key => $producto)
			{
				$cantidadProducto		= $this->input->post('producto_' . $producto->id);
				$productosEnStock		= $productosEnStockArray[$producto->id];
				
				$cantidadTotal += $cantidadProducto;

				if ( $cantidadProducto > $productosEnStock )
				{
					flash_alert('Hay por lo menos un producto que supera el stock.');
					$stockOk = false;
				}	
			}

			// Chequea de que por lo menos se haya seleccionado 1 producto.
			if ( !$cantidadTotal )
			{
				flash_alert('Seleccioná la cantidad de al menos un producto.');
			}

			// Chequea de que no sea el mismo depósito.
			if ( $this->input->post('deposito_origen') == $this->input->post('deposito_destino') )
			{
				flash_alert('No son posibles traslados dentro del mismo depósito.');
				$mismoDeposito = true;
			}

			if ( $stockOk && $cantidadTotal && !$mismoDeposito )
			{

				$trasladoModel = new Traslado_model();
				$trasladoId = $trasladoModel->nuevoTraslado(
														$this->input->post('fecha'),
														$this->input->post('deposito_origen'),
														$this->input->post('deposito_destino'),
														$cantidadTotal,
														$this->input->post('detalle')
													);

				if ( $trasladoId )
				{
					$cantidadTraslado	= 0;
	
					$movimientosProductosModel	= new MovimientosProductos_model();
					$trasladoProductoModel		= new Traslado_Producto_model();
					
					// Para cada uno de los productos
					foreach($todosLosProductos as $key => $producto)
					{
						$cantidad			= 0;
						$movimientosArray	= array();

						// Si la cantidad es > 1
						if ($this->input->post('producto_' . $producto->id))
						{
							// Un registro por producto.
							for($i=1; $i<=$this->input->post('producto_' . $producto->id); $i++)
							{
								$movimientosArray[] = $movimientosProductosModel->trasladarMovimientos($producto->id, $selectedDepositoOrigen, $selectedDepositoDestino );

								$cantidad			+= 1;
								$cantidadTraslado	+= 1;
							}

							$trasladoProductoModel->insertProducto(
																	$trasladoId,
																	$producto->id,
																	$cantidad,
																	implode(",", $movimientosArray )
																);
						}
					}
	
					redirect(base_url( 'index.php/traslado/ver/' . $trasladoId), 'location');
				}
			}
		}
		
		$data['selectedDepositoOrigen']		= $selectedDepositoOrigen;
		$data['selectedDepositoDestino']	= $selectedDepositoDestino;

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_oficina', $data );
		$this->load->view( 'traslado_nuevo' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function ver( $trasladoId )
	{
	
		$data	=	array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'traslado';

		$trasladoModel = new Traslado_model();
		$traslado = $trasladoModel->getTraslado( $trasladoId );

		// Grocery Crud
		$crud = new grocery_CRUD();

        $crud	->set_table( 'traslado_productos' );
        
        $this	->db->select('*, producto_id as producto_id_original');
        
        $where = 'traslado_id = ' . $trasladoId;
        $crud	->where($where);

		$crud	->order_by('producto_id');
		
		$crud	->set_relation( 'producto_id', 'producto', 'nombre');

        $crud	->columns( 'producto_id_original', 'producto_id', 'cantidad' );

        $crud	->callback_column( 'cantidad',				array($this,'cantidadColumn') )
		 		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
		;

        $crud	->display_as( 'producto_id',			'Producto' )
        		->display_as( 'producto_id_original',	'')
        ;

        $crud	->unset_delete()
        		->unset_add()
        		->unset_edit()
        		->unset_export()
				->unset_print()
        ;

        // Render crud
        $data['crud'] = $crud->render();
        
        // Variables al view
        $data['traslado']	= $traslado;

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'traslado_ver' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}
}