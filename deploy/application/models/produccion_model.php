<?php
class Produccion_model extends My_Model {

	public function getProductosDestino() {
		
		$this->db->select('DISTINCT(productoDestino) as productoDestino, producto.*');

		$this->db->from('produccion');
		
		$this->db->join('producto', 'producto.id = produccion.productoDestino');

		$query = $this->db->get();
		
		return $query->result();		
	}
	
	public function productosOrigenPorProductoDestino($productoId)
	{
		$this->db->from('produccion');
		
		$this->db->where('productoDestino = ' . $productoId);
		
		$this->db->join('producto', 'producto.id = produccion.productoOrigen');

		$query = $this->db->get();
		
		return $query->result();	
	}
	
	public function producir($productoDestinoId, $cantidad, $depositoId)
	{
		$movimientosProductosModel	= new MovimientosProductos_model();
		$movimientosProductosModel->setDeposito( $depositoId );
		
		$productosModel				= new Producto_model();

		$productosStock				= $this->productosOrigenPorProductoDestino($productoDestinoId);
		
		$precioCompra = 0;

		foreach($productosStock as $producto)
		{
			//print_r($producto);
			$productosConsignables = $movimientosProductosModel->movimientosConsignablesPorProducto($producto->id, $depositoId, true);

			$precioCompra += $productosModel->getProducto($producto->id)->precio;
			
			for($i=0; $i< $cantidad * $producto->cantidadOrigen; $i++)
			{
				$movimientosProductosModel->marcarProduccion($productosConsignables[$i]->id, 1);
			}
		}

		for($i=0; $i<$cantidad; $i++)
		{
			$movimientosProductosModel->nuevaProduccion(1, $productoDestinoId, $precioCompra);
		}
		
		return true;
	}
}