<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposito extends My_Controller {


	public function index()
	{
		$rows	= array();
		$data	= array();

		$data['menu_header_seleccionado']	= 'oficina';
		$data['menu_seleccionado']			= 'deposito';
	
		$productoModel		= new Producto_model();
		$depositoModel		= new Deposito_Model();
		$stockModel			= new Stock_model();

		$depositos			= $depositoModel->getDepositosTodos();
		$productos			= $productoModel->getProductosTodos();
		$consignacion		= array();
		$totalesPorDeposito	= array();
		$totalesPorProducto	= array();
		$totales 			= array(
									'cantidad'	=> 0,
									'total'		=> 0
									);
		$totalConsignadas	= 0;
		$cantidadConsignadas= 0;

		foreach($productos as $key => $producto)
		{
			$rows[$producto->id] = array();
			
			$rows[$producto->id]['producto_nombre'] = $producto->nombre;
			
			$totalesPorProducto[$producto->id] = array(
														'cantidad'	=> 0,
														'total'		=> 0
														);
		
			foreach($depositos as $deposito)
			{
				if ( !isset($totalesPorDeposito[$deposito->id]) )
				{
					$totalesPorDeposito[$deposito->id]	= array(
																	'cantidad'	=> 0,
																	'total'		=> 0
																	);
				}

				$rows[$producto->id][$deposito->id] = array();
				
				$rows[$producto->id][$deposito->id]['deposito_nombre']	= $deposito->nombre;
			
				$resultado = $stockModel->getStockEnDepositoPorProducto($producto->id, $deposito->id);

				$rows[$producto->id][$deposito->id]['cantidad'] = $resultado[0]->cantidad;
				
				$totalesPorDeposito[$deposito->id]['cantidad']	+= $resultado[0]->cantidad;
				$totalesPorDeposito[$deposito->id]['total']		+= $resultado[0]->total;

				$totalesPorProducto[$producto->id]['cantidad']	+= $resultado[0]->cantidad;
				$totalesPorProducto[$producto->id]['total']		+= $resultado[0]->total;

				$totales['cantidad']							+= $resultado[0]->cantidad;
				$totales['total']								+= $resultado[0]->total;
			}
			
			$consignacion[$producto->id]	= $stockModel->getStockConsignadoPorProducto($producto->id);
			
			$totalConsignadas		+= $consignacion[$producto->id][0]->total;
			$cantidadConsignadas	+= $consignacion[$producto->id][0]->cantidad;
		}

		$data['depositos']			= $depositos;
		$data['productos']			= $productos;
		$data['totales']			= $totales;
		$data['rows']				= $rows;
		$data['totalesPorProducto']	= $totalesPorProducto;
		$data['totalesPorDeposito']	= $totalesPorDeposito;
		$data['consignacion']		= $consignacion;
		$data['totalConsignadas']	= $totalConsignadas;
		$data['cantidadConsignadas']= $cantidadConsignadas;
		
        $this->load->view( 'templates/head', $data );
        $this->load->view( 'templates/menu_oficina', $data);
		$this->load->view( 'deposito' , $data );
		$this->load->view( 'templates/footer' , $data );
	}

	public function getProductosPorDeposito($depositoId)
	{
		$productoModel		= new Producto_model();
		$stockModel			= new Stock_model();

		$productos			= $productoModel->getProductosTodos();
		
		$resultArray	= array();

		foreach($productos as $key => $producto)
		{
			$resultado = $stockModel->getStockEnDepositoPorProducto($producto->id, $depositoId);
		
			$resultArray[$producto->id] = $resultado[0];
		}
		
		echo json_encode( $resultArray );
		die;
	}

	public function setFiltrosParaDeposito()
	{
		$this->set_filtros( 'stock/deposito', $this->uri->uri_to_assoc() );
		
		redirect(base_url( 'index.php/stock/deposito' ), 'location');
	}
}