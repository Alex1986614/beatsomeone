<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Teaser class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 티저 페이지를 담당하는 controller 입니다.
 */
class Teaser extends CB_Controller
{
	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('querystring'));
	}

	/**
	 * 전체 메인 페이지입니다
	 */
	public function index()
	{
	    $this->load->view('teaser');
	}
}
