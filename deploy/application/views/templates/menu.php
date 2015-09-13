<ul class="menu">
	<?
	if ($local)
	{
		?><li><a href="<?=base_url('index.php/liquidacion/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='liquidacion') ? ' class="selected"' : ""?>>LIQUIDACION</a></li><?
	}
	?>
	<li>
		<a href="<?=base_url('index.php/nc/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='nc') ? ' class="selected"' : ""?>>NC</a><span><?
		if($ncParaHacer)
		{
			?><a href="<?=base_url('index.php/nc/nuevaNc')?>" class="menu_alert_bubble"><?=$ncParaHacer?></a><?
		}
		?></span>
	</li>
	<li><a href="<?=base_url('index.php/consignacion/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='consignacion') ? ' class="selected"' : ""?>>PRODUCTOS EN CONSIGNACION</a></li>
	<li>
		<a href="<?=base_url('index.php/recibo/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='recibo') ? ' class="selected"' : ""?>>RECIBOS</a><span id="recibos_bubble"><?
		if($recibosParaHacer)
		{
			?><a href="<?=base_url('index.php/recibo/generarRecibo')?>" class="menu_alert_bubble"><?=$recibosParaHacer?></a><?
		}
		?></span>
	</li>
	<li><a href="<?=base_url('index.php/consignacion/listar')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='facturas') ? ' class="selected"' : ""?>>FACTURAS</a></li>
	<?
	if ($local)
	{
		?><li><a href="<?=base_url('index.php/stock/resumen_local')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='resumen') ? ' class="selected"' : ""?>>RESUMEN DEL LOCAL</a></li><?
	}
	else
	{
		?><li><a href="<?=base_url('index.php/stock/local')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='resumen') ? ' class="selected"' : ""?>>RESUMEN</a></li><?
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