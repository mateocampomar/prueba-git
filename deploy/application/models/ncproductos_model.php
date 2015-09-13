<?php
class Ncproductos_model extends My_Model {

	public function getProductosPorNc($ncId)
	{
		$this->db->from('nc_productos');
		
		// where
		$this->db->where('nc_id', $ncId);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function insertNcProducto($ncId, $productoId, $cantidad, $productoPrecio)
	{
		$data = array(
			'nc_id'				=> $ncId,
			'producto_id'		=> $productoId,
			'producto_precio'	=> $productoPrecio,
			'cantidad'			=> $cantidad
		);
		
		return $this->db->insert('nc_productos', $data);
	}
}