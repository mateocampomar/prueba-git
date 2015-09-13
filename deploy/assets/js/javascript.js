function textToFloat(text)
{
	text = text.replace(".","");
	text = text.replace(" ","");
	text = text.replace("$","");
	text = text.replace(",",".");

	return parseFloat( text );
} 

$( document ).ready(function() {
	$( "#search" ).keyup(function()
	{
		var toSearch = $(this).val();
		
		$( ".producto-nombre" ).each(function()
		{
			if ($(this).text().search(new RegExp( toSearch , "i")) < 0)
			{
				$(this).parent().fadeOut();
			}
			else
			{
				$(this).parent().fadeIn();
			}
		});
	});
	
	cambiarPrecio();
});

function ajax_vendido(action, id)
{
	if ( action == 'vendido' )
	{
		var url = baseurl + 'index.php/consignacion/vendido/' + id;
	}
	else
	{
		var url = baseurl + 'index.php/consignacion/desmarcarVendido/' + id;
	}

	$.ajax({
		url: url,
		beforeSend: function( xhr ) {
			
			var width = $('#mov' + id).css('width');
			$('#mov' + id + ' .custom_action').html('...');
		}
	})
	.done(function( data )
	{
		obj = JSON.parse(data);
		
		$('#mov' + id).html(obj.button);
		$('#recibos_bubble').html(obj.recibos);
	});
}

function cambiarPrecio()
{
	$('.guardarPrecioCompra').keypress(function (e)
	{
		if(e.which == 13)
		{
			var id		= $(this).attr('movid');
			var value	= $(this).attr('value');
			
			var thisObj	= this;
		
			$.ajax({
				url: baseurl + 'index.php/stock/cambiarPrecioCompra/' + id + '/' + value,
				beforeSend: function( xhr ) {
		
				}
			})
			.done(function( data )
			{
				if ( data != 'false' )
				{
					$('#dif_precio_compra').html(data);
					$(thisObj).css('background-color', '#ccffcc');
				}
				else
					$(thisObj).css('background-color', '#fff');
			});
		}
	});
}

function displayFiltros()
{
	$('.filtros').css('display', 'block');
}

function editarComentarioMovProd(id, comentario)
{
	var nuevoComentario = prompt("Comentario:", comentario);
	if (nuevoComentario != null && nuevoComentario != comentario ) {

		$('#mp_' + id).css('color', '#ccc');
		$('#mp_' + id).html(nuevoComentario);

		$.ajax({
			url: baseurl + 'index.php/stock/editarComentarioMovProd/' + id + '/' + nuevoComentario,
			beforeSend: function( xhr ) {
	
			}
		})
		.done(function( data )
		{
			$('#mp_' + id).css('color', '#000');
		});
	}
}