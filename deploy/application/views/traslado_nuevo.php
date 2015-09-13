<div class="canvas" style="width: 800px">
	<h1><strong>Nuevo Traslado:</strong></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/traslado/nuevo' ))?>
		<table class="info">
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium">Depósito Origen:</td>
				<td>
					<?=form_dropdown('deposito_origen', $depositosOptions, $selectedDepositoOrigen, 'id = "selDepositoOrigen"')?>
				</td>
				<td><img src="<?=base_url( 'assets/img/campomar-truck.png')?>" style="width:50px;position:relative;top:-10px;" /></td>
				<td class="medium">Depósito Destino:</td>
				<td>
					<?=form_dropdown('deposito_destino', $depositosOptions, $selectedDepositoDestino, 'id = "selDepositoDestino"')?>
				</td>
			</tr>
			<tr>
				<td class="medium" style="vertical-align: top;">Detalle:</td>
				<td class="detalle" colspan="4">
					<?=form_textarea($detalle)?>
				</td>
			</tr>
		</table>
	</div>
	<input type="text" id="search" autocomplete="off" placeholder="Buscar..." />
	<h2>Productos a Trasladar:</h2>
	<div class="canvas-body">
		<table border="0" width="100%" cellspacing="1" class="prodSelect">
			<?
			$cssClass = "trdark";
			
			foreach($productos as $key => $producto)
			{
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
	
				?>
				<tr class="<?=$cssClass?>">
					<td><?=productoThumbnail( $producto['id'] )?></td>
					<td width="30"><?=form_input($producto['cantidad'])?></td>
					<td style="width:40px;">/ <strong class="disponibles" id="disponibles_<?=$producto['id']?>"> - </strong></td>
					<td class="producto-nombre"><?=$producto['nombre']?></td>
				</tr>
				<?
			}
			?>
		</table>
		<br />
		<div class="footer_canvas">
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <span id="cantProd">0</span></td>
				</tr>
			</table>
		</div>
		<br/>
		<div class="submit">
			<a href="<?=base_url( 'index.php/traslado')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Generar Nuevo Traslado')?>
		</div>
		<?=form_close()?>
	</div>
</div>
<script type="text/javascript">

	$( ".input_cantProd" ).keyup(function()
	{
		calcularTotales();
	});
	
	$( ".pcioUnit" ).keyup(function()
	{
		calcularTotales();
	});
	
	function calcularTotales()
	{
		var cantidadTotal	= 0;

		$( ".input_cantProd" ).each(function()
		{
			if ($( this ).val())
			{
				var cantidadValue = parseInt( $(this).val() );
				
				cantidadTotal	= cantidadTotal + cantidadValue;
			}
		});
		
		$("#cantProd").html(cantidadTotal);
	}
	
	calcularTotales();

	function cargarDeposito()
	{
		$( "#selDepositoOrigen option:selected" ).each(function()
		{
			var selectedDeposito = $( this ).val();

			$('.disponibles').html(' - ');
			
			if ( selectedDeposito != 0 )
			{

				$.ajax({
					url: '<?=base_url( 'index.php/deposito/getProductosPorDeposito')?>/' + selectedDeposito,
					beforeSend: function( xhr ) {
	
					}
				})
				.done(function( data )
				{
					var jsonObj = JSON.parse(data);
					
					$.each(jsonObj, function(productoId, prodObject)
					{
						$('#disponibles_'+ productoId).html(prodObject.cantidad);
					});
				});
			}
		});
	}

	$( "#selDepositoOrigen" ).change(function ()
	{
		cargarDeposito();
	})
	.change();

</script>