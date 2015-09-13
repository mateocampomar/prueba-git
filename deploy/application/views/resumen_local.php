<div class="canvas" style="width:95%">
	<h1>Resumen: <strong><?=$localObj->nombre?></strong> <span><a href="<?=base_url('index.php/stock/resumen_local')?>"><b>Resumen</b></a> | <a href="<?=base_url('index.php/cuenta')?>">Estado de Cuenta</a> | <a href="<?=base_url('index.php/local/situacion')?>">Situación</a></span></h1>
	<div class="canvas-body">
		<table class="resumen" width="100%">
			<tr>
				<td>
					<p>
						<?
						echo ( !empty( $localObj->razon_social ) )	? $localObj->razon_social . "<br/>"	: "";
						echo ( !empty( $localObj->rut ) )			? $localObj->rut . "<br/>"			: "";
						echo ( !empty( $localObj->direccion ) )		? $localObj->direccion . "<br/>"	: "";
						
						echo nl2br($localObj->detalle)
						?>
					</p>
				</td>
				<td class="item" valign="top">
					<h3>Consignadas</h3>
					<?=$totales['consignacion']['cantidad']?> <span style="font-size: 9pt;color:#666;">$ <?=currency_format( $totales['consignacion']['total'] )?></span>
				</td>
				<td class="item" valign="top">
					<h3>VENTAS</h3>
					<?=$totales['venta']['cantidad']?> <span style="font-size: 9pt;color:#666;">$ <?=currency_format( $totales['venta']['total'] )?></span>
				</td>
				<td class="item" valign="top">
					<h3>Ultima Liquidación</h3>
					<?=($localObj->ultima_liquidacion) ? fechaToText( $localObj->ultima_liquidacion ) : '-'?>
				</td>
				<td class="item" valign="top">
					<h3>Saldo Actual</h3>
					$ <?=currency_format( $saldo )?>
				</td>
				<td class="item" valign="top">
					<h3>Situación</h3>
					<?=situacion( $situacion )?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="canvas" style="width:95%;margin-top: 5px;">
	<div class="canvas-body">
		<table border="0" cellspacing="2" class="stock">
			<tr class="header1">
				<td></td>
				<td></td>
				<td colspan="2" class="total">TOTAL</td>
				<td colspan="2" class="consignado"><a href="<?=base_url('index.php/consignacion')?>">CONSIGNADAS</a></td>
				<td colspan="2" class="vendido">VENTAS</td>
			</tr>
			<tr class="header2">
				<td></td>
				<td></td>
				<td class="total unidades" width="60">UNIDADES</td>
				<td class="total monto" width="60">$</td>
				<td class="consignado unidades">UNIDADES</td>
				<td class="consignado monto">$</td>
				<td class="vendido unidades">UNIDADES</td>
				<td class="vendido monto">$</td>
			</tr>
			<?
			$cssClass = "trlight";
	
			foreach ($productos as $producto)
			{
				if ( $producto['precioConsignacion'] || $producto['cantidad'] )
				{
			
					$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
		
					?>
					<tr class="<?=$cssClass?>">
						<td><?=productoThumbnail( $producto['id'] )?></td>
						<td><?=$producto['nombre'] ?></td>
						<td align="center" class="light"><?=ceroReplace( $producto['cantidad'] )?></td>
						<td align="right" class="light"><?=ceroReplace( currency_format( $producto['total'] ))?></td>
						<td align="center"><?=ceroReplace( $producto['consignacion']->cantidad )?></td>
						<td align="right"><?=ceroReplace( currency_format( $producto['consignacion']->total ))?></td>
						<td align="center"><?=ceroReplace( $producto['venta']->cantidad )?></td>
						<td align="right"><?=ceroReplace( currency_format( $producto['venta']->total ))?></td>
					</tr>
					<?
				}	
			}
			?>
			<tr class="totales">
				<td></td>
				<td></td>
				<td align="center" class="light"><?=$totales['totales']['cantidad']?></td>
				<td align="right" class="light"><?=currency_format( $totales['totales']['total'] )?></td>
				<td align="center"><?=$totales['consignacion']['cantidad']?></td>
				<td align="right"><?=currency_format( $totales['consignacion']['total'] )?></td>
				<td align="center"><?=$totales['venta']['cantidad']?></td>
				<td align="right"><?=currency_format( $totales['venta']['total'] )?></td>
			</tr>
		</table>
	</div>
</div>