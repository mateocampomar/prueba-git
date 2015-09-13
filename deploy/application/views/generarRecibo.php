<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="ventas canvas" style="width:600px;">
	<h1><?='Nuevo Recibo: <strong>' . $localObj->nombre . '</strong>'?></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/recibo/generarRecibo' ))?>
		<table class="info">
			<tr>
				<td width="90"></td>
				<td></td>
			</tr>
			<tr>
				<td class="medium" width="30%">Recibo Número:</td>
				<td><?=form_input($reciboNumero)?></td>
			</tr>
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium" style="vertical-align: top;">Detalle:</td>
				<td class="detalle <?=( $showDetalle ) ? 'on' : 'off'?>">
					<a href="#" id="agregarDetalle">Agregar detalle</a>
					<?=form_textarea($detalle)?>
				</td>
			</tr>
			<?
			if (!count($paraHacerRecibo))
			{
				?>
				<tr>
					<td class="medium">TOTAL:</td>
					<td><?=form_input($total)?></td>
				</tr>
				<?
			}
			?>
		</table>
		<?
		if (!count($paraHacerRecibo))
		{
			?><hr/><?
		}
		?>
		<div class="submit">
			<a href="<?=base_url( 'index.php/recibo/')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Generar Recibo')?>
		</div>
		<?=form_close()?>
		<?
		if (count($paraHacerRecibo))
		{
			?>
			</div>
			<h2>Detalle del Recibo:</h2>
			<div class="canvas-body">
			<div>
				<?=$crud->output?>
				<br />
				<div class="footer_canvas">
					<table border="0" align="right">
						<tr>
							<td>Cantidad de Productos:</td>
							<td width="100" class="numb"><?=$cantidadRecibos?></td>
						</tr>
						<tr class="total">
							<td>TOTAL:</td>
							<td class="numb">$ <?=currency_format($totalRecibos)?></td>
						</tr>
					</table>
				</div>
			</div>
			<?
		}
		?>
	</div>
</div>