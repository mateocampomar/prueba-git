<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Charts extends My_Controller {

	public function ventasPorAno()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'ventas';
		
		$movimientosProductosModel	= new MovimientosProductos_model();

		$chartArray = array();
		$anosArray	= array();
		$anos		= array();

		// Chart Header
		$chartHeader = "['Fecha', ";
		for($y=2014; $y<=date('Y'); $y++)
		{	
			$anos[] = $y;
			$chartHeader .= "'" . $y . "',";
		}
		$chartHeader .= "]";

		// Chart Body
		$dia = strtotime("1 Jan 2012");
		for ($i=1; $i<=366; $i++)
		{
			foreach($anos as $inx => $y)
			{			
				$cantidad = $movimientosProductosModel->getVentasPorDia($y . "-" . date("m-d", $dia))->cantidad;

				if (!isset($anosArray[$y]))	$anosArray[$y]  = $cantidad;
				else						$anosArray[$y] += $cantidad;
			
				$chartArray[date("d-m", $dia)][$y] = $anosArray[$y];
			}
			
			$dia = strtotime('+1 Day', $dia);	
		}
		
		//print_r($chartArray);
		
		$chartText = "";
		foreach($chartArray as $inx => $val)
		{
			list($dia, $mes) = explode('-', $inx);
			
			if ( true )
			{
				if ( $dia == '01' || $dia == '15' )
				{
					// Este loop calcula los valores que deben de salir null en el gráfico.
					foreach( $val as $ano => $dump )
					{
						$diferenciaDeDias = ( strtotime( date("Y-m-d") ) - strtotime( $ano."-".$mes."-".$dia ) ) . "<br/>";
						
						if ( isset( $valoresNull ) )
						{
							$val[date('Y')] = 'null';
						}
						
						if ( $diferenciaDeDias < 0 )
						{
							$valoresNull = true;
						}
					}
				
					$chartText .= "['" . $inx . "', " . implode(",\t", $val) . "],\n";
				}
			}
			else
			{
				$chartText .= "['" . $inx . "', " . implode(",\t", $val) . "],\n";
			}
		}
	
		$data['chartHeader']	= $chartHeader;
		$data['chartText']		= $chartText;
	
		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'chart_ventasPorAno' , $data );
		$this->load->view( 'templates/footer' , $data );	
	}

	public function ventasPorProducto()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'ventas';
		$data['h1']							= 'Ventas por Producto';

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();

		// Chart Header
		$chartHeader = "['Producto', 'Ventas']";

		// Chart Body
		$chartText = "";
		$todosLosProductos = $productoModel->getProductosTodos(false, true, true);
		foreach($todosLosProductos as $key => $producto)
		{
			$venta			= $stockModel->getStockVendidoPorProducto($producto->id);
		
			$chartText .= "\t['" . $producto->nombre . "',\t" . $venta[0]->cantidad . "],\n";
		}

		$data['chartHeader']	= $chartHeader;
		$data['chartText']		= $chartText;

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'chart_ventasPorProducto' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function ventasPorProductoTipo()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'ventas';
		$data['h1']							= 'Ventas por Colección';

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();
		$productoTipoModel	= new producto_tipo_model();
		$venta_directaModel	= new Ventadirecta_model();
		
		$tipoProductos = $productoTipoModel->getTodos();
		//print_r($tipoProductos);

		// Chart Header
		$chartHeader		= "['Producto', 'Ventas']";
		$chartText			= "";
		$chartTextTotal		= "";
		$chartTextGanancia	= "";
		
		// Chart Body
		foreach( $tipoProductos as $productoTipo )
		{
			$productoModel		= new Producto_model();
			$productoModel->setTipo( $productoTipo->id );
			$todosLosProductos = $productoModel->getProductosTodos(false, true, true);

			$cantidad	= 0;
			$total		= 0;
			$ganancia	= 0;
			
			foreach($todosLosProductos as $key => $producto)
			{
				$venta			= $stockModel->getStockVendidoPorProducto( $producto->id );
				$venta_directa	= $venta_directaModel->getStockVentaDirectaPorProducto($producto->id);
				
				$cantidad	+= $venta[0]->cantidad + $venta_directa[0]->cantidad;
				$total		+= $venta[0]->total + $venta_directa[0]->total;
				$ganancia	+= $venta[0]->total - $venta[0]->totalCompra;
			}
		
			$chartText			.= "\t['" . addslashes($productoTipo->nombre) . "',\t" . $cantidad . "],\n";
			$chartTextTotal		.= "\t['" . addslashes($productoTipo->nombre) . "',\t" . $total . "],\n";
			$chartTextGanancia	.= "\t['" . addslashes($productoTipo->nombre) . "',\t" . $ganancia . "],\n";
		}

		$data['chartHeader']		= $chartHeader;
		$data['chartText']			= $chartText;
		$data['chartTextTotal']		= $chartTextTotal;
		$data['chartTextGanancia']	= $chartTextGanancia;

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'chart_ventasPorProductoTipo' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function stockPorProductoTipo()
	{
		$data = array();
		$data['menu_header_seleccionado']	= 'stock';
		$data['menu_seleccionado']			= 'stock';
		$data['h1']							= 'Stock por Colección';

		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();
		$productoTipoModel	= new producto_tipo_model();
		
		$tipoProductos = $productoTipoModel->getTodos();

		// Chart Header
		$chartHeader		= "['Producto', 'Stock']";
		$chartText			= "";
		$chartTextTotal		= "";
		
		// Chart Body
		foreach( $tipoProductos as $productoTipo )
		{
			$productoModel		= new Producto_model();
			$productoModel->setTipo( $productoTipo->id );
			$todosLosProductos = $productoModel->getProductosTodos(false, true, true);

			$cantidad	= 0;
			$total		= 0;
			
			foreach($todosLosProductos as $key => $producto)
			{
				$deposito		= $stockModel->getStockEnDepositoPorProducto( $producto->id );
				
				$cantidad	+= $deposito[0]->cantidad;
				$total		+= $deposito[0]->total;
			}
		
			$chartText		.= "\t['" . addslashes( $productoTipo->nombre ) . "',\t" . $cantidad . "],\n";
			$chartTextTotal	.= "\t['" . addslashes( $productoTipo->nombre ) . "',\t" . $total . "],\n";
		}

		$data['chartHeader']	= $chartHeader;
		$data['chartText']		= $chartText;
		$data['chartTextTotal']	= $chartTextTotal;

		$this->load->view( 'templates/head', $data );
		$this->load->view( 'templates/menu_stock' , $data );
		$this->load->view( 'chart_stockPorProductoTipo' , $data );
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
		
		$filtros = array();

		/*
		if( isset( $_POST['fecha_desde'] ))
		{
			$fechaDesde = datepicker_to_mysql( $_POST['fecha_desde'] );
			$filtros['fechaDesde']	=	$_POST['fecha_desde'];
		}
		else
		{
			$fechaDesde = false;
			$filtros['fechaDesde']	=	'';
		}
		*/

		$data['locales'] = array();
		$data['filtros'] = $filtros;

		$localModel	= new Local_model();
		$stockModel	= new Stock_model();
		$reciboModel= new Recibo_model();

		foreach($localModel->getLocalesTodos() as $key => $local)
		{
			// Datos desde el modelo
			$consignacion	= $stockModel->getStockConsignadoPorLocal($local->id);
			$venta			= $stockModel->getStockVendidoPorLocal($local->id);
		
			$data['locales'][$local->id] = array(
														'id'			=> $local->id,
														'nombre'		=> $local->nombre,
														'consignacion'	=> $consignacion[0],
														'venta'			=> $venta[0],
														'situacion'		=> $localModel->calcularSituacion( $local->id )
														);
		}

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'stock')				$this->load->view( 'templates/menu_stock', $data );
        else									$this->load->view( 'templates/menu', $data );
		$this->load->view( 'chart_local' , $data );
		$this->load->view( 'templates/footer' , $data );
	}
}