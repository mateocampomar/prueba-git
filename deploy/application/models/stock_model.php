<?php
class Stock_model extends My_Model {
	
	public function getStockEnDepositoPorProducto($productoId, $depositoId=false)
	{
		$this->db->select('count(*) as cantidad, SUM(producto_precioCompra) as total');

		$this->db->from('movimientos_productos');

		$depositoWhere = ($depositoId) ? ' AND `deposito_id` = ' . $depositoId : '';

		$this->db->where('producto_id = ' . $productoId . ' AND consignacion_id IS NULL AND status = 1 AND venta_oficina_id IS NULL AND devolucion IS NULL AND produccion_id IS NULL' . $depositoWhere);

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getStockVentaOficinaPorProducto($productoId, $fechaDesde = null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();
	
		$this->db->select('count(*) as cantidad, SUM(vendidoPrecio) as total');

		$this->db->from('movimientos_productos');

		$where = 'producto_id = ' . $productoId . ' AND consignacion_id IS NULL AND status = 1 AND venta_oficina_id IS NOT NULL';

		if ( $fechaDesde )
		{
			$where .= " AND fechaVendido >= '" . $fechaDesde . " 00:00:00'";
		}
		
		$this->db->where( $where );


		$query = $this->db->get();
		
		return $query->result();
	}

	public function getStockConsignadoPorProducto($productoId, $localId=false)
	{
		$this->db->select('count(*) as cantidad, SUM(consignacionPrecio) as total');

		$this->db->from('movimientos_productos');

		$where = 'producto_id = ' . $productoId . ' AND consignacion_id IS NOT NULL AND recibo_id IS NULL AND status = 1 AND venta_oficina_id IS NULL';
		if ($localId)
		{
			$where .= ' AND local_id = ' . $localId;
		}
		$this->db->where($where);

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getStockVendidoPorProducto($productoId, $localId=false, $fechaDesde = null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();

		$this->db->select('count(*) as cantidad, SUM(vendidoPrecio) as total, SUM(producto_precioCompra) as totalCompra');

		$this->db->from('movimientos_productos');

		$where = 'producto_id = ' . $productoId . ' AND vendidoPrecio IS NOT NULL AND status = 1';
		// (recibo_id IS NOT NULL OR venta_oficina_id IS NOT NULL)
		if ($localId)
		{
			$where .= ' AND local_id = ' . $localId;
		}
		
		if ( $fechaDesde )
		{
			$where .= " AND fechaVendido >= '" . $fechaDesde . " 00:00:00'";
		}
		
		$this->db->where($where);

		$query = $this->db->get();
		
		//echo $this->db->last_query();
		
		return $query->result();
	}

	public function getStockVendidoConsignacionPorProducto($productoId, $localId=false, $fechaDesde=null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();

		$this->db->select('count(*) as cantidad, SUM(producto_precioCompra) as total');

		$this->db->from('movimientos_productos');

		$where = 'producto_id = ' . $productoId . ' AND recibo_id IS NOT NULL AND status = 1';
		if ($localId)
		{
			$where .= ' AND local_id = ' . $localId;
		}
		
		if ( $fechaDesde )
		{
			$where .= " AND fechaVendido >= '" . $fechaDesde . " 00:00:00'";
		}
		
		$this->db->where($where);

		$query = $this->db->get();
		
		//echo $this->db->last_query();
		
		return $query->result();
	}

	public function getStockComprasPorProducto( $productoId, $fechaDesde = null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();

		$this->db->select('count(*) as cantidad, SUM(producto_precioCompra) as total');

		$this->db->from('movimientos_productos');
		
		$this->db->join('compra', 'compra.id = movimientos_productos.compra_id');

		// Where
		$where = 'producto_id = ' . $productoId . ' AND compra_id IS NOT NULL AND movimientos_productos.status = 1';		
		
		if ( $fechaDesde )
			$where .= " AND compra.fechaManual >= '" . $fechaDesde . "'";
		
		$this->db->where( $where );


		$query = $this->db->get();
		
		return $query->result();
	}

	/*
	 * Esta función valua los productos al precio de consignación.
	 */
	public function getStockConsignadoPorLocal($localId)
	{
		$this->db->select('count(*) as cantidad, SUM(consignacionPrecio) as total');

		$this->db->from('movimientos_productos');
		
									$where  = '';
		if ( $localId != null )		$where .= 'local_id = ' . $localId . ' AND ';
									$where .= 'consignacion_id IS NOT NULL AND recibo_id IS NULL AND status = 1';

		$this->db->where($where);

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getStockVendidoPorLocal($localId, $fechaDesde = null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();

		$this->db->select('count(*) as cantidad, SUM(vendidoPrecio) as total');

		$this->db->from('movimientos_productos');

									$where  = '';
		if ( $localId != null )		$where .= 'local_id = ' . $localId . ' AND ';
		if ( $fechaDesde )			$where .= "fechaVendido >= '" . $fechaDesde . " 00:00:00' AND ";
									$where .= 'recibo_id IS NOT NULL AND status = 1';

		$this->db->where($where);

		$query = $this->db->get();
		
		return $query->result();
	}
}