<?php
class Nc_model extends My_Model {
	
	public function nuevaNc($localId, $ncNumero, $fechaManual, $depositoId, $detalle, $total)
	{

		$data = array(
			'fechaManual'	=> $fechaManual,
			'fechaAuto'		=> date( 'Y-m-d H:i:s' ),
			'ncNumero'		=> $ncNumero,
			'total'			=> $total,
			'detalle'		=> $detalle,
			'local_id'		=> $localId,
			'deposito_id'	=> ($depositoId) ? $depositoId : NULL,
			'status'		=> 1
		);
		
		$this->db->insert('nota_de_credito', $data);
		
		$insertId = $this->db->insert_id();
		
		if ( $insertId )
		{
			$cuentaModel	= new cuenta_model();
			$ncObj			= $this->getNc($insertId);
			
			if ( $cuentaModel->nuevoRegistro('nc', $ncObj) )
			{
				return $insertId;
			}
		}
	}

	public function getNc($ncId)
	{
		$this->db->select(' nota_de_credito.*, local.nombre as localNombre, deposito.nombre as deposito_nombre');

		$this->db->from('nota_de_credito');
		
		$this->db->join('local', 	'local.id = nota_de_credito.local_id');
		$this->db->join('deposito', 'deposito.id = nota_de_credito.deposito_id', 'left');

		$this->db->where('nota_de_credito.id = ' . $ncId);

		$query = $this->db->get();
		
		$return = $query->result();
		
		return $return[0];
	}

	public function totalNc($localId=false)
	{
		$this->db->select('SUM(total) as totalNc');
	
		$this->db->from('nota_de_credito');
	
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

	public function anularNc( $ncId )
	{
		// Chequeo si la NC está efectivamente anulada.
		
		// Validacion de que los productos están en stock.
		$stockOk					= true;
	
		$movimientosProductosModel		= new MovimientosProductos_model();
		$ncProductosModel				= new NcProductos_model();


		$productosDeLaNc = $ncProductosModel->getProductosPorNc( $ncId );
		
		// Si la nota de crédito tiene productos es porque fue hecha para un local en consignación.
		if ( count( $productosDeLaNc ) )
		{
			$todosLosProductos	= $movimientosProductosModel->getMovimientosPorNc( $ncId );
			$ncObj				= $this->getNc( $ncId );

			// Chequeo de Stock.
			foreach( $productosDeLaNc as $key => $producto)
			{
				$movimientosConsignablesPorProducto = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->producto_id, $ncObj->deposito_id );
				$movimientosConsignablesPorProducto = $movimientosConsignablesPorProducto[0];
				$movimientosProductosEnStock  = $movimientosConsignablesPorProducto->cantidad;

				if ( $producto->cantidad > $movimientosProductosEnStock )
				{
					flash_alert('Hay por lo menos un producto que supera el stock.');
					$stockOk = false;
				}
			}

			// Movimientos en stock
			if ( $stockOk )
			{
				foreach($todosLosProductos as $key => $producto)
				{
					$movimientosProductosModel->consignarMovimientos( $producto->producto_id, $producto->consignacion_id, $ncObj->local_id, $producto->consignacionPrecio, $ncObj->deposito_id );
					
					$movimientosProductosModel->deleteMovimiento( $producto->id );
				}
			}
		}
	
	
		if ( $stockOk )
		{
			// Edito el estado del recibo.
			$data = array(
				'status'	=> 0,
			);
	
			$this->db->where('id', $ncId);
	
			if ( $this->db->update('nota_de_credito', $data) )
			{
				$cuentaModel	= new cuenta_model();
				$ncObj			= $this->getNc($ncId);
				
				return $cuentaModel->nuevoRegistro('anula nc', $ncObj);
			}
		}
		else
		{
			return false;
		}
	}
	
	public function proximaNc()
	{
		
	}
}