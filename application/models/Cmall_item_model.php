<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall item model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class
Cmall_item_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'cmall_item';

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'cit_id'; // 사용되는 테이블의 프라이머리키

	public $allow_order = array('cit_order asc', 'cit_datetime desc', 'cit_datetime asc', 'cit_hit desc', 'cit_hit asc', 'cit_review_count desc',
		'cit_review_count asc', 'cit_review_average desc', 'cit_review_average asc', 'cit_price desc', 'cit_price asc', 'cit_sell_count desc');

	function __construct()
	{
		parent::__construct();
	}

    public function get_main_list($config)
    {
        log_message('debug','Genre : ' . element('genre', $config));
        $where['cit_status'] = 1;
        if (element('genre', $config) && element('genre', $config) !== 'All Genre') {
            $where['p1.cim_value'] = element('genre', $config);
        }

        $limit = element('limit', $config) ? element('limit', $config) : 4;

        $this->db->join('cb_cmall_item_meta as p1','p1.cit_id = cmall_item.cit_id AND p1.cim_key = "info_content_1"','left');
        $this->db->join('cb_cmall_item_meta as p2','p2.cit_id = cmall_item.cit_id AND p2.cim_key = "info_content_2"','left');
        $this->db->join('cb_cmall_item_meta as p3','p3.cit_id = cmall_item.cit_id AND p3.cim_key = "info_content_3"','left');
        $this->db->join('cb_cmall_item_detail as m1','m1.cit_id = cmall_item.cit_id','left');
        $this->db->join('cb_cmall_wishlist as w','w.cit_id = cmall_item.cit_id AND m1.mem_id = "'.$this->member->item('mem_id').'"','left');



        $this->db->select('cmall_item.*, p1.cim_value as genre, p2.cim_value as bpm, p3.cim_value as musician, m1.cde_id, m1.cde_price, (CASE WHEN w.cit_id IS NOT NULL THEN 1 ELSE 0 END) as is_wish');
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->order_by('cit_order', 'asc');
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    
    // 위시 리스트 토글링
    public function toggle_wish_item($config)
    {
        $result = false;
        // 존재 확인
        $where['cit_id'] = element('cit_id', $config);
        $where['mem_id'] = $this->member->item('mem_id');
        $query = $this->db
                ->where($where)
                ->get('cmall_wishlist');

        // 존재하면 삭제
        if ($query->num_rows() > 0){
            $result = $this->db
                ->where($where)
                ->delete('cmall_wishlist');
        }
        // 미존재시 추가
        else{
            $data = array(
                'mem_id' => $this->member->item('mem_id'),
                'cit_id'  => element('cit_id', $config),
            );
            $result = $this->db->insert('cmall_wishlist', $data);

        }

        return $result;
    }

	public function get_latest($config)
	{
		$where['cit_status'] = 1;
		if (element('cit_type1', $config)) {
			$where['cit_type1'] = 1;
		}
		if (element('cit_type2', $config)) {
			$where['cit_type2'] = 1;
		}
		if (element('cit_type3', $config)) {
			$where['cit_type3'] = 1;
		}
		if (element('cit_type4', $config)) {
			$where['cit_type4'] = 1;
		}
		$limit = element('limit', $config) ? element('limit', $config) : 4;

		$this->db->select('cmall_item.*');
		$this->db->where($where);
		$this->db->limit($limit);
		$this->db->order_by('cit_order', 'asc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}


	/**
	 * List 페이지 커스테마이징 함수
	 */
	public function get_item_list($limit = '', $offset = '', $where = '', $category_id = 0, $orderby = '', $sfield = '', $skeyword = '', $sop = 'OR')
	{

		if ( ! in_array(strtolower($orderby), $this->allow_order)) {
			$orderby = 'cit_order asc';
		}
		$sop = (strtoupper($sop) === 'AND') ? 'AND' : 'OR';
		if (empty($sfield)) {
			$sfield = array('cit_name', 'cit_content');
		}

		$search_where = array();
		$search_like = array();
		$search_or_like = array();
		if ($sfield && is_array($sfield)) {
			foreach ($sfield as $skey => $sval) {
				$ssf = $sval;
				if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
					if (in_array($ssf, $this->search_field_equal)) {
						$search_where[$ssf] = $skeyword;
					} else {
						$swordarray = explode(' ', $skeyword);
						foreach ($swordarray as $str) {
							if (empty($ssf)) {
								continue;
							}
							if ($sop === 'AND') {
								$search_like[] = array($ssf => $str);
							} else {
								$search_or_like[] = array($ssf => $str);
							}
						}
					}
				}
			}
		} else {
			$ssf = $sfield;
			if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
				if (in_array($ssf, $this->search_field_equal)) {
					$search_where[$ssf] = $skeyword;
				} else {
					$swordarray = explode(' ', $skeyword);
					foreach ($swordarray as $str) {
						if (empty($ssf)) {
							continue;
						}
						if ($sop === 'AND') {
							$search_like[] = array($ssf => $str);
						} else {
							$search_or_like[] = array($ssf => $str);
						}
					}
				}
			}
		}

		$this->db->select('cmall_item.*');
		$this->db->from($this->_table);

		if ($where) {
			$this->db->where($where);
		}
		if ($search_where) {
			$this->db->where($search_where);
		}
		$category_id = (int) $category_id;
		if ($category_id) {
			$this->db->join('cmall_category_rel', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
			$this->db->where('cca_id', $category_id);
		}
		if ($search_like) {
			foreach ($search_like as $item) {
				foreach ($item as $skey => $sval) {
					$this->db->like($skey, $sval);
				}
			}
		}
		if ($search_or_like) {
			$this->db->group_start();
			foreach ($search_or_like as $item) {
				foreach ($item as $skey => $sval) {
					$this->db->or_like($skey, $sval);
				}
			}
			$this->db->group_end();
		}

		$this->db->order_by($orderby);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$qry = $this->db->get();
		$result['list'] = $qry->result_array();

		$this->db->select('count(*) as rownum');
		$this->db->from($this->_table);
		if ($where) {
			$this->db->where($where);
		}
		if ($search_where) {
			$this->db->where($search_where);
		}
		if ($category_id) {
			$this->db->join('cmall_category_rel', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
			$this->db->where('cca_id', $category_id);
		}
		if ($search_like) {
			foreach ($search_like as $item) {
				foreach ($item as $skey => $sval) {
					$this->db->like($skey, $sval);
				}
			}
		}
		if ($search_or_like) {
			$this->db->group_start();
			foreach ($search_or_like as $item) {
				foreach ($item as $skey => $sval) {
					$this->db->or_like($skey, $sval);
				}
			}
			$this->db->group_end();
		}
		$qry = $this->db->get();
		$rows = $qry->row_array();
		$result['total_rows'] = $rows['rownum'];

		return $result;
	}


	public function update_hit($primary_value = '')
	{
		if (empty($primary_value)) {
			return false;
		}

		$this->db->where($this->primary_key, $primary_value);
		$this->db->set('cit_hit', 'cit_hit+1', false);
		$result = $this->db->update($this->_table);
		return $result;
	}

    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $join = [
            [
                'table' => 'member',
                'on' => 'member.mem_id = cmall_item.mem_id',
                'type' => 'left'
            ],
//            [
//                'table' => 'cmall_item_meta a',
//                'on' => "a.cit_id = cmall_item.cit_id and a.cim_key = 'info_content_1'",
//                'type' => 'left'
//            ],
//            [
//                'table' => 'cmall_item_meta b',
//                'on' => "b.cit_id = cmall_item.cit_id and b.cim_key = 'info_content_4'",
//                'type' => 'left'
//            ],
//            [
//                'table' => 'cmall_item_meta c',
//                'on' => "c.cit_id = cmall_item.cit_id and c.cim_key = 'info_content_5'",
//                'type' => 'left'
//            ],
        ];
        $select = 'cmall_item.*, mem_username as seller_mem_username';

        return $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
    }
}
