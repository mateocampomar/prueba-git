<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	/**
	 * is_unique_and_not_id
	 *
	 * @params	$params		table.columna.id
	 *
	 */
	function is_unique_and_not_id($str, $params)
	{
		list($table, $field, $notId)=explode('.', $params);

		$this->CI->form_validation->set_message('is_unique_and_not_id', 'El campo %s ya existe.');

		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str, 'id !=' => $notId ));
	
		return $query->num_rows() === 0;
	}
}