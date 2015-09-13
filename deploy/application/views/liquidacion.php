<script type="text/javascript">

	var loadIframe1 = <?=($cantidadRecibos) ? 'false' : 'true'?>;
	var loadIframe2 = false;
	//var myWindow 	= null

	function imprimirPage()
	{
		var myWindow = window.open('<?=base_url('index.php/liquidacion/index/imprimir')?>');
	}

	function resizeIframe(obj)
	{
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
		
		<?
		if (isset($imprimir) && $imprimir)
		{
			?>
			
			if ( loadIframe1 && loadIframe2 )
			{
				window.print();
			}
			<?
		}
		?>
	}
</script>
<style>
	.pDiv2, .tDiv {
		display: none;
	}
</style>
<div class="liquidacion canvas" style="width:660px;">
	<?
	if (isset($imprimir) && $imprimir)
	{
		?>
		<img src="<?=base_url('assets/img/logo-imprimir.png')?>" class="imprimir_logo" />
		<hr/>
		<?
	}
	?>
	<div class="fecha_right"><?=fechaToText( date('Y-m-d') )?></div>
	<h1><?=($localObj) ? 'Liquidación: <strong>' . $localObj->nombre . '</strong>' : '<strong>Notas de Crédito</strong>'?></h1>
	<div class="canvas-body">
		<table class="info">
			<!--
			<tr>
				<td class="small" width="40%">Liquidación anterior:</td>
				<td><?=($localObj->ultima_liquidacion) ? fechaToText( $localObj->ultima_liquidacion ) : '-'?></td>
			</tr>
			-->
			<tr>
				<td class="small" width="200">Cantidad de productos en consignación:</td>
				<td><?=($movimientosEnConsignacion->cantidadEnConsignacion) ? $movimientosEnConsignacion->cantidadEnConsignacion : 0?></td>
			</tr>
			<tr>
				<td class="small">Total:</td>
				<td>$ <?=currency_format( $movimientosEnConsignacion->totalEnConsignacion * $this->config->item('iva') )?> <span style="font-size:8pt;font-weight:normal;color:#999;">(iva inc.)</span></td>
			</tr>
	
		</table>
	</div>
	<?
	if ( $cantidadRecibos )
	{
		?>
		<h2>Detalle de ventas informadas:</h2>
		<div class="canvas-body">
			<div>
				<iframe onload="javascript:loadIframe1=true;resizeIframe(this);" name="sin_vender" src="<?=base_url('index.php/liquidacion/iFrameConsignadosVendidos')?><?if(isset($imprimir) && $imprimir) echo "/width100";?>" frameborder="0" scrolling="no" id="iframe_vendidos" style="width:100%;"></iframe>
			</div>
			<div class="footer_canvas">
				<br/>
				<table border="0" align="right">
					<tr>
						<td>Cantidad de Productos:</td>
						<td class="numb">x <?=$cantidadRecibos ?></td>
					</tr>
					<tr>
						<td>Subtotal</td>
						<td>$ <?=currency_format( $totalRecibos / $this->config->item('iva') )?></td>
					</tr>
					<tr>
						<td>iva</td>
						<td>$ <?=currency_format( $totalRecibos - $totalRecibos / $this->config->item('iva') )?></td>
					</tr>
					<tr class="total">
						<td>Total ventas informadas:</td>
						<td class="numb">$ <?=currency_format( $totalRecibos )?></td>
					</tr>
				</table>
			</div>
		</div>
		<br/>
		<?
	}

	// Productos con Nota de Crédito
	if ( $cantidadNc )
	{
		?>
		<h2>Detalle de Notas de Crédito:</h2>
		<div class="canvas-body">
			<div>
				<iframe onload="javascript:loadIframe1=true;resizeIframe(this);" name="con_nc" src="<?=base_url('index.php/liquidacion/iFrameConsignadosConNC')?><?if(isset($imprimir) && $imprimir) echo "/width100";?>" frameborder="0" scrolling="no" id="iframe_nc" style="width:100%;"></iframe>
			</div>
			<div class="footer_canvas">
				<br/>
				<table border="0" align="right">
					<tr>
						<td>Cantidad de Productos:</td>
						<td class="numb">x <?=$cantidadNc ?></td>
					</tr>
					<tr>
						<td>Subtotal</td>
						<td>$ <?=currency_format( $totalNc / $this->config->item('iva') )?></td>
					</tr>
					<tr>
						<td>iva</td>
						<td>$ <?=currency_format( $totalNc - $totalNc / $this->config->item('iva') )?></td>
					</tr>
					<tr class="total">
						<td>Total Notas de Crédito:</td>
						<td class="numb">$ <?=currency_format( $totalNc )?></td>
					</tr>
				</table>
			</div>
		</div>
		<br/>
		<?
	}
	?>
	<h2>Detalle de productos que continúan en consignación:</h2>
	<div class="canvas-body">
		<div>
			<iframe onload="javascript:loadIframe2=true;resizeIframe(this);" name="sin_vender" src="<?=base_url('index.php/liquidacion/iFrameConsignadosSinVender')?><?if(isset($imprimir) && $imprimir) echo "/width100";?>" frameborder="0" scrolling="no" id="iframe_sin_vender" style="width:100%;"></iframe>
		</div>
		<div class="footer_canvas">
			<br/>
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <?=($movimientosEnConsignacion->cantidadEnConsignacion - $cantidadRecibos - $cantidadNc ) ?></td>
				</tr>
				<tr>
					<td>Subtotal</td>
					<?
					$subTotal = $movimientosEnConsignacion->totalEnConsignacion - $totalRecibos / $this->config->item('iva') - $totalNc / $this->config->item('iva');
					?>
					<td>$ <?=currency_format( $subTotal )?></td>
				</tr>
				<tr>
					<td>iva</td>
					<td>$ <?=currency_format( $subTotal * $this->config->item('iva') - $subTotal )?></td>
				</tr>
				<tr class="total">
					<td>Total productos que continúan en consignación:</td>
					<td class="numb">$ <?=currency_format( $subTotal * $this->config->item('iva') )?></td>
				</tr>
			</table>
		</div>
		<br />
		<hr/>
		<div class="datos">
			<div class="actions">
				<?
				if (!isset($imprimir) || !$imprimir)
				{
					?>
					<a id="guararLiquidacion">Marcar Fecha de Liquidación</a>
					<input type="text" id="fechaLiquidacion" name="fechaLiquidacion" style="border:1px solid #fff;width:0;height: 0;" />
					 | 
					<a href="javascript:imprimirPage()">Imprimir</a>
					<?
				
				}
				?>
			</div>
			<!-- Creado el: <?=date('Y-m-d H:i:s')?>--><br/>
		</div>
	</div>
</div>
<script type="text/javascript">

	$('#guararLiquidacion').click(function() {
	
		$('#fechaLiquidacion').datepicker({
			onSelect: function(date) {
				var myWindow = window.open("<?=base_url('index.php/liquidacion/guardarLiquidacion/" + date + "')?>", "_self");
			}
		});
		
		$('#fechaLiquidacion').focus()
	});

</script>