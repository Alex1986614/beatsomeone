<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall wishlist model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_wishlist_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'cmall_wishlist';

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'cwi_id'; // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}


	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
	{
		$select = 'cmall_wishlist.*, member.mem_id, member.mem_userid, member.mem_nickname, member.mem_is_admin,
			member.mem_icon, cmall_item.cit_name, cmall_item.cit_key, cmall_item.cit_file_1';
		$join[] = array('table' => 'cmall_item', 'on' => 'cmall_wishlist.cit_id = cmall_item.cit_id', 'type' => 'inner');
		$join[] = array('table' => 'member', 'on' => 'cmall_wishlist.mem_id = member.mem_id', 'type' => 'left');
		$result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}


	public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
	{
		$select = 'cmall_wishlist.*, cmall_item.cit_name, cmall_item.cit_key, cmall_item.cit_file_1, cmall_item.cit_type3, cmall_item.cit_start_datetime, cmall_item.cit_freebeat, cmall_item.cit_lease_license_use, cmall_item.cit_mastering_license_use, cmall_item.cit_include_copyright_transfer, cmall_item.cit_officially_registered, cmall_item.waveform, cmall_item_detail.cde_id as preview_cde_id,m.mem_nickname, cmall_item_meta_v.hashTag';
		$join[] = array('table' => 'cmall_item', 'on' => 'cmall_wishlist.cit_id = cmall_item.cit_id', 'type' => 'inner');
        $join[] = array('table' => 'cmall_item_detail', 'on' => "cmall_wishlist.cit_id = cmall_item_detail.cit_id and cmall_item_detail.cde_title = 'PREVIEW'", 'type' => 'left');
        $join[] = array('table' => 'cb_member as m', 'on' => "m.mem_id = cmall_item.mem_id", 'type' => 'left');
        $join[] = array('table' => 'cmall_item_meta_v', 'on' => 'cmall_item_meta_v.cit_id = cmall_item.cit_id', 'type' => 'left');
		$result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}


	public function get_rank($start_date = '', $end_date = '')
	{
		if (empty($start_date) OR empty($end_date)) {
			return false;
		}

		$this->db->where('left(cwi_datetime, 10) >=', $start_date);
		$this->db->where('left(cwi_datetime, 10) <=', $end_date);
		$this->db->select('cmall_wishlist.cit_id, cmall_item.cit_name');
		$this->db->join('cmall_item', 'cmall_wishlist.cit_id = cmall_item.cit_id', 'inner');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}
}
