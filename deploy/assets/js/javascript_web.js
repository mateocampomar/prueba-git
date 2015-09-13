var zIndexCount = 0;
var selectedEq	= 0;
var timeout;
var lapse		= 10000;

function resizeScreen()
{
	var imgWidth		= 900;
	var imgHeight		= 615;
	var numberOfSlider	= 0;


	// Resize del fondo
	$('.bgimage').css('width', winWidth + 'px');
	$('.bgimage').css('height', winHeight + 'px');

	var newHeight = Math.round( imgHeight * winWidth / imgWidth);
	
	if (newHeight < winHeight)
	{
		var newWidth	= Math.round( imgWidth * winHeight / imgHeight);
			newHeight	= winHeight;
	}
	else
	{
		var newWidth = winWidth;
	}

	// Resize de cada slide container <li>
	$( ".bgslider li" ).css('width', winWidth + 'px');
	$( ".bgslider li" ).css('height', winHeight + 'px');

	// Resize de las imagenes
	$( ".bgslider li img" ).css('width', newWidth + 'px');
	$( ".bgslider li img" ).css('height', newHeight + 'px');
	
	// Posicionamiento de las imagenes
	$( ".bgslider li img" ).css('top',	Math.round( ( winHeight	- newHeight )	/ 2 ) + 'px');
	$( ".bgslider li img" ).css('left',	Math.round( ( winWidth	- newWidth )	/ 2 ) + 'px');

	// Resize de cada uno de los slides.
	$( ".bgslider li" ).each(function() {
		numberOfSlider++;
  	});
  	
  	// Títulos y otras cosas.
  	
  	var h1Top	= Math.round( winHeight / 3 );
  	var h1Left	= Math.round( ( winWidth - $('h1.center').width() ) / 2 );
  	
  	$('h1.center').css('margin-top', h1Top + 'px');
  	$('h1.center').css('margin-left', h1Left + 'px');
  	
  	$('h2.center').css('margin-top', ( h1Top + $('h1.center').height() + 30 ) + 'px');
  	$('h2.center').css('margin-left', Math.round( ( winWidth - $('h2.center').width() ) / 2 ) + 'px');
  	
  	$('.submenu').css('left', Math.round( ( winWidth - $('.submenu').width() ) / 2 ) + 'px');
  	$('.submenu').css('top', Math.round( winHeight / 1.4 ) + 'px');
	
	// Ejecutar scroll
	calculateScroll();
}

function comenzarSlider()
{	
	$( ".bgslider li" ).each(function() {
		
		zIndexCount--;
	
		$(this).css('zIndex', zIndexCount);
  	});
  	
  	timeout = setTimeout('siguienteSlide()', lapse);
  	
  	$('.bgslider').click(function() {
	
		siguienteSlide();
	});
}

function siguienteSlide()
{
	zIndexCount--;
	
	clearTimeout(timeout);

	if ($( ".bgslider li" ).length <= selectedEq)
	{
		selectedEq = 0;
	}

	$( ".bgslider" ).find( "li" ).eq( selectedEq ).fadeOut( 1500, function() {

		$(this).css('zIndex', zIndexCount);
		
		$(this).fadeIn( "fast", function() {
		
		});
	});

	selectedEq++;
	
	timeout = setTimeout('siguienteSlide()', lapse);
}

$( window ).scroll(function() {

	calculateScroll();
});

function calculateScroll()
{
	var scroll = $(this).scrollTop();

	//$('.cart_container').css('bottom', scroll * 4 + 10 + 'px');
	
	//$('.bgimage h1.fixed').css('top', scroll + 'px' );
	
	if (scroll > 0)
	{
		if ( (winHeight + scroll) <= $(document).height() )
		{
			$('.bgimage').css('height', winHeight - scroll + 'px');
		}
	}
	else
	{
		$('.bgimage').css('height', winHeight + 'px');
	}
	
	console.log( (winHeight) + "-" + $(document).height() );
}

function selectProducto(id)
{
	selectedId = id;

	$('#precio').html(precio[id]).formatCurrency();
	
	$('#precio').html($('#precio').html());
	
	precioActual = precio[id];
	
	$('#nombre_web').html(nombre_web[id]);
	$('#medidas').html(medidas[id]);
	
	document.location.hash = nombre_unico[id] + '-' + id;
	
	//$('.select_producto li').css('border-color', '#fff');
	
	//$('.bgimage .large_photo').css('display', 'none');
	//$('#foto_' + selectedId).css('display', '');
	
	$('#prod' + id).css('border-color', '#666');
	
	calcularSubTotal();

}

function calcularSubTotal()
{
	$('#subtotal').html( precioActual * $("#cantidad").val() ).formatCurrency();
	
	$('#subtotal').html("$" + $('#subtotal').html())
}

function addToCart()
{
	if ( !$('#addtocart').hasClass('disabled') )
	{
		var url = baseurl + 'index.php/web/addToCart/' + selectedId + '/' + $("#cantidad").val();
	
		$.ajax({
			url: url,
			beforeSend: function( xhr ) {
				
				$('#addtocart').addClass('disabled');
				
			}
		})
		.done(function( data )
		{
			var obj = jQuery.parseJSON( data );
			
			$(obj.view).appendTo("#shoppingcart_quickDisplay").hide().fadeIn( 1000 ).delay( 5000 ).slideUp( 1000 );
			
			$('#totalEnCarrito').html( obj.total ).formatCurrency();
			$('#cantidadEnCarrito').html( obj.cantidad );
		
			$('#addtocart').removeClass('disabled');
		});
	}
}