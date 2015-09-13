<div class="venta_directa canvas" style="width: 600px">
	<h1>Nueva Venta Directa: <strong><?=$localObj->nombre?></strong></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/venta_directa/nuevaVenta' ))?>
		<table class="info" border="0">
			<tr>
				<td class="medium" width="35%">Cliente:</td>
				<td width="65%"><?=form_input($cliente)?></td>
			</tr>
			<tr>
				<td class="medium">Factura Proveedor Número:</td>
				<td><?=form_input($facturaNumero)?></td>
			</tr>
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
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
	<h2>Detalle de los Productos</h2>
	<div class="canvas-body">
		<table border="0" width="100%" cellspacing="1" class="prodSelect">
			<?
			$cssClass = "trdark";
			
			foreach($productos as $key => $producto)
			{
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td width="30"><?=form_input($producto['cantidad'])?></td>
					<td class="producto-nombre"><?=$producto['nombre']?></td>
					<td align="right"><?=form_input($producto['precio'])?></td>
				</tr>
				<?
			}
			?>
		</table>
		<hr/>
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
			<a href="<?=base_url( 'index.php/venta_directa')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Generar Venta / Factura')?>
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
		var total			= 0;

		$( ".input_cantProd" ).each(function()
		{
			if ($( this ).val())
			{
				var inputValue = parseInt( $(this).val() );
				var pcioUnit = $(this).parent().parent().find( ".pcioUnit" ).val();
				
				cantidadTotal	= cantidadTotal + inputValue;
				total			= total + pcioUnit * inputValue;
			}
		});
		
		$("#cantProd").html(cantidadTotal);
		$("#subTotal").html(total).formatCurrency();
		$("#iva").html(total * <?=$_iva?> - total).formatCurrency();
		$("#total").html(total * <?=$_iva?>).formatCurrency();
	}
	
	calcularTotales();

</script>