<ul class="menu">
	<li><a href="<?=base_url('index.php/charts/ventasPorProductoTipo/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='ventas') ? ' class="selected"' : ""?>>VENTAS</a></li>
	<li><a href="<?=base_url('index.php/stock/local')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='local') ? ' class="selected"' : ""?>>POR LOCAL</a></li>
	<li><a href="<?=base_url('index.php/stock/')?>"<?=(isset($menu_seleccionado) && $menu_seleccionado=='stock') ? ' class="selected"' : ""?>>STOCK</a></li>
</ul>