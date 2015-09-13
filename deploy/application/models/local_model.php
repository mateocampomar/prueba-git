<?php
class Local_model extends My_Model {

	public function getLocalesTodos($consignacion=true, $proveedor=false, $statusFilter=true)
	{
		$this->db->from('local');
		
		if ( $statusFilter )
			$this->db->where('status', 1);
		
		if ($consignacion || $proveedor)
		{
			
			if ($consignacion)
			{
				$this->db->where('consignacion', 1);
			}
			
			if ($proveedor)
			{
				$this->db->where('proveedor', 1);
			}
		}
		
		$this->db->order_by('nombre', 'asc');

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function getLocal($localId)
	{
		$this->db->from('local');
		
		$this->db->where('id = ' . $localId );

		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}
	
	public function guardarLiquidacion($date, $localId)
	{
		$data = array(
			'ultima_liquidacion'			=> $date,
		);
		
		$this->db->where('id', $localId);

		return $this->db->update('local', $data);
	}

	public function calcularSituacion( $localId )
	{
		$stockModel			= new Stock_model();
		$consignacionModel	= new Consignacion_model();
		
		$dateNow = new DateTime('now');

		
		// Validar si el local en algún momento vendió algo.
		$ventaHistorica			= $stockModel->getStockVendidoPorLocal( $localId, false );
		
		if ( !$ventaHistorica[0]->cantidad )
			return null;


		// Consignaciones
		$consignacion	= $stockModel->getStockConsignadoPorLocal( $localId );
		if ( !$consignacion[0]->cantidad )
			return 0;


		// Ventas último año
		$date = new DateTime('now');
		$date->modify('-1 year');
		$fechaVentasDesde = $date->format('Y-m-d');

		$venta			= $stockModel->getStockVendidoPorLocal( $localId, $fechaVentasDesde );

		if ( !$venta[0]->cantidad )
			return false;	


		// Verificar cuando fue la primer consignación.
		$primeraConsignacion = $consignacionModel->getPrimeraConsignacionPorLocal( $localId );
		$datePrimeraConsignacion  = new DateTime( $primeraConsignacion[0]->fechaAuto );
		
		$dDiff = $dateNow->diff( $datePrimeraConsignacion );

		$diasDeConsignacion = ( $dDiff->days > 365 ) ? 365 : $dDiff->days;

		//echo $consignacion[0]->total * 1.22 . " / ( " . $venta[0]->total . " / " . $diasDeConsignacion . " * 365 )<br/>";

		// Ventas en el último año.
		$return = $consignacion[0]->total / ( $venta[0]->total / $diasDeConsignacion * 365 ) * 1;
		
		return $return * 10;
	}
}