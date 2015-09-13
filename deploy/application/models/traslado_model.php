<?php
class Traslado_model extends My_Model {

	public function getTrasladosTodos()
	{
		$this->db->from('traslado');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getTraslado( $trasladoId )
	{
		$this->db->select(' traslado.*, deposito_origen.nombre as deposito_origen_nombre, deposito_destino.nombre as deposito_destino_nombre' );
	
		$this->db->from('traslado');
		
		$this->db->join('deposito as deposito_origen',		'deposito_origen.id = traslado.depositoOrigen', 'left');
		$this->db->join('deposito as deposito_destino',		'deposito_destino.id = traslado.depositoDestino', 'left');
		
		// where
		$this->db->where( 'traslado.id', $trasladoId );

		$query = $this->db->get();
		
		$return = $query->result();
		
		if (isset($return[0]))
		{
			return $return[0];
		}
		else
		{
			return false;
		}
	}
	
	public function nuevoTraslado( $fechaManual, $depositoOrigen, $depositoDestino, $cantidad, $detalle='' )
	{
		$data = array(
			'fechaManual'		=> datepicker_to_mysql( $fechaManual ),
			'fechaAuto'			=> date( 'Y-m-d H:i:s' ),
			'depositoOrigen'	=> $depositoOrigen,
			'depositoDestino'	=> $depositoDestino,
			'status'			=> 1,
			'detalle'			=> $detalle,
			'cantidad'			=> $cantidad,
		);
		
		$this->db->insert('traslado', $data);
		
		return $this->db->insert_id();
	}
}