<?php
/**
 * Example controller that uses the TwoCheckout_Lib library to pass sales to 2Checkout in any of the 4 supported parameter sets and validate the
 * response passed back to the approved URL.
 */

class TwoCheckout extends My_Controller {

	protected $ventaId = null;

	function TwoCheckout()
	{
		parent::__construct();
		$this->load->library('twocheckout_lib');
	}

	function index($ventaId)
	{
		//Setup Account Info, ('2Checkout Account Number', '2Checkout Secret Word', 'Demo Setting')
		$this->twocheckout_lib->set_acct_info('202215050', 'tango', 'Y');
		$this->ventaId = $ventaId;
		$this->form();
	}

	function form()
	{
		$this->twocheckout_lib->button('Click to Pay!');
	    $data['payment_form'] = $this->submit_form();

		$this->load->view('web/2checkout', $data); 
        
	}

	function submit_form()
	{
		$this->twocheckout_lib->add_field('mode', '2CO');					//Required - Will always be '2CO'
		$this->twocheckout_lib->add_field('sid', $this->twocheckout_lib->sid);

		$oficinaProductosModel		= new Venta_oficinaProductos_model();
		$productos					= $oficinaProductosModel->getProductosPorVenta($this->ventaId);
		
		$count			= 0;
		$totalQuantity	= 0;
		
		foreach($productos as $producto)
		{
			$productoModel = new Producto_model();
			$productoObj = $productoModel->getProducto($producto->producto_id);		
					
			$productoTipoModel	= new producto_tipo_model();
			$tipoProducto = $productoTipoModel->getOne($productoObj->producto_tipo);
			
			$productoCategoriaModel = new producto_categoria_model();
			$categoriaProducto = $productoCategoriaModel->getOne( $productoObj->producto_categoria );	
		
			$this->twocheckout_lib->add_field('li_' . $count . '_type', 'product');							//Required Lineitem Type - ‘product’, ‘shipping’, ‘tax’ or ‘coupon’
			$this->twocheckout_lib->add_field('li_' . $count . '_name', $categoriaProducto->nombre_singular . ' ' . $tipoProducto->nombre . ' (' . $productoObj->nombre_web . ')');
																											//Required - Lineitem name
			$this->twocheckout_lib->add_field('li_' . $count . '_price', $producto->producto_precio);		//Required - Lineitem Price
			$this->twocheckout_lib->add_field('li_' . $count . '_tangible', 'Y');							//Required -  Tangible - ‘Y’ or ‘N’, if li_#_type is ‘shipping’ forced to ‘Y’
			$this->twocheckout_lib->add_field('li_' . $count . '_quantity', $producto->cantidad);			//Quantity - defaults to 1 if not passed in
			$this->twocheckout_lib->add_field('li_' . $count . '_product_id', $producto->producto_id);		//Prodcut ID
			$this->twocheckout_lib->add_field('li_' . $count . '_description', $productoObj->medidas_web); 	//Description
			
			$totalQuantity += $producto->cantidad;
			
			$count++;
		}

		// Shipping
		$this->twocheckout_lib->add_field('li_' . $count . '_type', 'shipping');						//Required Lineitem Type - ‘product’, ‘shipping’, ‘tax’ or ‘coupon’
		$this->twocheckout_lib->add_field('li_' . $count . '_name', 'Example Shipping Lineitem');	//Required - Lineitem name
		$this->twocheckout_lib->add_field('li_' . $count . '_price', config_item('shipping_' . $this->shippingMethod ) * $totalQuantity );						//Required - Lineitem Price

		// Additional params
		$this->twocheckout_lib->add_field('demo', $this->twocheckout_lib->demo);	//Either Y or N
		$this->twocheckout_lib->add_field('lang', 'en');							//Language
		$this->twocheckout_lib->add_field('merchant_order_id', $this->ventaId);		//Merchant Order ID (50 characters max) - Additonal sale identifier, passed back as vendor_order_id on INS messages
		#$this->twocheckout_lib->add_field('skip_landing', '1');					//If set to '1' landing page of the multi-purchase will be skipped.

		//Customer Billing Information
		$this->twocheckout_lib->add_field('first_name',				$this->session->userdata('fullname')); 	//First Name
		$this->twocheckout_lib->add_field('last_name',				$this->session->userdata('fullname'));	//Last Name
		$this->twocheckout_lib->add_field('email',					$this->session->userdata('email'));		//Email Address 
		$this->twocheckout_lib->add_field('phone',					$this->session->userdata('phone'));		//Phone Number
		$this->twocheckout_lib->add_field('street_address',			$this->session->userdata('address1'));	//Street Address 1
		$this->twocheckout_lib->add_field('street_address2',		$this->session->userdata('address2'));	//Street Address 2
		$this->twocheckout_lib->add_field('city',					$this->session->userdata('city'));		//City
		$this->twocheckout_lib->add_field('state',					$this->session->userdata('province'));	//State
		$this->twocheckout_lib->add_field('zip',					$this->session->userdata('zip'));		//Postal Code
		$this->twocheckout_lib->add_field('country',				$this->session->userdata('country'));	//Country

		//Customer Shipping Information
		$this->twocheckout_lib->add_field('ship_name',				$this->session->userdata('fullname')); 	//Recipient Name
		$this->twocheckout_lib->add_field('ship_street_address',	$this->session->userdata('address1'));	//Recipient Street Address 1
		$this->twocheckout_lib->add_field('ship_street_address2',	$this->session->userdata('address2'));	//Recipient Street Address 2
		$this->twocheckout_lib->add_field('ship_city',				$this->session->userdata('city'));		//Recipient City
		$this->twocheckout_lib->add_field('ship_state',				$this->session->userdata('province'));	//Recipient State
		$this->twocheckout_lib->add_field('ship_zip',				$this->session->userdata('zip'));		//Recipient Postal Code
		$this->twocheckout_lib->add_field('ship_country',			$this->session->userdata('country'));	//Recipient Country

	    $this->twocheckout_lib->submit_form();
	}
}