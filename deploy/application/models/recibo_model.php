<?php
class Recibo_model extends My_Model {

	public function getReciboTodos($localId=false)
	{
		$this->db->from('recibo');
		
		if ($localId)
		{
			$this->db->where('local_id = ' . $localId);
		}

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function totalRecibos($localId=false)
	{
		$this->db->select('SUM(total) as totalRecibos');
	
		$this->db->from('recibo');
	
		$where = 'status = 1';
		if ($localId)
		{
			$where .= ' AND local_id = ' . $localId;
		}
		$this->db->where($where);
	
		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}
	
	public function nuevoRecibo($localId, $reciboNumero, $fechaManual, $total, $detalle)
	{
		$data = array(
			'fechaManual'	=> $fechaManual,
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'reciboNumero'	=> $reciboNumero,
			'total'			=> $total,
			'local_id'		=> $localId,
			'detalle'		=> $detalle,
			'status'		=> 1
		);
		
		$this->db->insert('recibo', $data);
		
		$insertId = $this->db->insert_id();
		
		if ( $insertId )
		{
			$reciboObj		= $this->getRecibo($insertId);
			
			return $insertId;
		}
	}
	
	public function getRecibo($reciboId)
	{
		$this->db->select(' recibo.*, local.nombre as localNombre');
		
		$this->db->from('recibo');
		
		$this->db->join('local', 'local.id = recibo.local_id');
		
		$this->db->where('recibo.id = ' . $reciboId);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}

	public function getReciboByNumero($reciboNumero)
	{
		$this->db->from('recibo');
		$this->db->where('reciboNumero = ' . $reciboNumero);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return isset($return[0]);
	}

	public function editReciboNumero($reciboId, $reciboNumero)
	{
		$data = array(
			'reciboNumero'	=> $reciboNumero,
		);
		
		$this->db->where('id', $reciboId);

		return $this->db->update('recibo', $data);
	}

	public function cobrarRecibo($reciboId)
	{
		$data = array(
			'status'	=> 2,
			'fechaCobro'=> date( 'Y-m-d H:i:s' ),
		);
		
		$this->db->where('id', $reciboId);

		if ( $this->db->update('recibo', $data) )
		{
			$cuentaModel	= new cuenta_model();
			$reciboObj		= $this->getRecibo($reciboId);
			
			$cuentaModel->nuevoRegistro('cobra recibo', $reciboObj);
			
			return true;
		}
	}

	public function anularRecibo($reciboId)
	{
		// Retira recibo_id del movimiento.
		$movimientosProductosModel = new MovimientosProductos_model();
		$movimientosProductosModel->retiraReciboDelMovimiento($reciboId);
		
		$reciboOriginalObj		= $this->getRecibo($reciboId);
	
		// Edito el estado del recibo.
		$data = array(
			'status'	=> 0,
		);
		$this->db->where('id', $reciboId);

		if ( $this->db->update('recibo', $data) )
		{
		
			if ( $reciboOriginalObj->status == 2 )
			{
				$cuentaModel	= new cuenta_model();
				$reciboObj		= $this->getRecibo($reciboId);
				
				$cuentaModel->nuevoRegistro('anula recibo', $reciboObj);
			}
			
			return true;
		}
	}
	
	public function getFechaUltimoReciboPorLocal($localId)
	{
		$this->db->from('recibo');

		$this->db->where('local_id = ' . $localId);
		
		$this->db->order_by('id', 'DESC');
		
		$this->db->limit(1);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return isset($return[0]) ? $return[0] : false;
	}
}