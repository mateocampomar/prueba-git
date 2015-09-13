<script type="text/javascript">
	
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
		<li><img src="<?=base_url('assets/img/web/catalogo/manta.jpg')?>" class="large_photo" /></li>
		<li><img src="<?=base_url('assets/img/web/catalogo/frazada.jpg')?>" class="large_photo" /></li>
		<li><img src="<?=base_url('assets/img/web/catalogo/manta.jpg')?>" class="large_photo" /></li>
		<li><img src="<?=base_url('assets/img/web/catalogo/frazada.jpg')?>" class="large_photo" /></li>
	</ul>
	<h1 class="center"><span><?=$categoria->nombre?></span></h1>
	<h2 class="center"><?=$categoria->descripcion?></h2>
	<ul class="submenu">
		<?
		foreach($tipoProductos as $tipo_producto)
		{
			?>
			<li>
				<strong><a href="<?=base_url('index.php/web/producto/' . $categoria->unique_name . '/' . $tipo_producto->unique_name . '-' . $tipo_producto->id )?>"><?=$tipo_producto->nombre?></a></strong><br/>
				<?=$tipo_producto->descripcion?><br/>
				<br/>
				<small>desde: <?=currency_webFormat($tipo_producto->min_precioWeb)?></small>
			</li>
			<?
		}
		?>
	</ul>
</div>