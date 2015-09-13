<?php
class Venta_oficinaProductos_model extends My_Model {

	public function getProductosPorVenta($id)
	{
		$this->db->from('ventas_oficina_productos');
		
		// where
		$this->db->where('venta_oficina_id', $id);
		
		// Join
		$this->db->join('producto', 'producto.id = ventas_oficina_productos.producto_id');

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertVentaOficinaProducto($id, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'venta_oficina_id'	=> $id,
			'producto_id'		=> $productoId,
			'cantidad'			=> $cantidad,
			'producto_precio'	=> $productoPrecio
		);
		
		return $this->db->insert('ventas_oficina_productos', $data);
	}
}