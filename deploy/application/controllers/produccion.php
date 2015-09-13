<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Produccion extends My_Controller {

	public function index()
	{	
		$data = array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'produccion';

		$crud = new grocery_CRUD();
		
		$this->db->select('*, productoDestino_id as productoDestino_id_original');

        $crud->set_table( 'orden_produccion' );

		$crud->order_by('fechaManual', 'desc');

		$crud->set_relation( 'productoDestino_id', 'producto', 'nombre');

        $crud->columns( 'id', 'detalle', 'productoDestino_id_original', 'productoDestino_id', 'cantidad', 'fechaManual');

        $crud	->display_as( 'id','Número de Producción' )
        		->display_as( 'fechaManual', 'Fecha')
        		->display_as( 'productoDestino_id', 'Producto Producido' )
        		->display_as( 'cantidad', 'Cantidad de Unidades' )
        		->display_as( 'productoDestino_id_original', '');
        		
        $crud->set_lang_string('list_add', "+&nbsp;&nbsp;Nueva Producción");
        
        $crud	->callback_column( 'id',							array($this,'idColumn') )
        		->callback_column( 'productoDestino_id_original',	array($this,'producto_thumbnail') )
        		->callback_column( 'cantidad',						array($this,'cantidadColumn') );

        $crud->unset_delete();
        $crud->add_url('produccion/nuevaOP');
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'produccion_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function idColumn( $value, $row )
	{
		$toReturn = '<a href="' . base_url('index.php/produccion/verOP/' . $row->id) . '">' . addZeros( $row->id ) . '</a>';
	
		return $toReturn;
	}

	public function nuevaOP()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'produccion';
		
		$data['selectedDeposito']	= $this->input->post('deposito') ? $this->input->post('deposito') : 0;

		// Dropdown Productos Destino
		$produccionModel	= new Produccion_Model();
		$productos			= $produccionModel->getProductosDestino();
		$productosArray		= array(0 => 'seleccioná');
		
		$data['selectedProducto'] = $this->input->post('productoDestino') ? $this->input->post('productoDestino') : 0;
		
		foreach ($productos as $producto)
		{
			$productosArray[$producto->id]		= $producto->nombre;
		}
		$data['productosArray']	= $productosArray;

		$data['fecha'] = array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'autocomplete'	=> 'off',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
		);

		$data['detalle'] = array(
									'name'        	=> 'detalle',
									'autocomplete'	=> 'off',
									'value'			=> ($this->input->post('detalle')) ? $this->input->post('detalle') : '',
		);

		// Cantidad
		$data['cantidad'] 			= array(
										'name'      	=> 'cantidad',
										'value'			=> $this->input->post('cantidad'),
										'autocomplete'	=> 'off',
										'class'			=> 'numeric',
										'maxlength'		=> '2',
										'size'			=> '2',
									);
		
		// Dropdown Deposito
		$depositoModel		= new Deposito_Model();
		$depositos			= $depositoModel->getDepositosTodos();
		$depositosOptions	= array(0 => 'seleccioná');
		foreach ($depositos as $deposito)
		{
			$depositosOptions[$deposito->id]		= $deposito->nombre;
		}
		$data['depositosOptions']	= $depositosOptions;


		$this->form_validation->set_rules('productoDestino',	'Producto Destino',		'required|is_natural_no_zero');
		$this->form_validation->set_rules('cantidad',			'Cantidad',				'required|is_natural_no_zero');
		$this->form_validation->set_rules('deposito',			'Depósito',				'required|is_natural_no_zero');

		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			$cantidad			= $this->input->post('cantidad');
			$productoDestinoId	= $this->input->post('productoDestino');
			$detalle			= $this->input->post('detalle');
			$fechaManual		= $this->input->post('fecha');

			$movimientosProductosModel	= new MovimientosProductos_model();
			$productosStock				= $produccionModel->productosOrigenPorProductoDestino($productoDestinoId);

			$validaStock = true;
			foreach($productosStock as $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $data['selectedDeposito']);
				$movimientosProductosEnStock  = $movimientosConsignablesPorProducto[0];
			
				$disponiblesEnStock = $movimientosProductosEnStock->cantidad;

				if ( $disponiblesEnStock < $cantidad * $producto->cantidadOrigen )
				{
					$validaStock = false;
				}
			}
			
			if (!$validaStock)
			{
				flash_alert('Falta stock para al menos un producto.');
			}
			else
			{
				if ( $produccionModel->producir($productoDestinoId, $cantidad, $data['selectedDeposito']) )
				{
					$opModel			= new Orden_produccion_model();
					$opProductosModel	= new Orden_produccionProductos_model();
					
					$opId = $opModel->nuevaOP($fechaManual, $detalle, $cantidad, $productoDestinoId, $data['selectedDeposito']);
					
					if ( $opId )
					{
						foreach($productosStock as $producto)
						{
							$opProductosModel->insertOPProducto($opId, $producto->id, $cantidad, 'origen');
						}
						
						$opProductosModel->insertOPProducto($opId, $productoDestinoId, $cantidad, 'destino');
								
						redirect(base_url( 'index.php/produccion/verOP/' . $opId), 'location');
					}
				}
				else flash_error('Error al producir');
			}
		}

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'produccion_nueva' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function mostrarProductosOrigen($productoDestinoId, $depositoId)
	{
		$data = array();
		
		$produccionModel	= new Produccion_Model();
		$productos			= $produccionModel->productosOrigenPorProductoDestino($productoDestinoId);
		
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		$productosArray = array();
		foreach($productos as $producto)
		{
			$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $depositoId);
			$movimientosProductosEnStock  = $movimientosConsignablesPorProducto[0];
		
			$disponiblesEnStock = $movimientosProductosEnStock->cantidad;
		
			$productosArray[$producto->productoOrigen]	= array(
																'id'				=> $producto->id,
																'nombre'			=> $producto->nombre,
																'cantidadOrigen'	=> $producto->cantidadOrigen,
																'precio'			=> $producto->precio,
																'enstock'			=> $disponiblesEnStock
															);
		}
		
		$data['productosArray']	= $productosArray;
	
		$this->load->view( 'produccion_mostrarProductosOrigen' , $data );
	}

	public function verOP( $opId )
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'produccion';
	
		$opModel = new orden_produccion_model();
		
		$op = $opModel->getOP( $opId );
		$data['op'] = $op;

		//$compraProductosModel = new CompraProductos_model();
		//$data['productosDeLaCompra'] = $compraProductosModel->getProductosPorCompra($compra->id);

		// Grocery Crud
		$crud = new grocery_CRUD();
		
		$this->db->select('*, producto_id as producto_id_original');

        $crud->set_table( 'orden_produccion_productos' );
        
        $where = 'orden_produccion_id = ' . $opId;
        $crud->where($where);

		$crud->order_by('destinoOrigen');
		
		$crud->set_relation( 'producto_id', 'producto', 'nombre');

        $crud->columns( 'producto_id_original', 'producto_id', 'cantidad', 'destinoOrigen');

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'producto_id_original','' )
        		->display_as( 'destinoOrigen','Materia Prima / Producto Final' );
        
        $crud	->callback_column( 'destinoOrigen',			array($this,'arrowCol') )
        		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
        		->callback_column( 'cantidad',				array($this,'cantidadColumn') );

        $crud->unset_delete();
        $crud->unset_add();
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'produccion_ver' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function arrowCol( $value, $row )
	{
		$toReturn = '<div style="text-align:center">';
		
		if ( $value == 'destino' )
		{
			$toReturn .= '<img src="' . base_url('assets/img/arrow_green_right.png') . '"/>';
		}
		else
		{
			$toReturn .= '<img src="' . base_url('assets/img/arrow_red_left.png') . '"/>';
		}
		
		$toReturn .= '</div>';
	
		return $toReturn;
	}
}