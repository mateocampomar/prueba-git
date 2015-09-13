<?php
class producto_categoria_model extends My_Model {

	protected $status		= 1;
	protected $unique_name	= null;
	protected $id			= null;

	public function getTodos( )
	{
		$where = array();
		
		if ($this->status)			$where[] = '`status` = ' . $this->status;
		if ($this->id)				$where[] = '`id` = ' . $this->id;
		if ($this->unique_name)		$where[] = '`unique_name` = \'' . $this->unique_name . '\'';
	
		$this->db->select('*, (SELECT MIN(producto.precioWeb) FROM (producto) WHERE producto.producto_categoria = producto_categoria.id) as min_precioWeb');
	
		$this->db->from('producto_categoria');
		
		$this->db->where( implode(' AND ', $where) );

		$this->db->order_by('nombre');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getOne()
	{
		$result = $this->getTodos();
		
		return $result[0];
	}

	public function setUnique_name($unique_name)
	{
		$this->unique_name = $unique_name;
	}

	public function setId($id)
	{
		$this->id = $id;
	}
}