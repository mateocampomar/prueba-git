<div class="ventas canvas" style="width:600px;">
	<h1><?='Nuevo Movimiento en la Cuenta: <strong>' . $localObj->nombre . '</strong>'?></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/cuenta/nuevoMovimiento' ))?>
		<table class="info">
			<tr>
				<td class="medium">Detalle:</td>
				<td><?=form_input($detalle)?></td>
			</tr>
			<tr>
				<td class="medium">TOTAL:</td>
				<td><?=form_input($total)?></td>
			</tr>
		</table>
		<div class="submit">
			<a href="<?=base_url( 'index.php/cuenta/')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Generar Movimiento')?>
		</div>
		<?=form_close()?>
	</div>
</div>