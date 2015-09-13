<script type="text/javascript">

	var selectedId	= null;
	var precio		= new Array;
	var medidas		= new Array;
	var nombre_web	= new Array;
	var nombre_unico= new Array;
	var precioActual= 0;
	
    $(window).resize(function () {
		resizeScreen();
    });
    
    $(window).load(function() {
	    resizeScreen();
	    comenzarSlider();
    });

</script>
<div class="bgimage">
	<ul class="bgslider">
		<?
		foreach($selectProducto as $producto)
		{
			if ( file_exists( 'assets/img/web/catalogo/large/' . $producto['nombre_unico'] . '.jpg' ) )
			{
				?>
				<li>
					<img src="<?=base_url('assets/img/web/catalogo/large/' . $producto['nombre_unico'])?>.jpg" id="foto_<?=$producto['id']?>" class="large_photo" />
				</li>
				<?
				echo "\n";
			}
		}
		?>
	</ul>
	<h1 class="fixed"><span><?=$categoria->nombre_singular?></span> > <?=$tipoProducto->nombre?></h1>
	<div class="cart_container">
		<p><?=$tipoProducto->descripcion?></p>
		<p><span id="medidas">-</span></p>
		<ul class="select_producto">
		<?
		
		foreach($selectProducto as $producto)
		{
			$firstId = ( !isset($firstId) ) ? $producto['id'] : $firstId;
		
			?>
			<li onclick="selectProducto(<?=$producto['id']?>)" id="prod<?=$producto['id']?>" title="<?=$producto['nombre_web']?>">
				<img src="<?=base_url('assets/img/web/catalogo/50x50/' . $producto['nombre_unico'])?>.jpg" />
				<script type="text/javascript">
				
					precio[<?=$producto['id']?>]		= <?=$producto['precio_web']?>;
					medidas[<?=$producto['id']?>]		= "<?=addslashes( $producto['medidas_web'] )?>";
					nombre_web[<?=$producto['id']?>]	= '<?=$producto['nombre_web']?>';
					nombre_unico[<?=$producto['id']?>]	= '<?=$producto['nombre_unico']?>';
				
				</script>
			</li>
			<?
		}
		
		?>
		</ul>
		<table width="100%">
			<tr>
				<td>Quantity</td>
				<td></td>
			</tr>
			<tr>
				<td><?=$dropdown_qty?> x <span id="precio">-</span></td>
				<td colspan="2" style="text-align:right;">subtotal <strong><span id="subtotal">-</span></strong></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;"><?=$subimt?></td>
			</tr>
		</table>
	</div>
</div>
<div id="ficha_tecnica">

	<h2>Descripción del Producto</h2>
	<p>afasfdsM</p>
	<p>afasfdsM</p>
	<p>afasfdsM</p>
	<p>afasfdsM</p>
	<p>afasfdsM</p>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<script type="text/javascript">
	$(document).ready(function()
	{
		var hash = window.location.hash.substr(1);

		if (hash)
		{
			var parts = hash.split("-");
			
			selectProducto( parts[parts.length-1] );
		}
		else		selectProducto(<?=$firstId?>);
	});

</script>