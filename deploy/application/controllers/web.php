<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web extends My_Controller {

	public function index()
	{
		redirect( base_url( 'index.php/web/catalogo/' ), 'location');
	}
	
	public function catalogo( $unique_tipo = null )
	{
		$data = array();

		if ( !$unique_tipo )
		{
			$data = array();
		
			$this->load->view( 'web/templates/head', $data );
			$this->load->view( 'web/catalogo_categoria', $data );
			$this->load->view( 'web/templates/footer', $data );
		}
		else
		{
			$productoTipoModel		= new producto_tipo_model();
			$productoCategoriaModel = new producto_categoria_model();

			$productoCategoriaModel->setUnique_name($unique_tipo);
			$categoria = $productoCategoriaModel->getOne();

			$productoTipoModel->setCategoria($categoria->id);
			$tipoProductos = $productoTipoModel->getTodos();
			
			$data['categoria']		= $categoria;
			$data['tipoProductos']	= $tipoProductos;
			
			// Redirecciono si hay solo 1 tipo de producto para esa categoría.
			if ( count($tipoProductos) == 1 )
			{
				redirect(base_url( 'index.php/web/producto/' . $tipoProductos[0]->id ), 'location');
			}
		
			$this->load->view( 'web/templates/head', $data );
			$this->load->view( 'web/catalogo_tipo', $data );
			$this->load->view( 'web/templates/footer', $data );
		}
	}
	
	public function producto( $productoCategoria, $productoTipo )
	{
		$productoModel			= new Producto_model();
		$productoTipoModel		= new producto_tipo_model();
		$productoCategoriaModel = new producto_categoria_model();

		// Producto Tipo
		$productoTipoId	= end( explode('-', $productoTipo ));
		$tipoProducto = $productoTipoModel->getOne($productoTipoId);

		// Producto Categoria
		$productoCategoriaModel->setUnique_name($productoCategoria);
		$categoria = $productoCategoriaModel->getOne();

		// Productos
		$productoModel->setTipo($productoTipoId);
		$productoModel->setCategoria($categoria->id);
		$productoModel->setStatusWeb(1);
		$productos = $productoModel->getProductosTodos(false, false, false);
		
		// Productos
		foreach($productos as $producto)
		{
			$selectProducto[$producto->id] = array(
													'id'			=> $producto->id,
													'nombre_web'	=> $producto->nombre_web,
													'precio_web'	=> $producto->precioWeb,
													'nombre_unico'	=> $producto->nombre_unico,
													'medidas_web'	=> $producto->medidas_web
												);
			
			$productoCategoria = $producto->producto_categoria;
		}
		
		// Cantidad
		$optionsQty = array();
        for ($i=1; $i <= 10; $i++)
        {
	        $optionsQty[$i] = $i;
        }
		$data['dropdown_qty'] = form_dropdown('cantidad', $optionsQty, 0, 'id="cantidad" onchange="calcularSubTotal()"');
		
		// Add to Cart
		$data['subimt'] = form_submit('addtocart', 'Add to Cart', 'onclick="addToCart()" id="addtocart"');

		$data['tipoProducto']	= $tipoProducto;
		$data['productos']		= $productos;
		$data['selectProducto']	= $selectProducto;
		$data['categoria']		= $categoria;


		$this->load->view( 'web/templates/head', $data );
		$this->load->view( 'web/producto', $data );
		$this->load->view( 'web/templates/footer', $data );
	}
	
	public function addToCart($id, $cantidad)
	{
		$shoppingCartModel = new Shoppingcart_model();
		$insertId = $shoppingCartModel->nuevo_registro($id, $cantidad);
		
		
		$nuevoRegistro = $shoppingCartModel->getOne($insertId);
		
		$productoModel = new Producto_model();
		$producto = $productoModel->getProducto($nuevoRegistro->producto_id);		
				
		$productoTipoModel	= new producto_tipo_model();
		$tipoProducto = $productoTipoModel->getOne($producto->producto_tipo);
		
		$productoCategoriaModel = new producto_categoria_model();
		$categoriaProducto = $productoCategoriaModel->getOne( $producto->producto_categoria );
		
		$shoppingCartModel->setSessionId($this->session->userdata('store_id'));
		
		$data['nuevoRegistro']		= $nuevoRegistro;
		$data['tipoProducto']		= $tipoProducto;
		$data['categoriaProducto']	= $categoriaProducto;
		$data['producto']			= $producto;
		$data['insertId']			= 'sc_' . $insertId;
		
		
		$json['insertId']	= $data['insertId'];
		$json['total']		= $shoppingCartModel->getTotal()->total;
		$json['cantidad']	= $shoppingCartModel->getTotal()->cantidad;
		$json['view']		= $this->load->view('web/shoppingcart_quickdisplay', $data, true);
		
		echo json_encode( $json );
		die;
	}
	
	public function cart()
	{
		$data = array();
		
		$data['header_title'] = "Shopping Cart";
		
		// Cambiar Shipping
		if ( $this->input->post('shipping') )		$this->session->set_userdata('shipping', $this->input->post('shipping') );


		$shoppingCartModel = new Shoppingcart_model();
		$shoppingCartModel->setSessionId($this->session->userdata('store_id'));
		$shoppingCartModel->setGroupByProducto(true);
		
		$productos = $shoppingCartModel->getTodos();

		$shopping_cart = array();
		$shopping_cartTotal			= 0;
		$shopping_cartItemsCount	= 0;
		foreach($productos as $producto)
		{
		
			$this->form_validation->set_rules('producto_' . $producto->producto_id,				'',		'integer');
			
			$shopping_cartTotal			+= ( $producto->precioWeb * $producto->cantidad );
			$shopping_cartItemsCount	+= $producto->cantidad;
			
			$shopping_cart[$producto->producto_id] = array(
														'producto_id'		=> $producto->producto_id,
														'categoria_nombre'	=> $producto->categoria_nombre,
														'categoria_nombre_singular' => $producto->categoria_nombre_singular,
														'categoria_unique_name'		=> $producto->categoria_unique_name,
														'producto_categoria'=> $producto->producto_categoria,
														'tipo_nombre'		=> $producto->tipo_nombre,
														'tipo_unique_name'	=> $producto->tipo_unique_name,
														'tipo_id'			=> $producto->tipo_id,
														'producto_tipo'		=> $producto->producto_tipo,
														'nombre_web'		=> $producto->nombre_web,
														'nombre_unico'		=> $producto->nombre_unico,
														'precioWeb'			=> $producto->precioWeb,
														'precio_avg'		=> $producto->precio_avg,
														'medidas_web'		=> $producto->medidas_web,
														'subtotal'			=> ( $producto->precioWeb * $producto->cantidad ),
														'cantidad'			=> $producto->cantidad,
														'input_cantidad'	=> form_input( array(
																					              'name'        => 'producto_' . $producto->producto_id,
																					              'maxlength'   => '2',
																					              'size'		=> '2',
																					              'style'		=> 'text-align:center;',
																					              'autocomplete'=> 'off',
																					              'class'		=> 'numeric',
																					              'value'		=> ($this->input->post('producto_' . $producto->producto_id)) ? $this->input->post('producto_' . $producto->producto_id) : $producto->cantidad
																					       ))
														);
		}
		
		$shippingForm['fullname'] 	= array('Full Name', 'fullname', form_input( array(
											              'name'        => 'fullname',
											              'value'		=> ( $this->input->post('fullname') ) ? $this->input->post('fullname') : ''
											       )));
		$shippingForm['email'] 		= array('Email', 'email', form_input( array(
											              'name'        => 'email',
											              'value'		=> ( $this->input->post('email') ) ? $this->input->post('email') : ''
											       )));

		$shippingForm['address1'] 	= array('Address Line 1', 'address1', form_input( array(
											              'name'        => 'address1',
											              'placeholder'	=> 'Street address, P.O. box, company name, c/o',
											              'value'		=> ( $this->input->post('address1') ) ? $this->input->post('address1') : ''
											       )));
		$shippingForm['address2'] 	= array('Address Line 2', 'address2', form_input( array(
											              'name'        => 'address2',
											              'placeholder'	=> 'Apartment, suit, unit, building, floor, etc',
											              'value'		=> ( $this->input->post('address2') ) ? $this->input->post('address2') : ''
											       )));
		$shippingForm['city'] 		= array('City', 'city', form_input( array(
											              'name'        => 'city',
											              'value'		=> ( $this->input->post('city') ) ? $this->input->post('city') : ''
											       )));
		$shippingForm['province'] 	= array('State/Province/Region', 'province', form_input( array(
											              'name'        => 'province',
											              'value'		=> ( $this->input->post('province') ) ? $this->input->post('province') : ''
											       )));
		$shippingForm['zip'] 		= array('Zip', 'zip', form_input( array(
											              'name'        => 'zip',
											              'value'		=> ( $this->input->post('zip') ) ? $this->input->post('zip') : ''
											       )));
		$shippingForm['country'] 	= array('Country', 'country', form_input( array(
											              'name'        => 'country',
											              'value'		=> ( $this->input->post('country') ) ? $this->input->post('country') : ''
											       )));
		$shippingForm['phone'] 		= array('Phone Number', 'phone', form_input( array(
											              'name'        => 'phone',
											              'value'		=> ( $this->input->post('phone') ) ? $this->input->post('phone') : ''
											       )));

		// Validation
		if ($this->input->post('updateorCheckout') == 'checkout' && config_item('cart_shipping_address'))
		{
			$this->form_validation->set_rules('fullname',	'Full name',	'trim|required|min_length[5]|max_length[50]');
			$this->form_validation->set_rules('email',		'Email',		'trim|required|valid_email');
			$this->form_validation->set_rules('address1',	'Address 1',	'trim|required|min_length[5]|max_length[30]');
			$this->form_validation->set_rules('city',		'City',			'trim|required|min_length[5]|max_length[30]');
			$this->form_validation->set_rules('province',	'Province',		'trim|required|min_length[2]|max_length[30]');
			$this->form_validation->set_rules('zip',		'Zip code',		'trim|required|min_length[5]|max_length[30]');
			$this->form_validation->set_rules('country',	'Country',		'trim|required|min_length[5]|max_length[30]');
			$this->form_validation->set_rules('phone',		'Phone',		'trim|required|min_length[5]|max_length[30]');
		}

		
		$data['shippingForm']				= $shippingForm;
		$data['shopping_cartTotal']			= $shopping_cartTotal;
		$data['shopping_cartItemsCount']	= $shopping_cartItemsCount;
		
		// Submit
		if ($this->form_validation->run() == FALSE)
		{
			// Not ok
		}
		else
		{
			$shoppingCartModel = new Shoppingcart_model();
			$shoppingCartModel->setSessionId( $this->session->userdata('store_id') );

			foreach($productos as $producto)
			{
				$shoppingCartModel->removeProducto( $producto->producto_id );
				
				if( $this->input->post('producto_' . $producto->producto_id ) )
				{
					$insertId = $shoppingCartModel->nuevo_registro($producto->producto_id, $this->input->post('producto_' . $producto->producto_id));
				}
		    }

			if ($this->input->post('updateorCheckout') == 'checkout')
			{
				$this->session->set_userdata( 'fullname',	$this->input->post('fullname'));
				$this->session->set_userdata( 'email',		$this->input->post('email'));
				$this->session->set_userdata( 'address1',	$this->input->post('address1'));
				$this->session->set_userdata( 'address2',	$this->input->post('address2'));
				$this->session->set_userdata( 'city',		$this->input->post('city'));
				$this->session->set_userdata( 'province',	$this->input->post('province'));
				$this->session->set_userdata( 'zip',		$this->input->post('zip'));
				$this->session->set_userdata( 'country',	$this->input->post('country'));
				$this->session->set_userdata( 'phone',		$this->input->post('phone'));
			
				redirect(base_url( 'index.php/web/confirmOrder/' ), 'location');
			}
			else
			{
				redirect(base_url( 'index.php/web/cart/' ), 'location');
			}
		}
		
		$data['form_open']		= form_open(base_url( 'index.php/web/cart' ));
		$data['shopping_cart']	= $shopping_cart;
		$data['form_close']		= form_close();
		$data['checkout']		= base_url( 'index.php/web/confirmOrder' );
	
		$this->load->view( 'web/templates/head', $data );
		$this->load->view( 'web/cart', $data );
		$this->load->view( 'web/templates/footer', $data );
	}
	
	public function confirmOrder()
	{
		$shoppingCartModel	= new Shoppingcart_model();
		$oficinaModel		= new Oficina_model();

		$shoppingCartModel->setSessionId($this->session->userdata('store_id'));
		$shoppingCartModel->setGroupByProducto(true);
		
		$oficinaModel->setEstadoWeb('not-paid');
		
		$productos = $shoppingCartModel->getTodos();

		// [TODO]
		$oficinaModel->setClienteId();
		// [TODO]
		$oficinaModel->setShipping();

		$ventaId = $oficinaModel->nuevaVenta(
												null,
												'0',
												date( 'm/d/Y' ),
												'',
												'web'
												);

		if ($ventaId)
		{
			$totalVenta		= 0;
			$cantidadVenta	= 0;

			$oficinaProductosModel		= new Venta_oficinaProductos_model();
			
			// Para cada uno de los productos
			foreach($productos as $key => $producto)
			{
				$totalVenta 	+= $producto->precio_avg * $producto->cantidad;
				$cantidadVenta	+= $producto->cantidad;

				$oficinaProductosModel->insertVentaOficinaProducto(
																$ventaId,
																$producto->producto_id,
																$producto->cantidad,
																$producto->precio_avg
															);
				
				$shoppingCartModel->removeProducto( $producto->producto_id );
			}
			
			$oficinaModel->updateTotal($ventaId, $totalVenta, $cantidadVenta, null);

			redirect(base_url( 'index.php/TwoCheckout/index/' . $ventaId), 'location');
		}
	}
	
	public function payment()
	{
		die('payment');
	}
	
	public function paymentResponse()
	{
		die('payment response');
	}
	
	public function estatica( $page )
	{
		$data = array();
	
		$this->load->view( 'web/templates/head', $data );
		$this->load->view( 'web/static_' . $page , $data );
		$this->load->view( 'web/templates/footer', $data );
	}
}