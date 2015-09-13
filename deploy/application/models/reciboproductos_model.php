<?php
class ReciboProductos_model extends My_Model {

	public function getProductosPorRecibo($reciboId)
	{
		$this->db->from('recibo_productos');
		
		// where
		$this->db->where('recibo_id', $reciboId);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertReciboProducto($reciboId, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'recibo_id'			=> $reciboId,
			'producto_id'		=> $productoId,
			'producto_precio'	=> $productoPrecio,
			'cantidad'			=> $cantidad
		);
		
		return $this->db->insert('recibo_productos', $data);
	}
}