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
		<li><img src="<?=base_url('assets/img/web/mockups/home1.jpg')?>" class="large_photo" /></li>
		<li><img src="<?=base_url('assets/img/web/mockups/home2.jpg')?>" class="large_photo" /></li>
		<li><img src="<?=base_url('assets/img/web/mockups/home3.jpg')?>" class="large_photo" /></li>
	</ul>
	<h1 class="center"><span>Amar la Lana</span></h1>
	<h2 class="center">desde 1902</h2>
	<ul class="submenu">
		<?
		foreach($categoriaProductos as $categoriaProducto)
		{
			?>
			<li>
				<strong><a href="<?=base_url('index.php/web/catalogo/' . $categoriaProducto->unique_name )?>"><?=$categoriaProducto->nombre?></a></strong><br/>
				<?=$categoriaProducto->descripcion?><br/>
				<br/>
				<small>desde: <?=currency_webFormat($categoriaProducto->min_precioWeb)?></small>
			</li>
			<?
		}
		?>
	</ul>
</div>