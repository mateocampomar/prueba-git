<?php
class ConsignacionProductos_model extends My_Model {

	public function getProductosPorConsignacion($consignacionId)
	{
		$this->db->from('consignacion_productos');
		
		// where
		$this->db->where('consignacion_id', $consignacionId);
		
		// Join
		$this->db->join('producto', 'producto.id = consignacion_productos.producto_id');

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertConsignacionProducto($consignacionId, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'consignacion_id'	=> $consignacionId,
			'producto_id'		=> $productoId,
			'cantidad'			=> $cantidad,
			'producto_precio'	=> $productoPrecio
		);
		
		return $this->db->insert('consignacion_productos', $data);
	}
}