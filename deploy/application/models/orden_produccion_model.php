<?php
class Orden_produccion_model extends My_Model {
	
	public function nuevaOP($fechaManual, $detalle, $cantidad, $productoDestinoId, $depositoId)
	{
		$data = array(
			'fechaManual'		=> datepicker_to_mysql( $fechaManual ),
			'detalle'			=> $detalle,
			'fechaAuto'			=> date( 'Y-m-d H:i:s' ),
			'cantidad'			=> $cantidad,
			'status'			=> 1,
			'productoDestino_id'=> $productoDestinoId,
			'deposito_id'		=> $depositoId
		);
		
		$this->db->insert('orden_produccion', $data);
		
		return $this->db->insert_id();
	}

	public function getOP($opId)
	{
		$this->db->select(' orden_produccion.*, deposito.nombre as deposito_nombre, producto.nombre as producto_nombre');
	
		$this->db->from('orden_produccion');

		$this->db->join('producto', 'producto.id = orden_produccion.productoDestino_id');
		$this->db->join('deposito', 'deposito.id = orden_produccion.deposito_id', 'left');

		$this->db->where('orden_produccion.id = ' . $opId);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}
}