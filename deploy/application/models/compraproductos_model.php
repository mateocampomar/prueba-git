<?php
class CompraProductos_model extends My_Model {

	public function getProductosPorCompra($compraId)
	{
		$this->db->from('compra_productos');
		
		// where
		$this->db->where('compra_id', $compraId);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertCompraProducto($compraId, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'compra_id'			=> $compraId,
			'producto_id'		=> $productoId,
			'cantidad'			=> $cantidad,
			'producto_precio'	=> $productoPrecio
		);
		
		return $this->db->insert('compra_productos', $data);
	}
}