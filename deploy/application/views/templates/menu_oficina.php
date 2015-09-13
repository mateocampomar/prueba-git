<ul class="menu">
	<!-- <li><a href="<?=base_url('index.php/oficina/index/web')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='web') ? ' class="selected"' : ""?>>WEB</a></li> -->
	<li><a href="<?=base_url('index.php/oficina/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='ventas') ? ' class="selected"' : ""?>>VENTAS</a></li>
	<li><a href="<?=base_url('index.php/produccion')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='produccion') ? ' class="selected"' : ""?>>PRODUCCIÓN</a></li>
	<li><a href="<?=base_url('index.php/deposito')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='deposito') ? ' class="selected"' : ""?>>DEPÓSITOS</a></li>
	<li><a href="<?=base_url('index.php/traslado')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='traslado') ? ' class="selected"' : ""?>>TRASLADOS</a></li>
</ul>