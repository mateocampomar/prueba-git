<?
	if ( is_array( $filtros ) )		$filtrosStyle = "";
	else							$filtrosStyle = "display:none;";
?>
<div class="canvas facturas" style="width:95%">
	<h1>
		<?=($localObj) ? 'Productos en Consignación: <strong>' . $localObj->nombre . '</strong>' : '<strong>Productos en Consignación</strong>'?>
		<span><a href="javascript:displayFiltros();">Filtros</a></span>
	</h1>
	<form method="post" action="<?=base_url( 'index.php/consignacion')?>" class="filtros" style="<?=$filtrosStyle?>">
		<input type="hidden" name="setfilter" value="1" />
		<span class="reset_filters" onclick="window.location.href='<?=base_url( 'index.php/consignacion/index/reset_filters')?>'">X</span>
		<ul>
			<li class="h2"><h2>FILTROS:</h2></li>
			<?php
				if ( isset($filtros['local']) && $filtros['local'] == 'todos' )
				{
					?>
					<li style="position:relative;top:5px;"><span style="position:relative;top:5px;">Local: <strong>Todos</strong></span></li>
					<input type="hidden" name="local" value="todos" />
					<?
				}
			?>
			<li style="position:relative;top:5px;">Producto: <?=form_dropdown('producto', $productosParaFiltro, (isset($filtros['producto'])) ? $filtros['producto'] : 0); ?></li>
			<li><input type="submit" name="submit" value="Filtrar" /></li>
		</ul>
	</form>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>