<div class="canvas" style="width:800px;">
	<div class="fecha_right"><?=fechaToText( $traslado->fechaManual )?></div>
	<h1>Traslado: <strong><?=addZeros( $traslado->id )?></strong></h1>
	<div class="canvas-body">
		<table class="info">
			<tr>
				<td class="small" width="20%">Depósito de Origen</td>
				<td></td>
				<td class="small" width="20%" style="text-align:right;">Depósito de Destino</td>
			</tr>
			<tr>
				<td width="25%" class="depositoOrigen" style="text-align:center;"><span class="texto"><?=$traslado->deposito_origen_nombre?><span class="triangle-right"></span></span></td>
				<td width="10%" style="text-align:center;"><img src="<?=base_url( 'assets/img/campomar-truck.png')?>" style="width:100px;position:relative;top:10px;" /></td>
				<td width="25%" class="depositoDestino" style="text-align:center;"><span class="texto"><?=$traslado->deposito_destino_nombre?><span class="triangle-right"></span></span></td>
			</tr>
			<?
			if ( $traslado->detalle )
			{
				?>
				<tr><td><br /><br /></td></tr>
				<tr>
					<td class="small detalle">Detalle:</td>
				</tr>
				<tr>
					<td colspan="4"><p><?=nl2br($traslado->detalle)?></p></td>
				</tr>
				<tr><td><br /></td></tr>
				<?
			}		
			?>
		</table>
		<div>
			<?=$crud->output?>
		</div>
		<br />
		<div class="footer_canvas">
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <?=$traslado->cantidad ?></td>
				</tr>
			</table>
		</div>
		<br />
		<hr/>
		<div class="datos">
			Creado el: <?=$traslado->fechaAuto?><br/>
		</div>
	</div>
</div>