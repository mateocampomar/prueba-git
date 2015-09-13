<?php defined('BASEPATH') OR exit('No direct script access allowed');

class My_Controller extends CI_Controller
{

	protected $local;
	protected $modo;
	protected $shippingMethod;
	protected $filtros			= false;
	protected $filterKey		= false;


	public function _remap($method, $params = array())
	{
	    //$method = 'process_'.$method;
	    if (method_exists($this, $method))
	    {
	    	if ( in_array("reset_filters", $params) )
	    	{
		    	$this->resetFiltros();
	    	}
	    
	        return call_user_func_array(array($this, $method), $params);
	    }
	    show_404();
	}

    function __construct()
    {
        parent::__construct();
        
        //print_r( $this->session->userdata('authUser') );
        
        if ( $this->router->class != 'auth' && !$this->session->userdata('authUser') )
        {
	        redirect(base_url( 'index.php/auth/login'), 'location');
        }
        
        $this->filterKey = $this->router->class . "/" . $this->router->method;

        if ( !$this->session->userdata('store_id') )
        	$this->session->set_userdata( 'store_id', $this->session->userdata('session_id') );
        
        if ( $this->router->class == 'web' || $this->router->class == 'TwoCheckout' )
        {
        	$shoppingCartModel = new Shoppingcart_model();
	        $shoppingCartModel->setSessionId( $this->session->userdata('store_id') );
	        
	        $productoCategoriaModel = new producto_categoria_model();
			$categoriaProductos = $productoCategoriaModel->getTodos();
			
			$this->shippingMethod = ( $this->session->userdata( 'shipping' ) ) ? $this->session->userdata( 'shipping' ) : 'free';
	        
	        $this->load->vars(array(
	        					'shoppingCartTotal'		=> $shoppingCartModel->getTotal()->total,
	        					'shoppingCartCantidad'	=> $shoppingCartModel->getTotal()->cantidad,
	        					'categoriaProductos'	=> $categoriaProductos,
	        					'shippingMethod'		=> $this->shippingMethod
	        					));
        }
		else
		{
			// Modelos
			$localModel = new Local_model();
			
			if ( !$this->session->userdata('modo') )
			{
				$this->session->set_userdata( 'modo', 'consignacion');
			}
	
			// Modo
			if ($this->session->userdata('modo') == 'consignacion')
			{
				$this->local	= $this->session->userdata('local');
				$this->modo		= 'consignacion';
				$todosLosLocales= $localModel->getLocalesTodos();
			}
			elseif($this->session->userdata('modo') == 'stock')
			{
				$this->modo		= 'stock';
				$todosLosLocales= $localModel->getLocalesTodos();
			}
			else
			{
				$this->local	= $this->session->userdata('local_proveedor');
				$this->modo		= 'proveedores';
				$todosLosLocales=$localModel->getLocalesTodos(false, true);
			}
	
			if ($this->local)
			{
				$this->localObj = $localModel->getLocal($this->local);
			}
			else
			{
				$this->localObj = null;
			}
	
	        // Local Select
			$localesArray	= array(0 => 'Todos los Locales');
			foreach($todosLosLocales as $key => $locales)
			{
				$localesArray[$locales->id]		= $locales->nombre;
			}
	
	        $movimientosProductosModel = new MovimientosProductos_model();
			$recibosParaHacer	= $movimientosProductosModel->movimientosRecibosParaHacer($this->local);
			$ncParaHacer		= $movimientosProductosModel->movimientosNcParaHacer($this->local);
			
			// Filtros
			if ( $this->input->post('setfilter') )
			{
				$this->set_filtros( false, $this->input->post() );
			}
			
			$this->load_filtros();
	
			// Paso variables al view
			$this->load->vars(array(
	        					'todosLosLocales'	=> $localesArray,
	        					'local'				=> $this->local,
	        					'recibosParaHacer'	=> $this->local ? count($recibosParaHacer) : 0,
	        					'ncParaHacer'		=> $this->local ? count($ncParaHacer) : 0,
	        					'localObj'			=> $this->localObj,
	        					'_iva'				=> $this->config->item('iva'),
	        					'_cierreEjercicio'	=> cierreDeEjercicio(),
	        					'_cierreEjeConfig'	=> $this->config->item('cierre_de_ejercicio'),
	        					'modo'				=> $this->modo,
	        					'filtros'			=> $this->filtros
	        					));
	    }
    }

    public function currencyColumn( $value )
	{
		return '<div class="currency-right">' . currency_format( $value ) . '</div>';
	}
	
	public function producto_thumbnail( $value )
	{
		return '<div class="thumbnail_container">' . productoThumbnail( $value ) . '</div>';
	}
	
    public function masIvaColumn( $value )
	{
		return '<div class="currency-right">' . currency_format( $value * $this->config->item('iva') ) . '</div>';
	}

    public function cantidadColumn( $value )
	{
		return '<div class="cantidad">x ' . $value . '</div>';
	}
	
	public function comentarioMovimientosProductosColumn($value, $row)
	{
		if ( $row->mp_comentario )	$comentario = $row->mp_comentario;
		else						$comentario = '.';
	
		return '<a href="javascript:editarComentarioMovProd(' . $row->id . ', \'' . $row->mp_comentario . '\')" style="color:#000;" id="mp_' . $row->id . '">' . urldecode( $comentario ) . '</a>';
	}
	
	public function set_filtros( $filterKey=false, $filtros )
	{
		$filterKey = ( $filterKey ) ? $filterKey : $this->filterKey;
	
		if ( $filtros )
		{
			$this->session->set_userdata( 'filtro_' . $filterKey, $filtros );
		}
	}

	public function load_filtros( $filterKey=false )
	{
		$filterKey = ( $filterKey ) ? $filterKey : $this->filterKey;
	
		if ( $this->session->userdata('filtro_' . $filterKey ) )
		{
			$this->filtros = $this->session->userdata('filtro_' . $filterKey );
		}
	}
	
	public function resetFiltros( $filterKey=false, $redirect=true )
	{
		$filterKey = ( $filterKey ) ? $filterKey : $this->filterKey;
	
		$this->session->unset_userdata( 'filtro_' . $filterKey );
		
		if ( $redirect )
		{
			redirect(base_url( 'index.php/' . $filterKey ), 'location');
		}
		else
		{
			return true;
		}
	}
}