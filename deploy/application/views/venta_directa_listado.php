<div class="canvas venta_directa" style="width:95%;">
	<h1><?=($localObj) ? 'Venta Directa: <strong>' . $localObj->nombre . '</strong>' : '<strong>Ventas Directa</strong>'?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>