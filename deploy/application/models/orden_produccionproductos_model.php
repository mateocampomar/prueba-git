<?php
class Orden_produccionProductos_model extends My_Model {

	public function getProductosPorOp( $opId )
	{
		$this->db->from('orden_produccion_productos');
		
		// where
		$this->db->where('orden_produccion_id', $opId);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertOPProducto($opId, $productoId, $cantidad, $tipo)
	{
		$data = array(
			'orden_produccion_id'	=> $opId,
			'producto_id'			=> $productoId,
			'cantidad'				=> $cantidad,
			'destinoOrigen'			=> $tipo
		);
		
		return $this->db->insert('orden_produccion_productos', $data);
	}
}