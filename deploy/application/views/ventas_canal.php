<div class="canvas" style="width:95%">
	<h1><strong>Ventas por Canal</strong> <span><a href="<?=base_url('index.php/charts/ventasPorProducto')?>">Por Producto</a> | <a href="<?=base_url('index.php/charts/ventasPorProductoTipo')?>">Gráfico: Por Colección</a> | <a href="<?=base_url('index.php/charts/ventasPorAno')?>">Acumulado Por Año</a> | <a href="<?=base_url('index.php/stock/ventasPorCanal')?>"><b>Por Canal</b></a></span></h1>
</div>
<div class="canvas" style="width:95%;margin-top: 5px;">
	<div class="canvas-body">
		<table border="0" cellspacing="2" class="stock">
			<tr class="header1">
				<td></td>
				<td colspan="2" class="vendido">VENTAS por CONSIGNACION</td>
				<td colspan="2" class="oficina">OFICINA</td>
				<td colspan="2" class="venta_directa">VENTA DIRECTA</td>
				<td colspan="2" class="total">TOTAL</td>
			</tr>
			<tr class="header2">
				<td></td>
				<td class="vendido unidades">UNIDADES</td>
				<td class="vendido monto">$</td>
				<td class="oficina unidades">UNIDADES</td>
				<td class="oficina monto">$</td>
				<td class="venta_directa unidades">UNIDADES</td>
				<td class="venta_directa monto">$</td>
				<td class="total unidades">UNIDADES</td>
				<td class="total monto">$</td>
			</tr>
			<?
			$cssClass = "trlight";
	
			foreach ($productos as $producto)
			{	
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td><strong><?=$producto['nombre']?></strong></td>
					<td align="center"><?=ceroReplace( $producto['venta']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['venta']->total ) )?></td>
					<td align="center"><?=ceroReplace( $producto['oficina']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['oficina']->total ) )?></td>
					<td align="center"><?=ceroReplace( $producto['venta_directa']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['venta_directa']->total ) )?></td>
					<td align="center"><?=ceroReplace( $producto['cantidadVentas'] )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['totalVentas'] ) )?></td>
				</tr>
				<?		
			}
			?>
			<tr class="totales">
				<td></td>
				<td align="center"><?=$totales['venta']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['venta']['total'] )?></td>
				<td align="center"><?=$totales['oficina']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['oficina']['total'] )?></td>
				<td align="center"><?=$totales['venta_directa']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['venta_directa']['total'] )?></td>
				<td align="center"><?=$totales['total']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['total']['total'] )?></td>
			</tr>
		</table>
	</div>
</div>