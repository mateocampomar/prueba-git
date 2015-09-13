<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modo extends My_Controller {

	public function cambiarModo( $modo, $localId=false )
	{
		// Set Modo
		if		($modo == 'consignacion')	$modo = 'consignacion';
		elseif	($modo == 'stock')			$modo = 'stock';
		else								$modo = 'proveedores';


		// Set Session
		$this->session->set_userdata( 'modo', $modo);


		// Redirect
		if ($modo == 'consignacion')
		{
			if ( $localId )
			{
				redirect( base_url( 'index.php/local/cambiarSession/' . $localId ) , 'location');
			}
			else
			{
				redirect(base_url( 'index.php/stock/resumen_local'), 'location');
			}
		}
		elseif	($modo == 'stock')				redirect(base_url( 'index.php/stock/local'), 'location');
		else									redirect(base_url( 'index.php/compras'), 'location');
	}
}