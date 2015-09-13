<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="nc canvas" style="width:600px;">
	<h1><?='Nueva Nota de Crédito: <strong>' . $localObj->nombre . '</strong>'?></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/nc/nuevaNc' ))?>
		<table class="info">
			<?
			if (count($productosConDevolucion))
			{
				?>
				<tr>
					<td class="medium">Depósito de Destino:</td>
					<td>
						<?=form_dropdown('deposito', $depositosOptions, $selectedDeposito, 'id = "selDeposito"')?>
					</td>
				</tr>
				<?
			}
			?>
			<tr>
				<td class="medium" width="35%">NC Número:</td>
				<td><?=form_input($ncNumero)?></td>
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
			if (!count($productosConDevolucion))
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
		if (!count($productosConDevolucion))
		{
			?><hr/><?
		}
		?>
		<div class="submit">
			<a href="<?=base_url( 'index.php/nc/')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Crear Nota de Crédito')?>
		</div>
		<?
		if (count($productosConDevolucion))
		{
			?><hr/><?
		}
		?>
		<?
		if (count($productosConDevolucion))
		{
			?>
			<h2>Detalle de la Nota de Crédito:</h2>
			<?
			echo $crud->output;
			?>
			<br />
			<div class="footer_canvas">
				<table border="0" align="right">
					<tr>
						<td>Cantidad de Productos:</td>
						<td width="100" class="numb"><?=$cantidadNc?></td>
					</tr>
					<tr class="total">
						<td>TOTAL:</td>
						<td class="numb">$ <?=currency_format( $totalNc )?></td>
					</tr>
				</table>
			</div>
			<?
		}
		?>
		<?=form_close()?>
	</div>
</div>