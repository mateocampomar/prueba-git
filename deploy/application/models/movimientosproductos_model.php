<?php
class MovimientosProductos_model extends My_Model {

	protected $deposito	= 2;		// Depósito por defecto

	public function nuevaCompra($compraId, $productoId, $productoPrecio)
	{
		$data = array(
			'compra_id'				=> $compraId,
			'producto_id'			=> $productoId,
			'producto_precioCompra'	=> $productoPrecio,
			'deposito_id'			=> $this->deposito
		);
		
		$this->db->insert('movimientos_productos', $data);
		
		return $this->db->insert_id();
	}

	public function nuevaProduccion($produccionId, $productoId, $productoPrecio)
	{
		$data = array(
			'produccion_id'			=> NULL,
			'producto_id'			=> $productoId,
			'producto_precioCompra'	=> $productoPrecio,
			'deposito_id'			=> $this->deposito
		);
		
		$this->db->insert('movimientos_productos', $data);
		
		return $this->db->insert_id();
	}

	public function nuevaNotaDeCredito( $ncId, $productoId, $consignacionId, $consignacionPrecio )
	{
		$data = array(
			'nc_id'					=> $ncId,
			'producto_id'			=> $productoId,
			'consignacion_id'		=> $consignacionId,
			'consignacionPrecio'	=> $consignacionPrecio,
			'status'				=> 0
		);
		
		$this->db->insert('movimientos_productos', $data);
		
		return $this->db->insert_id();
	}
	
	/**
	 * Busca un movimiento en la base de datos.
	 */
	public function getMovimiento($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);

		$query = $this->db->get('movimientos_productos');
		
		$return = $query->result();
		
		return $return[0];
	}

	public function getVentasPorDia($date)
	{
		$this->db->select('count(1) as cantidad');

		$this->db->where("fechaVendido LIKE '" . $date . "%' AND status = 1");

		$query = $this->db->get('movimientos_productos');
		
		$return = $query->result();
		
		return $return[0];
	}
	
	/**
	 * Marca el producto como venido y freezea el precio.
	 */
	public function marcarVendido( $id, $tomarPrecioDe='producto' )
	{
		if ( $tomarPrecioDe == 'producto' )
		{
			$productoModel = new Producto_model();
			$producto = $productoModel->getProducto($this->getMovimiento($id)->producto_id);	

			$data = array(
				'fechaVendido'	=> date( 'Y-m-d H:i:s' ),
				'vendidoPrecio' => $producto->precioVenta,
			);
		}
		elseif ( $tomarPrecioDe == 'consignacion' )
		{
			$data = array(
				'fechaVendido'	=> date( 'Y-m-d H:i:s' ),
				'vendidoPrecio' => $this->getMovimiento($id)->consignacionPrecio,
			);
		}
		else
		{
			return false;
		}
		
		$this->db->where('id', $id);

		return $this->db->update('movimientos_productos', $data);
	}

	public function retiraReciboDelMovimiento($reciboId)
	{
		$data = array(
						'recibo_id' => NULL
					);
	
		$this->db->where('recibo_id', $reciboId);

		return $this->db->update('movimientos_productos', $data);
	}

	public function retiraConsignacionDelMovimiento($consignacionId)
	{
		$data = array(
						'local_id'			=> NULL,
						'consignacion_id'	=> NULL,
						'consignacionPrecio'=> NULL
					);
	
		$this->db->where('consignacion_id', $consignacionId);

		return $this->db->update('movimientos_productos', $data);
	}
	
	public function anularVentaDelMovimiento($idVenta)
	{
		$data = array(
						'venta_oficina_id'	=> NULL,
						'fechaVendido'		=> NULL,
						'vendidoPrecio'		=> NULL
					);
	
		$this->db->where('venta_oficina_id', $idVenta);

		return $this->db->update('movimientos_productos', $data);
	}

	/**
	 * Desmarca el producto como venido y freezea el precio.
	 */
	public function desmarcarVendido($idMovimiento)
	{
		$this->db->where('id', $idMovimiento);

		return $this->db->update('movimientos_productos', $this->desmarcarVendidoArray());
	}
	
	private function desmarcarVendidoArray()
	{
		return array(
			'fechaVendido'	=> NULL,
			'vendidoPrecio' => NULL,
		);
		
		
	}
	
	/**
	 * Arra de los movimientos que necesitan un recibo.
	 */
	public function movimientosRecibosParaHacer($localId)
	{
		if (!$localId)
			return 0;
	
		$this->db->select('*');
		
		// Where
		$where = "fechaVendido IS NOT NULL AND recibo_id IS NULL AND local_id = " . $localId;
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function movimientosNcParaHacer($localId)
	{
		if (!$localId)
			return 0;
	
		$this->db->select('*');
		
		// Where
		$where = "devolucion IS NOT NULL AND local_id = " . $localId;
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function movimientosCambioPrecioCompra()
	{
		$this->db->select('*');
		
		// Where
		$where = "cambio_precioCompra IS NOT NULL";
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function totalMovimientosCambioPrecioCompra()
	{
		$this->db->select('SUM(cambio_precioCompra) as total, SUM(producto_precioCompra) as totalOriginal');
		
		// Where
		$where = "cambio_precioCompra IS NOT NULL";
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		$return = $query->result();
		
		return $return[0];
	}
	
	public function updateRecibo($movimientoId, $reciboId)
	{
		$data = array(
			'recibo_id'	=> $reciboId,
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}
	
	public function cambiarPrecioCompra($movimientoId, $precio)
	{
		$data = array(
			'cambio_precioCompra'	=> $precio,
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}
	
	public function movimientosConsignablesPorCompra($compraId)
	{
		$this->db->select('*');
		
		// Where
		$where = "local_id IS NULL AND compra_id = " . $compraId . ' AND status = 1 AND venta_oficina_id IS NULL AND produccion_id IS NULL';
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}
	
	public function getMovimientoLiquidacionPorPrecio($localId, $productoId, $consignacionPrecio, $vendido = false)
	{
		// ttttttttttttttttttttttttttttttt
		$this->db->select('*, COUNT(1) as cantidad');

		$this->db->group_by(array(
        							'consignacion_id',
        						)); 
        						
        if ( $vendido )	$vendidoPrecio = 'vendidoPrecio IS NOT NULL';
        else			$vendidoPrecio = 'vendidoPrecio IS NULL';	
		
		// Where
		$where = "local_id = " . $localId . " AND producto_id = " . $productoId . ' AND consignacionPrecio = ' . $consignacionPrecio . ' AND status = 1 AND ' . $vendidoPrecio . ' AND produccion_id IS NULL AND recibo_id IS NULL';
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}
	
	public function consignarMovimientos($productoId, $consignacionId, $localId, $productoPrecio, $depositoOrigenId)
	{
		$this->db->select('id');
		
		// Where
		
		// [TODO] Cuidado con este Where que es muy parecido a las consultas de stock.
		$where  = "producto_id = " . $productoId . " AND local_id IS NULL AND status = 1 AND venta_oficina_id IS NULL AND produccion_id IS NULL";
		$where .= ' AND `deposito_id` = ' . $depositoOrigenId;
		
		$this->db->where($where);
		
		$this->db->order_by('id', 'desc');
		
		$this->db->limit(1);

		$query = $this->db->get('movimientos_productos');
		
		if ( count( $query->result() ))
		{
			$data = array(
				'local_id'			=> $localId,
				'consignacion_id'	=> $consignacionId,
				'consignacionPrecio'=> $productoPrecio
			);
			
			$return = $query->result();
			
			$this->db->where('id', $return[0]->id);

			return $this->db->update('movimientos_productos', $data);
		}
		else
		{
			flash_error('Mateo: Error! No hay movimiento producto para consignar');
		}
	}

	public function venderMovimientos($productoId, $ventaId, $productoPrecio, $depositoId)
	{
		$this->db->select('id');
		
		// Where
		$where = "producto_id = " . $productoId . " AND local_id IS NULL AND status = 1 AND venta_oficina_id IS NULL AND produccion_id IS NULL AND devolucion IS NULL AND deposito_id = " . $depositoId;
		// [TODO] Misma consulta que en movimientosConsignablesPorProducto
		// Esta parte del código para mi devuelve el mismo array que movimientosConsignablesPorProducto().

		$this->db->where($where);
		
		$this->db->order_by('id', 'desc');
		
		$this->db->limit(1);

		$query = $this->db->get('movimientos_productos');
		
		if ( count( $query->result() ))
		{
			$data = array(
				'fechaVendido'		=> date( 'Y-m-d H:i:s' ),
				'venta_oficina_id'	=> $ventaId,
				'vendidoPrecio'		=> $productoPrecio,
			);
			
			$return = $query->result();
			
			$this->db->where('id', $return[0]->id);

			return $this->db->update('movimientos_productos', $data);
		}
		else
		{
			flash_error('No hay movimiento producto para consignar');
		}
	}
	
	/*
	 * Los movimientos consignables son los que están en depósito
	 *
	 * [TODO] Cuidado, esta función es igual (mismo WHERE) que getStockenDepositoPorProducto()
	 * El if de abajo es un intento de arreglo de eso.
	 *
	 */
	public function movimientosConsignablesPorProducto($productoId, $depositoId=false, $forzarFuncionOriginal=false)
	{
		if ( $depositoId !== false && $forzarFuncionOriginal != true)
		{
			$stockModel			= new Stock_model();

			return $stockModel->getStockEnDepositoPorProducto($productoId, $depositoId);
		}
		else
		{
			$this->db->select('*');
	
			// Where
			$where = "consignacion_id IS NULL AND producto_id = " . $productoId . ' AND status = 1 AND venta_oficina_id IS NULL AND produccion_id IS NULL AND devolucion IS NULL';
	
			if ($depositoId)
			{
				$where .= ' AND deposito_id=' . $depositoId;
			}
	
			$this->db->where($where);
	
			$query = $this->db->get('movimientos_productos');
		
			return $query->result();
		}
	}

	public function movimientosConsignadosPorLocal($localId)
	{
		$this->db->select('SUM(consignacionPrecio) as totalEnConsignacion, SUM(1) as cantidadEnConsignacion, ');
		
		// Where
		$where = "local_id = " . $localId . ' AND status = 1 AND recibo_id IS NULL';
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		$return = $query->result();
		
		return $return[0];
	}
	
	public function movimimientosConsignados( $localId=false )
	{
		// Where
		$where  = 'movimientos_productos.status = 1 AND recibo_id IS NULL';
		
		if ( $localId )		$where .= " AND local_id = " . $localId;
		else				$where .= ' AND local_id IS NOT NULL';
		
		$this->db->where($where);

		$this->db->join('producto', 'producto.id = movimientos_productos.producto_id');

		$query = $this->db->get('movimientos_productos');
		
		$return = $query->result();
		
		return $return;
	}
	
	public function anularMovimientosPorCompra($compraId)
	{
		$data = array(
			'status'	=> 0,
		);
		
		$this->db->where('compra_id', $compraId);

		return $this->db->update('movimientos_productos', $data);
	}

	public function devolver($movimientoId, $depositoId)
	{
		$movimientoObj = $this->getMovimiento( $movimientoId );
		
		if ( $movimientoObj )
		{
			$data = array(
				'local_id'			=> NULL,
				'consignacion_id'	=> NULL,
				'consignacionPrecio'=> NULL,
				'devolucion'		=> NULL,
				'deposito_id'		=> $depositoId
			);
			
			$this->db->where('id', $movimientoId);
	
			if ( $this->db->update('movimientos_productos', $data) )
			{
				// Crea un nuevo producto ya que el anterior quedó asociadoa  la nota de crédito como anulado.
				return $this->nuevaNotaDeCredito( 0, $movimientoObj->producto_id, $movimientoObj->consignacion_id, $movimientoObj->consignacionPrecio );
			}
		}
		
		return false;
	}
	
	public function actualizar_NCId( $movimientoId, $ncId )
	{
		$data = array(
			'nc_id'	=> $ncId,
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}

	public function marcarDevolver($movimientoId)
	{
		$data = array(
			'devolucion'		=> date( 'Y-m-d H:i:s' ),
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}

	public function desmarcarDevolver($movimientoId)
	{
		$data = array(
			'devolucion'		=> NULL,
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}

	/*
	 * Marca el Movimiento Producto con stats 0 para indicar que el producto fue eliminado.
	 */
	public function eliminar($movimientoId)
	{
		$data = array(
			'status'		=> 0,
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}
	
	public function marcarProduccion($movimientoId, $produccionId)
	{
		$data = array(
			'produccion_id'		=> $produccionId
		);
		
		$this->db->where('id', $movimientoId);

		return $this->db->update('movimientos_productos', $data);
	}
	
	public function getMovimientosPorVentaOficina( $idVentaOficina )
	{
		$this->db->select('*');

		$where = "venta_oficina_id = " . $idVentaOficina;
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function getMovimientosPorRecibo( $idRecibo, $consignacionId = false, $productoId = false )
	{
		$this->db->select('*');

		// Where
								$where  = "recibo_id = " . $idRecibo;
		if ($consignacionId)	$where .= " AND consignacion_id = " . $consignacionId;
		if ($productoId)		$where .= " AND producto_id = " . $productoId;
		
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function getMovimientosPorNc( $ncId, $consignacionId = false, $productoId = false )
	{
		$this->db->select('*');

		// Where
								$where  = "nc_id = " . $ncId;
		if ($consignacionId)	$where .= " AND consignacion_id = " . $consignacionId;
		if ($productoId)		$where .= " AND producto_id = " . $productoId;
		
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}


	public function getMovimientosPorConsignacion( $consignacionId, $productoId = false )
	{
		$this->db->select('*');

		// Where
								$where  = "consignacion_id = " . $consignacionId;
		if ( $productoId )		$where .= " AND producto_id = " . $productoId;
		
		
		$this->db->where($where);

		$query = $this->db->get('movimientos_productos');
		
		return $query->result();
	}

	public function sumaRecibosParaHacer( $paraHacerRecibo )
	{
		$data = array();
	
		if (count( $paraHacerRecibo ))
		{
			$data['totalRecibos']		= 0;
			$data['cantidadRecibos']	= 0;

			foreach($paraHacerRecibo as $movimiento)
			{
				$iva = calcularIVA($movimiento->vendidoPrecio);
	
				$data['totalRecibos']		+= $iva['total_iva_inc'];
				$data['cantidadRecibos']	+= 1;
			}
		}
		
		return $data;
	}

	public function sumaNcParaHacer( $paraHacerNc )
	{
		$data = array();
	
		if (count( $paraHacerNc ))
		{
			$data['totalNc']		= 0;
			$data['cantidadNc']		= 0;

			foreach($paraHacerNc as $movimiento)
			{
				$iva = calcularIVA( $movimiento->consignacionPrecio );
	
				$data['totalNc']	+= $iva['total_iva_inc'];
				$data['cantidadNc']	+= 1;
			}
		}
		
		return $data;
	}
	
	public function getMovimientosVendidosSinReciboPorFactura( $consignacionId )
	{
		$this->db->select('movimientos_productos.*, producto.*');
		
		$this->db->from('movimientos_productos');
		
		$this->db->join('producto', 'producto.id = movimientos_productos.producto_id');

		$where = "fechaVendido IS NOT NULL AND recibo_id IS NULL AND consignacion_id = " . $consignacionId;
		$this->db->where($where);

		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function setDeposito( $depositoId )
	{
		return $this->deposito = $depositoId;
	}

	public function trasladarMovimientos($productoId, $depositoOrigen, $depositoDestino )
	{
		$this->db->select('id');
		
		// Where
		// [TODO] Cuidado con este Where que es muy parecido a las consultas de stock.
		$where  = "producto_id = " . $productoId . " AND local_id IS NULL AND status = 1 AND venta_oficina_id IS NULL AND produccion_id IS NULL";
		$where .= ' AND `deposito_id` = ' . $depositoOrigen;
		
		$this->db->where($where);
		
		$this->db->order_by('id', 'desc');
		
		$this->db->limit(1);

		$query = $this->db->get('movimientos_productos');
		
		if ( count( $query->result() ))
		{
			$data = array(
				'deposito_id'			=> $depositoDestino
			);
			
			$return = $query->result();
			
			$this->db->where('id', $return[0]->id);

			if ( $this->db->update('movimientos_productos', $data) )
			{
				return $return[0]->id;
			}
			else
			{
				flash_error("No pudo hacer el update de transladarMovimientos().");
			}
		}
		else
		{
			flash_error('Mateo: Error! No hay movimiento producto para consignar');
		}
	}

	public function editarComentario($id, $comentario)
	{
		$data = array(
						'mp_comentario'	=> $comentario
					);
	
		$this->db->where('id', $id);

		return $this->db->update('movimientos_productos', $data);

	}
	
	/**
	 * deleteMovimiento
	 *
	 * Elimina el movimiento por Id.
	 * NO es un borrado lógico, es un delete.
	 *
	 */
	public function deleteMovimiento( $movimientoId )
	{
		return $this->db->delete( 'movimientos_productos', array( 'id' => $movimientoId ) ); 
	}
}