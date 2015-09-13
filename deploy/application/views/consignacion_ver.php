<style>
	.pDiv2 {
		display: none;
	}
	
	<?
	if (isset($imprimir) && $imprimir)
	{
		?>
		.tDiv {
			display: none;
		}
		<?
	}
	?>
</style>
<script type="text/javascript">

	function renumerar()
	{
		
		var consignacionNumero=prompt("Factura Número:");

		if (consignacionNumero!=null)
		{
			window.location.href = "<?=base_url('index.php/consignacion/cambiarNumeroConsignacion/' . $consignacion->id)?>/" + consignacionNumero;
		}
	}
	
	function anular()
	{
		var r = confirm("Anular la Factura?\n\nNOTA IMPORTANTE: Los productos serán devueltos al Depósito de Origen:\n\n- <?=$consignacion->deposito_nombre?>");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/consignacion/anularConsignacion/' . $consignacion->id)?>/";
		}
	}

	function imprimirPage()
	{
		var myWindow = window.open('<?=base_url('index.php/consignacion/ver/' . $consignacion->id . "/imprimir")?>');
		
		myWindow.print();
		
	}

</script>
<div class="facturas canvas<?=($consignacion->status) ? '' : ' anulado'?>" style="width:660px;">
	<?
	if (isset($imprimir) && $imprimir)
	{
		?>
		<img src="<?=base_url('assets/img/logo-imprimir.png')?>" class="imprimir_logo" />
		<hr/>
		<?
	}
	?>
	<div class="fecha_right"><?=fechaToText( $consignacion->fechaManual )?></div>
	<h1>Consignación: <strong><?= ($consignacion->facturaNumero) ? addZeros( $consignacion->facturaNumero ) : "s/n" ?></strong></h1>
	<?
	if ( $consignacion->status == 2 )
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
		if ( $consignacion->status == 0 )
		{
			?><div class="anulado">ANULADO</div><?
		}
		?>
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/consignacion/ver/' . $consignacion->id ))?>
		<table class="info">
			<?
			if ( $consignacion->status != 0 )
			{
				?>
				<tr>
					<td class="small" width="30%" style="vertical-align:top;position:relative;top:5px;">Local:</td>
					<td>
						<?=nl2br($consignacion->localNombre)?>
						<div style="font-size:10pt; line-height: 2em; color: #666; padding-bottom:20px;">
							<?
							echo ( !empty( $consignacionLocal->razon_social ) )	? $consignacionLocal->razon_social . "<br/>"	: "";
							echo ( !empty( $consignacionLocal->rut ) )			? $consignacionLocal->rut . "<br/>"				: "";
							echo ( !empty( $consignacionLocal->direccion ) )	? $consignacionLocal->direccion . "<br/>"		: "";
							?>
						</div>
					</td>
				</tr>
				</table>
				<hr/>
				<br/>
				<table class="info">
			<?
			}

			// Factura Número
			if ( $consignacion->status == 2 )
			{
				?>
				<tr>
					<td class="medium" width="30%">Factura Número:</td>
					<td><?=form_input($facturaNumero)?></td>
				</tr>
				<?
			}

			// Depósito
			if ( $consignacion->status == 2 )
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
			elseif ( $consignacion->status == 1 )
			{
				?>
				<tr>
					<td class="small" width="30%">Depósito de Origen:</td>
					<td><?=nl2br($consignacion->deposito_nombre)?></td>
				</tr>
				<tr>
					<td class="small">Fecha de Procesado:</td>
					<td><p><?=nl2br( $consignacion->fechaProcesado )?></p></td>
				</tr>
				<?
			}
			
			// Detalle
			if ( $consignacion->status == 2 )
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
				if ( $consignacion->detalle )
				{
					?>
					<tr>
						<td class="small detalle">Detalle:</td>
						<td><p><?=nl2br($consignacion->detalle)?></p></td>
					</tr>
					<?
				}			
			}
			?>
		</table>
		<?
		if ( $consignacion->status == 2 )
		{
			?>
			<br/>
			<div class="submit">
				<a href="<?=base_url( 'index.php/consignacion/')?>">Cancelar</a> | 
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
					<td class="numb">x <?=$consignacion->cantidad ?></td>
				</tr>
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <?=currency_format( $consignacion->total )?></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <?=currency_format( $consignacion->iva  )?></td>
				</tr>
				<tr class="total">
					<td>TOTAL:</td>
					<td class="numb">$ <?=currency_format( $consignacion->total_iva_inc  )?></td>
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
					if ( $consignacion->status == 1 )
					{
	
						?>
							<a href="<?=base_url('index.php/consignacion/facturaOficial/' . $consignacion->id) ?>" target="_blank">Factura Oficial</a> | <a href="javascript:imprimirPage()">Imprimir</a> | <a href="javascript:renumerar()">Renumerar</a>
						<?
					}
					
					if ( $consignacion->status != 0 )
					{
						?> | <a href="javascript:anular()">Anular</a><?
					}
					?>
				</div>
				<?
			}
			?>
			Creado el: <?=$consignacion->fechaAuto?><br/>
		</div>
	</div>
</div>