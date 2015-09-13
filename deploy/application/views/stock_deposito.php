<script type="text/javascript">

	function eliminar(movimientoId)
	{
		var r = confirm("Estás por eliminar el producto del Stock.\n\nHacé la nota Bolóh!!!");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/stock/eliminar/')?>/" + movimientoId + "/<?=$compraId?>";
		}
	}
</script>
<?

	if ( is_array( $filtros ) )		$filtrosStyle = "";
	else							$filtrosStyle = "display:none;";

?>
<div class="canvas" style="width:95%">
	<h1>
		<strong>Listado de Productos en Depósito</strong><?=($compraId) ? ' / Compra #' . addZeros( $compraObj->facturaNumero ) : '' ?>
		<?
		/*
		Función deshabilitada por el momento.
		if ($modo == 'ver')
		{
			?><span><a href="<?=base_url('index.php/stock/deposito')?>/<?=($compraId) ? $compraId : 0 ?>/editar">Editar</a></span><?
		}
		else
		{
			?><span><a href="<?=base_url('index.php/stock/deposito')?>/<?=($compraId) ? $compraId : 0 ?>">Ver</a></span><?
		}
		*/
		?>
		<span><a href="javascript:displayFiltros();">Filtros</a></span>
	</h1>
	<form method="post" action="<?=base_url( 'index.php/stock/deposito')?>" class="filtros" style="<?=$filtrosStyle?>">
		<input type="hidden" name="setfilter" value="1" />
		<span class="reset_filters" onclick="window.location.href='<?=base_url( 'index.php/stock/deposito/reset_filters')?>'">X</span>
		<ul>
			<li class="h2"><h2>FILTROS:</h2></li>
			<li style="position:relative;top:5px;">Producto <?=form_dropdown('producto', $productosParaFiltro, (isset($filtros['producto'])) ? $filtros['producto'] : 0); ?></li>
			<li style="position:relative;top:5px;">Depósito <?=form_dropdown('deposito', $depositosParaFiltro, (isset($filtros['deposito'])) ? $filtros['deposito'] : 0); ?></li>
			<li><input type="submit" name="submit" value="Filtrar" /></li>
		</ul>
	</form>
	<div class="canvas-body">
		<?=$crud->output?>
	</div>
</div>