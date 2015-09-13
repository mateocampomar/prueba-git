<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Local extends My_Controller {

	public function index()
	{

	}

	public function cambiarSession( $localId=null )
	{
		if ( $localId === null )	$localId = $this->input->post('local');
	
		if ($this->session->userdata('modo') == 'consignacion')
		{
			// Modo Consignación
		
			$this->session->set_userdata( 'local', $localId );
			
			// Reseteo los filtros al cambiar de local.
			$this->resetFiltros( 'consignacion/index', false );

			if ( $localId )
			{
				redirect(base_url( 'index.php/stock/resumen_local'), 'location');
			}
			else
			{
				redirect(base_url( 'index.php/stock/local'), 'location');
			}
		}
		else
		{
			// Modo Proveedores
		
			$this->session->set_userdata( 'local_proveedor', $localId );
	
			if ( $localId )
			{
				redirect(base_url( 'index.php/compras'), 'location');
			}
			else
			{
				redirect(base_url( 'index.php/compras'), 'location');
			}
		}
	}
	
	public function situacion()
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
		
		$stockModel	= new Stock_model();

		// Chart Header
		$chartHeader = "['Estado', 'Cantidad']";
		
		$local = ($this->local) ? $this->local : null;
		
		$consignacion	= $stockModel->getStockConsignadoPorLocal( $local );
		$venta			= $stockModel->getStockVendidoPorLocal( $local );

		// Chart Body
		$chartText = "";
		$chartText .= "\t['Consignado',\t" . $consignacion[0]->total . "],\n";
		$chartText .= "\t['Ventas',\t" . (( $venta[0]->total ) ? $venta[0]->total : 0 ) . "],\n";

		// Chart Body
		$chartText2 = "";
		$chartText2 .= "\t['Consignado',\t" . $consignacion[0]->cantidad . "],\n";
		$chartText2 .= "\t['Ventas',\t" . (( $venta[0]->cantidad ) ? $venta[0]->cantidad : 0 ) . "],\n";


		$data['chartHeader']	= $chartHeader;
		$data['chartText']		= $chartText;
		$data['chartText2']		= $chartText2;

        $this->load->view( 'templates/head', $data );
        if ($this->modo == 'stock')				$this->load->view( 'templates/menu_stock', $data );
        else									$this->load->view( 'templates/menu', $data );
		$this->load->view( 'situacion' , $data );
		$this->load->view( 'templates/footer' , $data );
		
	}
}