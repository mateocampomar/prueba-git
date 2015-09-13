<div class="canvas" style="width:900px;">
	<h1>Estado de Cuenta: <strong><?=$localObj->nombre?></strong><?
	if (isset($modo) && $modo == 'consignacion')
	{
		?> <span><a href="<?=base_url('index.php/stock/resumen_local')?>">Resumen</a> | <a href="<?=base_url('index.php/cuenta')?>"><b>Estado de Cuenta</b></a> | <a href="<?=base_url('index.php/local/situacion')?>">Situación</a></span><?
	}
	
	?></h1>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>