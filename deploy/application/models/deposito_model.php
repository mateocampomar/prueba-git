<?php
class Deposito_model extends My_Model {

	public function getDepositosTodos()
	{
		$this->db->from('deposito');
		
		$this->db->where('status', 1);
		
		$this->db->order_by('nombre', 'asc');

		$query = $this->db->get();
		
		return $query->result();
	}
}