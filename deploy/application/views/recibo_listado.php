<div class="canvas ventas" style="width:95%;">
	<h1><?=($localObj) ? 'Recibos: <strong>' . $localObj->nombre . '</strong>' : '<strong>Recibos</strong>'?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>