<?php
class Oficina_model extends My_Model {

	protected $estado_web	= NULL;


	public function getVentasTodos()
	{
		$this->db->from('ventas_oficina');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getVenta($ventaId)
	{
		$this->db->select(' ventas_oficina.*, deposito.nombre as deposito_nombre');
	
		$this->db->from('ventas_oficina');
		
		$this->db->join('deposito', 'deposito.id = ventas_oficina.deposito_id', 'left');
		
		// where
		$this->db->where( 'ventas_oficina.id', $ventaId );

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
	
	public function nuevaVenta($cliente, $facturaNumero, $fechaManual, $detalle='', $tipoVenta='', $rut='', $direccion='')
	{
		$data = array(
			'fechaManual'	=> datepicker_to_mysql( $fechaManual ),
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'facturaNumero'	=> $facturaNumero,
			'cliente'		=> $cliente,
			'rut'			=> $rut,
			'direccion'		=> $direccion,
			'status'		=> 2,
			'detalle'		=> $detalle,
			'tipo_venta'	=> $tipoVenta,
			'estado_web'	=> $this->estado_web
		);
		
		$this->db->insert('ventas_oficina', $data);
		
		return $this->db->insert_id();
	}

	public function updateTotal($id, $total, $cantidad, $depositoId)
	{
		$iva = calcularIVA($total);
		
		$ventaObj = $this->getVenta($id);
		
		//print_r($ventaObj);
	
		$data = array(
			'total'			=> $total,
			'deposito_id'	=> $depositoId,
			'cantidad'		=> $cantidad,
			'iva'			=> ($ventaObj->tipo_venta != 'web') ? $iva['iva'] : 0,
			'total_iva_inc'	=> ($ventaObj->tipo_venta != 'web') ? $iva['total_iva_inc'] : $total
		);
		
		$this->db->where('id', $id);

		if ( $this->db->update('ventas_oficina', $data) )
		{
			$cuentaModel		= new cuenta_model();
			$ventaOficinaObj	= $this->getVenta($id);
			
			return $ventaOficinaObj;
		}
	}

	public function anular($id)
	{
		// Retira recibo_id del movimiento.
		$movimientosProductosModel = new MovimientosProductos_model();
		$movimientosProductosModel->anularVentaDelMovimiento($id);
	
		// Edito el estado del recibo.
		$data = array(
			'status'	=> 0,
		);
		$this->db->where('id', $id);
		
		//flash_alert('<strong>CUIDADO:</strong> Al anular la venta no se anularon los las comisiones de los proveedores. Debe hacerse con un movimiento manual.');

		return $this->db->update('ventas_oficina', $data);
	}
	
	public function setEstadoWeb($estado)
	{
		$this->estado_web = $estado;
	}
	
	function setShipping()
	{
		return $this;
	}

	function anvanzarSent( $ventaId, $depositoId )
	{
		$ventaObj = $this->getVenta( $ventaId );
	
		$data = array(
			'estado_web'	=> 'sent',
			'deposito_id'	=> $depositoId
		);
		
		$this->db->where('id', $ventaId);

		if ( $this->db->update('ventas_oficina', $data) )
		{
			$ventaOficinaObj	= $this->getVenta( $ventaId );
			
			return $ventaOficinaObj;
		}
	}

	function anvanzarEnProceso( $ventaId )
	{
		$ventaObj = $this->getVenta( $ventaId );
	
		$data = array(
			'estado_web'	=> 'en-proceso'
		);
		
		$this->db->where('id', $ventaId);

		if ( $this->db->update('ventas_oficina', $data) )
		{
			$ventaOficinaObj	= $this->getVenta( $ventaId );
			
			return $ventaOficinaObj;
		}
	}

	function setClienteId()
	{
		return $this;
	}

	public function procesarRetiroStockVenta( $ventaId )
	{
		$data = array(
			'status'			=> 1,
			'fechaProcesado'	=> date( 'Y-m-d H:i:s' )
		);
		
		$this->db->where('id', $ventaId);

		return $this->db->update('ventas_oficina', $data);
	}

	public function updateVentaOficinaProcesar( $ventaId, $facturaNumero, $depositoId, $detalle )
	{
		$data = array(
			'facturaNumero'	=> $facturaNumero,
			'deposito_id'	=> $depositoId,
			'detalle'		=> $detalle
		);
		
		$this->db->where('id', $ventaId);

		$return = $this->db->limit(1)->update( 'ventas_oficina', $data );
		
		return $return;
	}

	public function getOficinaByNumero( $numero )
	{
		$this->db->from( 'ventas_oficina' );

		$this->db->where( 'facturaNumero = ' . $numero );

		$query = $this->db->get();
		
		$return = $query->result();
		
		return isset($return[0]);
	}

	public function editFacturaNumero( $oficinaId, $facturaNumero )
	{
		$data = array(
			'facturaNumero'	=> $facturaNumero,
		);
		
		$this->db->where( 'id', $oficinaId );

		return $this->db->update( 'ventas_oficina', $data );
	}
}