<br/>
<br/>
<br/>
<br/>
<div class="canvas" style="width: 600px">
	<h1><strong>Config:</strong></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/configuraciones' ))?>
		<table class="info">
			<tr>
				<td class="medium">Cierre de Ejercicio:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium">Auth:</td>
				<td><a href="<?=base_url( 'index.php/auth/logout' )?>" style="font-size: 10pt;">Logout</a></td>
			</tr>
		</table>
		<br/>
		<div class="submit">
			<a href="<?=base_url( 'index.php/configuraciones')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Guardar Configuraciones')?>
		</div>
		<?=form_close()?>
	</div>
</div>