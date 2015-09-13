<?php
class shoppingcart_model extends My_Model {

	protected $id				= null;
	protected $session_id		= null;
	protected $producto_id		= null;
	protected $groupByProducto	= false;
	protected $status			= 1;

	public function getTodos( )
	{
		$where = array();
		
		if ( $this->id )
		{
			$where[] = '`shopping_cart`.`id` = ' . $this->id;
		}
		else
		{
			if ($this->session_id)		$where[] = "`session_id` = '" . $this->session_id . "'";
			if ($this->producto_id)		$where[] = '`producto_id` = ' . $this->producto_id;
			if ($this->status)			$where[] = '`shopping_cart`.`status` = ' . $this->status;
		}

		$select = ',
					 producto_categoria.nombre_singular as categoria_nombre_singular,
					 producto_categoria.nombre			as categoria_nombre,
					 producto_categoria.unique_name		as categoria_unique_name,
					 producto_tipo.nombre 				as tipo_nombre,
					 producto_tipo.id 					as tipo_id,
					 producto_tipo.unique_name 			as tipo_unique_name
				';
		
		
		if ($this->groupByProducto)		$this->db->select('*, SUM(cantidad) as cantidad, AVG(shopping_cart.precio) as precio_avg, producto.precioWeb as precio_producto' . $select);
		else							$this->db->select('*' . $select);
	
		$this->db->from('shopping_cart');
		
		$this->db->join('producto', 'shopping_cart.producto_id = producto.id');
		$this->db->join('producto_tipo', 'producto_tipo.id = producto.producto_tipo');
		$this->db->join('producto_categoria', 'producto_categoria.id = producto.producto_categoria');
		
		$this->db->where( implode(' AND ', $where) );
		
		if ($this->groupByProducto)
		{
			$this->db->group_by('producto_id');
		}

		$query = $this->db->get();
		
		//echo $this->db->last_query();
		
		return $query->result();
	}

	public function getOne( $id=null )
	{
		$this->id = $id;
	
		$result = $this->getTodos();
		
		return $result[0];
	}
	
	public function getTotal()
	{
		if ($this->session_id)		$where[] = "`session_id` = '" . $this->session_id . "'";
		if ($this->status)			$where[] = '`shopping_cart`.`status` = ' . $this->status;
		
		$this->db->select('SUM( cantidad * precio) as total, SUM(cantidad) as cantidad');
	
		$this->db->from('shopping_cart');
		
		$this->db->where( implode(' AND ', $where) );

		$query = $this->db->get();
		
		$result = $query->result();
		
		return $result[0];
	}
	
	public function setSessionId($id)
	{
		return $this->session_id = $id;
	}

	public function setProducto($id)
	{
		return $this->producto_id = $id;
	}
	
	public function setGroupByProducto($bool)
	{
		if ($bool)	$this->groupByProducto = true;
		else		$this->groupByProducto = false;
	}
	
	function nuevo_registro($producto_id, $cantidad)
	{
		$productoModel = new Producto_model();
		$producto = $productoModel->getProducto($producto_id);
	
		$data = array(
			'session_id'	=> $this->session->userdata('store_id'),
			'timestamp'		=> date( 'Y-m-d H:i:s' ),
			'cantidad'		=> $cantidad,
			'producto_id'	=> $producto_id,
			'precio'		=> $producto->precioWeb,
			'status'		=> $this->status
		);
		
		$this->db->insert('shopping_cart', $data);
		
		$insertId = $this->db->insert_id();
		
		if ( $insertId )
		{	
			return $insertId;
		}
	}
	
	public function removeProducto( $producto_id )
	{
		$data = array(
			'status'		=> 0
		);
		
		if ($this->session_id)		$where[] = "`session_id` = '" . $this->session_id . "'";
									$where[] = '`producto_id` = ' . $producto_id;
									
		$this->db->where( implode(' AND ', $where) );
		
		return $this->db->update('shopping_cart', $data);
	}
}