<div class="canvas nc" style="width:95%;">
	<h1><?=($localObj) ? 'Notas de Crédito: <strong>' . $localObj->nombre . '</strong>' : '<strong>Notas de Crédito</strong>'?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>