<div class="canvas compras" style="width:95%;">
	<h1><?=($localObj) ? 'Compras: <strong>' . $localObj->nombre . '</strong>' : '<strong>Compras</strong>'?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>