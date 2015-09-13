<?php
class Traslado_Producto_model extends My_Model {

	public function getProductosPorTraslado( $trasladoId )
	{
		$this->db->from('traslado_productos');
		
		// Where
		$this->db->where('traslado_id', $trasladoId);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertProducto($trasladoId, $productoId, $cantidad, $registroMovimientos)
	{
		$data = array(
			'traslado_id'			=> $trasladoId,
			'producto_id'			=> $productoId,
			'cantidad'				=> $cantidad,
			'registroMovimientos'	=> $registroMovimientos
		);
		
		return $this->db->insert('traslado_productos', $data);
	}
}