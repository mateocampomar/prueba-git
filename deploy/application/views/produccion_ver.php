<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="canvas" style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $op->fechaManual )?></div>
	<h1>Orden de Producción: <strong><?=addZeros( $op->id )?></strong></h1>
	<div class="canvas-body">
		<table class="info">
			<tr>
				<td class="small">Producto Destino:</td>
				<td><?=$op->producto_nombre?></td>
			</tr>
			<tr>
				<td class="small">Cantidad:</td>
				<td>x <?=$op->cantidad?></td>
			</tr>
			<tr>
				<td class="small">Depósito:</td>
				<td><?=nl2br($op->deposito_nombre)?></td>
			</tr>
		</table>
		<?
		if ( $op->detalle )
		{
			?>
			</div>
			<h2>Detalle:</h2>
			<div class="canvas-body">
				<div>
					<p><?=nl2br($op->detalle)?></p>
				</div>
			<?
		}
		?>
		<div>
			<?=$crud->output?>
		</div>
		<hr/>
		<div class="datos">
			Creada el: <?=$op->fechaAuto?><br/>
		</div>
	</div>
</div>