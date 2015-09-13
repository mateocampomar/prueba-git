<script type="text/javascript">
	function anularFactura()
	{
		var r = confirm("Anular la Venta Oficina?\n\nNOTA IMPORTANTE: Si la factura fue procesada, los productos serán devueltos al Depósito de Origen:\n\n- <?=$venta->deposito_nombre?>");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/oficina/anular/' . $venta->id)?>";
		}
	}

	function renumerar()
	{
		
		var oficinaNumero=prompt("Factura Número:");

		if ( oficinaNumero != null )
		{
			window.location.href = "<?=base_url('index.php/oficina/cambiarNumero/' . $venta->id)?>/" + oficinaNumero;
		}
	}
	
	function confirmarAvanzar()
	{

		$( "#selDeposito option:selected" ).each(function()
		{
			var selectedDeposito = $( this ).val();
			
			if ( selectedDeposito != '0')
			{

				var r = confirm("AVANZAR\n\nNOTA IMPORTANTE: Los productos serán retirados del depósito.");
				if (r == true)
				{
					window.location.href = "<?=base_url('index.php/oficina/avanzarPedido/' . $venta->id )?>/" + selectedDeposito;
				}
			}
			else
			{
				alert('Seleccioná un depósito');
			}
		});
	}

</script>
<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="oficina canvas<?=($venta->status) ? '' : ' anulado'?> " style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $venta->fechaManual )?></div>
	<h1><?
	
		if ( !$venta->tipo_venta )
		{
			?>Venta/Factura: <strong><?= ( $venta->facturaNumero ) ? addZeros( $venta->facturaNumero ) : "s/n" ?></strong><?
		}
		else
		{
			?>Venta Web: <strong><?=addZeros( $venta->id )?></strong><?
		}
	
	?></h1>
	<?
	if ( $venta->status == 2 )
	{
		?>
		<div class="message-box">
			<div class="flash_alert">
				<?
					if ( $stockOk )
					{
						?>Sin Procesar<?
					}
					else
					{
						?><strong>No se puede procesar:</strong> No hay stock de al menos un producto.<?
					}
				?>
			</div>	
		</div>
		<?
	}
	?>
	<div class="canvas-body">
		<?
			if ( $venta->status == 0)
			{
				?><div class="anulado">ANULADA</div><?
			}

			if ( $venta->tipo_venta == 'web' )
			{
				?>
				<table class="avance" border="0">
					<tr>
						<td width="33%" class="avance-confirmed<?=($venta->estado_web == 'confirmed' || $venta->estado_web == 'en-proceso' || $venta->estado_web == 'sent' ) ? ' ok' : ''?>">Pago Web</td>
						<td class="avance-sent<?=( $venta->estado_web == 'en-proceso' || $venta->estado_web == 'sent' ) ? ' ok' : ''?>">EN PROCESO</td>
						<td width="33%" class="avance-sent<?=( $venta->estado_web == 'sent' ) ? ' ok' : ''?>">ENVIADO</td>
					</tr>
				</table>
				<?
			}
		?>
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/oficina/verVenta/' . $venta->id ))?>
		<table class="info">
			<tr>
				<td class="small" width="30%">Cliente:</td>
				<td>
					<?
					if ( $venta->tipo_venta == 'web' )
					{
						?><a href=""><?=$venta->cliente?></a><?
					}
					else
					{
						echo $venta->cliente;
					}
					?>
				</td>
			</tr>
			
			<?

			// Factura Número
			if ( !empty( $venta->direccion) || !empty( $venta->rut ) )
			{
				?>
				<tr>
					<td class="small">RUT:</td>
					<td><?=$venta->rut?></td>
				</tr>
				<tr>
					<td class="small">Dirección:</td>
					<td><?=$venta->direccion?></td>
				</tr>
				<?
			}
			
			?>
		</table>
		<hr/>
		<br/>
		<table class="info">
			<?

			// Factura Número
			if ( $venta->status == 2 )
			{
				?>
				<tr>
					<td class="medium" width="30%">Factura Número:</td>
					<td><?=form_input($facturaNumero)?></td>
				</tr>
				<?
			}

			// Depósito
			if ( $venta->status == 2 || ($venta->tipo_venta == 'web' && $venta->estado_web == 'en-proceso') )
			{
				?>
				<tr>
					<td class="medium">Depósito:</td>
					<td>
						<?=form_dropdown('deposito', $depositosOptions, $selectedDeposito, 'id = "selDeposito"')?>
					</td>
				</tr>
				<?
			}
			else
			{
				?>		
				<tr>		
					<td class="small" width="30%">Depósito de Origen:</td>
					<td><?=$venta->deposito_nombre;?></td>
				</tr>
				<tr>
					<td class="small">Fecha de Procesado:</td>
					<td><p><?=nl2br( $venta->fechaProcesado )?></p></td>
				</tr>
				<?
			}

			// Detalle
			if ( $venta->status == 2 )
			{
				?>
				<tr>
					<td class="medium" style="vertical-align: top;">Detalle:</td>
					<td class="detalle <?=( $showDetalle ) ? 'on' : 'off'?>">
						<a href="#" id="agregarDetalle">Agregar detalle</a>
						<?=form_textarea($detalle)?>
					</td>
				</tr>
				<?
			}
			else
			{
				if ( $venta->detalle )
				{
					?>
					<tr>
						<td class="small detalle">Detalle:</td>
						<td><p><?=nl2br( $venta->detalle )?></p></td>
					</tr>
					<?
				}			
			}	
			?>
		</table>
		<br/>
		<?
		if ( $venta->status == 2 )
		{
			?>
			<br/>
			<div class="submit">
				<a href="<?=base_url( 'index.php/oficina/')?>">Cancelar</a> | 
				<?=form_submit('mysubmit', 'Procesar y Retirar Productos Del Stock')?>
			</div>
			<?
		}
		?>
	</div>
	<h2>Detalle del Pedido:</h2>
	<div class="canvas-body">
		<div>
			<?=$crud->output?>
		</div>
		<br />
		<div class="footer_canvas">
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <?=$venta->cantidad ?></td>
				</tr>
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <?=currency_format( $venta->total )?></td>
				</tr>
				<?
					if ( $venta->tipo_venta != 'web' )
					{
						?>
						<tr>
							<td>IVA:</td>
							<td class="numb">$ <?=currency_format( $venta->iva  )?></td>
						</tr>
						<?
					}
				?>
				<tr class="total">
					<td>TOTAL:</td>
					<td class="numb">$ <?=currency_format($venta->total_iva_inc)?></td>
				</tr>
			</table>
		</div>
		<?=form_close()?>
		<br/>
		<hr/>
		<div class="datos">
			<?
			if (!isset($imprimir) || !$imprimir)
			{
				?>
				<div class="actions">
					<?

					if ( $venta->status == 1 )
					{
	
						?><a href="<?=base_url('index.php/oficina/facturaOficial/' . $venta->id) ?>" target="_blank">Factura Oficial</a> | <a href="javascript:renumerar()">Renumerar</a> | <?
					}

					if ( $venta->status != 0 )
					{
						?><a href="javascript:anularFactura()">Anular</a><?
					}
					?>
				</div>
				<?
			}
			?>
			Creado el: <?=$venta->fechaAuto?><br/>
		</div>
	</div>
</div>