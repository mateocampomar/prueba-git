<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Liquidacion extends My_Controller {

	public function index( $imprimir = false )
	{		
		$data = array();
		$data['menu_header_seleccionado']	= 'consignacion';
		$data['menu_seleccionado']			= 'liquidacion';
		
		$data['title']						= 'Campomar Liquidación - ' . date('Y-m-d') . ' ' . $this->localObj->nombre;
		
		if ($imprimir)
		{
			$data['imprimir'] = true;
		}


		if (!$this->local) {
			redirect(base_url( 'index.php/consignacion/'), 'refresh');
			die;
		}
        
	    $movimientosProductosModel = new MovimientosProductos_model();
	    
	    // Recibos para sacar los Vendidos
		$data['paraHacerRecibo'] = $movimientosProductosModel->movimientosRecibosParaHacer($this->local);
		$sumaRecibosParaHacer = $movimientosProductosModel->sumaRecibosParaHacer( $data['paraHacerRecibo'] );
		$data['totalRecibos']		= ( isset( $sumaRecibosParaHacer['totalRecibos'] ))		? $sumaRecibosParaHacer['totalRecibos']		: 0;
		$data['cantidadRecibos']	= ( isset( $sumaRecibosParaHacer['cantidadRecibos'] ))	? $sumaRecibosParaHacer['cantidadRecibos']	: 0;
		
		// Notas de crédito
		$data['paraHacerNc'] = $movimientosProductosModel->movimientosNcParaHacer($this->local);
		$sumaNcParaHacer = $movimientosProductosModel->sumaNcParaHacer( $data['paraHacerNc'] );
		$data['totalNc']		= ( isset( $sumaNcParaHacer['totalNc'] ))	? $sumaNcParaHacer['totalNc']		: 0;
		$data['cantidadNc']	= ( isset( $sumaNcParaHacer['cantidadNc'] ))	? $sumaNcParaHacer['cantidadNc']	: 0;
		
		$movimientosProductosModel	= new MovimientosProductos_model();
		$data['movimientosEnConsignacion'] = $movimientosProductosModel->movimientosConsignadosPorLocal($this->local);

		//$data['debe'] = - $data['totalConsignaciones'] + $data['totalRecibos'] + $data['totalNc'];
		
		$cuentaModel = new Cuenta_model();
		$data['saldo']	= $cuentaModel->getSaldoLocal($this->local);

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu', $data );
		$this->load->view( 'liquidacion' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function iFrameConsignadosConNC($width100=false)
	{
		if (!$this->local) {
			redirect(base_url( 'index.php/consignacion/'), 'refresh');
			die;
		}
		
		$data['imprimir']						= true;
		if ($width100)		$data['width100']	= true;

		$crud = new grocery_CRUD();
		
		$this->db->select('*, COUNT(1) as cantidad, SUM(consignacionPrecio) as totalProducto, producto_id as producto_id_original' );

		$where = 'recibo_id IS NULL AND devolucion IS NOT NULL AND vendidoPrecio IS NULL AND local_id = ' . $this->local;		
		$crud->where($where);

        $crud->set_table( 'movimientos_productos' );
        
        $this->db->group_by(array(
        							'producto_id',
        							'consignacionPrecio'
        						)); 

        $crud->set_relation( 'producto_id', 'producto', 'nombre');
        $crud->set_relation( 'local_id', 'local', '{nombre}');

		$crud->order_by('producto_id');

		$columnsArray = array('producto_id', 'consignacion_id', 'cantidad', 'consignacionPrecio', 'totalProducto');

		if ( !$width100 ) array_unshift( $columnsArray, 'producto_id_original' );

        $crud->columns( $columnsArray );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'consignacion_id','Factura' )
        		->display_as( 'consignacionPrecio', 'Precio')
        		->display_as( 'cantidad', 'Cantidad' )
        		->display_as( 'totalProducto', 'Importe' )
        		->display_as( 'producto_id_original','' );
		
		$crud	->callback_column( 'consignacion_id',		array($this,'consignacionColumn') )
				->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
				->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
				->callback_column( 'totalProducto',			array($this,'currencyColumn') )
				->callback_column( 'cantidad',				array($this,'cantidadColumn') );

		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_export();
		$crud->unset_print();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/render_crud' , $data );
	}

	public function iFrameConsignadosSinVender($width100=false)
	{
		if (!$this->local) {
			redirect(base_url( 'index.php/consignacion/'), 'refresh');
			die;
		}
		
		$data['imprimir']						= true;
		if ($width100)		$data['width100']	= true;

		$crud = new grocery_CRUD();
		
		$this->db->select('*, COUNT(1) as cantidad, SUM(consignacionPrecio) as totalProducto, producto_id as producto_id_original' );

		$where = 'recibo_id IS NULL AND devolucion IS NULL AND vendidoPrecio IS NULL AND local_id = ' . $this->local;		
		$crud->where($where);

        $crud->set_table( 'movimientos_productos' );
        
        $this->db->group_by(array(
        							'producto_id',
        							'consignacionPrecio'
        						)); 

        $crud->set_relation( 'producto_id', 'producto', 'nombre');
        $crud->set_relation( 'local_id', 'local', '{nombre}');

		$crud->order_by('producto_id');

		$columnsArray = array('producto_id', 'consignacion_id', 'cantidad', 'consignacionPrecio', 'totalProducto');

		if ( !$width100 ) array_unshift( $columnsArray, 'producto_id_original' );

        $crud->columns( $columnsArray );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'consignacion_id','Factura' )
        		->display_as( 'consignacionPrecio', 'Precio')
        		->display_as( 'cantidad', 'Cantidad' )
        		->display_as( 'totalProducto', 'Importe' )
        		->display_as( 'producto_id_original','' );
		
		$crud	->callback_column( 'consignacion_id',		array($this,'consignacionColumn') )
				->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
				->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') )
				->callback_column( 'totalProducto',			array($this,'currencyColumn') )
				->callback_column( 'cantidad',				array($this,'cantidadColumn') );

		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_export();
		$crud->unset_print();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/render_crud' , $data );
	}

	public function iFrameConsignadosVendidos($width100=false)
	{
		if (!$this->local) {
			redirect(base_url( 'index.php/consignacion/'), 'refresh');
			die;
		}
		
		$data['imprimir']						= true;
		if ($width100)		$data['width100']	= true;

		$crud = new grocery_CRUD();
		
		$this->db->select('*, COUNT(1) as cantidad, SUM(consignacionPrecio) as totalProducto, producto_id as producto_id_original' );

		$where = 'recibo_id IS NULL AND vendidoPrecio IS NOT NULL AND local_id = ' . $this->local;		
		$crud->where($where);

        $crud->set_table( 'movimientos_productos' );
        
        $this->db->group_by(array(
        							'producto_id',
        							'consignacionPrecio'
        						)); 

        $crud->set_relation( 'producto_id', 'producto', 'nombre');
        $crud->set_relation( 'local_id', 'local', '{nombre}');

		$crud->order_by('producto_id');

		$columnsArray = array('producto_id', 'consignacion_id', 'cantidad', 'consignacionPrecio', 'totalProducto');

		if ( !$width100 ) array_unshift( $columnsArray, 'producto_id_original' );

        $crud->columns( $columnsArray );

        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'consignacion_id','Factura' )
        		->display_as( 'consignacionPrecio', 'Precio')
        		->display_as( 'cantidad', 'Cantidad' )
        		->display_as( 'totalProducto', 'Importe' )
        		->display_as( 'producto_id_original','' );
		
		$crud	->callback_column( 'consignacion_id',		array($this,'consignacionColumnVendido') )
				->callback_column( 'consignacionPrecio',	array($this,'currencyColumn') )
				->callback_column( 'totalProducto',			array($this,'currencyColumn') )
				->callback_column( 'cantidad',				array($this,'cantidadColumn') )
				->callback_column( 'producto_id_original',	array($this,'producto_thumbnail') );

		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_export();
		$crud->unset_print();

        // render crud
        $data['crud'] = $crud->render();

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/render_crud' , $data );
	}


	public function consignacionColumn( $value, $row )
	{
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion ( $row->consignacion_id );
		
		$movimientosProductosModel	= new MovimientosProductos_model();
		$movimientos = $movimientosProductosModel->getMovimientoLiquidacionPorPrecio($this->local, $row->producto_id, $row->consignacionPrecio);
		
		$return = '';
		
		if ( count($movimientos) )
		{
			foreach ($movimientos as $movimiento)
			{
				$consignacion = $consignacionModel->getConsignacion ( $movimiento->consignacion_id );
			
				$return .= '<small> x' . $movimiento->cantidad . '</small> <a href="' . base_url('index.php/consignacion/ver/' . $movimiento->consignacion_id) . '">' . addZeros( $consignacion->facturaNumero ) . '</a><br/>';
			}
		}
		
		return $return;
	}

	public function consignacionColumnVendido( $value, $row )
	{
		$consignacionModel = new Consignacion_model();
		$consignacion = $consignacionModel->getConsignacion ( $row->consignacion_id );
		
		$movimientosProductosModel	= new MovimientosProductos_model();
		$movimientos = $movimientosProductosModel->getMovimientoLiquidacionPorPrecio($this->local, $row->producto_id, $row->consignacionPrecio, true);
		
		$return = '';
		
		if ( count($movimientos) )
		{
			foreach ($movimientos as $movimiento)
			{
				$consignacion = $consignacionModel->getConsignacion ( $movimiento->consignacion_id );
			
				$return .= '<small> x' . $movimiento->cantidad . '</small> <a href="' . base_url('index.php/consignacion/ver/' . $movimiento->consignacion_id) . '">' . addZeros( $consignacion->facturaNumero ) . '</a><br/>';
			}
		}
		
		return $return;
	}
	
	public function guardarLiquidacion($month, $day, $year)
	{
		if (!$this->local) {
			redirect(base_url( 'index.php/consignacion/'), 'refresh');
			die;
		}
	
		$localModel = new Local_model();
		$localModel->guardarLiquidacion($year . "-" . $month . "-" . $day, $this->local);

		redirect(base_url( 'index.php/liquidacion/'), 'refresh');
	}
}