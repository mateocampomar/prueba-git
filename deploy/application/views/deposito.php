<div class="canvas" style="width:95%">
	<h1><strong>Stock En Depósito</strong></h1>
	<div class="canvas-body">
		<table border="0" cellspacing="2" class="stock">
			<tr class="header1">
				<td></td>
				<?php
				foreach ( $depositos as $deposito )
				{
					?><td class="deposito"><a href="<?=base_url('index.php/deposito/setFiltrosParaDeposito/deposito/' . $deposito->id )?>"><?=$deposito->nombre?></a></td><?
				}
				?>
				<td class="compras" width="10%">Total en Depósito</td>
				<td class="consignado" width="10%">CONSIGNADAS</td>
			</tr>
			<tr class="header2">
				<td></td>
				<?php
				foreach ( $depositos as $deposito )
				{
					?>
					<td class="deposito monto" style="text-align:center" width="10%">$ <?=currency_format( $totalesPorDeposito[$deposito->id]['total'] )?></td>
					<?
				}
				?>
				<td class="compras monto" style="text-align:center" width="11%">$ <?=currency_format( $totales['total'] )?></td>
				<td class="consignado unidades">$ <?=currency_format( $totalConsignadas )?></td>
			</tr>
			<?
			$cssClass = "trlight";
	
			foreach ($productos as $producto)
			{
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td><?=$producto->nombre?></td>
					<?
					foreach($depositos as $deposito)
					{
						?>
						<td align="center">
							<?php
								if ( $rows[$producto->id][$deposito->id]['cantidad'] )
								{
									?><a style="color:#000;" href="<?=base_url('index.php/deposito/setFiltrosParaDeposito/deposito/' . $deposito->id . '/producto/' . $producto->id )?>"><?=$rows[$producto->id][$deposito->id]['cantidad']?></a><?
								}
								else
								{
									echo ceroReplace( $rows[$producto->id][$deposito->id]['cantidad'] );
								}
							?>
						</td>
						<?
					}
					
					?>
					<td align="center">
						<?php
							if ( $totalesPorProducto[$producto->id]['cantidad'] )
							{
								?><a style="color:#000;" href="<?=base_url('index.php/deposito/setFiltrosParaDeposito/producto/' . $producto->id )?>"><?=$totalesPorProducto[$producto->id]['cantidad']?></a><?
							}
							else
							{
								echo ceroReplace( $totalesPorProducto[$producto->id]['cantidad'] );
							}
						?>
					</td>
					<td align="center">
						<?php
							if ( $consignacion[$producto->id][0]->cantidad )
							{
								?><a style="color:#000;" href="<?=base_url('index.php/consignacion/setFiltrosParaConsignacion/producto/' . $producto->id . '/local/todos' )?>"><?=$consignacion[$producto->id][0]->cantidad?></a><?
							}
							else
							{
								echo ceroReplace( $consignacion[$producto->id][0]->cantidad );
							}
						?>
					</td>
				</tr>
				<?		
			}
			?>
			<tr class="totales">
				<td></td>
				<?php
				foreach ( $depositos as $deposito )
				{
					?>
					<td align="center"><?=$totalesPorDeposito[$deposito->id]['cantidad']?></td>
					<?
				}
				?>
				<td align="center"><?=$totales['cantidad']?></td>
				<td align="center"><?=$cantidadConsignadas?></td>
			</tr>
		</table>
	</div>
</div>