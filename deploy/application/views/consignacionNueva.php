<div class="canvas facturas" style="width: 600px">
	<h1><?='<strong>' . $localObj->nombre . '</strong>'?></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/consignacion/nueva' ))?>
		<table class="info">
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium">Depósito:</td>
				<td>
					<?=form_dropdown('deposito', $depositosOptions, $selectedDeposito, 'id="selDeposito"')?><br/>
					<input type="checkbox" id="mostrarSinStock" onclick="cargarDeposito()" /> <span style="font-size:8pt;">Mostrar productos sin stock.</span><br/>
					<br/>
				</td>
			</tr>
			<tr>
				<td class="medium" style="vertical-align: top;">Detalle:</td>
				<td class="detalle <?=( $showDetalle ) ? 'on' : 'off'?>">
					<a href="#" id="agregarDetalle">Agregar detalle</a>
					<?=form_textarea($detalle)?>
				</td>
			</tr>
		</table>
	</div>
	<input type="text" id="search" autocomplete="off" placeholder="Buscar..." />
	<h2>Productos a Consignar</h2>
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
					<td width="30"><?=form_input($producto)?></td>
					<td style="width:40px;">/ <strong class="disponibles" id="disponibles_<?=$producto['id']?>"> - </strong></td>
					<td class="producto-nombre"><?=$producto['nombre']?></td>
					<td align="right"><strong class="pcioUnit">$ <?=currency_format( $producto['precio'] )?></strong></td>
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
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <span id="subTotal"><?=currency_format( 0 ) ?></span></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <span id="iva"><?=currency_format( 0 ) ?></span></td>
				</tr>
				<tr class="total">
					<td>Total:</td>
					<td class="numb">$ <span id="total"><?=currency_format( 0 ) ?></span></td>
				</tr>
			</table>
		</div>
		<br/>
		<div class="submit">
			<a href="<?=base_url( 'index.php/consignacion/')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Ingresar Consignación y Generar Factura')?>
		</div>
		<?=form_close()?>
	</div>
</div>
<script type="text/javascript">

	$( ".input_cantProd" ).keyup(function()
	{
		calcularTotales();
	});

	function calcularTotales()
	{
		var cantidadTotal	= 0;
		var total			= 0;

		$( ".input_cantProd" ).each(function()
		{
			if ($( this ).val())
			{
				var inputValue = parseInt( $(this).val() );
				var pcioUnit = textToFloat( $(this).parent().parent().find( ".pcioUnit" ).html() );
				
				cantidadTotal	= cantidadTotal + inputValue;
				total			= total + pcioUnit * inputValue;
			}
		});
		
		$("#cantProd").html(cantidadTotal);
		$("#subTotal").html(total).formatCurrency();
		$("#iva").html(total * <?=$_iva?> - total).formatCurrency();
		$("#total").html(total * <?=$_iva?>).formatCurrency();
	}
	
	function cargarDeposito()
	{
		$( "#selDeposito option:selected" ).each(function()
		{
			var selectedDeposito	= $( this ).val();
			var mostrarSinStock		= $('#mostrarSinStock').is(':checked');

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
					console.log('ok');
				
					var jsonObj = JSON.parse( data );
					
					$.each(jsonObj, function(productoId, prodObject)
					{
						$('#disponibles_'+ productoId).html(prodObject.cantidad);
						
						if ( prodObject.cantidad == 0 && !mostrarSinStock )
						{
							$( '#disponibles_' + productoId ).parent().parent().fadeOut();
						}
						else
						{
							$( '#disponibles_' + productoId ).parent().parent().fadeIn();
						}
					});

				});
			}
		});
	}

	$( "#selDeposito" ).change(function ()
	{
		cargarDeposito();
	})
	.change();
	
	calcularTotales();

</script>