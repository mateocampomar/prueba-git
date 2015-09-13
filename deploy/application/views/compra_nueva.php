<div class="compras canvas" style="width:600px;">
	<h1><strong>Nueva Compra</strong></h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/compras/nuevaCompra' ))?>
		<table class="info">
			<tr>
				<td class="medium" width="25%">Factura Proveedor:</td>
				<td><?=form_input($factura_numero)?></td>
			</tr>
			<tr>
				<td class="medium">Fecha:</td>
				<td><?=form_input($fecha)?></td>
			</tr>
			<tr>
				<td class="medium">Depósito:</td>
				<td>
					<?=form_dropdown('deposito', $depositosOptions, $selectedDeposito, 'id = "selDeposito"')?>
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
	<h2>Detalle de la Compra:</h2>
	<div class="canvas-body">
		<table border="0" width="100%" cellspacing="1" class="prodSelect">
			<?
			$cssClass = "trdark";
			
			foreach($productos as $key => $producto)
			{
				$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
			
				?>
				<tr class="<?=$cssClass?>">
					<td width="30"><?=form_input($producto)?></td>
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
			<a href="<?=base_url( 'index.php/compras/')?>">Cancelar</a> | 
			<?=form_submit('mysubmit', 'Crear Nueva Compra')?>
		</div>
		<?=form_close()?>
	</div>
</div>
<script type="text/javascript">
	
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
	
	$( ".input_cantProd" ).keyup(function(){
		calcularTotales();
	});
	
	calcularTotales();

</script>