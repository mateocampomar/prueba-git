<!DOCTYPE html>
<html>
	<head>
		<title><?=(isset($title)) ? $title : 'Mantas Campomar - Web'?></title>
		
		<meta charset="utf-8" />

		<script src="<?=base_url('assets/grocery_crud/js/jquery-1.10.2.min.js')?>"></script>
		<script src="<?=base_url('assets/js/jquery.numeric.js')?>"></script>
		<script src="<?=base_url('assets/js/javascript_web.js')?>"></script>
		<script src="<?=base_url('assets/js/jquery.formatCurrencyWeb-1.4.0.js')?>"></script>
		<script type="text/javascript">
		
			var baseurl = '<?=base_url()?>';

		</script>
		
		<!-- Custom -->
		<link rel="stylesheet" href="<?=base_url('assets/css/css_web.css')?>" />
	</head>
	<body>
		<script type="text/javascript">

				var winHeight	= null;
				var winWidth	= null;

				$(document).ready(function()
				{
					winHeight	= $(window).height();
					winWidth	= $(window).width();
					
					// Min width/height
					if (winHeight < 500)	winHeight	= 500;
					if (winWidth < 500)		winWidth	= 500;
				});
		
		</script>
		<?
		if (isset($header_title) && $header_title)
		{
			$header_style = ' style="position:relative;"';
		}
		else
		{
			$header_style = '';
		}
		?>
		<header<?=$header_style?>>
			<div class="top_menu">
				<ul class="main_menu">
					<li><a href="<?=base_url('index.php/web/estatica/fabrica' )?>">Historia de la Fábrica</a></li>
					<li><a href="<?=base_url('index.php/web/estatica/lana' )?>">La Lana</a></li>
					<li><a href="<?=base_url('index.php/web/estatica/contacto' )?>">Datos de Contacto</a></li>
					<li style="font-style: normal;"> | </li>
					<?	
					foreach($categoriaProductos as $categoriaProducto)
					{
						?>
						<li>
							<a href="<?=base_url('index.php/web/catalogo/' . $categoriaProducto->unique_name )?>"><?=$categoriaProducto->nombre?></a>
						</li>
						<?
					}
					?>
					<li><a href="<?=base_url('/')?>"><img src="<?=base_url('assets/img/web/ico-campomar.png')?>" /></a></li>
				</ul>
				<div class="cart_menu">
					<img src="<?=base_url('assets/img/shopping_cart.png')?>" /> 
					<span class="cart_menu_text">
						<span id="cantidadEnCarrito"><?=$shoppingCartCantidad?></span> Items (<span id="totalEnCarrito"><?=currency_webFormat( $shoppingCartTotal )?></span>)
						<span class="noItalics">|</span> 
						<a href="<?=base_url('index.php/web/cart')?>" class="checkout">CHECKOUT</a>
					</span>
				</div>
			</div>
			<ul id="shoppingcart_quickDisplay"></ul>
		</header>