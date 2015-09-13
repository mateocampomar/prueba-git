<?php
class Cuenta_model extends My_Model {

	public function nuevoRegistro($tipoMovimiento, $Obj)
	{
		$data = array();
	
		switch($tipoMovimiento)
		{
			case 'factura':

				$data[] = array(
					'local_id'			=> $Obj->local,
					'tipo_movimiento'	=> 'factura',
					'detalle'			=> 'Factura #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> $Obj->total_iva_inc
				);
				break;

			case 'anula factura':

				$data[] = array(
					'local_id'			=> $Obj->local,
					'tipo_movimiento'	=> 'anula_factura',
					'detalle'			=> 'Anulación Factura #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> -$Obj->total_iva_inc
				);
				break;

			case 'cobra recibo':

				$data[] = array(
					'local_id'			=> $Obj->local_id,
					'tipo_movimiento'	=> 'recibo',
					'detalle'			=> 'Recibo #' . $Obj->reciboNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> -$Obj->total
				);
				break;

			case 'anula recibo':

				$data[] = array(
					'local_id'			=> $Obj->local_id,
					'tipo_movimiento'	=> 'recibo',
					'detalle'			=> 'Anulación Recibo #' . $Obj->reciboNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> $Obj->total
				);
				break;

			case 'nc':

				$data[] = array(
					'local_id'			=> $Obj->local_id,
					'tipo_movimiento'	=> 'nc',
					'detalle'			=> 'Nota de crédito #' . $Obj->ncNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> -$Obj->total
				);
				break;

			case 'anula nc':

				$data[] = array(
					'local_id'			=> $Obj->local_id,
					'tipo_movimiento'	=> 'anula_nc',
					'detalle'			=> 'Anulación Nota de crédito #' . $Obj->ncNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> $Obj->total
				);
				break;

			case 'compra':

				$data[] = array(
					'local_id'			=> 17,//$Obj->local_id,
					'tipo_movimiento'	=> 'compra',
					'detalle'			=> 'Compra #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> -$Obj->total * $this->config->item('iva')
				);
				break;

			case 'anula compra':

				$data[] = array(
					'local_id'			=> 17,//$Obj->local_id,
					'tipo_movimiento'	=> 'anula_compra',
					'detalle'			=> 'Anulación Compra #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> $Obj->total * $this->config->item('iva')
				);
				break;

			case 'ventaDirecta':

				$localModel = new Local_model();
				$localObj	= $localModel->getLocal(17);

				$data[] = array(
					'local_id'			=> 17,//,
					'tipo_movimiento'	=> 'venta_directa',
					'detalle'			=> 'Comisión por Venta Directa #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> $Obj->total * $localObj->comision
				);
				break;

			case 'anula ventaDirecta':

				$localModel = new Local_model();
				$localObj	= $localModel->getLocal(17);

				$data[] = array(
					'local_id'			=> 17,//$Obj->local_id,
					'tipo_movimiento'	=> 'anula_venta_directa',
					'detalle'			=> 'Anulación de Comisión por Venta Directa #' . $Obj->facturaNumero,
					'relacion_obj'		=> $Obj->id,
					'movimiento'		=> -$Obj->total * $localObj->comision
				);
				break;
			
			case 'oficina':

				/*
				 * DEPRECADA
				 *
				 * Esta opción no se usa en los casos que se le cobran comisiones a los proveedores. Era para el viejo Agolan pero no se usa.
				 *
				 */
				 
				die('Función deprecada en la cuenta.');

				/*
				$movimientosProductosModel	= new MovimientosProductos_model();
				$compraModel				= new Compra_model();
				$localModel					= new Local_model();

				$movimientos = $movimientosProductosModel->getMovimientosPorVentaOficina( $Obj->id );
				$comisiones = array();
				
				foreach ($movimientos as $movimiento)
				{
					if ( $movimiento->compra_id )
					{
						$compra = $compraModel->getCompra ( $movimiento->compra_id );
						
						if ( isset($comisiones[$compra->local_id]) )
							$comisiones[$compra->local_id] += $movimiento->producto_precioCompra;
						else
							$comisiones[$compra->local_id] = $movimiento->producto_precioCompra;
					}
					else
					{
						//flash_alert('<strong>CUIDADO:</strong> No se generaron las comisiones por productos que fueron producidos.');
					}
				}
				
				foreach( $comisiones as $inx => $comision )
				{
					$localObj	= $localModel->getLocal( $inx );
					
					if ( $localObj->comision )
					{
						$data[] = array(
							'local_id'			=> $inx,
							'tipo_movimiento'	=> 'venta_oficina',
							'detalle'			=> 'Comision por Venta Oficina #' . $Obj->facturaNumero,
							'relacion_obj'		=> $Obj->id,
							'movimiento'		=> -$comision * $localObj->comision
						);
					}
				}
				*/

				break;
				
			case 'ventaPorConsignacion':
				
				/*
				 * DEPRECADA
				 *
				 * Esta opción no se usa en los casos que se le cobran comisiones a los proveedores. Era para el viejo Agolan pero no se usa.
				 *
				 */
				 
				die('Función deprecada en la cuenta.');
				
				/*
				$movimientosProductosModel	= new MovimientosProductos_model();
				$compraModel				= new Compra_model();
				$localModel					= new Local_model();

				$movimientos = $movimientosProductosModel->getMovimientosPorRecibo( $Obj->id );
				$comisiones = array();
				
				foreach ($movimientos as $movimiento)
				{
					if ( $movimiento->compra_id )
					{
						$compra = $compraModel->getCompra ( $movimiento->compra_id );
						
						if ( isset($comisiones[$compra->local_id]) )
							$comisiones[$compra->local_id] += $movimiento->producto_precioCompra;
						else
							$comisiones[$compra->local_id] = $movimiento->producto_precioCompra;
					}
					else
					{
						flash_alert('<strong>CUIDADO!</strong> No se generaron las comisiones por productos que fueron producidos.');
					}
				}
				
				foreach( $comisiones as $inx => $comision )
				{
					$localObj	= $localModel->getLocal( $inx );
					
					if ( $localObj->comision )
					{
						$data[] = array(
							'local_id'			=> $inx,
							'tipo_movimiento'	=> 'recibo',
							'detalle'			=> 'Comision por Recibo - Venta por Consignación #' . $Obj->reciboNumero,
							'relacion_obj'		=> $Obj->id,
							'movimiento'		=> -$comision * $localObj->comision
						);
					}
				}
				*/

				break;
				
			case 'movimiento_manual':
			
			
				$data[] = $Obj;
			
				break;
				
			default:

				flash_error ( 'Movimiento desconocido' );
				break;		
		}

		foreach ($data as $ec)
		{
			// Saldo
			$ec['saldo'] = $this->getSaldoLocal( $ec['local_id'] ) + $ec['movimiento'];
			
			// Execute
			$this->db->insert('estado_cuenta', $ec);
		}
		
		return true;
	}
	
	public function getSaldoLocal($localId)
	{
		$this->db->select('saldo');
	
		$this->db->from('estado_cuenta');
		
		// where
		$this->db->where( 'local_id', $localId );
		
		// Order
		$this->db->order_by('id', 'DESC');
		
		// Limit
		$this->db->limit(1);


		$query = $this->db->get();		
		$return = $query->result();
		
		if (isset($return[0]))
		{
			return $return[0]->saldo;
		}
		else
		{
			return 0;
		}
	}
}