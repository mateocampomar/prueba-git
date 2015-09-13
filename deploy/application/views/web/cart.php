<?
$this->form_validation->set_error_delimiters('', '');
?>
<script type="text/javascript">

	var producto_total		= <?=$shopping_cartTotal?>;

	function formMode(mode)
	{
		$('#updateorCheckout').val(mode);
	}

	$(document).ready(function()
	{ 
		$("*[type='radio']").click(function()
		{
			var shipping_total = parseFloat( $( '#shipping_' + $(this).attr('value') ).val() ) * <?=$shopping_cartItemsCount?>;
		
			$('#shipping_total').html( shipping_total ).formatCurrency();
			
			$('#order_total').html( producto_total + shipping_total ).formatCurrency();
	                 
		});
	});

</script>
<div class="bdycontainer">
	<?
	if ( count($shopping_cart) )
	{
		?>
		<?=$form_open?>
		<table border="0" width="100%" cellspacing="0">
			<tr>
				<td width="55%" valign="top">
					<h2>Order Details</h2>
					<table border="0" width="100%" class="orderdetails">
						<?
						foreach($shopping_cart as $producto)
						{
							?>
							<tr class="producto">
								<td valign="top">
									<img src="<?=base_url('assets/img/web/catalogo/50x50/' . $producto['nombre_unico'])?>.jpg" style="width:50px;height:50px;"/>
								</td>
								<td valign="top">
									<a class="product" href="<?=base_url('index.php/web/producto/' . $producto['categoria_unique_name'] . '/' . $producto['tipo_unique_name'] . '-' . $producto['tipo_id'] . '#' . $producto['nombre_unico'] . '-' . $producto['producto_id'] )?>">
										<?=$producto['categoria_nombre_singular']?> <?=$producto['tipo_nombre']?> (<?=$producto['nombre_web']?>)
									</a>
									<br/>
									<?=$producto['medidas_web']?><br/>
								</td>
								<td valign="top">
									<?=currency_webFormat( $producto['precioWeb'] )?><br/>
									<?=form_error('producto_' . $producto['producto_id'])?>
									<?=($producto['precio_avg'] != $producto['precioWeb'] ) ? '<span class="pricechange">The price of this item has changed.</span>' : '';?>
								</td>
								<td valign="top">
									x<?=$producto['input_cantidad']?>
								</td>
								<td valign="top" style="text-align:right;">
									<?=currency_webFormat( $producto['subtotal'] )?><br/>
								</td>
							</tr>
							<?
						}
						?>
						<tr>
							<td colspan="5"><hr/></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td style="text-align:right;" colspan="2">
								<?=form_submit('mysubmit', 'Update Cart', 'onclick="formMode(\'update\')"')?> | sub Total
								<strong>Items (<?=$shopping_cartItemsCount?>)</strong>
							</td>
							<td style="text-align:right;"><strong><?=currency_webFormat( $shopping_cartTotal )?></strong></td>
						</tr>
						<tr><td><br/><br/></td></tr>
						<tr>
							<td colspan="2"><h2>Shipping & handling</h2></td>
							<td colspan="2">
								<ul class="shipping">
									<li><input type="radio" name="shipping" class="shipping" value="free"<?=( $shippingMethod == 'free' )		? ' checked="checked"' : ''?>> FREE 15 day shipping</li>
									<li><input type="radio" name="shipping" class="shipping" value="two_day"<?=( $shippingMethod == 'two_day' )	? ' checked="checked"' : ''?>> Two day shipping</li>
								</ul>
								<input type="hidden" name="shipping_free" id="shipping_free" value="<?=config_item('shipping_free')?>"/>
								<input type="hidden" name="shipping_two_day" id="shipping_two_day" value="<?=config_item('shipping_two_day')?>"/>
							</td>
							<td valign="top" style="text-align:right;">
								<strong>
									<span id="shipping_total"><?=currency_webFormat( config_item('shipping_' . $shippingMethod ) * $shopping_cartItemsCount )?></span>
								</strong>
							</td>
						</tr>
						<tr>
							<td colspan="5"><hr/></td>
						</tr>
						<tr>
							<td colspan="3"></td>
							<td style="text-align:right;"><strong>TOTAL</strong></td>
							<td style="text-align:right;">
								<strong>
									<span id="order_total"><?=currency_webFormat( $shopping_cartTotal + config_item('shipping_' . $shippingMethod ) * $shopping_cartItemsCount )?></span>
								</strong>
							</td>
						</tr>
						<?
							if ( !config_item('cart_shipping_address') )
							{
								?>
								<tr>
									<td colspan="5" style="text-align:right;">
										<input type="hidden" name="updateorCheckout" id="updateorCheckout" value="update" />
										<input type="submit" value="CHECKOUT" onclick="formMode('checkout')"/>
									</td>
								<tr>
								<?
							}
						?>
					</table>
				</td>
				<?
				if ( config_item('cart_shipping_address') )
				{
					?>
					<td></td>
					<td width="35%" valign="top">
						<h2>Shipping Details</h2>
						<table width="100%" border="0" class="shipdetails">
							<?
							foreach($shippingForm as $input)
							{
								?>
								<tr>
									<td>
										<?
										if (form_error($input[1]))
										{
											?>
											<span style="color:red;"><?=$input[0]?> - <?=form_error($input[1])?></span>
											<?
										}
										else
										{
											?><?=$input[0]?><?
										}
										?>
									</td>
								</tr>
								<tr>
									<td><?=$input[2]?></td>
								</tr>
								<?
							}
							?>
							<tr>
								<td>
									<input type="hidden" name="updateorCheckout" id="updateorCheckout" value="update" />
									<input type="submit" value="CHECKOUT" onclick="formMode('checkout')"/>
								</td>
							<tr>
						</table>
					</td>
				<?
				}
			?>
			</tr>
		</table>
		<?=$form_close?>
		<?
	}
	else
	{
		?>No hay productos en el carrtito<?
	}
	?>
	<p>
		LEGALES: Traducción hecha por H. Rackham en 1914<br>
		<br/>
		"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?"
		<br/><br/>
		Sección 1.10.33 de "de Finibus Bonorum et Malorum", escrito por Cicero en el 45 antes de Cristo
		<br/><br/>
		"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat."
	</p>
</div>