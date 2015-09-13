<html>
	<head>
		<title><?=(isset($title)) ? $title : 'Mantas Campomar - Backend'?></title>
		
		<meta charset="utf-8" />
		
		<!-- grocery crud -->
        <?php
        if ( isset($crud->css_files) )
        {
            foreach ( $crud->css_files as $file ): ?><link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" /><?php endforeach;
        }
        else
        {
	        ?><script src="<?=base_url('assets/grocery_crud/js/jquery-1.10.2.min.js')?>"></script><?
        }
        if ( isset($crud->js_files) )
        {
            foreach( $crud->js_files as $file ): ?><script src="<?php echo $file; ?>"></script><?php endforeach;
        }
        ?>
		<!-- jQuery -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/redmond/jquery-ui.css" />
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="<?=base_url('assets/js/jquery.numeric.js')?>"></script>
		<script src="<?=base_url('assets/js/javascript.js')?>"></script>
		<script src="<?=base_url('assets/js/jquery.formatCurrency-1.4.0.js')?>"></script>
		<script src="<?=base_url('assets/js/bootstrap/js/bootstrap.js')?>"></script>
		<script src="<?=base_url('assets/js/bootstrap/bootstrap-confirmation.js')?>"></script>
		<script type="text/javascript">
		
			var baseurl = '<?=base_url()?>';
		
			$(function() {
				$( "#datepicker" ).datepicker();
			});
			
			/*
			setTimeout(function()
			{
				$('.message-box').fadeOut();
			}, 10000 );
			*/
			
			$(document).ready(function()
			{
			    $(".numeric").numeric();
			    
			    $("#agregarDetalle").click(function(){
					$('.detalle').addClass( "on" );
					$('.detalle').removeClass( "off" );
					//alert('s');
				});
				
				$('.btn').confirmation('hide');
				
				$(".message-box").click(function(){
				
					if ( $(this).find('.clickable').length )
					{
						$(this).find("span").css('display', 'none');
						$(this).find(".clickable").css('display', 'inline');
					}
				});
			});
		</script>
		
		<!-- Custom -->
		<link rel="stylesheet" href="<?=base_url('assets/css/css.css')?>" />
		<link rel="stylesheet" href="<?=base_url('assets/js/bootstrap/css/bootstrap.css')?>" />
		<?
		if (isset($imprimir) && $imprimir)
		{
			?>
			<style>
				header, ul.menu {
					display: none;
				}
				
				body {
					background-color: #fff;
				}
				
				.canvas {
					border: 0;
					margin: 0 auto;
					box-shadow: none;
					width: 95% !important;
				}
			</style>
			<?
		}
		?>
	</head>
	<body>
		<header>
			<img src="<?=base_url('assets/img/logo.png')?>" class="logo" />
			<ul class="menu_header">
				<li><a href="<?=base_url('index.php/configuraciones')?>"<?=(isset($menu_header_seleccionado) && $menu_header_seleccionado=='configuraciones') ? ' class="selected"' : ""?>><span><img src="<?=base_url('assets/img/ico-config.png')?>" class="ico-config" /></span></a></li>
				<li><a href="<?=base_url('index.php/modo/cambiarModo/proveedores')?>"<?=(isset($menu_header_seleccionado) && $menu_header_seleccionado=='agolan') ? ' class="selected"' : ""?>><span>PROVEEDORES</span></a></li>
				<li><a href="<?=base_url('index.php/modo/cambiarModo/stock')?>"<?=(isset($menu_header_seleccionado) && $menu_header_seleccionado=='stock') ? ' class="selected"' : ""?>><span>INFORMES</span></a></li>
				<li><a href="<?=base_url('index.php/modo/cambiarModo/consignacion')?>"<?=(isset($menu_header_seleccionado) && $menu_header_seleccionado=='consignacion') ? ' class="selected"' : ""?>><span>CONSIGNACION</span></a></li>
				<li><a href="<?=base_url('index.php/oficina"')?>"<?=(isset($menu_header_seleccionado) && $menu_header_seleccionado=='oficina') ? ' class="selected"' : ""?>><span>RAUTA</span></a></li>
			</ul>
		</header>
		<?
			$flash_alert	= get_flash_alert();
			$flash_ok		= get_flash_ok();
				
			if ($flash_alert)
			{
				?>
				<div class="message-box">
					<div class="flash_alert"><?=$flash_alert?></div>
				</div>
				<?
			}

			if ($flash_ok)
			{
				?>
				<div class="message-box-ok">
					<div class="flash_ok"><?=$flash_ok?></div>
				</div>
				<?
			}
			
			if ( $_cierreEjercicio != $_cierreEjeConfig )
			{
			
				?><div class="cierre-de-ejercicio">Cierre de Ejercicio: <?=$_cierreEjercicio?></div><?
			}
		?>