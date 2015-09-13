<ul class="menu">
	<li><a href="<?=base_url('index.php/compras/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='compras') ? ' class="selected"' : ""?>>COMPRAS</a></li>
	<?
	/*
	<li><a href="<?=base_url('index.php/venta_directa/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='venta_directa') ? ' class="selected"' : ""?>>VENTA DIRECTA </a></li>
	<li><a href="<?=base_url('index.php/nc/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='nc') ? ' class="selected"' : ""?>>NC</a></li>
	<li><a href="<?=base_url('index.php/recibo/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='recibo') ? ' class="selected"' : ""?>>RECIBOS</a></li>
	*/
	?>
	<?
	if ($local)
	{
		?><li><a href="<?=base_url('index.php/cuenta/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='cuenta') ? ' class="selected"' : ""?>>ESTADO DE CUENTA</a></li><?
	}
	?>
	<li style="margin-right:10px;">
		<div class="cambiar_local">
			<?=form_open(base_url( 'index.php/local/cambiarSession' ))?>
			<?=form_dropdown('local', $todosLosLocales, $local, 'onChange="this.form.submit()" id="selectLocal"'); ?> <span>:</span>
			<?=form_close()?>
		</div>
	</li>
</ul>