<div class="charts canvas" style="width: 600px">
	<h1><strong>Producción</strong></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/produccion/nuevaOP' ))?>
		<table class="info">
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium">Producto a Producir:</td>
				<td><?=form_dropdown('productoDestino', $productosArray, $selectedProducto, 'id = "selProdDestino"')?></td>
			</tr>
			<tr>
				<td class="medium">Depósito:</td>
				<td>
					<?=form_dropdown('deposito', $depositosOptions, $selectedDeposito, 'id = "selDeposito"')?>
				</td>
			</tr>
			<tr>
				<td class="medium">Cantidad a Producir:</td>
				<td><?=form_input($cantidad)?></td>
			</tr>
			<tr>
				<td class="medium" style="vertical-align: top;">Detalle:</td>
				<td colspan="2"><?=form_textarea($detalle)?></td>
			</tr>
		</table>
		<div class="submit">
			<?=form_submit('mysubmit', 'Producir')?>
		</div>
		<?=form_close()?>
		<hr/>
		<div id="productoOrigen"></div>
	</div>
</div>
<script type="text/javascript">

	$( "#selProdDestino" ).change(function() {
		getMateriaPrima();
	});

	$( "#selDeposito" ).change(function() {
		getMateriaPrima();
	});
	
	function getMateriaPrima()
	{
		var productoId = $('#selProdDestino').val();
		var depositoId = $('#selDeposito').val();
	
		if ( productoId != 0 && depositoId != 0 )
		{
			$.ajax({
				url: '<?=base_url( 'index.php/produccion/mostrarProductosOrigen' )?>/' + productoId + '/' + depositoId,
			})
			.done(function( data ) {
				$( "#productoOrigen" ).html(data);
			});
		}
		else
		{
			$( "#productoOrigen" ).html('');
		}
	}
	
	getMateriaPrima();

</script>
