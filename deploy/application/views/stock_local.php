<div class="canvas" style="width:95%">
	<h1><strong>Resumen Todos los Locales</strong> <span><a href="<?=base_url('index.php/stock/local')?>"><b>Resumen</b></a> | <a href="<?=base_url('index.php/charts/local')?>">Gráfico: Ventas vs Consignación</a></span></h1>
	<div class="canvas-body">
		<table border="0" cellspacing="2" class="stock">
			<tr class="header1">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="2" class="consignado">CONSIGNADAS</td>
				<td colspan="2" class="vendido">VENTAS</td>
				<td colspan="2" class="total">TOTAL</td>
			</tr>
			<tr class="header2">
				<td></td>
				<td class="unidades"><strong>ULTIMA LIQUIDACION</strong></td>
				<td class="unidades"><strong>ULTIMO RECIBO</strong></td>
				<td class="unidades"><strong>SITUACION</strong></td>
				<td class="consignado unidades">UNIDADES</td>
				<td class="consignado monto">$</td>
				<td class="vendido unidades">UNIDADES</td>
				<td class="vendido monto">$</td>
				<td class="total unidades" width="60">UNIDADES</td>
				<td class="total monto" width="60">$</td>
			</tr>
			<?
			$cssClass = "trlight";
	
			foreach ($locales as $local)
			{
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td><strong><?=$local['nombre']?></strong></td>
					<td align="center"><?=($local['ultima_liquidacion']) ? fechaToText( $local['ultima_liquidacion'] ) : '-'?></td>
					<td align="center"><?=($local['ultimo_recibo'] != "-") ? fechaToText( $local['ultimo_recibo'] ) : '-'?></td>
					<td align="center"><?=situacion( $local['situacion'] )?></td>
					<td align="center"><?=ceroReplace( $local['consignacion']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $local['consignacion']->total ))?></td>
					<td align="center"><?=ceroReplace( $local['venta']->cantidad )?></td>
					<td align="right"><?=ceroReplace( currency_format( $local['venta']->total ))?></td>
					<td align="center" class="light"><?=ceroReplace( $local['cantidad'] )?></td>
					<td align="right" class="light"><?=ceroReplace( currency_format( $local['total'] ))?></td>
				</tr>
				<?		
			}
			?>
			<tr class="totales">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="center"><?=$totales['consignacion']['cantidad']?></td>
				<td align="right"><?=currency_format( $totales['consignacion']['total'] )?></td>
				<td align="center"><?=$totales['venta']['cantidad']?></td>
				<td align="right"><?=currency_format( $totales['venta']['total'] )?></td>
				<td align="center" class="light"><?=$totales['totales']['cantidad']?></td>
				<td align="right" class="light"><?=currency_format( $totales['totales']['total'] )?></td>
			</tr>
		</table>
	</div>
</div>