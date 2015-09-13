<?php
class Compra_model extends My_Model {
	
	public function nuevaCompra($fechaManual, $facturaNumero, $localId, $depositoId, $detalle)
	{
		$data = array(
			'fechaManual'	=> datepicker_to_mysql( $fechaManual ),
			'facturaNumero'	=> $facturaNumero,
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'status'		=> 1,
			'local_id'		=> $localId,
			'deposito_id'	=> $depositoId,
			'detalle'		=> $detalle
		);
		
		$this->db->insert('compra', $data);
		
		return $this->db->insert_id();
	}
	
	public function updateCompraTotal($compraId, $totalCompra, $cantidadCompra)
	{
		$iva = calcularIVA($totalCompra);
	
		$data = array(
			'total'			=> $totalCompra,
			'cantidad'		=> $cantidadCompra,
			'iva'			=> $iva['iva'],
			'total_iva_inc'	=> $iva['total_iva_inc']
		);
		
		$this->db->where('id', $compraId);

		if ( $this->db->update('compra', $data) )
		{
			$cuentaModel	= new cuenta_model();
			$compraObj		= $this->getCompra($compraId);
			
			return $cuentaModel->nuevoRegistro('compra', $compraObj);
		}
	}

	public function getCompra($compraId)
	{
		$this->db->select('compra.*, local.*, compra.detalle as detalle_compra, compra.id as id_compra, compra.status as status_compra, deposito.nombre as deposito_nombre');
	
		$this->db->from('compra');

		$this->db->join('local', 'local.id = compra.local_id');
		$this->db->join('deposito', 'deposito.id = compra.deposito_id');

		$this->db->where('compra.id = ' . $compraId);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}

	public function anularCompra($compraId)
	{
		// Edito el estado del recibo.
		$data = array(
			'status'	=> 0,
		);
		$this->db->where('id', $compraId);

		if ( $this->db->update('compra', $data) )
		{
			$cuentaModel	= new cuenta_model();
			$compraObj		= $this->getCompra($compraId);
			
			return $cuentaModel->nuevoRegistro('anula compra', $compraObj);
		}
	}
}