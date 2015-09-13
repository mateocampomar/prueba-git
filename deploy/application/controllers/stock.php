<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock extends My_Controller {

	public function index( $mostrarSinStock=false )
	{	
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'stock';

		$data['productos'] = array();

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();
		$venta_directaModel	= new Ventadirecta_model();
		
		$alMenos1productoSinStock	=	false;

		$data['totales']['deposito']['cantidad']		= 0;
		$data['totales']['consignacion']['cantidad']	= 0;
		$data['totales']['oficina']['cantidad']			= 0;
		$data['totales']['venta']['cantidad']			= 0;
		$data['totales']['compras']['cantidad']			= 0;
		$data['totales']['venta_directa']['cantidad']	= 0;
		$data['totales']['totales']['cantidad']			= 0;

		$data['totales']['deposito']['total']			= 0;
		$data['totales']['consignacion']['total']		= 0;
		$data['totales']['oficina']['total']			= 0;
		$data['totales']['venta']['total']				= 0;
		$data['totales']['compras']['total']			= 0;
		$data['totales']['venta_directa']['total']		= 0;
		$data['totales']['totales']['total']			= 0;
		
		$data['saldo']									= 0;

		foreach($productoModel->getProductosTodos() as $key => $producto)
		{
			// Datos desde el modelo
			$deposito		= $stockModel->getStockEnDepositoPorProducto($producto->id);
			$consignacion	= $stockModel->getStockConsignadoPorProducto($producto->id);
			// Compras
			$compras		= $stockModel->getStockComprasPorProducto($producto->id);
			// Ventas
			$venta			= $stockModel->getStockVendidoConsignacionPorProducto($producto->id);
			$ventaOficina	= $stockModel->getStockVentaOficinaPorProducto($producto->id);
			$venta_directa	= $venta_directaModel->getStockVentaDirectaPorProducto($producto->id);

			if ( $deposito[0]->cantidad == 0 && $consignacion[0]->cantidad == 0 && !$mostrarSinStock )
			{
				$alMenos1productoSinStock = true;
			}
			else
			{
				// Total Ventas
				$cantidadVentas	= $venta[0]->cantidad + $ventaOficina[0]->cantidad;
				$totalVentas	= $venta[0]->total + $ventaOficina[0]->total;
	
				// Totales
				$data['totales']['deposito']['cantidad']		+= $deposito[0]->cantidad;
				$data['totales']['consignacion']['cantidad']	+= $consignacion[0]->cantidad;
				$data['totales']['venta']['cantidad']			+= $venta[0]->cantidad;
				$data['totales']['oficina']['cantidad']			+= $ventaOficina[0]->cantidad;
				$data['totales']['compras']['cantidad']			+= $compras[0]->cantidad;
				$data['totales']['venta_directa']['cantidad']	+= $venta_directa[0]->cantidad;
				$data['totales']['totales']['cantidad']			+= $cantidadVentas;
	
				$data['totales']['deposito']['total']			+= $deposito[0]->total;
				$data['totales']['consignacion']['total']		+= $consignacion[0]->total;
				$data['totales']['oficina']['total']			+= $ventaOficina[0]->total;
				$data['totales']['venta']['total']				+= $venta[0]->total;
				$data['totales']['compras']['total']			+= $compras[0]->total;
				$data['totales']['venta_directa']['total']		+= $venta_directa[0]->total;
				$data['totales']['totales']['total']			+= $totalVentas;

				// Agrego row con el producto.
				$data['productos'][$producto->id] = array(
											'id'			=> $producto->id,
											'nombre'		=> $producto->nombre,
											'deposito'		=> $deposito[0],
											'consignacion'	=> $consignacion[0],
											'venta'			=> $venta[0],
											'oficina'		=> $ventaOficina[0],
											'compras'		=> $compras[0],
											'venta_directa'	=> $venta_directa[0]
											);


				$data['saldo']	+= - $compras[0]->total + $deposito[0]->total + $consignacion[0]->total + $ventaOficina[0]->total + $venta[0]->total;
			}
		}
		
		$data['alMenos1productoSinStock']	= $alMenos1productoSinStock;

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'stock' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function resumen_local()
	{
		// No se puede ir al resumen del local si no hay un local Seleccionado
		if (!$this->local)
		{
			redirect(base_url( 'index.php/consignacion/listar/'), 'refresh');
			die;
		}
	
		$data = array();
		$data['menu_seleccionado']			= 'resumen';
		$data['menu_header_seleccionado']	= 'consignacion';

		$data['productos'] = array();

		$productoModel	= new Producto_model();
		$stockModel		= new Stock_model();
		$localModel		= new Local_model();

		$data['totales']['consignacion']['cantidad']	= 0;
		$data['totales']['venta']['cantidad']			= 0;
		$data['totales']['totales']['cantidad']			= 0;

		$data['totales']['consignacion']['total']		= 0;
		$data['totales']['venta']['total']				= 0;
		$data['totales']['totales']['total']			= 0;

		$data['situacion']		= $localModel->calcularSituacion( $this->local );
		
		//$reciboModel = new recibo_model();
		//$data['totalRecibos'] = $reciboModel->totalRecibos($this->local)->totalRecibos;
		
		//$consignacionModel = new consignacion_model();
		//$data['totalConsignaciones'] = $consignacionModel->totalConsignaciones($this->local)->totalConsignaciones;
		
		//$ncModel = new nc_model();
		//$data['totalNc'] = $ncModel->totalNc($this->local)->totalNc;
		
		//$data['debe'] = - $data['totalConsignaciones'] + $data['totalRecibos'] + $data['totalNc'];

		$cuentaModel = new Cuenta_model();
		$data['saldo']	= $cuentaModel->getSaldoLocal($this->local);

		foreach($productoModel->getProductosTodos(false, true, false) as $key => $producto)
		{
			// Datos desde el modelo
			$consignacion		= $stockModel->getStockConsignadoPorProducto($producto->id, $this->local);
			$venta				= $stockModel->getStockVendidoPorProducto($producto->id, $this->local);

			// Totales por Producto
			$cantidadProductos	= $consignacion[0]->cantidad + $venta[0]->cantidad;
			$totalProductos		= $consignacion[0]->total + $venta[0]->total;

			if ( $cantidadProductos )
			{
				// Totales
				$data['totales']['consignacion']['cantidad']	+= $consignacion[0]->cantidad;
				$data['totales']['venta']['cantidad']			+= $venta[0]->cantidad;
				$data['totales']['totales']['cantidad']			+= $cantidadProductos;
	
				$data['totales']['consignacion']['total']		+= $consignacion[0]->total;
				$data['totales']['venta']['total']				+= $venta[0]->total;
				$data['totales']['totales']['total']			+= $totalProductos;
			
				$data['productos'][$producto->id] = array(
															'id'				=> $producto->id,
															'nombre'			=> $producto->nombre,
															'consignacion'		=> $consignacion[0],
															'venta'				=> $venta[0],
															'cantidad'			=> $cantidadProductos,
															'total'				=> $totalProductos,
															'precioConsignacion'=> $producto->precioVenta
															);
			}
		}

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu', $data );
		$this->load->view( 'resumen_local' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function local()
	{	
		$data = array();

		if ($this->modo == 'stock')
		{
			$data['menu_header_seleccionado']	= 'stock';
			$data['menu_seleccionado']			= 'local';
		}
		else
		{
			$data['menu_header_seleccionado']	= 'consignacion';
			$data['menu_seleccionado']			= 'resumen';
		}

		$data['locales'] = array();

		$localModel	= new Local_model();
		$stockModel	= new Stock_model();
		$reciboModel= new Recibo_model();

		$data['totales']['consignacion']['cantidad']	= 0;
		$data['totales']['venta']['cantidad']			= 0;
		$data['totales']['totales']['cantidad']			= 0;

		$data['totales']['consignacion']['total']		= 0;
		$data['totales']['venta']['total']				= 0;
		$data['totales']['totales']['total']			= 0;

		foreach($localModel->getLocalesTodos(true, false, false) as $key => $local)
		{
			// Datos desde el modelo
			$consignacion	= $stockModel->getStockConsignadoPorLocal($local->id);
			$venta			= $stockModel->getStockVendidoPorLocal($local->id);
			$ultimoRecibo	= $reciboModel->getFechaUltimoReciboPorLocal($local->id);

			// Totales por Producto
			$cantidad	= $consignacion[0]->cantidad	+ $venta[0]->cantidad;
			$total		= $consignacion[0]->total		+ $venta[0]->total;

			// Totales
			$data['totales']['consignacion']['cantidad']	+= $consignacion[0]->cantidad;
			$data['totales']['venta']['cantidad']			+= $venta[0]->cantidad;
			$data['totales']['totales']['cantidad']			+= $cantidad;

			$data['totales']['consignacion']['total']		+= $consignacion[0]->total;
			$data['totales']['venta']['total']				+= $venta[0]->total;
			$data['totales']['totales']['total']			+= $total;
		
			$data['locales'][$local->id] = array(
														'id'				=> $local->id,
														'nombre'			=> $local->nombre,
														'consignacion'		=> $consignacion[0],
														'venta'				=> $venta[0],
														'cantidad'			=> $cantidad,
														'total'				=> $total,
														'ultima_liquidacion'=>$local->ultima_liquidacion,
														'ultimo_recibo'		=> isset($ultimoRecibo->fechaManual) ? $ultimoRecibo->fechaManual : "-",
														'situacion'			=> $localModel->calcularSituacion( $local->id )
														);
		}

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'stock')				$this->load->view( 'templates/menu_stock', $data );
        else									$this->load->view( 'templates/menu', $data );
		$this->load->view( 'stock_local' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function deposito($compraId=null, $modo='ver')
	{
		if ( $compraId = 'null' )
			$compraId = null;

		$data = array();
		$data['menu_seleccionado']			= 'deposito';
		$data['menu_header_seleccionado']	= 'oficina';

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();

		// Filtros - Productos
		$productosParaFiltro = array('Todos...');
		$todosLosProductos = $productoModel->getProductosTodos(false, true, true);
		foreach($todosLosProductos as $key => $producto)
		{
			$productosEnDeposito		= $stockModel->getStockEnDepositoPorProducto( $producto->id, $this->filtros['deposito'] );
			
			if ( $productosEnDeposito[0]->cantidad > 0  )
				$productosParaFiltro[ $producto->id ] = $producto->nombre;
		}

		// Filtros - Depósitos
		$depositoModel		= new Deposito_Model();
		$depositos			= $depositoModel->getDepositosTodos();
		$depositosParaFiltro	= array(0 => 'Todos...');
		foreach ($depositos as $deposito)
		{
			if ( isset( $this->filtros['producto'] ) && $this->filtros['producto'] )
			{
				$productosEnDeposito		= $stockModel->getStockEnDepositoPorProducto( $this->filtros['producto'], $deposito->id );
				
				if ( $productosEnDeposito[0]->cantidad == 0 )
				{
					continue;
				}
			}
		
			$depositosParaFiltro[$deposito->id]		= $deposito->nombre;
		}


		$data['compraId']	= $compraId;
		$data['modo']		= $modo;

		if (is_numeric( $compraId ))
		{
			$compraModel = new Compra_model();
			$compra = $compraModel->getCompra ( $compraId );
			$data['compraObj']	= $compra;
		}
	
	    $crud = new grocery_CRUD();

		// Where
		$where  = 'produccion_id IS NULL AND consignacion_id IS NULL AND venta_oficina_id IS NULL AND devolucion IS NULL AND movimientos_productos.status = 1';
		$where .= (is_numeric( $compraId )) ? ' AND compra_id = ' . $compraId : '';
		
		if ( is_array( $this->filtros ) )
		{
			$where .= ( isset($this->filtros['producto']) && $this->filtros['producto'] ) ? ' AND producto_id = ' . $this->filtros['producto'] : '';
			$where .= ( isset($this->filtros['deposito']) && $this->filtros['deposito'] ) ? ' AND deposito_id = ' . $this->filtros['deposito'] : '';
		}
		$crud->where($where);

        $crud->set_table( 'movimientos_productos' );

        $crud	->set_relation( 'producto_id', 'producto', 'nombre')
        		->set_relation( 'deposito_id', 'deposito', '{nombre}')
        ;

		$crud->order_by('producto_id');

		if ($modo == 'editar')
		{
			$crud->columns( 'producto_id', 'compra_id', 'producto_precioCompra', 'acciones');
			
			$movimientosProductosModel	= new MovimientosProductos_model();
			$diferenciaPreciosCompra	= $movimientosProductosModel->movimientosCambioPrecioCompra();
			$totalDifPrecioCompra		= $movimientosProductosModel->totalMovimientosCambioPrecioCompra();
			
			flash_alert('Estás editando los preios de compra. La diferencia es de $<span id="dif_precio_compra">' . currency_format( $totalDifPrecioCompra->total - $totalDifPrecioCompra->totalOriginal ) . '</span>. <a href="' . base_url('index.php/stock/guardarPreciosCompra') . '">Guardar los cambios</a>');
		}
        else						$crud->columns( 'id', 'producto_id', 'compra_id', 'deposito_id', 'producto_precioCompra');
		
        $crud	->display_as( 'producto_id','Producto' )
        		->display_as( 'producto_precioCompra', 'Precio Compra' )
        		->display_as( 'compra_id', 'Compra' )
        		->display_as( 'deposito_id', 'Depósito' )
        ;

		$crud->callback_column( 'acciones',				array($this,'accionesColumn') );
		$crud->callback_column( 'compra_id',			array($this,'consignacionColumn') );
		$crud->callback_column( 'producto_precioCompra',array($this,'currencyColumn') );
		
		if ( $modo == 'editar' )		$crud->callback_column( 'producto_precioCompra',array($this,'_callback_EditarPrecio') );
		else							$crud->callback_column( 'producto_precioCompra',array($this,'_callback_verPrecio') );

        $crud->unset_delete();
       	$crud->unset_add();
        $crud->unset_edit();

        // render crud
        $data['crud'] = $crud->render();
        
        // Para el view
        $data['depositosParaFiltro']	= $depositosParaFiltro;
        $data['productosParaFiltro']	= $productosParaFiltro;
            
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data );
		$this->load->view( 'stock_deposito' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function _callback_EditarPrecio( $value, $row  )
	{
		$precio		= ( $row->cambio_precioCompra ) ? $row->cambio_precioCompra : $row->producto_precioCompra;
		$bgColor	= ( $row->cambio_precioCompra ) ? '#ccffcc' : '#fff';
	
		return '<div class="currency-right"><input type="text" value="' . $precio . '" maxlength="10" size="10" class="pcioUnit numeric guardarPrecioCompra" style="text-align:right; background-color:' . $bgColor . '" autocomplete="off" movid="' . $row->id .'"/></div>';
	}
	
	public function _callback_verPrecio( $value, $row )
	{
		$html = '<div class="currency-right">';
		
		if ($row->cambio_precioCompra)
		{
			$html .= '<span>' . currency_format( $value ) . '</span> ';// . currency_format( $row->cambio_precioCompra );
		}
		else
		{
			$html .= currency_format( $value );
		}
		
		 $html .= '</div>';
		 
		 return $html;
	}
	
	public function cambiarPrecioCompra($idMov, $precio)
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		$movimientoObj = $movimientosProductosModel->getMovimiento( $idMov );
		
		if ( $movimientoObj->producto_precioCompra != $precio || $movimientoObj->cambio_precioCompra )
		{		
			$movimientosProductosModel->cambiarPrecioCompra( $idMov, $precio );
		
			$totalDifPrecioCompra		= $movimientosProductosModel->totalMovimientosCambioPrecioCompra();
			
			echo currency_format( $totalDifPrecioCompra->total - $totalDifPrecioCompra->totalOriginal );
		}
		else
		{
			echo 'false';
		}
	}

	public function consignacionColumn( $value, $row )
	{
		if (!$row->compra_id)
		{
			return "-";
		}
		else
		{
			$compraModel = new Compra_model();
			$compra = $compraModel->getCompra ( $row->compra_id );
		
			return '<a href="' . base_url('index.php/compras/verCompra/' . $row->compra_id) . '">' . addZeros( $compra->facturaNumero ) . '</a> - <small>' . fechaToText( $compra->fechaManual ) . '</small>';
		}
	}

	public function accionesColumn( $value, $row )
	{
		$return  = '<div class="currency-right" style="position:relative;top:13px"><a href="javascript: eliminar(' . $row->id . ')" class="custom_action">Eliminar del Stock</a></div>';
		
		return $return;
	}
	
	public function eliminar( $movimientoId, $compraId=null )
	{
		if ($compraId)	$compraId = $compraId;
		else			$compraId = '';
	
		$movimientosProductosModel = new MovimientosProductos_model();
		
		if ($movimientosProductosModel->eliminar($movimientoId))
		{
			redirect(base_url( 'index.php/stock/deposito/' . $compraId ), 'location');
		}
	}

	public function ventasPorCanal()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'ventas';

		$data['productos'] = array();

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();
		$venta_directaModel	= new Ventadirecta_model();

		$data['totales']['oficina']['cantidad']			= 0;
		$data['totales']['venta']['cantidad']			= 0;
		$data['totales']['venta_directa']['cantidad']	= 0;
		$data['totales']['total']['cantidad']			= 0;

		$data['totales']['oficina']['total']			= 0;
		$data['totales']['venta']['total']				= 0;
		$data['totales']['venta_directa']['total']		= 0;
		$data['totales']['total']['total']				= 0;

		foreach($productoModel->getProductosTodos(false, true, true) as $key => $producto)
		{
			// Datos desde el modelo
			$ventaOficina	= $stockModel->getStockVentaOficinaPorProducto($producto->id);
			$venta			= $stockModel->getStockVendidoConsignacionPorProducto($producto->id);
			$venta_directa	= $venta_directaModel->getStockVentaDirectaPorProducto($producto->id);

			// Total Ventas
			$cantidadVentas	= $venta[0]->cantidad + $ventaOficina[0]->cantidad + $venta_directa[0]->cantidad;
			$totalVentas	= $venta[0]->total + $ventaOficina[0]->total + $venta_directa[0]->total;

			// Totales
			$data['totales']['venta']['cantidad']			+= $venta[0]->cantidad;
			$data['totales']['oficina']['cantidad']			+= $ventaOficina[0]->cantidad;
			$data['totales']['venta_directa']['cantidad']	+= $venta_directa[0]->cantidad;
			$data['totales']['total']['cantidad']			+= $cantidadVentas;

			$data['totales']['oficina']['total']			+= $ventaOficina[0]->total;
			$data['totales']['venta']['total']				+= $venta[0]->total;
			$data['totales']['venta_directa']['total']		+= $venta_directa[0]->total;
			$data['totales']['total']['total']				+= $totalVentas;
		
			$data['productos'][$producto->id] = array(
														'id'			=> $producto->id,
														'nombre'		=> $producto->nombre,
														'venta'			=> $venta[0],
														'oficina'		=> $ventaOficina[0],
														'venta_directa'	=> $venta_directa[0],
														'cantidadVentas'=> $cantidadVentas,
														'totalVentas'	=> $totalVentas
														);
		}

        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'ventas_canal' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
	
	public function editarComentarioMovProd($id, $comentario)
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		
		echo $movimientosProductosModel->editarComentario($id, $comentario);
	}
}