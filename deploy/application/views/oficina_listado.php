<div class="canvas oficina" style="width:95%;">
	<h1><strong><?
	
		if ($filter == 'web')		echo 'Ventas Web';
		else						echo 'Ventas Oficina';
	
	?></strong>
	<?
		if ($filter == 'web')
		{
			?>
			<span>
				<?=form_open(base_url( 'index.php/oficina/index/web' ))?>
				<select name="filter_web" onchange="this.form.submit()">
					<option value="nuevo"<?=($filter_web == 'nuevo') ? ' selected="selected"' : ''?>>Nuevos</option>
					<option value="armar"<?=($filter_web == 'armar') ? ' selected="selected"' : ''?>>Para Armar</option>
					<option value="enviado"<?=($filter_web == 'enviado') ? ' selected="selected"' : ''?>>Enviado</option>
				</select>
				<?=form_close()?>
			</span>
			<?
		}
	?>
	</h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>