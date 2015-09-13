<?php
class Producto_model extends My_Model {

	protected $local;
	protected $precioCompra			= true;
	protected $precioConsignacion	= true;
	protected $precioOficina		= true;
	protected $precioWeb			= true;
	protected $status				= false;
	protected $tipo					= null;
	protected $categoria			= null;
	protected $statusWeb			= null;

	public function getProductosTodos($compra=true, $venta=true, $oficina=true, $local_id = null)
	{
		// Where Or
		$where = array();
		if ($compra)					$where[] = 'precio IS NOT NULL';
		if ($venta)						$where[] = 'precioVenta IS NOT NULL';
		if ($oficina)					$where[] = 'precioOficina IS NOT NULL';
		if ($this->precioWeb)			$where[] = 'precioWeb IS NOT NULL';
		if ($this->status !== false)	$where[] = 'status = ' . $this->status;
		if ($this->statusWeb !== null)	$where[] = 'statusWeb = ' . $this->statusWeb;
		
		if ( count($where) )
		{
			$precioWhere = '(' . implode(" OR ", $where) . ')';
			
			$localWhere	= ($local_id) ? ' AND proveedor_id = ' . $local_id : '';
			
			$this->db->where( $precioWhere . $localWhere );
		}
		
		// Where And
		if ( $this->tipo		!== null )	$this->db->where( 'producto_tipo',		$this->tipo );
		if ( $this->statusWeb	!== null )	$this->db->where( 'statusWeb',			$this->statusWeb );
		if ( $this->categoria	!== null )	$this->db->where( 'producto_categoria',	$this->categoria );
	
		$this->db->from('producto');

		$this->db->order_by('nombre');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getProducto($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);

		$query = $this->db->get('producto');
		
		$return = $query->result();
		
		return $return[0];
	}
	
	public function setPrecioWeb($bool)
	{
		$this->precioWeb = ($bool) ? true : false;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function setTipo( $tipo )
	{
		$this->tipo = $tipo;
	}

	public function setCategoria( $categoria )
	{
		$this->categoria = $categoria;
	}

	public function setStatusWeb( $status )
	{
		$this->statusWeb = $status;
	}
}