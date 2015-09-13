<div class="canvas" style="width:95%">
	<h1><strong>Stock por Producto</strong> <span><a href="<?=base_url('index.php/stock')?>"><b>Listado</b></a> | <a href="<?=base_url('index.php/charts/stockPorProductoTipo')?>">Gráfico: Por Colección</a> | <a href="<?=base_url('index.php/local/situacion')?>">Situación</a></span></h1>
</div>
<div class="canvas" style="width:95%;margin-top: 5px;">
	<div class="canvas-body">
		<table border="0" cellspacing="2" class="stock">
			<tr class="header1">
				<td></td>
				<td colspan="2" class="deposito"><a href="<?=base_url('index.php/stock/deposito')?>">DEPOSITO RAUTA</a></td>
				<td colspan="2" class="consignado">CONSIGNADAS</td>
				<td colspan="2" class="vendido">VENTAS</td>
				<td colspan="2" class="compras">COMPRAS</td>
			</tr>
			<tr class="header2">
				<td></td>
				<td class="deposito unidades">UNIDADES</td>
				<td class="deposito monto">$</td>
				<td class="consignado unidades">UNIDADES</td>
				<td class="consignado monto">$</td>
				<td class="vendido unidades">UNIDADES</td>
				<td class="vendido monto">$</td>
				<td class="compras unidades">UNIDADES</td>
				<td class="compras monto">$</td>
			</tr>
			<?
			$cssClass = "trlight";
	
			foreach ($productos as $producto)
			{	
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td><strong><?=$producto['nombre']?></strong></td>
					<td align="center"><?=ceroReplace( $producto['deposito']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['deposito']->total ))?></td>
					<td align="center"><?=ceroReplace( $producto['consignacion']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['consignacion']->total ))?></td>
					<td align="center"><?=ceroReplace( $producto['venta']->cantidad + $producto['oficina']->cantidad + $producto['venta_directa']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $producto['venta']->total + $producto['oficina']->total + $producto['venta_directa']->total ) )?></td>
					<td align="center" class="light"><?=ceroReplace($producto['compras']->cantidad )?></td>
					<td align="right" class="light"><?=ceroReplace( currency_format( $producto['compras']->total ))?></td>
				</tr>
				<?		
			}
			?>
			<tr class="totales">
				<td></td>
				<td align="center"><?=$totales['deposito']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['deposito']['total'])?></td>
				<td align="center"><?=$totales['consignacion']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['consignacion']['total'] )?></td>
				<td align="center"><?=$totales['venta']['cantidad'] + $totales['venta_directa']['cantidad'] + $totales['oficina']['cantidad']?></td>
				<td align="right">$ <?=currency_format( $totales['venta']['total'] + $totales['venta_directa']['total'] + $totales['oficina']['total'] )?></td>
				<td align="center" class="light"><?=$totales['compras']['cantidad']?></td>
				<td align="right" class="light">$ <?=currency_format( $totales['compras']['total'] )?></td>
			</tr>
		</table>
	</div>
</div>
<div class="canvas" style="width:95%">
	<div class="float-right">
		<table class="resumen" width="100%">
			<tr>
				<td></td>
				<td class="item" valign="top">
					<h3 title="Consignación + Depósito">Unid. en Rauta</h3>
					<?= $totales['consignacion']['cantidad'] + $totales['deposito']['cantidad'] ?>
				</td>
				<td class="item" valign="top">
					<h3 title="Consignación + Depósito">$ en Rauta</h3>
					$ <?=currency_format( $totales['consignacion']['total'] + $totales['deposito']['total'] )?>
				</td>
			</tr>
		</table>
	</div>
	<h1><strong>Totales</strong></h1>
</div>
<div class="canvas-footer" style="text-align:right;">
	<?
	if ( $alMenos1productoSinStock )
	{
		?><a href="<?=base_url('index.php/stock/index/sinstock')?>">Mostrar productos que no tienen stock</a><?
	}
	else
	{
		?><a href="<?=base_url('index.php/stock')?>">Mostrar solo productos que tienen stock</a><?
	}
	?>
</div>