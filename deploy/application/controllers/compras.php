<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Compras extends My_Controller {

	public function index()
	{	
		$data = array();
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'compras';

		$crud = new grocery_CRUD();

		$where = '';
		if ($this->local)
		{
			$where .= 'local_id = ' . $this->local;
			$crud->where($where);
		}

        $crud->set_table( 'compra' );

		$crud->order_by('id', 'desc');
		
		$crud->set_relation( 'local_id', 'local', '{nombre}');

        $crud->columns( 'id', 'local_id', 'facturaNumero', 'fechaManual', 'cantidad', 'total', 'iva', 'total_iva_inc');

        $crud	->display_as( 'id','Número de Compra' )
        		->display_as( 'fechaManual', 'Fecha de Compra')
        		->display_as( 'total', 'Subtotal' )
        		->display_as( 'iva', 'IVA' )
        		->display_as( 'total_iva_inc', 'TOTAL' )
        		->display_as( 'local_id', 'Local' );
        		
        $crud->set_lang_string('list_add', "+&nbsp;&nbsp;Nueva Compra");
        
        $crud	->callback_column( 'id',			array($this,'idColumn') )
				->callback_column( 'total',			array($this,'currencyColumn') )
				->callback_column( 'iva',			array($this,'currencyColumn') )
				->callback_column( 'cantidad',		array($this,'cantidadColumn') )
				->callback_column( 'total_iva_inc',	array($this,'currencyColumn') );

        $crud->unset_delete();
        if (!$this->local)
		{
        	$crud->unset_add();
        }
        else
        {
        	$crud->add_url('compras/nuevaCompra');
        }
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_proveedores', $data);
		$this->load->view( 'compras_listado' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function idColumn( $value, $row )
	{
		$toReturn = '<a href="' . base_url('index.php/compras/verCompra/' . $row->id) . '">' . addZeros( $row->id ) . '</a>';
		
		if ($row->status == 0)
		{
			$toReturn .= '<span class="anulada">ANULADA</span>';
		}
	
		return $toReturn;
	}
	
	public function nuevaCompra()
	{
		if (!$this->local)
		{
			redirect(base_url( 'index.php/nc'), 'location');
			die;
		}

		$data = array();
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'compras';

		// Dropdown Deposito
		$depositoModel		= new Deposito_Model();
		$depositos			= $depositoModel->getDepositosTodos();
		$depositosOptions	= array(0 => '');
		$data['selectedDeposito'] = $this->input->post('deposito') ? $this->input->post('deposito') : 0;
		foreach ($depositos as $deposito)
		{
			$depositosOptions[$deposito->id]		= $deposito->nombre;
		}
		$data['depositosOptions']	= $depositosOptions;


		$data['showDetalle']		= false;

		// Datos necesiarios para el form.
		$productoModel = new Producto_model();
		$productoModel->setStatus(1);

		$todosLosProductos = $productoModel->getProductosTodos(true, false, false, $this->local);
		$data['productos'] = array();
		foreach($todosLosProductos as $key => $producto)
		{
			$data['productos'][$producto->id] = array(
										              'name'        => 'producto_' . $producto->id,
										              'id'			=> $producto->id,
										              'maxlength'   => '3',
										              'size'		=> '2',
										              'style'		=> 'text-align:center;',
										              'nombre'		=> $producto->nombre,
										              'precio'		=> $producto->precio,
										              'class'		=> 'input_cantProd numeric',
										              'autocomplete'=> 'off',
										              'value'		=> ($this->input->post('producto_' . $producto->id)) ? $this->input->post('producto_' . $producto->id) : '',
												);
		}

		$data['fecha'] 			= array(
										'name'      	=> 'fecha',
										'id'			=> 'datepicker',
										'value'			=> ( $this->input->post('fecha') ) ? $this->input->post('fecha') : date( 'm/d/Y' ),
										'autocomplete'	=> 'off'
		);

		$data['factura_numero'] = array(
										'name'      	=> 'factura_numero',
										'value'			=> $this->input->post('factura_numero'),
										'autocomplete'	=> 'off',
										'size'			=> 10,
										'maxlength'		=> 10,
										'class'			=> 'numeric'
		);

		$data['detalle'] = array(
										'name'        	=> 'detalle',
										'autocomplete'	=> 'off',
										'value'			=> ( $this->input->post('detalle') ) ? $this->input->post('detalle') : '',
		);
		
		$data['form_open']		= array('onsubmit'	=> "checkProductos()");

		// Validaciones
		$this->form_validation->set_rules('fecha',				'Fecha del Factura',		'required');
		$this->form_validation->set_rules('factura_numero',		'Factura Porveedor Número',	'required');
		$this->form_validation->set_rules('deposito',			'Depósito',					'is_natural_no_zero');

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
			$totalCompraCantidad	= 0;

			foreach($todosLosProductos as $key => $producto)
			{
				$nuevaCompraCantidad	= $this->input->post('producto_' . $producto->id);
				
				$totalCompraCantidad += $nuevaCompraCantidad;
			}

			if (!$totalCompraCantidad)
			{
				flash_alert('Seleccioná la cantidad de al menos un producto.');
			}

			if ($totalCompraCantidad)
			{
				$compraModel = new Compra_model();
				$compraId = $compraModel->nuevaCompra(
														$this->input->post('fecha'),
														$this->input->post('factura_numero'),
														$this->local,
														$this->input->post('deposito'),
														$this->input->post('detalle')
													);
	
				// Si la compra salió bien.
				if ($compraId)
				{
					$totalCompra		= 0;
					$cantidadCompra		= 0;
				
					$movimientosProductosModel	= new MovimientosProductos_model();
					$compraProductosModel	= new CompraProductos_model();
					
					// Seteo de Depósito
					$movimientosProductosModel->setDeposito( $this->input->post('deposito') );
					
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
								$movimientosProductosModel->nuevaCompra($compraId, $producto->id, $producto->precio);
																						
								$totalCompra 	+= $producto->precio;
								$cantidadCompra += 1;
	
								$cantidad		+= 1;
							}
	
							$compraProductosModel->insertCompraProducto(
																		$compraId,
																		$producto->id,
																		$cantidad,
																		$producto->precio
																	);
						}
					}
	
					$compraModel->updateCompraTotal($compraId, $totalCompra, $cantidadCompra);
					
					redirect(base_url( 'index.php/compras/'), 'location');
				}
			}
		}
		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_proveedores', $data);
		$this->load->view( 'compra_nueva' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}

	public function verCompra($compraId)
	{
		$data['menu_header_seleccionado']	= 'agolan';
		$data['menu_seleccionado']			= 'compras';
	
		$compraModel = new compra_model();
		
		$compra = $compraModel->getCompra($compraId);
		$data['compra'] = $compra;

		$compraProductosModel = new CompraProductos_model();
		$data['productosDeLaCompra'] = $compraProductosModel->getProductosPorCompra($compra->id_compra);

		// Grocery Crud
		$crud = new grocery_CRUD();

        $crud->set_table( 'compra_productos' );
        
        $this->db->select('*, producto_id as producto_id_original, producto_precio as producto_precio_original, cantidad as cantidad_original');
        
        $where = 'compra_id = ' . $compraId;
        $crud->where($where);

		$crud->order_by('producto_id');
		
		$crud->set_relation( 'producto_id', 'producto', 'nombre');

        $crud->columns( 'producto_id', 'cantidad', 'producto_precio', 'subtotal');

        $crud	->callback_column( 'subtotal',				array($this,'subtotalColumn') )
		 		->callback_column( 'cantidad',				array($this,'cantidadColumn') )
		 		->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
		 		->callback_column( 'producto_precio',		array($this,'currencyColumn') );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'producto_id_original', '')
        		->display_as( 'producto_precio','Precio Unidad' );

        $crud->unset_delete();
        $crud->unset_add();
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_proveedores', $data);
		$this->load->view( 'compra_ver' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}

	public function subtotalColumn( $value, $row )
	{
		return  '<div class="currency-right">' . currency_format( $row->cantidad_original * $row->producto_precio_original ) . "</div>";
	}

	public function anularCompra($compraId)
	{
		// Retira recibo_id del movimiento.
		$movimientosProductosModel = new MovimientosProductos_model();
		$movimientosConsignablesPorCompra = $movimientosProductosModel->movimientosConsignablesPorCompra($compraId);


		$compraModel = new Compra_model();
		$compra = $compraModel->getCompra($compraId);
		
		if ($compra->cantidad == count($movimientosConsignablesPorCompra))
		{
			if ($movimientosProductosModel->anularMovimientosPorCompra($compraId))
			{		
				if ($compraModel->anularCompra($compraId))
				{
					redirect(base_url( 'index.php/compras/verCompra/' . $compraId), 'location');
				}
				else flash_error('Error al anular compra.');
			}
			else flash_error('Error al anular movimientos.');
		}
		else
		{
			flash_alert('Los productos de esta compra están consignados');
			redirect(base_url( 'index.php/compras/verCompra/' . $compraId), 'location');
		}
	}
}