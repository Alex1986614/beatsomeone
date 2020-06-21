<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Beatsomeone Mypage API class
 *
 * Copyright (c) Inpreter
 * Cmall 기반 음원거래 사이트 MYPAGE API
 *
 * @author Inpreter (empicy@gmail.com)
 */

/**
 * Beatsomeone Mypage API
 */
class BeatsomeoneMypageApi extends CB_Controller
{

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array();

	/**
	 * 헬퍼를 로딩합니다
	 */
	protected $helpers = array('form', 'array', 'cmall', 'dhtml_editor');

	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('pagination', 'querystring', 'accesslevel', 'cmalllib'));

	}

    /**
     * 사용자 정보 조회
     */
    public function getUserInfo()
    {
        $mem_id = $this->member->item('mem_id');
        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Member_model');


        $result = $this->Member_model->get_by_memid($mem_id);

        $result = array(
            'mem_id' => $result['mem_id'],
            'mem_username' => $result['mem_username'],
            'mem_location' => $result['mem_location'],
            'mem_email' => $result['mem_email'],
            'mem_firstname' => $result['mem_firstname'],
            'mem_lastname' => $result['mem_lastname'],
            'mem_type' => $result['mem_type'],
            'mem_profile_content' => $result['mem_profile_content'],
            'mem_usertype' => $result['mem_usertype'],
            'mem_nickname' => $result['mem_nickname'],
            'mem_photo' => $result['mem_photo'],
        );

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    /**
     * 대시보드 정보 조회
     */
    public function getDashboardInfo()
    {
        $mem_id = $this->member->item('mem_id');

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');
        $this->load->model('Member_model');

        $userinfo = $this->Member_model->get_by_memid($mem_id);
        $userinfo = array(
            'mem_id' => $userinfo['mem_id'],
            'mem_username' => $userinfo['mem_username'],
            'mem_location' => $userinfo['mem_location'],
            'mem_email' => $userinfo['mem_email'],
            'mem_firstname' => $userinfo['mem_firstname'],
            'mem_lastname' => $userinfo['mem_lastname'],
            'mem_type' => $userinfo['mem_type'],
            'mem_profile_content' => $userinfo['mem_profile_content'],
            'mem_usertype' => $userinfo['mem_usertype'],
            'mem_nickname' => $userinfo['mem_nickname'],
            'mem_photo' => $userinfo['mem_photo'],
        );

        $result = array(
            'UserInfo' => $userinfo,
            'OrderDetails' => $this->Beatsomeone_mypage_model->getOrderDetails($mem_id),
            'ProductDetails' => $this->Beatsomeone_mypage_model->getProductDetails($mem_id),
            'ExpiredSoon' => $this->Beatsomeone_mypage_model->getExpiredSoon($mem_id),
            'RecentlyListen' => $this->Beatsomeone_mypage_model->getRecentlyListen($mem_id),
            'Message' => $this->Beatsomeone_mypage_model->getMessage($mem_id),
            'SupportCase' => $this->Beatsomeone_mypage_model->getSupportCase($mem_id),
            'SettlementOverview' => $this->Beatsomeone_mypage_model->getSettlementOverview($mem_id),
            'Chart' => $this->Beatsomeone_mypage_model->getChart($mem_id),
        );

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }


    /**
     * 개인정보 변경 : 사용자이름 중복 확인
     */
    public function checkDuplicateMemUsername()
    {
        $mem_username = $this->input->post('mem_username');
        $mem_id = $this->member->item('mem_id');

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');

        $result = $this->Beatsomeone_mypage_model->checkDuplicateMemUsername($mem_id,$mem_username);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    /**
     * 개인정보 변경 : 사용자이름 변경
     */
    public function updateMemUsername()
    {
        $mem_username = $this->input->post('mem_username');
        $mem_id = $this->member->item('mem_id');

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');

        $result = $this->Beatsomeone_mypage_model->updateMemUsername($mem_id,$mem_username);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    /**
     * 개인정보 변경 : 이메일 중복 확인
     */
    public function checkDuplicateEmail()
    {
        $mem_email = $this->input->post('mem_email');
        $mem_id = $this->member->item('mem_id');

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');

        $result = $this->Beatsomeone_mypage_model->checkDuplicateEmail($mem_id,$mem_email);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }


    /**
     * 개인정보 변경 : 이메일 변경
     */
    public function updateEmail()
    {
        $mem_email = $this->input->post('mem_email');
        $mem_id = $this->member->item('mem_id');

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');

        $result = $this->Beatsomeone_mypage_model->updateEmail($mem_id,$mem_email);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    /**
     * 개인정보 변경
     */
    public function updateUserInfo()
    {
        $mem_id = $this->member->item('mem_id');
        $param = array(
            'mem_type' => $this->input->post('mem_type'),
            'mem_firstname' => $this->input->post('mem_firstname'),
            'mem_lastname' => $this->input->post('mem_lastname'),
            'mem_location' => $this->input->post('mem_location'),
            'mem_profile_content' => $this->input->post('mem_profile_content'),
        );

        // 비로그인 사용자 거부
        if(!$mem_id) {
            $this->output->set_status_header('412');
            return;
        }

        $this->load->model('Beatsomeone_mypage_model');

        $result = $this->Beatsomeone_mypage_model->updateUserInfo($mem_id,$param);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }




}