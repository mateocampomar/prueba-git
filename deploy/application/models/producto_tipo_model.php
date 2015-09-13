<?php
class producto_tipo_model extends My_Model {

	protected $status		= 1;
	protected $categoria	= null;
	protected $id			= null;
	protected $unique_name	= null;

	public function getTodos()
	{
		$where = array();
		
		if ($this->status)		$where[] = 'status = ' . $this->status;
		if ($this->id)			$where[] = 'id = ' . $this->id;
		if ($this->unique_name)	$where[] = 'unique_name = ' . $this->unique_name;
		if ($this->categoria)	$where[] = '`id` IN(SELECT  `producto_tipo` FROM `producto` WHERE `producto_categoria` = ' . $this->categoria . ' AND `precioWeb` IS NOT NULL)';
	
		$this->db->select('*, (SELECT MIN(producto.precioWeb) FROM (producto) WHERE producto.producto_tipo = producto_tipo.id) as min_precioWeb');
	
		$this->db->from('producto_tipo');

		$this->db->where( implode(' AND ', $where) );

		$this->db->order_by('nombre');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getOne( $id )
	{
		$this->setId($id);
	
		$result = $this->getTodos();
		
		return $result[0];
	}
	
	public function setCategoria($id)
	{
		$this->categoria = $id;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}

	public function setUnique_name($unique_name)
	{
		$this->unique_name = $unique_name;
	}
}