<?php
class Consignacion_model extends My_Model {

	public function getConsignacionTodos()
	{
		$this->db->select('*');
		
		$this->db->from('consignacion');

		$query = $this->db->get();
		
		return $query->result();
	}

	public function getConsignacion( $consignacionId )
	{
		$this->db->select(' consignacion.*, local.nombre as localNombre, deposito.nombre as deposito_nombre');
	
		$this->db->from('consignacion');
		
		$this->db->join('local', 'local.id = consignacion.local');
		$this->db->join('deposito', 'deposito.id = consignacion.deposito_id', 'left');
		
		// where
		$this->db->where( 'consignacion.id', $consignacionId );

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
	
	public function getPrimeraConsignacionPorLocal( $localId )
	{
		$this->db->select('*');
		
		$this->db->from('consignacion');
		
		$this->db->where('local', $localId );
		
		$this->db->order_by('id', 'ASC');
		
		$this->db->limit(1);

		$query = $this->db->get();
		
		return $query->result();
	}

	public function totalConsignaciones($localId=false)
	{
		$this->db->select('SUM(total_iva_inc) as totalConsignaciones');
	
		$this->db->from('consignacion');
	
		$where = 'status = 1';
		if ($localId)
		{
			$where .= ' AND local = ' . $localId;
		}
		$this->db->where($where);
	
		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];

	}
	
	public function nuevaConsignacion($local, $facturaNumero, $fechaManual, $depositoId, $detalle)
	{
		$data = array(
			'fechaManual'	=> datepicker_to_mysql( $fechaManual ),
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'facturaNumero'	=> $facturaNumero,
			'local'			=> $local,
			'deposito_id'	=> $depositoId,
			'detalle'		=> $detalle,
			'status'		=> 2
		);
		
		$this->db->insert('consignacion', $data);
		
		return $this->db->insert_id();
	}

	public function updateConsignacionTotal($consignacionId, $totalConsignacion, $cantidadConsignacion)
	{
		$iva = calcularIVA($totalConsignacion);
	
		$data = array(
			'total'			=> $totalConsignacion,
			'cantidad'		=> $cantidadConsignacion,
			'iva'			=> $iva['iva'],
			'total_iva_inc'	=> $iva['total_iva_inc']
		);
		
		$this->db->where('id', $consignacionId);

		$this->db->update('consignacion', $data);
	}


	public function updateConsignacionProcesar($consignacionId, $facturaNumero, $depositoId, $detalle)
	{
		$data = array(
			'facturaNumero'	=> $facturaNumero,
			'deposito_id'	=> $depositoId,
			'detalle'		=> $detalle
		);
		
		$this->db->where('id', $consignacionId);

		if ( $this->db->update('consignacion', $data) )
		{
			$cuentaModel		= new cuenta_model();
			$consignacionObj	= $this->getConsignacion($consignacionId);
			
			return $cuentaModel->nuevoRegistro('factura', $consignacionObj);
		}
	}

	public function getConsignacionByNumero($consignacionNumero)
	{
		$this->db->from('consignacion');
		$this->db->where('facturaNumero = ' . $consignacionNumero);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return isset($return[0]);
	}

	public function editConsignacionNumero($consignacionId, $consignacionNumero)
	{
		$data = array(
			'facturaNumero'	=> $consignacionNumero,
		);
		
		$this->db->where('id', $consignacionId);

		return $this->db->update('consignacion', $data);
	}

	public function procesarRetiroConsignacion( $consignacionId )
	{
		$data = array(
			'status'			=> 1,
			'fechaProcesado'	=> date( 'Y-m-d H:i:s' ),
		);
		
		$this->db->where('id', $consignacionId);

		return $this->db->update('consignacion', $data);
	}

	public function anularConsignacion($consignacionId)
	{
		// Retira recibo_id del movimiento.
		$movimientosProductosModel = new MovimientosProductos_model();

		$movimientosProductosModel->retiraConsignacionDelMovimiento($consignacionId);
	
		// Edito el estado del recibo.
		$data = array(
			'status'	=> 0,
		);
		$this->db->where('id', $consignacionId);
		
		if ( $this->db->update('consignacion', $data) )
		{
			$cuentaModel		= new cuenta_model();
			$consignacionObj	= $this->getConsignacion($consignacionId);
			
			return $cuentaModel->nuevoRegistro('anula factura', $consignacionObj);
		}
	}
	
	public function esAnulable($consignacionId)
	{		
		$this->db->from('movimientos_productos');

		$this->db->where('consignacion_id = ' . $consignacionId . ' AND `fechaVendido` IS NOT NULL');

		$query = $this->db->get();
		
		if ( count($query->result()) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function getProductosConsignacion( $consignacionId )
	{
		$this->db->from('consignacion_productos');

		$this->db->where('consignacion_id = ' . $consignacionId);

		$query = $this->db->get();
		
		return $query->result();
	}
}