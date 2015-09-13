<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('datepicker_to_mysql'))
{
    function datepicker_to_mysql ( $date )
    {
		$newDate = date('Y-m-d', strtotime(str_replace('-', '/', $date)));

        return $newDate;
    }
}

if ( ! function_exists('mysql_to_datepicker'))
{
    function mysql_to_datepicker ( $date )
    {
		$newDate = date('m/d/Y', strtotime( $date ));

        return $newDate;
    }
}

if ( ! function_exists('currency_format'))
{
	function currency_format ( $number )
	{
		return number_format($number, 2, ',', '.');;
	}
}

if ( ! function_exists('currency_webFormat'))
{
	function currency_webFormat ( $number )
	{
		return "$ " . number_format($number, 2, '.', '');;
	}
}

if ( ! function_exists('flash_alert'))
{
	function flash_alert ( $alert, $properties = null )
	{
		$CI = & get_instance();

		if ( is_array($properties) )
		{
			// [TODO] - Mejorar las flash alerts pudiendo pasar un color o la instrucción de que no se cierre de forma automática.
		}

		return $CI->session->set_userdata( 'flash_alert', $alert);
	}
}

if ( ! function_exists('get_flash_alert'))
{
	function get_flash_alert ()
	{
		$CI = & get_instance();
	
		$session = $CI->session->userdata( 'flash_alert');

		$CI->session->set_userdata( 'flash_alert', null);
		
		return $session;
	}
}


function flash_ok ( $alert, $properties = null )
{
	$CI = & get_instance();

	if ( is_array($properties) )
	{
		// [TODO] - Mejorar las flash alerts pudiendo pasar un color o la instrucción de que no se cierre de forma automática.
	}

	return $CI->session->set_userdata( 'flash_ok', $alert);
}

function get_flash_ok ()
{
	$CI = & get_instance();

	$session = $CI->session->userdata( 'flash_ok');

	$CI->session->set_userdata( 'flash_ok', null);
	
	return $session;
}


if ( ! function_exists('flash_error'))
{
	function flash_error ( $text )
	{
		error_log( $text );

		die( 'Error grave. Hablar con Mateo: ' . $text );
	}
}

if ( ! function_exists('ceroReplace'))
{
	function ceroReplace ( $text )
	{
		return ($text == 0 || $text == '0,00') ? '-' : $text;
	}
}

if ( ! function_exists('calcularIVA'))
{
	/**
	 * Devuelve un array con los cálculos de IVA
	 */
	function calcularIVA($total)
	{
		$ci = get_instance(); // CI_Loader instance
		$_iva = $ci->config->item('iva');
	
		$array = array(
						'total'			=> $total,
						'iva'			=> $total * $_iva - $total,
						'total_iva_inc'	=> $total * $_iva
		);
	

		return $array;
	}
}

if ( ! function_exists('fechaToText'))
{
	function fechaToText($fecha)
	{
		$parts = explode("-", $fecha);
	

		return $parts[2] . "/" . $parts[1] . "/" . $parts[0];
	}
}

function addZeros($num, $numberOfZeros=5)
{
	return sprintf("%0" . $numberOfZeros . "s", $num);
}

function productoThumbnail( $idProducto )
{
	$productoModel		= new Producto_model();
	$producto = $productoModel->getProducto( $idProducto );
	
	if ( file_exists( 'assets/img/web/catalogo/50x50/' . $producto->nombre_unico . '.jpg' ) )
	{
	
		return '<img src="' . base_url( 'assets/img/web/catalogo/50x50/' . $producto->nombre_unico . '.jpg' ) . '" class="productThumb50"/>';
	}
	else
	{
		return '<img src="' . base_url( 'assets/img/_defaultLogo.png' ) . '" class="productThumb50"/>';
	}
}

function cierreDeEjercicio()
{
	$ci = get_instance(); // CI_Loader instance

	if ( $ci->session->userdata('cierre_de_ejercicio') )
	{
		return	$ci->session->userdata('cierre_de_ejercicio');
	}

	return $ci->config->item('cierre_de_ejercicio');
}

function situacion( $numero )
{
	if		( $numero === null )	$numero	= '<span style="color:#999;">Sin Ventas</span>';
	elseif	( $numero === 0 )		$numero = '<span style="color:#999;">Sin Consig.</span>';
	elseif	( $numero === false )	$numero = '<span style="color:#999;">s/v año</span>';
	else
	{
		if 		( $numero < 15 )		$numero = '<span style="background-color:green; color: #fff; padding: 3px 5px; border-radius: 5px;"> <strong>' . number_format( round( $numero, 1 ), 1, '.', ',') . '</strong> </span>';
		elseif	( $numero > 20 )	$numero = '<span style="background-color:red; color: #fff; padding: 3px 5px; border-radius: 5px;"> <strong>' . number_format( round( $numero, 1 ), 1, '.', ',') . '</strong> </span>';
		else						$numero = '<span style="background-color:#ff9900; color: #fff; padding: 3px 5px; border-radius: 5px;"> <strong>' . number_format( round( $numero, 1 ), 1, '.', ',') . '</strong> </span>';
	}							

	return $numero;
}