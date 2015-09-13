<?php
class Ventadirecta_model extends My_Model {

	public function getVentasTodos()
	{
		$this->db->from('ventas_directa');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getVenta($ventaId)
	{
		$this->db->from('ventas_directa');
		
		// where
		$this->db->where( 'id', $ventaId );

		$query = $this->db->get();
		
		$return = $query->result();
		
		if (isset($return[0]))
		{
			return $return[0];
		}
		else
		{
			return false;
		}
	}
	
	public function nuevaVenta($cliente, $facturaNumero, $fechaManual, $detalle)
	{
		$data = array(
			'fechaManual'	=> datepicker_to_mysql( $fechaManual ),
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'facturaNumero'	=> $facturaNumero,
			'cliente'		=> $cliente,
			'detalle'		=> $detalle,
			'status'		=> 1
		);
		
		$this->db->insert('ventas_directa', $data);
		
		return $this->db->insert_id();
	}

	public function updateTotal($id, $total, $cantidad)
	{
		$iva = calcularIVA($total);
	
		$data = array(
			'total'			=> $total,
			'cantidad'		=> $cantidad,
			'iva'			=> $iva['iva'],
			'total_iva_inc'	=> $iva['total_iva_inc']
		);
		
		$this->db->where('id', $id);

		if ( $this->db->update('ventas_directa', $data) )
		{
			$cuentaModel		= new cuenta_model();
			$ventaDirectaObj	= $this->getVenta($id);
			
			return $cuentaModel->nuevoRegistro('ventaDirecta', $ventaDirectaObj);
		}
	}

	public function anular($id)
	{
		// Edito el estado del recibo.
		$data = array(
			'status'	=> 0,
		);
		$this->db->where('id', $id);

		if ( $this->db->update('ventas_directa', $data) )
		{
			$cuentaModel		= new cuenta_model();
			$ventaDirectaObj	= $this->getVenta($id);
			
			return $cuentaModel->nuevoRegistro('anula ventaDirecta', $ventaDirectaObj);
		}
	}
	
	public function getStockVentaDirectaPorProducto($productoId, $fechaDesde = null )
	{
		if ( $fechaDesde === null )
			$fechaDesde = cierreDeEjercicio();

		$this->db->select('sum(ventas_directa_productos.cantidad) as cantidad, SUM(ventas_directa_productos.producto_precio * ventas_directa_productos.cantidad) as total');		
		
		$this->db->join('ventas_directa', 'ventas_directa.id = ventas_directa_productos.venta_directa_id');

		$this->db->from('ventas_directa_productos');

		$where = 'producto_id = ' . $productoId . ' AND ventas_directa.status = 1';
		if ( $fechaDesde )
		{
			$where .= " AND ventas_directa.fechaManual >= '" . $fechaDesde . "'";
		}
		$this->db->where( $where );

		$query = $this->db->get();
		
		//echo $this->db->last_query();
		
		return $query->result();
	}
}