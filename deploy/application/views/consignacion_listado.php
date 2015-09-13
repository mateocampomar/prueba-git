<div class="canvas facturas" style="width:95%;">
	<h1><?=($localObj) ? 'Facturas: <strong>' . $localObj->nombre . '</strong>' : '<strong>Facturas</strong>'?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>