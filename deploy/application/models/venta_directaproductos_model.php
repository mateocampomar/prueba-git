<?php
class Venta_directaProductos_model extends My_Model {

	public function getProductosPorVenta($id)
	{
		$this->db->from('venta_directa_productos');
		
		// where
		$this->db->where('ventas_oficina_id', $id);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertVentaDirectaProducto($id, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'venta_directa_id'	=> $id,
			'producto_id'		=> $productoId,
			'cantidad'			=> $cantidad,
			'producto_precio'	=> $productoPrecio
		);
		
		return $this->db->insert('ventas_directa_productos', $data);
	}
}