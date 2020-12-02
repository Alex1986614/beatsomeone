<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cmall class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 컨텐츠몰 페이지에 관한 controller 입니다.
 */
class Cmall extends CB_Controller
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

        if (!$this->cbconfig->item('use_cmall')) {
            alert('이 웹사이트는 ' . html_escape($this->cbconfig->item('cmall_name')) . ' 기능을 사용하지 않습니다');
            return;
        }
    }


    /**
     * 컨텐츠몰 메인페이지입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model('Cmall_item_model');

        $config = array(
            'cit_type1' => '1',
            'limit' => '4',
        );
        $view['view']['type1'] = $this->Cmall_item_model->get_latest($config);

        $config = array(
            'cit_type2' => '1',
            'limit' => '4',
        );
        $view['view']['type2'] = $this->Cmall_item_model->get_latest($config);

        $config = array(
            'cit_type3' => '1',
            'limit' => '4',
        );
        $view['view']['type3'] = $this->Cmall_item_model->get_latest($config);

        $config = array(
            'cit_type4' => '1',
            'limit' => '4',
        );
        $view['view']['type4'] = $this->Cmall_item_model->get_latest($config);

        $view['view']['canonical'] = site_url('cmall');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall');
        $page_name = $this->cbconfig->item('site_page_name_cmall');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'cmall',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    public function lists($category_id = '')
    {
        redirect('/beatsomeone/sublist', 'location', 301);
        return;

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_lists';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_item_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품 목록을 볼 수 있는 권한이 없습니다'
            : '비회원은 상품목록에 접근할 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_list = $this->cbconfig->item('access_cmall_list');
        $access_list_level = $this->cbconfig->item('access_cmall_list_level');
        $access_list_group = $this->cbconfig->item('access_cmall_list_group');
        $this->accesslevel->check(
            $access_list,
            $access_list_level,
            $access_list_group,
            $alertmessage,
            ''
        );

        $findex = ($this->input->get('findex') && in_array($this->input->get('findex'), $allow_order_field)) ? $this->input->get('findex') : 'cit_order asc';
        $sfield = $this->input->get('sfield', null, '');
        if ($sfield === 'cit_both') {
            $sfield = array('cit_name', 'cit_content');
        }
        $skeyword = $this->input->get('skeyword', null, '');

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        $this->Cmall_item_model->allow_search_field = array('cit_id', 'cit_name', 'cit_content', 'cit_both', 'cit_price'); // 검색이 가능한 필드
        $this->Cmall_item_model->search_field_equal = array('cit_id'); // 검색중 like 가 아닌 = 검색을 하는 필드

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cit_status'] = 1;
        $result = $this->Cmall_item_model
            ->get_item_list($per_page, $offset, $where, $category_id, $findex, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $view['view']['data'] = $result;

        $view['view']['category_nav'] = $this->cmalllib->get_nav_category($category_id);
        $view['view']['category_all'] = $this->cmalllib->get_all_category();
        $view['view']['category_id'] = $category_id;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/lists/' . $category_id) . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_list');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_list');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_list');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_list');
        $page_name = $this->cbconfig->item('site_page_name_cmall_list');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'lists',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    public function item($cit_key = '')
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_item';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        if (empty($cit_key)) {
            show_404();
        }
        $this->load->model(array('Cmall_item_model', 'Cmall_item_meta_model', 'Cmall_item_detail_model'));

        $where = array(
            'cit_key' => $cit_key,
        );
        $data = $this->Cmall_item_model->get_one('', '', $where);
        if (!element('cit_id', $data)) {
            show_404();
        }
        if (!element('cit_status', $data)) {
            alert('이 상품은 현재 판매하지 않습니다');
        }

        $data['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $data));
        $data['detail'] = $this->Cmall_item_detail_model->get_all_detail(element('cit_id', $data));

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품 페이지를 볼 수 있는 권한이 없습니다'
            : '비회원은 상품 페이지를 볼 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_read = $this->cbconfig->item('access_cmall_read');
        $access_read_level = $this->cbconfig->item('access_cmall_read_level');
        $access_read_group = $this->cbconfig->item('access_cmall_read_group');
        $this->accesslevel->check(
            $access_read,
            $access_read_level,
            $access_read_group,
            $alertmessage,
            ''
        );

        // 구매 혹은 장바구니 담기
        // stype : order / cart
        if ($this->input->post('stype')) {
            // 로그인 여부 확인
            if (!$mem_id) {
                $this->session->set_flashdata(
                    'message',
                    '로그인 후 이용이 가능합니다'
                );
                redirect('login?url=' . urlencode(current_full_url()));
            }

            // 주문 상품 번호 확인
            $cit_id = (int)$this->input->post('cit_id');
            if (empty($cit_id) or $cit_id < 1) {
                show_404();
            }

            // 위시 리스트 담기
            if ($this->input->post('stype') === 'wish') {
                $return = $this->cmalllib->addwish($mem_id, $cit_id);
                if ($return) {
                    $this->output->set_content_type('text/json');
                    $result = [
                        'status' => $return,
                    ];
                    $this->output->set_output(json_encode($return));
                    return;
                }
            } // 장바구니 담기
            elseif ($this->input->post('stype') === 'cart'
                && $this->input->post('chk_detail')
                && is_array($this->input->post('chk_detail'))
                && $this->input->post('detail_qty')) {
                $return = $this->cmalllib->addcart(
                    $mem_id,
                    $cit_id,
                    $this->input->post('chk_detail'),
                    $this->input->post('detail_qty')
                );
                $this->output->set_content_type('text/json');
                if ($return) {
                    $result = [
                        'message' => 'Success',
                    ];
                    $this->output->set_output(json_encode($result));
                    return;
                } else {
                    $this->output->set_status_header(400);
                    $result = [
                        'message' => 'Failed',
                    ];
                    $this->output->set_output(json_encode($result));
                    return;
                }
            } // 바로구매
            elseif ($this->input->post('stype') === 'order'
                && $this->input->post('chk_detail')
                && is_array($this->input->post('chk_detail'))
                && $this->input->post('detail_qty')) {
                $return = $this->cmalllib->addorder(
                    $mem_id,
                    $cit_id,
                    $this->input->post('chk_detail'),
                    $this->input->post('detail_qty')
                );
                if ($return) {
                    redirect('cmall/order');
                }
            }
        }

        if (!$this->session->userdata('cmall_item_id_' . element('cit_id', $data))) {
            $this->Cmall_item_model->update_hit(element('cit_id', $data));
            $this->session->set_userdata(
                'cmall_item_id_' . element('cit_id', $data),
                '1'
            );
        }

        $data['content'] = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? (
            element('cit_mobile_content', $data)
                ? element('cit_mobile_content', $data)
                : element('cit_content', $data)
            )
            : element('cit_content', $data);
        $thumb_width = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_mobile_thumb_width')
            : $this->cbconfig->item('cmall_product_thumb_width');
        $autolink = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('use_cmall_product_mobile_auto_url')
            : $this->cbconfig->item('use_cmall_product_auto_url');
        $popup = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_mobile_content_target_blank')
            : $this->cbconfig->item('cmall_product_content_target_blank');
        $data['content'] = display_html_content(
            element('content', $data),
            element('cit_content_html_type', $data),
            $thumb_width,
            $autolink,
            $popup,
            $writer_is_admin = true
        );

        $data['header_content'] = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? display_html_content(element('mobile_header_content', element('meta', $data)), 1, $thumb_width)
            : display_html_content(element('header_content', element('meta', $data)), 1, $thumb_width);

        $data['footer_content'] = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? display_html_content(element('mobile_footer_content', element('meta', $data)), 1, $thumb_width)
            : display_html_content(element('footer_content', element('meta', $data)), 1, $thumb_width);

        $view['view']['data'] = $data;
        $view['view']['item_key'] = $cit_key;

        $layout_dir = (element('item_layout', element('meta', $data))) ? element('item_layout', element('meta', $data)) : $this->cbconfig->item('layout_cmall');
        $mobile_layout_dir = (element('item_mobile_layout', element('meta', $data))) ? element('item_mobile_layout', element('meta', $data)) : $this->cbconfig->item('mobile_layout_cmall');
        $use_sidebar = (element('item_layout', element('meta', $data))) ? element('item_sidebar', element('meta', $data)) : $this->cbconfig->item('sidebar_cmall');
        $use_mobile_sidebar = (element('item_mobile_layout', element('meta', $data))) ? element('item_mobile_sidebar', element('meta', $data)) : $this->cbconfig->item('mobile_sidebar_cmall');
        $skin_dir = (element('item_skin', element('meta', $data))) ? element('item_skin', element('meta', $data)) : $this->cbconfig->item('skin_cmall');
        $mobile_skin_dir = (element('item_mobile_skin', element('meta', $data))) ? element('item_mobile_skin', element('meta', $data)) : $this->cbconfig->item('mobile_skin_cmall');

        $view['view']['canonical'] = cmall_item_url($cit_key);

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_item');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_item');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_item');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_item');
        $page_name = $this->cbconfig->item('site_page_name_cmall_item');

        $searchconfig = array(
            '{컨텐츠몰명}',
            '{상품명}',
            '{판매가격}',
            '{기본설명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
            element('cit_name', $data),
            element('cit_price', $data),
            element('cit_summary', $data),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'item',
            'layout_dir' => $layout_dir,
            'mobile_layout_dir' => $mobile_layout_dir,
            'use_sidebar' => $use_sidebar,
            'use_mobile_sidebar' => $use_mobile_sidebar,
            'skin_dir' => $skin_dir,
            'mobile_skin_dir' => $mobile_skin_dir,
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function ajax_item($cit_key = '')
    {
        $this->output->set_content_type('text/json');

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_item';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        if (empty($cit_key)) {
            $this->output->set_status_header('404');
            return false;
        }
        $this->load->model(array('Cmall_item_model', 'Cmall_item_meta_model', 'Cmall_item_detail_model'));

        $data = $this->Cmall_item_model->get_one_with_author($cit_key);
        if (!element('cit_id', $data)) {
            $this->output->set_status_header('404');
            return false;
        }
        if (!element('cit_status', $data) && !element('view_permission_type', $data)) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode([
                'message' => '이 상품은 현재 판매하지 않습니다'
            ]));
            return false;
        }

        $data['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $data));
        $detail_items = $this->Cmall_item_detail_model->get_all_detail(element('cit_id', $data));
        $detail = array();
        foreach ($detail_items as $detail_item) {
            if (!empty($data['cit_freebeat'])) {
                $detail_item['cde_price'] = 0;
                $detail_item['cde_price_d'] = 0;
            }
            $detail[$detail_item['cde_title']] = $detail_item;
        }
        $data['detail'] = $detail;

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품 페이지를 볼 수 있는 권한이 없습니다'
            : '비회원은 상품 페이지를 볼 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_read = $this->cbconfig->item('access_cmall_read');
        $access_read_level = $this->cbconfig->item('access_cmall_read_level');
        $access_read_group = $this->cbconfig->item('access_cmall_read_group');
        $this->accesslevel->check(
            $access_read,
            $access_read_level,
            $access_read_group,
            $alertmessage,
            ''
        );

        if (!$this->session->userdata('cmall_item_id_' . element('cit_id', $data))) {
            $this->Cmall_item_model->update_hit(element('cit_id', $data));
            $this->session->set_userdata(
                'cmall_item_id_' . element('cit_id', $data),
                '1'
            );
        }

        $data['info_content'] = str_replace('-', '', strip_tags($data['cit_content']));
        if ($this->cbconfig->get_device_view_type() === 'mobile') {
            $data['content'] = element('cit_mobile_content', $data) ? element('cit_mobile_content', $data) : element('cit_content', $data);
            $thumb_width = $this->cbconfig->item('cmall_product_mobile_thumb_width');
            $autolink = $this->cbconfig->item('use_cmall_product_mobile_auto_url');
            $popup = $this->cbconfig->item('cmall_product_mobile_content_target_blank');
        } else {
            $data['content'] = element('cit_content', $data);
            $thumb_width = $this->cbconfig->item('cmall_product_thumb_width');
            $autolink =  $this->cbconfig->item('use_cmall_product_auto_url');
            $popup = $this->cbconfig->item('cmall_product_content_target_blank');
        }

        $data['content'] = display_html_content(
            element('content', $data),
            element('cit_content_html_type', $data),
            $thumb_width,
            $autolink,
            $popup,
            $writer_is_admin = true
        );

        $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
        return true;
    }


    public function cartoption()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_cartoption';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $cit_id = (int)$this->input->post('cit_id');
        if (empty($cit_id) or $cit_id < 1) {
            show_404();
        }

        if ($this->member->is_member() === false) {
            show_404();
        }
        $mem_id = (int)$this->member->item('mem_id');

        $this->load->model(array('Cmall_item_model', 'Cmall_item_detail_model', 'Cmall_cart_model'));

        $item = $this->Cmall_item_model->get_one($cit_id);
        if (!element('cit_id', $item)) {
            show_404();
        }

        $detail = $this->Cmall_item_detail_model->get_all_cart_detail($cit_id);
        if ($detail) {
            foreach ($detail as $key => $value) {
                $detail[$key]['cart'] = $this->Cmall_cart_model
                    ->get_item_is_cart(element('cde_id', $value), $mem_id);
            }
        }

        if (!element('cit_id', $item)) {
            show_404();
        }

        $view['view']['item'] = $item;
        $view['view']['detail'] = $detail;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('mobile_skin_cmall')
            : $this->cbconfig->item('skin_cmall');
        if (empty($skindir)) {
            $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? $this->cbconfig->item('mobile_skin_default')
                : $this->cbconfig->item('skin_default');
        }
        if (empty($skindir)) {
            $skindir = 'basic';
        }
        $skin = 'cmall/' . $skindir . '/cartoption';

        $this->data = $view;
        $this->view = $skin;
    }


    public function itemimage($cit_key = '')
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_itemimage';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        if (empty($cit_key)) {
            show_404();
        }
        $this->load->model(array('Cmall_item_model'));

        $where = array(
            'cit_key' => $cit_key,
        );
        $data = $this->Cmall_item_model->get_one('', '', $where);
        if (!element('cit_id', $data)) {
            show_404();
        }

        $view['view']['data'] = $data;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = element('cit_name', $data) . ' > ' . $this->cbconfig->item('cmall_name') . ' 이미지 크게보기';
        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout_popup',
            'skin' => 'itemimage',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    public function cart()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_cart';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_cart_model', 'Beatsomeone_model'));

        $config = array(
            'mem_id' => $this->member->item('mem_id'),
        );
        $cor_id_list = $this->Beatsomeone_model->get_sales_history($config);
        log_message('error', print_r($cor_id_list, true));

        if ($this->input->post('chk')) {
            $cit_id = $this->input->post('chk');
            $return = $this->cmalllib->cart_to_order(
                $mem_id,
                $cit_id
            );
            if ($return) {
                redirect('cmall/order');
            }
        }

        $cachename = 'delete_old_cart_cache';
        $cachetime = 3600;
        if (!$result = $this->cache->get($cachename)) {
            $days = $this->cbconfig->item('cmall_cart_keep_days')
                ? $this->cbconfig->item('cmall_cart_keep_days') : 14;
            $cartdays = cdate('Y-m-d H:i:s', ctimestamp() - $days * 86400);
            $deletewhere = array(
                'cct_datetime <' => $cartdays,
            );
            $this->Cmall_cart_model->delete_where($deletewhere);
            $this->cache->save($cachename, cdate('Y-m-d H:i:s'), $cachetime);
        }


        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $result = $this->Cmall_cart_model->get_cart_list($where, $findex, $forder);

        if ($result) {
            foreach ($result as $key => $val) {
                $result[$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                //$result[$key]['detail'] = $this->Cmall_cart_model->get_cart_detail($mem_id, element('cit_id', $val));
                $result[$key]['detail'] = $this->Beatsomeone_model->get_product_info(element('cit_id', $val));
                log_message('error', print_r($result[$key]['detail'], true));
            }
        }

        $view['view']['data'] = $result;
        $view['view']['list_delete_url'] = site_url('cmallact/cart_delete/?' . $param->output());

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_cart');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_cart');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_cart');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_cart');
        $page_name = $this->cbconfig->item('site_page_name_cmall_cart');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        /*
        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'cart',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        */
        $layoutconfig = array(
            'path' => 'beatsomeone',
            'layout' => 'layout',
            'skin' => 'cart/cart',
            'layout_dir' => $this->cbconfig->item('layout_beatsomeone'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_beatsomeone'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );


        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function ajax_cart()
    {
        $this->output->set_content_type('text/json');

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_cart';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $this->load->model(array('Cmall_cart_model'));

        $cachename = 'delete_old_cart_cache';
        $cachetime = 3600;
        if (!$result = $this->cache->get($cachename)) {
            $days = $this->cbconfig->item('cmall_cart_keep_days')
                ? $this->cbconfig->item('cmall_cart_keep_days') : 14;
            $cartdays = cdate('Y-m-d H:i:s', ctimestamp() - $days * 86400);
            $deletewhere = array(
                'cct_datetime <' => $cartdays,
            );
            $this->Cmall_cart_model->delete_where($deletewhere);
            $this->cache->save($cachename, cdate('Y-m-d H:i:s'), $cachetime);
        }


        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $result = $this->Cmall_cart_model->get_cart_list($where, $findex, $forder);
        if ($result) {
            foreach ($result as $key => $val) {
                $result[$key]['thumb'] = cover_thumb_name(element('cit_file_1', $val), 'list');
                $result[$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result[$key]['waveform'] = json_decode(element('waveform', $val), true);
                $result[$key]['detail'] = $this->Cmall_cart_model->get_cart_detail($mem_id, element('cit_id', $val));
            }
        }

        $this->output->set_output(json_encode($result));
    }

    public function ajax_orderstart()
    {
        $this->output->set_content_type('text/json');

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_cart';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_cart_model'));

        if ($this->input->post('chk')) {
            $cit_id = $this->input->post('chk');
            $return = $this->cmalllib->cart_to_order(
                $mem_id,
                $cit_id
            );
            if ($return) {
                $this->output->set_status_header('200');
                $this->output->set_output(json_encode([
                    'message' => 'Success'
                ]));
            } else {
                $this->output->set_status_header('400');
                $this->output->set_output(json_encode([
                    'message' => 'Error'
                ]));
            }
        } else {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode([
                'message' => 'Error'
            ]));
        }
    }

    /**
     * AJAX 주문하기 입니다
     */
    public function ajax_order()
    {
        $this->output->set_content_type('text/json');
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_order';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view = array();

        // 이벤트가 존재하면 실행합니다
        $view['event']['before'] = Events::trigger('before', $eventname);

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품을 구매할 수 있는 권한이 없습니다'
            : '비회원은 상품을 구매할 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_buy = $this->cbconfig->item('access_cmall_buy');
        $access_buy_level = $this->cbconfig->item('access_cmall_buy_level');
        $access_buy_group = $this->cbconfig->item('access_cmall_buy_group');
        $this->accesslevel->check(
            $access_buy,
            $access_buy_level,
            $access_buy_group,
            $alertmessage,
            ''
        );

        $this->load->model(array('Cmall_cart_model'));

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $result = $this->Cmall_cart_model->get_order_list($where, $findex, $forder);
        $good_name = '';
        $good_count = -1;
        $session_cct_id = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $result[$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result[$key]['detail'] = $this->Cmall_cart_model
                    ->get_order_detail($mem_id, element('cit_id', $val));
                if (empty($good_name)) {
                    $good_name = element('cit_name', $val);
                }
                $good_count++;
                $session_cct_id[] = element('cct_id', $val);
            }
        }
        $view['data'] = $result;

        $this->load->model('Unique_id_model');
        $unique_id = $this->Unique_id_model->get_id($this->input->ip_address());
        $view['unique_id'] = $unique_id;
        $view['good_name'] = $good_name;
        if ($good_count > 0) {
            $view['good_name'] .= ' 외 ' . $good_count . '건';
        }
        $this->session->set_userdata(
            'unique_id',
            $unique_id
        );
        $this->session->set_userdata(
            'order_cct_id',
            implode('-', $session_cct_id)
        );

//        $view['view']['use_pg'] = $use_pg = false;
//        if ($this->cbconfig->item('use_payment_card')
//            OR $this->cbconfig->item('use_payment_realtime')
//            OR $this->cbconfig->item('use_payment_vbank')
//            OR $this->cbconfig->item('use_payment_phone')
//            OR $this->cbconfig->item('use_payment_easy')) {
//            $view['view']['use_pg'] = $use_pg = true;
//        }
//
//
//        if ($this->cbconfig->item('use_payment_pg') === 'kcp' && $use_pg) {
//            $this->load->library('paymentlib');
//            $view['view']['pg'] = $this->paymentlib->kcp_init();
//            /*	 //삭제예정
//            if ($this->cbconfig->get_device_type() !== 'mobile') {
//                $view['view']['body_script'] = 'onLoad="CheckPayplusInstall();"';
//            }
//            */
//        }
//
//        if ($this->cbconfig->item('use_payment_pg') === 'lg' && $use_pg) {
//            $this->load->library('paymentlib');
//            $view['view']['pg'] = $this->paymentlib->lg_init();
//            /*	 //삭제예정
//            if ($this->cbconfig->get_device_type() !== 'mobile') {
//                $view['view']['body_script'] = 'onload="isActiveXOK();"';
//            }
//            */
//        }
//
//        if ($this->cbconfig->item('use_payment_pg') === 'inicis' && $use_pg) {
//            $this->load->library('paymentlib');
//            $view['view']['pg'] = $this->paymentlib->inicis_init();
//            /* //삭제예정
//            if ($this->cbconfig->get_device_type() !== 'mobile') {
//                $view['view']['body_script'] = 'onload="enable_click();"';
//            }
//            */
//        }

        $view['ptype'] = 'cmall';

        $view['form1name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_1' : 'form_1';
        $view['form2name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_2' : 'form_2';
        $view['form3name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_3' : 'form_3';
        $view['form4name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_4' : 'form_4';

        // 이벤트가 존재하면 실행합니다
        $view['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        $this->output->set_output(json_encode($view));

    }

    public function billing()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_cart';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품을 구매할 수 있는 권한이 없습니다'
            : '비회원은 상품을 구매할 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_buy = $this->cbconfig->item('access_cmall_buy');
        $access_buy_level = $this->cbconfig->item('access_cmall_buy_level');
        $access_buy_group = $this->cbconfig->item('access_cmall_buy_group');
        $this->accesslevel->check(
            $access_buy,
            $access_buy_level,
            $access_buy_group,
            $alertmessage,
            ''
        );

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_cart');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_cart');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_cart');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_cart');
        $page_name = $this->cbconfig->item('site_page_name_cmall_cart');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'beatsomeone',
            'layout' => 'layout',
            'skin' => 'cart/billing',
            'layout_dir' => $this->cbconfig->item('layout_beatsomeone'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_beatsomeone'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );


        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function complete($cor_id = '')
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderresult';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        $this->load->model(array('Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model'));

        $order = $this->Cmall_order_model->get_one($cor_id);
        if (!element('cor_id', $order)) {
            alert('잘못된 접근입니다');
        }
        if ($this->member->is_admin() === false
            && (int)element('mem_id', $order) !== $mem_id) {
            alert('잘못된 접근입니다');
        }
        $orderdetail = $this->Cmall_order_detail_model->get_by_item($cor_id);
        if ($orderdetail) {
            foreach ($orderdetail as $key => $value) {
                $orderdetail[$key]['item'] = $item
                    = $this->Cmall_item_model->get_one(element('cit_id', $value));
                $orderdetail[$key]['itemdetail'] = $itemdetail
                    = $this->Cmall_order_detail_model
                    ->get_detail_by_item($cor_id, element('cit_id', $value));

                $orderdetail[$key]['item']['possible_download'] = 1;
                if (element('cod_download_days', element(0, $itemdetail)) && element('cor_approve_datetime', $order)) {
                    $endtimestamp = strtotime(element('cor_approve_datetime', $order))
                        + 86400 * element('cod_download_days', element(0, $itemdetail));
                    $orderdetail[$key]['item']['download_end_date'] = $enddate
                        = cdate('Y-m-d', $endtimestamp);

                    $orderdetail[$key]['item']['possible_download'] = ($enddate >= date('Y-m-d')) ? 1 : 0;
                }
            }
        }
        if (element('cor_status', $order) === '1') {
            $this->session->set_userdata(
                'cmall_item_download_' . element('cor_id', $order),
                '1'
            );
        }

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_orderresult');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_orderresult');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_orderresult');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_orderresult');
        $page_name = $this->cbconfig->item('site_page_name_cmall_orderresult');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'beatsomeone',
            'layout' => 'layout',
            'skin' => 'cart/complete',
            'layout_dir' => $this->cbconfig->item('layout_beatsomeone'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_beatsomeone'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );


        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    /**
     * 주문하기 입니다
     */
    public function order()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_order';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $alertmessage = $this->member->is_member()
            ? '회원님은 상품을 구매할 수 있는 권한이 없습니다'
            : '비회원은 상품을 구매할 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
        $access_buy = $this->cbconfig->item('access_cmall_buy');
        $access_buy_level = $this->cbconfig->item('access_cmall_buy_level');
        $access_buy_group = $this->cbconfig->item('access_cmall_buy_group');
        $this->accesslevel->check(
            $access_buy,
            $access_buy_level,
            $access_buy_group,
            $alertmessage,
            ''
        );

        $this->load->model(array('Cmall_cart_model'));

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $result = $this->Cmall_cart_model->get_order_list($where, $findex, $forder);
        $good_name = '';
        $good_count = -1;
        $session_cct_id = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $result[$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result[$key]['detail'] = $this->Cmall_cart_model
                    ->get_order_detail($mem_id, element('cit_id', $val));
                if (empty($good_name)) {
                    $good_name = element('cit_name', $val);
                }
                $good_count++;
                $session_cct_id[] = element('cct_id', $val);
            }
        }
        $view['view']['data'] = $result;

        $this->load->model('Unique_id_model');
        $unique_id = $this->Unique_id_model->get_id($this->input->ip_address());
        $view['view']['unique_id'] = $unique_id;
        $view['view']['good_name'] = $good_name;
        if ($good_count > 0) {
            $view['view']['good_name'] .= ' 외 ' . $good_count . '건';
        }
        $this->session->set_userdata(
            'unique_id',
            $unique_id
        );
        $this->session->set_userdata(
            'order_cct_id',
            implode('-', $session_cct_id)
        );

        $view['view']['use_pg'] = $use_pg = false;
        if ($this->cbconfig->item('use_payment_card')
            or $this->cbconfig->item('use_payment_realtime')
            or $this->cbconfig->item('use_payment_vbank')
            or $this->cbconfig->item('use_payment_phone')
            or $this->cbconfig->item('use_payment_easy')) {
            $view['view']['use_pg'] = $use_pg = true;
        }


        if ($this->cbconfig->item('use_payment_pg') === 'kcp' && $use_pg) {
            $this->load->library('paymentlib');
            $view['view']['pg'] = $this->paymentlib->kcp_init();
            /*	 //삭제예정
            if ($this->cbconfig->get_device_type() !== 'mobile') {
                $view['view']['body_script'] = 'onLoad="CheckPayplusInstall();"';
            }
            */
        }

        if ($this->cbconfig->item('use_payment_pg') === 'lg' && $use_pg) {
            $this->load->library('paymentlib');
            $view['view']['pg'] = $this->paymentlib->lg_init();
            /*	 //삭제예정
            if ($this->cbconfig->get_device_type() !== 'mobile') {
                $view['view']['body_script'] = 'onload="isActiveXOK();"';
            }
            */
        }

        if ($this->cbconfig->item('use_payment_pg') === 'inicis' && $use_pg) {
            $this->load->library('paymentlib');
            $view['view']['pg'] = $this->paymentlib->inicis_init();
            /* //삭제예정
            if ($this->cbconfig->get_device_type() !== 'mobile') {
                $view['view']['body_script'] = 'onload="enable_click();"';
            }
            */
        }

        $view['view']['ptype'] = 'cmall';

        $view['view']['form1name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_1' : 'form_1';
        $view['view']['form2name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_2' : 'form_2';
        $view['view']['form3name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_3' : 'form_3';
        $view['view']['form4name'] = ($this->cbconfig->get_device_type() === 'mobile') ? 'mform_4' : 'form_4';

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_order');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_order');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_order');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_order');
        $page_name = $this->cbconfig->item('site_page_name_cmall_order');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'order',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    public function orderresult($cor_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderresult';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $this->load->library(array('paymentlib'));
        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        if (empty($cor_id) or $cor_id < 1) {
            alert('잘못된 접근입니다');
        }

        $this->load->model(array('Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model'));

        $order = $this->Cmall_order_model->get_one($cor_id);
        if (!element('cor_id', $order)) {
            alert('잘못된 접근입니다');
        }
        if ($this->member->is_admin() === false
            && (int)element('mem_id', $order) !== $mem_id) {
            alert('잘못된 접근입니다');
        }
        $orderdetail = $this->Cmall_order_detail_model->get_by_item($cor_id);
        if ($orderdetail) {
            foreach ($orderdetail as $key => $value) {
                $orderdetail[$key]['item'] = $item
                    = $this->Cmall_item_model->get_one(element('cit_id', $value));
                $orderdetail[$key]['itemdetail'] = $itemdetail
                    = $this->Cmall_order_detail_model
                    ->get_detail_by_item($cor_id, element('cit_id', $value));

                $orderdetail[$key]['item']['possible_download'] = 1;
                if (element('cod_download_days', element(0, $itemdetail)) && element('cor_approve_datetime', $order)) {
                    $endtimestamp = strtotime(element('cor_approve_datetime', $order))
                        + 86400 * element('cod_download_days', element(0, $itemdetail));
                    $orderdetail[$key]['item']['download_end_date'] = $enddate
                        = cdate('Y-m-d', $endtimestamp);

                    $orderdetail[$key]['item']['possible_download'] = ($enddate >= date('Y-m-d')) ? 1 : 0;
                }
            }
        }
        if (element('cor_status', $order) === '1') {
            $this->session->set_userdata(
                'cmall_item_download_' . element('cor_id', $order),
                '1'
            );
        }

        //핸드폰의 영수증 정보
        if (element('cor_pay_type', $order) === 'phone') {
            switch (element('cor_pg', $order)) {
                case 'lg' :
                    $init = $this->paymentlib->lg_init();
                    $LGD_MID = element('LGD_MID', $init);
                    $LGD_TID = element('cor_tno', $order);
                    $LGD_MERTKEY = element('pg_lg_key', $init);
                    $LGD_HASHDATA = md5($LGD_MID . $LGD_TID . $LGD_MERTKEY);

                    if (element('is_test', $order)) {
                        $order['card_receipt_js'] = 'http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js';
                    } else {
                        $order['card_receipt_js'] = 'http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js';
                    }
                    $order['card_receipt_script'] = 'showReceiptByTID(\'' . $LGD_MID . '\', \'' . $LGD_TID . '\', \'' . $LGD_HASHDATA . '\');';
                    break;
                case 'inicis' :
                    $order['card_receipt_script'] = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=' . element('cor_tno', $order) . '&noMethod=1\',\'receipt\',\'width=430,height=700\');';
                    break;
                case 'kcp' :
                    if (element('is_test', $order)) {
                        $receipturl = 'https://testadmin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
                    } else {
                        $receipturl = 'https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
                    }
                    $order['card_receipt_script'] = 'window.open(\'' . $receipturl . 'mcash_bill&tno=' . element('cor_tno', $order) . '&order_no=' . element('cor_id', $order) . '&trade_mony=' . element('cor_cash', $order) . '\', \'winreceipt\', \'width=470,height=815,scrollbars=yes,resizable=yes\');';
                    break;
            }
        }

        if (element('cor_pg', $order) === 'allat') {
            if (strcasecmp(element('cor_pay_type', $order), 'card') === 0) {
                $order['card_receipt_js'] = "http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=" . $this->cbconfig->item('pg_allat_shop_id') . "&order_no=" . element('cor_id', $order);
            }
        } elseif (element('cor_pg', $order) === 'paypal') {

        } else {

        }

        //카드의 영수증 정보
//		if( element('cor_pay_type', $order) === 'card' ){
//			switch( element('cor_pg', $order) ){
//				case 'lg' :
//					$init = $this->paymentlib->lg_init();
//					$LGD_MID		= element('LGD_MID', $init);
//					$LGD_TID		= element('cor_tno', $order);
//					$LGD_MERTKEY	= element('pg_lg_key', $init);
//					$LGD_HASHDATA	= md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);
//
//					if ( element('is_test', $order) ) {
//						$order['card_receipt_js'] = 'http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js';
//					} else {
//						$order['card_receipt_js'] = 'http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js';
//					}
//					$order['card_receipt_script'] = 'showReceiptByTID(\''.$LGD_MID.'\', \''.$LGD_TID.'\', \''.$LGD_HASHDATA.'\');';
//					break;
//				case 'inicis' :
//					$order['card_receipt_script'] = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid='.element('cor_tno', $order).'&noMethod=1\',\'receipt\',\'width=430,height=700\');';
//					break;
//				case 'kcp' :
//					if ( element('is_test', $order) ) {
//						$receipturl = 'https://testadmin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
//					} else {
//						$receipturl = 'https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
//					}
//					$order['card_receipt_script'] = 'window.open(\''.$receipturl.'card_bill&tno='.element('cor_tno', $order).'&order_no='.element('cor_id', $order).'&trade_mony='.element('cor_cash', $order).'\', \'winreceipt\', \'width=470,height=815,scrollbars=yes,resizable=yes\');';
//					break;
//			}
//		}

        $view['view']['data'] = $order;
        $view['view']['orderdetail'] = $orderdetail;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_orderresult');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_orderresult');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_orderresult');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_orderresult');
        $page_name = $this->cbconfig->item('site_page_name_cmall_orderresult');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'order_result',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function ajax_orderresult($cor_id = 0)
    {
        $this->output->set_content_type('text/json');

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderresult';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        if (empty($cor_id) or $cor_id < 1) {
            $this->output->set_status_header('404');
            return false;
        }

        $this->load->model(array('Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model', 'Cmall_download_log_model'));

        $order = $this->Cmall_order_model->get_one($cor_id);
        if (!element('cor_id', $order)) {
            $this->output->set_status_header('404');
            return false;
        }
        if ($this->member->is_admin() === false
            && (int)element('mem_id', $order) !== $mem_id) {
            $this->output->set_status_header('403');
            return false;
        }
        $orderdetail = $this->Cmall_order_detail_model->get_by_item($cor_id);
        if ($orderdetail) {
            foreach ($orderdetail as $key => $value) {
                $orderdetail[$key]['item'] = $item
                    = $this->Cmall_item_model->get_one_with_tags(element('cit_id', $value));
                $orderdetail[$key]['itemdetail'] = $itemdetail
                    = $this->Cmall_order_detail_model
                    ->get_detail_by_item($cor_id, element('cit_id', $value));
                $where = [
                    'cit_id' => element('cit_id', $value),
                    'cde_id' => $itemdetail[0]['cde_id'],
                    'mem_id' => $mem_id,
                    'cor_id' => $cor_id
                ];

                $download_log = $this->Cmall_download_log_model->get_one('', '', $where);
                if ($download_log != false) {
                    $orderdetail[$key]['item']['possible_refund'] = 0;
                } else {
                    $orderdetail[$key]['item']['possible_refund'] = 1;
                }

                if (element('cod_status', element(0, $itemdetail)) == 'order') {
                    if (strcasecmp(element('cde_title', element(0, $itemdetail)), "LEASE") == 0) {
                        if (element('cor_approve_datetime', $order)) {
                            $endtimestamp = strtotime(element('cor_approve_datetime', $order))
                                + 86400 * 60;
                            $orderdetail[$key]['item']['download_end_date'] = $enddate
                                = cdate('Y-m-d', $endtimestamp);

                            $orderdetail[$key]['item']['possible_download'] = ($enddate >= date('Y-m-d')) ? 1 : 0;
                        } else {
                            $orderdetail[$key]['item']['possible_download'] = 0;
                        }
                    } elseif (strcasecmp(element('cde_title', element(0, $itemdetail)), "STEM") == 0) {
                        $orderdetail[$key]['item']['possible_download'] = 1;
                    } else {
                        $orderdetail[$key]['item']['possible_download'] = 0;
                    }
                } else {
                    $orderdetail[$key]['item']['possible_download'] = 0;
                }
            }
        }
        if (element('cor_status', $order) === '1') {
            $this->session->set_userdata(
                'cmall_item_download_' . element('cor_id', $order),
                '1'
            );
        }

        if (element('cor_pg', $order) === 'allat') {
            if (strcasecmp(element('cor_pay_type', $order), 'card') === 0) {
                $order['card_receipt_js'] = "http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=" . $this->cbconfig->item('pg_allat_shop_id') . "&order_no=" . element('cor_id', $order);
            }
        } elseif (element('cor_pg', $order) === 'paypal') {

        }

        $result = [
            'data' => $order,
            'orderdetail' => $orderdetail
        ];

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        $this->output->set_output(json_encode($result));
        return true;
    }

    /**
     * 주문 업데이트 함수입니다
     */
    public function orderupdate($agent_type = '')
    {
        if ('mobile' == $agent_type && $this->cbconfig->item('use_payment_pg') === 'inicis' && ($unique_id = $this->session->userdata('unique_id')) && $exist_order = get_cmall_order_data($unique_id)) {    //상품주문
            exists_inicis_cmall_order($unique_id, array(), $exist_order['cor_datetime']);
            exit;
        }

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderupdate';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        if ('bank' != $this->input->post('pay_type') && $this->cbconfig->item('use_payment_pg') === 'lg'
            && !$this->input->post('LGD_PAYKEY')) {
            alert('결제등록 요청 후 주문해 주십시오');
        }

        if (!$this->session->userdata('unique_id') or !$this->input->post('unique_id') or $this->session->userdata('unique_id') !== $this->input->post('unique_id')) {
            alert('잘못된 접근입니다');
        }
        if (!$this->session->userdata('order_cct_id')) {
            alert('잘못된 접근입니다');
        }

        $this->load->model('Cmall_cart_model');
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';
        $session_cct_id = array();

        $good_mny = $this->input->post('good_mny', null, 0);    //request 값으로 받은 값
        $item_cct_price = 0;        //주문한 상품의 총 금액의 초기화

        $orderlist = $this->Cmall_cart_model->get_order_list($where, $findex, $forder);
        if ($orderlist) {
            foreach ($orderlist as $key => $val) {
                $details = $this->Cmall_cart_model->get_order_detail($mem_id, element('cit_id', $val));

                if (!empty($details)) {
                    foreach ((array)$details as $detail) {
                        if (empty($detail)) continue;

                        $item_cct_price += ((int)element('cit_price', $val) + (int)element('cde_price', $detail)) * element('cct_count', $detail);
                    }
                }

                $session_cct_id[] = element('cct_id', $val);
            }
        }

        if ($item_cct_price != $good_mny) {
        }

        if ($this->session->userdata('order_cct_id') !== implode('-', $session_cct_id)) {
            alert('결제 내역이 상이합니다, 관리자에게 문의하여주세요');
        }

        if (!is_numeric($this->input->post('order_deposit'))) {
            alert(html_escape($this->cbconfig->item('deposit_name')) . ' 의 값은 숫자만 와야 합니다');
        }
        if (!is_numeric($this->input->post('total_price_sum'))) {
            alert('총 결제금액의 값은 숫자만 와야 합니다');
        }
        $order_deposit = (int)$this->input->post('order_deposit');
        $total_price_sum = (int)$this->input->post('total_price_sum');
        if ($order_deposit) {
            if ($order_deposit < 0) {
                alert(html_escape($this->cbconfig->item('deposit_name')) . ' 의 값은 0 보다 작을 수 없습니다 ', site_url('cmall/order'));
            }
            if ($order_deposit > $total_price_sum) {
                alert(html_escape($this->cbconfig->item('deposit_name')) . ' 의 값은 총 결제금액보다 클 수 없습니다', site_url('cmall/order'));
            }
            if ($order_deposit > (int)$this->member->item('total_deposit')) {
                alert(html_escape($this->cbconfig->item('deposit_name')) . ' 값이 회원님이 보유하고 계신 값보다 큰 값이 입력되어서 진행할 수 없습니다', site_url('cmall/order'));
            }
        }


        $this->load->library('paymentlib');

        $insertdata = array();
        $result = '';
        $od_status = 'order'; //주문상태

        if ($this->input->post('pay_type') === 'bank') {        //무통장입금
            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
            $insertdata['cor_total_money'] = $total_price_sum;
            $insertdata['cor_cash_request'] = $this->input->post('good_mny', null, 0);
            $insertdata['cor_deposit'] = $order_deposit;
            $insertdata['cor_cash'] = 0;

            /*	 //request 요청값으로 체크하면 안됨
            if ($this->input->post('good_mny')) {
            }
            */

            if (((int)$item_cct_price - (int)$order_deposit) != 0) {
                $insertdata['cor_status'] = 0;
                $insertdata['cor_approve_datetime'] = null;
            } else {
                $insertdata['cor_status'] = 1;
                $insertdata['cor_approve_datetime'] = date('Y-m-d H:i:s');
                $od_status = 'deposit'; //주문상태
            }

        } elseif ($this->input->post('pay_type') === 'realtime') {
            if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                $result = $this->paymentlib->kcp_pp_ax_hub();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                $result = $this->paymentlib->xpay_result();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                $result = $this->paymentlib->inipay_result($agent_type);
            }

            $insertdata['cor_tno'] = element('tno', $result);
            $insertdata['cor_app_no'] = element('app_no', $result) ? element('app_no', $result) : '';
            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['cor_approve_datetime'] = preg_replace(
                "/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/",
                "\\1-\\2-\\3 \\4:\\5:\\6",
                element('app_time', $result)
            );
            $insertdata['cor_total_money'] = $total_price_sum;
            $insertdata['cor_cash_request'] = element('amount', $result);
            $insertdata['cor_deposit'] = $order_deposit;
            $insertdata['cor_cash'] = $cor_cash = element('amount', $result);
            $insertdata['cor_status'] = 1;
            $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
            $insertdata['cor_pg'] = $this->cbconfig->item('use_payment_pg');

            if (((int)$item_cct_price - (int)$order_deposit - $cor_cash) == 0) {
                $od_status = 'deposit'; //주문상태
            }

        } elseif ($this->input->post('pay_type') === 'vbank') {
            if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                $result = $this->paymentlib->kcp_pp_ax_hub();

                $result['bankname'] = iconv("cp949", "utf-8", $result['bankname']);
                $result['depositor'] = iconv("cp949", "utf-8", $result['depositor']);

            } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                $result = $this->paymentlib->xpay_result();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                $result = $this->paymentlib->inipay_result($agent_type);
            }

            $insertdata['cor_tno'] = element('tno', $result);
            $insertdata['cor_app_no'] = element('app_no', $result);
            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['cor_total_money'] = $total_price_sum;
            $insertdata['cor_cash_request'] = element('amount', $result);
            $insertdata['cor_deposit'] = $order_deposit;
            $insertdata['cor_cash'] = 0;
            $insertdata['cor_status'] = 0;
            $insertdata['mem_realname'] = element('depositor', $result);
            $insertdata['cor_vbank_expire'] = element('cor_vbank_expire', $result) ? date("Y-m-d", strtotime(element('cor_vbank_expire', $result))) : '1000-01-01 00:00:00';
            $insertdata['cor_bank_info'] = element('bankname', $result) . ' ' . element('account', $result);
            $insertdata['cor_pg'] = $this->cbconfig->item('use_payment_pg');
        } elseif ($this->input->post('pay_type') === 'phone') {
            if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                $result = $this->paymentlib->kcp_pp_ax_hub();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                $result = $this->paymentlib->xpay_result();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                $result = $this->paymentlib->inipay_result($agent_type);
            }

            $insertdata['cor_tno'] = element('tno', $result);
            $insertdata['cor_app_no'] = element('commid', $result) . ' ' . element('mobile_no', $result);
            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['cor_approve_datetime'] = preg_replace(
                "/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/",
                "\\1-\\2-\\3 \\4:\\5:\\6",
                element('app_time', $result)
            );
            $insertdata['cor_total_money'] = $total_price_sum;
            $insertdata['cor_cash_request'] = element('amount', $result);
            $insertdata['cor_deposit'] = $order_deposit;
            $insertdata['cor_cash'] = $cor_cash = element('amount', $result);
            $insertdata['cor_status'] = 1;
            $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
            $insertdata['cor_bank_info'] = element('mobile_no', $result);
            $insertdata['cor_pg'] = $this->cbconfig->item('use_payment_pg');

            if (((int)$item_cct_price - (int)$order_deposit - $cor_cash) == 0) {
                $od_status = 'deposit'; //주문상태
            }

        } elseif ($this->input->post('pay_type') === 'card') {
            if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                $result = $this->paymentlib->kcp_pp_ax_hub();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                $result = $this->paymentlib->xpay_result();
            } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                $result = $this->paymentlib->inipay_result($agent_type);
            }

            $insertdata['cor_tno'] = element('tno', $result);
            $insertdata['cor_app_no'] = element('app_no', $result);
            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['cor_approve_datetime'] = preg_replace(
                "/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/",
                "\\1-\\2-\\3 \\4:\\5:\\6",
                element('app_time', $result)
            );
            $insertdata['cor_total_money'] = $total_price_sum;
            $insertdata['cor_cash_request'] = element('amount', $result);
            $insertdata['cor_deposit'] = $order_deposit;
            $insertdata['cor_cash'] = $cor_cash = element('amount', $result);
            $insertdata['cor_bank_info'] = element('card_name', $result);
            $insertdata['cor_status'] = 1;
            $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
            $insertdata['cor_pg'] = $this->cbconfig->item('use_payment_pg');

            if (((int)$item_cct_price - (int)$order_deposit - $cor_cash) == 0) {
                $od_status = 'deposit'; //주문상태
            }

        } else {
            alert('결제 수단이 잘못 입력되었습니다');
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('step1', $eventname);

        //실제로 결제된 금액
        $real_total_price = $total_price_sum - $order_deposit;

        // 주문금액과 결제금액이 일치하는지 체크
        if (element('tno', $result) && (int)element('amount', $result) !== $real_total_price) {
            if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                $this->paymentlib->kcp_pp_ax_hub_cancel($result);
            } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                $this->paymentlib->xpay_cancel($result);
            } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                $this->paymentlib->inipay_cancel($result, $agent_type);
            }
            alert('결제가 완료되지 않았습니다. 다시 시도해주십시오', site_url('cmall/order'));
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('step2', $eventname);

        // 정보 입력
        $cor_id = $this->session->userdata('unique_id');
        $insertdata['cor_id'] = $cor_id;
        $insertdata['mem_id'] = $mem_id;
        $insertdata['mem_nickname'] = $this->member->item('mem_nickname');
        $insertdata['mem_email'] = $this->input->post('mem_email', null, '');
        $insertdata['mem_phone'] = $this->input->post('mem_phone', null, '');
        $insertdata['cor_pay_type'] = $this->input->post('pay_type', null, '');
        $insertdata['cor_content'] = $this->input->post('cor_content', null, '');
        $insertdata['cor_ip'] = $this->input->ip_address();
        $insertdata['cor_useragent'] = $this->agent->agent_string();
        $insertdata['is_test'] = $this->cbconfig->item('use_pg_test');
        $insertdata['status'] = $od_status;

        $this->load->model(array('Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model'));
        $res = $this->Cmall_order_model->insert($insertdata);
        if ($res) {
            $cwhere = array(
                'mem_id' => $mem_id,
                'cct_order' => 1,
            );
            $cartorder = $this->Cmall_cart_model->get('', '', $cwhere);
            if ($cartorder) {
                foreach ($cartorder as $key => $val) {
                    $item = $this->Cmall_item_model
                        ->get_one(element('cit_id', $val), 'cit_download_days');
                    $insertdetail = array(
                        'cor_id' => $cor_id,
                        'mem_id' => $mem_id,
                        'cit_id' => element('cit_id', $val),
                        'cde_id' => element('cde_id', $val),
                        'cod_download_days' => element('cit_download_days', $item),
                        'cod_count' => element('cct_count', $val),
                        'cod_status' => $od_status,
                    );
                    $this->Cmall_order_detail_model->insert($insertdetail);
                    $deletewhere = array(
                        'mem_id' => $mem_id,
                        'cit_id' => element('cit_id', $val),
                        'cde_id' => element('cde_id', $val),
                    );
                    $this->Cmall_cart_model->delete_where($deletewhere);
                }
            }
            if ($order_deposit) {
                $this->load->library('depositlib');
                $this->depositlib->do_deposit_to_contents(
                    $mem_id,
                    $order_deposit,
                    $pay_type = '',
                    $content = '상품구매 주문번호 : ' . $cor_id,
                    $admin_memo = ''
                );
            }
        }

        if (empty($res)) {
            if ($this->input->post('pay_type') !== 'bank') {
                if ($this->cbconfig->item('use_payment_pg') === 'kcp') {
                    $this->paymentlib->kcp_pp_ax_hub_cancel($result);
                } elseif ($this->cbconfig->item('use_payment_pg') === 'lg') {
                    $this->paymentlib->xpay_cancel($result);
                } elseif ($this->cbconfig->item('use_payment_pg') === 'inicis') {
                    $this->paymentlib->inipay_cancel($result, $agent_type);
                }
            }
            alert('결제가 완료되지 않았습니다. 다시 시도해주십시오', site_url('cmall/order'));
        }


        if ($this->input->post('pay_type') === 'bank') {
            $this->cmalllib->orderalarm('bank_to_contents', $cor_id);
        } else {
            $this->cmalllib->orderalarm('cash_to_contents', $cor_id);
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        $this->session->set_userdata('unique_id', '');
        $this->session->set_userdata('order_cct_id', '');

        redirect('cmall/orderresult/' . $cor_id);
    }

    /**
     * 주문 AJAX 업데이트 함수입니다
     */
    public function ajax_orderupdate($agent_type = '')
    {
        $this->output->set_content_type('text/json');

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderupdate';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);


        if (!$this->session->userdata('unique_id') or !$this->input->post('unique_id') or $this->session->userdata('unique_id') !== $this->input->post('unique_id')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode([
                'message' => '잘못된 접근입니다. 오류: 주문번호 없음'
            ], JSON_UNESCAPED_UNICODE));
            return false;
        }
        if (!$this->session->userdata('order_cct_id')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode([
                'message' => '잘못된 접근입니다. 오류: order_cct_id 없음'
            ], JSON_UNESCAPED_UNICODE));
            return false;
        }

        $this->load->model(array('Cmall_cart_model', 'Cmall_order_model', 'Cmall_item_detail_model', 'Member_group_member_model'));
        $where = array();
        $where['cmall_cart.mem_id'] = $mem_id;
        $findex = 'cmall_item.cit_id';
        $forder = 'desc';
        $session_cct_id = array();

        $good_mny = floatval($this->input->post('good_mny', null, 0));    //request 값으로 받은 값
        $cor_point = intval($this->input->post('cor_point', null, 0));
        $pay_type = $this->input->post('pay_type');
        $item_cct_price = 0;        //주문한 상품의 총 금액의 초기화
        $item_cct_price_d = 0.0;

        $orderlist = $this->Cmall_cart_model->get_order_list($where, $findex, $forder);
        if ($orderlist) {
            foreach ($orderlist as $key => $val) {
                $details = $this->Cmall_cart_model->get_order_detail($mem_id, element('cit_id', $val));

                if (!empty($details)) {
                    foreach ((array)$details as $detail) {
                        if (empty($detail)) continue;

                        $item_cct_price += intval(element('cde_price', $detail));
                        $item_cct_price_d += (float)element('cde_price_d', $detail) * element('cct_count', $detail);
                    }
                }

                $session_cct_id[] = element('cct_id', $val);
            }
        }

        if ($this->input->post('pay_type') === 'allat') {
            if ($item_cct_price != $good_mny + $cor_point) {
                $this->output->set_status_header('400');
                $this->output->set_output(json_encode([
                    'message' => '결제 금액이 상이합니다. total: ' . $item_cct_price . ', good_mny: ' . $good_mny . ', point: '.$cor_point,
                ], JSON_UNESCAPED_UNICODE));
                return false;
            }
        } elseif ($this->input->post('pay_type') === 'paypal') {
            if ($item_cct_price_d != $good_mny + $cor_point) {
                $this->output->set_status_header('400');
                $this->output->set_output(json_encode([
                    'message' => '결제 금액이 상이합니다'
                ], JSON_UNESCAPED_UNICODE));
                return false;
            }
        }

        $insertdata = array();
        $result = '';
        $od_status = 'order'; //주문상태

        if ($this->input->post('pay_type') === 'allat') {        //올앳
            include(FCPATH . 'plugin/pg/allat/allatutil.php');
            $at_cross_key = $this->cbconfig->item('pg_allat_crosskey');    //설정필요 [사이트 참조 - http://www.allatpay.com/servlet/AllatBiz/support/sp_install_guide_scriptapi.jsp#shop]
            $at_shop_id = $this->cbconfig->item('pg_allat_shop_id');        //설정필요
            $at_amt = $item_cct_price - $cor_point;                        //결제 금액을 다시 계산해서 만들어야 함(해킹방지), ( session, DB 사용 )

            // 요청 데이터 설정
            //----------------------
            $at_data = "allat_shop_id=" . $at_shop_id .
                "&allat_amt=" . $at_amt .
                "&allat_enc_data=" . $_POST["allat_enc_data"] .
                "&allat_cross_key=" . $at_cross_key;
            // 올앳 결제 서버와 통신 : ApprovalReq->통신함수, $at_txt->결과값
            //----------------------------------------------------------------
            // PHP5 이상만 SSL 사용가능
            $at_txt = ApprovalReq($at_data, "SSL");
            // $at_txt = ApprovalReq($at_data, "NOSSL"); // PHP5 이하버전일 경우
            // 이 부분에서 로그를 남기는 것이 좋습니다.
            // (올앳 결제 서버와 통신 후에 로그를 남기면, 통신에러시 빠른 원인파악이 가능합니다.)
            log_message('debug', 'Allat: ' . $at_txt);

            // 결제 결과 값 확인
            //------------------
            $REPLYCD = getValue("reply_cd", $at_txt);        //결과코드
            $REPLYMSG = getValue("reply_msg", $at_txt);       //결과 메세지

            // 결과값 처리
            //--------------------------------------------------------------------------
            // 결과 값이 '0000'이면 정상임. 단, allat_test_yn=Y 일경우 '0001'이 정상임.
            // 실제 결제   : allat_test_yn=N 일 경우 reply_cd=0000 이면 정상
            // 테스트 결제 : allat_test_yn=Y 일 경우 reply_cd=0001 이면 정상
            //--------------------------------------------------------------------------
            if (!strcmp($REPLYCD, "0000") || !strcmp($REPLYCD, "0001")) {
                // reply_cd "0000" 일때만 성공
                $ORDER_NO = getValue("order_no", $at_txt);
                $AMT = getValue("amt", $at_txt);
                $PAY_TYPE = getValue("pay_type", $at_txt);
                $APPROVAL_YMDHMS = getValue("approval_ymdhms", $at_txt);
                $SEQ_NO = getValue("seq_no", $at_txt);
                $APPROVAL_NO = getValue("approval_no", $at_txt);
                $CARD_ID = getValue("card_id", $at_txt);
                $CARD_NM = getValue("card_nm", $at_txt);
                $SELL_MM = getValue("sell_mm", $at_txt);
                $ZEROFEE_YN = getValue("zerofee_yn", $at_txt);
                $CERT_YN = getValue("cert_yn", $at_txt);
                $CONTRACT_YN = getValue("contract_yn", $at_txt);
                $SAVE_AMT = getValue("save_amt", $at_txt);
                $CARD_POINTDC_AMT = getValue("card_pointdc_amt", $at_txt);
                $BANK_ID = getValue("bank_id", $at_txt);
                $BANK_NM = getValue("bank_nm", $at_txt);
                $CASH_BILL_NO = getValue("cash_bill_no", $at_txt);
                $ESCROW_YN = getValue("escrow_yn", $at_txt);
                $ACCOUNT_NO = getValue("account_no", $at_txt);
                $ACCOUNT_NM = getValue("account_nm", $at_txt);
                $INCOME_ACC_NM = getValue("income_account_nm", $at_txt);
                $INCOME_LIMIT_YMD = getValue("income_limit_ymd", $at_txt);
                $INCOME_EXPECT_YMD = getValue("income_expect_ymd", $at_txt);
                $CASH_YN = getValue("cash_yn", $at_txt);
                $HP_ID = getValue("hp_id", $at_txt);
                $TICKET_ID = getValue("ticket_id", $at_txt);
                $TICKET_PAY_TYPE = getValue("ticket_pay_type", $at_txt);
                $TICKET_NAME = getValue("ticket_nm", $at_txt);
                $PARTCANCEL_YN = getValue("partcancel_yn", $at_txt);

                $params = array();
                $params['REPLYCD'] = $REPLYCD;
                $params['REPLYMSG'] = $REPLYMSG;
                $params['ORDER_NO'] = $ORDER_NO;
                $params['AMT'] = $AMT;
                $params['PAY_TYPE'] = $PAY_TYPE;
                $params['APPROVAL_YMDHMS'] = $APPROVAL_YMDHMS;
                $params['SEQ_NO'] = $SEQ_NO;
                $params['APPROVAL_NO'] = $APPROVAL_NO;
                $params['CARD_ID'] = $CARD_ID;
                $params['CARD_NM'] = $CARD_NM;
                $params['SELL_MM'] = $SELL_MM;
                $params['ZEROFEE_YN'] = $ZEROFEE_YN;
                $params['CERT_YN'] = $CERT_YN;
                $params['CONTRACT_YN'] = $CONTRACT_YN;
                $params['SAVE_AMT'] = $SAVE_AMT;
                $params['CARD_POINTDC_AMT'] = $CARD_POINTDC_AMT;
                $params['BANK_ID'] = $BANK_ID;
                $params['BANK_NM'] = $BANK_NM;
                $params['CASH_BILL_NO'] = $CASH_BILL_NO;
                $params['ESCROW_YN'] = $ESCROW_YN;
                $params['ACCOUNT_NO'] = $ACCOUNT_NO;
                $params['ACCOUNT_NM'] = $ACCOUNT_NM;
                $params['INCOME_ACC_NM'] = $INCOME_ACC_NM;
                $params['INCOME_LIMIT_YMD'] = $INCOME_LIMIT_YMD;
                $params['INCOME_EXPECT_YMD'] = $INCOME_EXPECT_YMD;
                $params['CASH_YN'] = $CASH_YN;
                $params['HP_ID'] = $HP_ID;
                $params['TICKET_ID'] = $TICKET_ID;
                $params['TICKET_PAY_TYPE'] = $TICKET_PAY_TYPE;
                $params['TICKET_NAME'] = $TICKET_NAME;
                $params['PARTCANCEL_YN'] = $PARTCANCEL_YN;
                $params['RAW_DATA'] = $at_txt;
                $params['ENC_DATA'] = $_POST["allat_enc_data"];

                $this->Cmall_order_model->allat_log_insert($params);

                $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
                $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
                $insertdata['cor_total_money'] = $item_cct_price;
                $insertdata['cor_cash_request'] = $this->input->post('good_mny', null, 0);
                $insertdata['cor_deposit'] = 0;
                $insertdata['cor_cash'] = $AMT;
                $insertdata['is_test'] = intval($REPLYCD);

                /*	 //request 요청값으로 체크하면 안됨
                if ($this->input->post('good_mny')) {
                }
                */

                $insertdata['cor_status'] = 1;
                $approve_datetime = DateTime::createFromFormat("YmdHis", $APPROVAL_YMDHMS);
                $insertdata['cor_approve_datetime'] = $approve_datetime->format("Y-m-d H:i:s");
                $insertdata['cor_pay_type'] = $PAY_TYPE;
                $insertdata['cor_pg'] = 'allat';
                if (strcasecmp($PAY_TYPE, 'card') == 0 || strcasecmp($PAY_TYPE, '3d') == 0 || strcasecmp($PAY_TYPE, 'nor') == 0) {
                    $insertdata['cor_bank_info'] = $CARD_NM;
                    $insertdata['cor_app_no'] = $APPROVAL_NO;
                } elseif (strcasecmp($PAY_TYPE, 'abank' == 0)) {
                    $insertdata['cor_bank_info'] = $BANK_NM;
                    $insertdata['cor_app_no'] = $CASH_BILL_NO;
                }

//                echo "결과코드              : " . $REPLYCD . "<br>";
//                echo "결과메세지            : " . $REPLYMSG . "<br>";
//                echo "주문번호              : " . $ORDER_NO . "<br>";
//                echo "승인금액              : " . $AMT . "<br>";
//                echo "지불수단              : " . $PAY_TYPE . "<br>";
//                echo "승인일시              : " . $APPROVAL_YMDHMS . "<br>";
//                echo "거래일련번호          : " . $SEQ_NO . "<br>";
//                echo "에스크로 적용 여부    : " . $ESCROW_YN . "<br>";
//                echo "=============== 신용 카드 ===============================<br>";
//                echo "승인번호              : " . $APPROVAL_NO . "<br>";
//                echo "카드ID                : " . $CARD_ID . "<br>";
//                echo "카드명                : " . $CARD_NM . "<br>";
//                echo "할부개월              : " . $SELL_MM . "<br>";
//                echo "무이자여부            : " . $ZEROFEE_YN . "<br>";   //무이자(Y),일시불(N)
//                echo "인증여부              : " . $CERT_YN . "<br>";      //인증(Y),미인증(N)
//                echo "직가맹여부            : " . $CONTRACT_YN . "<br>";  //3자가맹점(Y),대표가맹점(N)
//                echo "세이브 결제 금액      : " . $SAVE_AMT . "<br>";
//                echo "포인트할인 결제 금액  : " . $CARD_POINTDC_AMT . "<br>";
//                echo "=============== 계좌 이체 / 가상계좌 ====================<br>";
//                echo "은행ID                : " . $BANK_ID . "<br>";
//                echo "은행명                : " . $BANK_NM . "<br>";
//                echo "현금영수증 일련 번호  : " . $CASH_BILL_NO . "<br>";
//
//                echo "부분취소가능여부 : " . $PARTCANCEL_YN . "<br>";

            } else {
                $this->output->set_status_header('400');
                $this->output->set_output(json_encode([
                    'reply_cd' => $REPLYCD,
                    'reply_msg' => $REPLYMSG,
                    'message' => $REPLYMSG . ', '. $REPLYCD,
                ], JSON_UNESCAPED_UNICODE));
                return false;
            }

        } elseif ($this->input->post('pay_type') === 'paypal') {
            $paypalData = $_POST["paypal_data"];

            if (empty($paypalData)) {
                $this->output->set_status_header('400');
                return false;
            }

            $params['raw_data'] = $paypalData;
            $paypalData = json_decode($paypalData, true);
            $params['id'] = $paypalData['id'];
            $params['create_time'] = $paypalData['create_time'];
            $params['update_time'] = $paypalData['update_time'];
            $params['state'] = $paypalData['state'];
            $params['intent'] = $paypalData['intent'];
            $params['payment_method'] = $paypalData['payer']['payment_method'];
            $params['email'] = $paypalData['payer']['payer_info']['email'];
            $params['first_name'] = $paypalData['payer']['payer_info']['first_name'];
            $params['last_name'] = $paypalData['payer']['payer_info']['last_name'];
            $params['payer_id'] = $paypalData['payer']['payer_info']['payer_id'];
            $params['invoice_number'] = $paypalData['transactions'][0]['invoice_number'];
            $params['amount'] = $paypalData['transactions'][0]['amount']['total'];
            $params['currency'] = $paypalData['transactions'][0]['amount']['currency'];
            $params['links'] = $paypalData['links']['href'];

            $this->Cmall_order_model->paypal_log_insert($params);

            $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
            $insertdata['mem_realname'] = $this->input->post('mem_realname', null, '');
            $insertdata['cor_total_money'] = $item_cct_price_d;
            $insertdata['cor_cash_request'] = $this->input->post('good_mny', null, 0);
            $insertdata['cor_deposit'] = 0;
            $insertdata['cor_cash'] = $paypalData['transactions'][0]['amount']['total'];
            $insertdata['cor_pg'] = 'paypal';
            $insertdata['is_test'] = $this->cbconfig->item('use_pg_test');
            $insertdata['cor_pay_type'] = 'paypal';

            /*	 //request 요청값으로 체크하면 안됨
            if ($this->input->post('good_mny')) {
            }
            */

            $insertdata['cor_status'] = 1;
            $dt = new DateTime($paypalData['create_time']);
            $kstTimezone = new DateTimeZone('Asia/Seoul');
            $dt->setTimezone($kstTimezone);
            $create_time = $dt->format("Y-m-d H:i:s");
            $insertdata['cor_approve_datetime'] = $create_time;
        } else {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode([
                'message' => '결제 수단이 잘못 입력되었습니다'
            ], JSON_UNESCAPED_UNICODE));
            return false;
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('step1', $eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('step2', $eventname);

        // 정보 입력
        $cor_id = $this->session->userdata('unique_id');
        $insertdata['cor_id'] = $cor_id;
        $insertdata['cor_point'] = $cor_point;
        $insertdata['mem_id'] = $mem_id;
        $insertdata['mem_nickname'] = $this->member->item('mem_nickname');
        $insertdata['mem_email'] = $this->member->item('mem_email');
        $insertdata['mem_phone'] = $this->member->item('mem_phone');
        $insertdata['cor_content'] = $this->input->post('cor_content', null, '');
        $insertdata['cor_ip'] = $this->input->ip_address();
        $insertdata['cor_useragent'] = $this->agent->agent_string();
        $insertdata['status'] = $od_status;
        log_message('debug', 'Insert Data: ' . json_encode($insertdata));

        $this->load->model(array('Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model'));
        $res = $this->Cmall_order_model->insert($insertdata);
        if ($res) {
            $this->db->query("INSERT INTO cb_point (mem_id, poi_datetime, poi_content, poi_point, poi_type, poi_related_id, poi_action) VALUES (?, ?, ?, ?, ?, ?, ?)", [
                $mem_id,
                cdate('Y-m-d H:i:s'),
                cdate('Y-m-d H:i:s') . ' 주문',
                -$cor_point,
                'order',
                $this->member->item('mem_id'),
                $this->member->item('mem_id') . '-' . $cor_id
            ]);
            $this->db->query("UPDATE cb_member SET mem_point=mem_point-? WHERE mem_id=?", [$cor_point, $mem_id]);
            $cwhere = array(
                'mem_id' => $mem_id,
                'cct_order' => 1,
            );
            $cartorder = $this->Cmall_cart_model->get('', '', $cwhere);
            if ($cartorder) {
                foreach ($cartorder as $key => $val) {
                    $item_detail = $this->Cmall_item_detail_model->get_one(element('cde_id', $val));
                    $cde_price = intval($item_detail['cde_price']);
                    $cde_price_d = floatval($item_detail['cde_price_d']);
                    if ($pay_type == 'allat') {
                        $item_point = round($cde_price / $item_cct_price * $cor_point);
                    } else {
                        $item_point = round($cde_price_d / $item_cct_price_d * $cor_point);
                    }
                    $member_group = $this->Member_group_member_model->get_with_group($item_detail['mem_id']);

                    $insertdetail = array(
                        'cor_id' => $cor_id,
                        'mem_id' => $mem_id,
                        'cit_id' => element('cit_id', $val),
                        'cde_id' => element('cde_id', $val),
                        'cod_count' => element('cct_count', $val),
                        'cod_status' => $od_status,
                        'cod_point' => $item_point,
                        'cod_seller_usertype' => element('mgr_id', $member_group[0])
                    );
                    $this->Cmall_order_detail_model->insert($insertdetail);
                    $deletewhere = array(
                        'mem_id' => $mem_id,
                        'cit_id' => element('cit_id', $val),
                        'cde_id' => element('cde_id', $val),
                    );
                    $this->Cmall_cart_model->delete_where($deletewhere);
                }
            }
        } else {
            $this->output->set_status_header('500');
            $this->output->set_output(json_encode([
                'message' => '주문이 디비에 입력되지 않았습니다.'
            ], JSON_UNESCAPED_UNICODE));
            return false;
        }


        if ($this->input->post('pay_type') === 'allat') {
            $this->cmalllib->orderalarm('bank_to_contents', $cor_id);
        } else {
            $this->cmalllib->orderalarm('cash_to_contents', $cor_id);
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        $this->session->set_userdata('unique_id', '');
        $this->session->set_userdata('order_cct_id', '');

        $this->output->set_status_header('200');
        $this->output->set_output(json_encode([
            'message' => 'Success'
        ]));
        return true;
    }


    public function orderlist()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderlist';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_order_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = $this->Cmall_order_model->primary_key;
        $forder = 'desc';

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['mem_id'] = $this->member->item('mem_id');

        $result = $this->Cmall_order_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        $view['view']['data'] = $result;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/orderlist') . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_orderlist');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_orderlist');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_orderlist');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_orderlist');
        $page_name = $this->cbconfig->item('site_page_name_cmall_orderlist');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'order_list',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function ajax_orderlist()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderlist';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_order_model', 'Cmall_order_detail_model', 'Cmall_item_model', 'Cmall_download_log_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $forder = $this->input->get('forder');

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */

        $this->member->item('mem_id');
        $cor_status = $this->input->get('cor_status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $is_download = $this->input->get('is_download');
        $mem_id = $this->member->item('mem_id');

        $where = "cor.mem_id = " . $mem_id;
        $where_total = $where;
        $where1 = $where;
        $where2 = $where;
        $where3 = $where;
        $where_cancel = $where;
        $join = "";

        if ($cor_status == '0' || $cor_status == '1') {
            $where .= " AND cor_status = " . $cor_status;
        } else {
            $where .= " AND (cor_status = 1 OR cor_status = 0)";
        }

        $where1 .= " AND (cor_status = 1 OR cor_status = 0)";
        $where2 .= " AND (cor_status = 0)";
        $where3 .= " AND (cor_status = 1)";
        $where_total .= " AND (cor_status = 1 OR cor_status = 0)";
        $where_cancel .= " AND (cor_status = 2)";

        if ($start_date && $end_date) {
            $where .= " AND cor_datetime >= '" . $start_date . " 00:00:00' AND cor_datetime <= '" . $end_date . " 23:59:59'";
            $where1 .= " AND cor_datetime >= '" . $start_date . " 00:00:00' AND cor_datetime <= '" . $end_date . " 23:59:59'";
            $where2 .= " AND cor_datetime >= '" . $start_date . " 00:00:00' AND cor_datetime <= '" . $end_date . " 23:59:59'";
            $where3 .= " AND cor_datetime >= '" . $start_date . " 00:00:00' AND cor_datetime <= '" . $end_date . " 23:59:59'";
        }

        if ($is_download == '0' || $is_download == '1') {
            $join .= "LEFT JOIN cb_cmall_download_log as cdl on cor.cor_id = cdl.cor_id ";
        }

        if ($join) {
            $sql = "SELECT * FROM (SELECT cor.*, IF(cdl.cdl_id IS NULL, 0, 1) as is_download FROM cb_cmall_order as cor " . $join . " WHERE " . $where . " GROUP BY cor.cor_id ORDER BY cor_datetime " . $forder . ") as order_with_download_log WHERE is_download = " . $is_download;
            $sql1 = "SELECT COUNT(*) as rownum FROM (SELECT cor.*, IF(cdl.cdl_id IS NULL, 0, 1) as is_download FROM cb_cmall_order as cor " . $join . " WHERE " . $where1 . " GROUP BY cor.cor_id ORDER BY cor_datetime " . $forder . ") as order_with_download_log WHERE is_download = " . $is_download;
            $sql2 = "SELECT COUNT(*) as rownum FROM (SELECT cor.*, IF(cdl.cdl_id IS NULL, 0, 1) as is_download FROM cb_cmall_order as cor " . $join . " WHERE " . $where2 . " GROUP BY cor.cor_id ORDER BY cor_datetime " . $forder . ") as order_with_download_log WHERE is_download = " . $is_download;
            $sql_complete = "SELECT COUNT(*) as rownum FROM (SELECT cor.*, IF(cdl.cdl_id IS NULL, 0, 1) as is_download FROM cb_cmall_order as cor " . $join . " WHERE " . $where3 . " GROUP BY cor.cor_id ORDER BY cor_datetime " . $forder . ") as order_with_download_log WHERE is_download = " . $is_download;
        } else {
            $sql = "SELECT * FROM cb_cmall_order as cor WHERE " . $where . " ORDER BY cor.cor_datetime " . $forder;
            $sql1 = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where1;
            $sql2 = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where2;
            $sql_complete = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where3;
        }

        $sql_total = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where_total;
        $sql_cancel = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where_cancel;

        $data = array();

        $data['list'] = $this->db->query($sql)->result_array();

        $data['total_rows'] = ($this->db->query($sql_total)->row_array())['rownum'];
        $data['total_order_rows'] = ($this->db->query($sql1)->row_array())['rownum'];
        $data['total_deposit_rows'] = ($this->db->query($sql2)->row_array())['rownum'];
        $data['total_complete_rows'] = ($this->db->query($sql_complete)->row_array())['rownum'];
        $data['total_cancel_rows'] = ($this->db->query($sql_cancel)->row_array())['rownum'];

        foreach ($data['list'] as $key0 => $order) {
            $orderdetail = $this->Cmall_order_detail_model->get_by_item($order['cor_id']);
            foreach ($orderdetail as $key1 => $value) {
                $item = $this->Cmall_item_model->get_one(element('cit_id', $value));
                if ($item) {
                    $orderdetail[$key1]['item'] = $item;
                    $orderdetail[$key1]['itemdetail'] = $itemdetail
                        = $this->Cmall_order_detail_model
                        ->get_detail_by_item($order['cor_id'], element('cit_id', $value));
                    $where = [
                        'cit_id' => element('cit_id', $value),
                        'cde_id' => $itemdetail[0]['cde_id'],
                        'mem_id' => $mem_id,
                        'cor_id' => $order['cor_id']
                    ];

                    $download_log = $this->Cmall_download_log_model->get_one('', '', $where);
                    if ($download_log != false) {
                        $orderdetail[$key1]['item']['possible_refund'] = 0;
                    } else {
                        $orderdetail[$key1]['item']['possible_refund'] = 1;
                    }

                    $orderdetail[$key1]['item']['possible_download'] = 1;

                    if (element('cor_status', $order) == '1') {
                        if (strcasecmp(element('cde_title', element(0, $itemdetail)), "LEASE") == 0) {
                            if (element('cor_approve_datetime', $order)) {
                                $endtimestamp = strtotime(element('cor_approve_datetime', $order))
                                    + 86400 * 60;
                                $orderdetail[$key1]['item']['download_end_date'] = $enddate
                                    = cdate('Y-m-d', $endtimestamp);

                                $orderdetail[$key1]['item']['possible_download'] = ($enddate >= date('Y-m-d')) ? 1 : 0;
                            } else {
                                $orderdetail[$key1]['item']['possible_download'] = 0;
                            }
                        } elseif (strcasecmp(element('cde_title', element(0, $itemdetail)), "STEM") == 0) {
                            $orderdetail[$key1]['item']['possible_download'] = 1;
                        } else {
                            $orderdetail[$key1]['item']['possible_download'] = 0;
                        }
                    } else {
                        $orderdetail[$key1]['item']['possible_download'] = 0;
                    }

                } else {
                    $orderdetail[$key1]['item'] = null;
                    $orderdetail[$key1]['itemdetail'] = null;
                }
            }
            $data['list'][$key0]['detail'] = $orderdetail;
        }

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/orderlist') . '?' . $param->replace('page');
        $config['total_rows'] = $data['total_order_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $paging = $this->pagination->create_links();


        // 이벤트가 존재하면 실행합니다
        Events::trigger('before_layout', $eventname);

        $result = [
            'data' => $data,
            'page' => $page,
            'paging' => $paging,
        ];

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    public function ajax_cancellist()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_orderlist';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        ajax_required_user_login();

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_order_model', 'Cmall_order_detail_model', 'Cmall_item_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = 'cor_datetime';
        $forder = $this->input->get('forder');

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */

        $this->member->item('mem_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $mem_id = $this->member->item('mem_id');

        $where = "cor.mem_id = " . $mem_id;
        $where_total = $where;
        $where_cancel = $where;

        $where .= " AND cor_status = 2 ";

        $where_total .= " AND (cor_status = 1 OR cor_status = 0)";
        $where_cancel .= " AND (cor_status = 2)";

        if ($start_date && $end_date) {
            $where .= " AND cor_datetime >= '" . $start_date . " 00:00:00' AND cor_datetime <= '" . $end_date . " 23:59:59'";
        }

        $sql = "SELECT * FROM cb_cmall_order as cor WHERE " . $where . " ORDER BY cor.cor_datetime " . $forder;

        $sql_total = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where_total;
        $sql_cancel = "SELECT COUNT(*) as rownum FROM cb_cmall_order as cor WHERE " . $where_cancel;

        $data = array();

        $data['list'] = $this->db->query($sql)->result_array();

        $data['total_rows'] = ($this->db->query($sql_total)->row_array())['rownum'];
        $data['total_cancel_rows'] = ($this->db->query($sql_cancel)->row_array())['rownum'];

        foreach ($data['list'] as $key0 => $order) {
            $orderdetail = $this->Cmall_order_detail_model->get_by_item($order['cor_id']);
            foreach ($orderdetail as $key1 => $value) {
                $item = $this->Cmall_item_model->get_one(element('cit_id', $value));
                if ($item) {
                    $orderdetail[$key1]['item'] = $item;
                    $orderdetail[$key1]['itemdetail'] = $itemdetail
                        = $this->Cmall_order_detail_model
                        ->get_detail_by_item($order['cor_id'], element('cit_id', $value));

                } else {
                    $orderdetail[$key1]['item'] = null;
                    $orderdetail[$key1]['itemdetail'] = null;
                }
            }
            $data['list'][$key0]['detail'] = $orderdetail;
        }

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/orderlist') . '?' . $param->replace('page');
        $config['total_rows'] = $data['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $paging = $this->pagination->create_links();


        // 이벤트가 존재하면 실행합니다
        Events::trigger('before_layout', $eventname);

        $result = [
            'data' => $data,
            'page' => $page,
            'paging' => $paging,
        ];

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }

    public function wishlist()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_wishlist';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_wishlist_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = $this->Cmall_wishlist_model->primary_key;
        $forder = 'desc';

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_wishlist.mem_id'] = $this->member->item('mem_id');
        $where['cit_status'] = 1;
        $result = $this->Cmall_wishlist_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result['list'][$key]['delete_url'] = site_url('cmallact/wishlist_delete/' . element('cwi_id', $val) . '?' . $param->output());
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $view['view']['data'] = $result;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/wishlist') . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $page_title = $this->cbconfig->item('site_meta_title_cmall_wishlist');
        $meta_description = $this->cbconfig->item('site_meta_description_cmall_wishlist');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_wishlist');
        $meta_author = $this->cbconfig->item('site_meta_author_cmall_wishlist');
        $page_name = $this->cbconfig->item('site_page_name_cmall_wishlist');

        $searchconfig = array(
            '{컨텐츠몰명}',
        );
        $replaceconfig = array(
            $this->cbconfig->item('cmall_name'),
        );

        $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
        $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
        $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
        $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
        $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

        $layoutconfig = array(
            'path' => 'cmall',
            'layout' => 'layout',
            'skin' => 'wishlist',
            'layout_dir' => $this->cbconfig->item('layout_cmall'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
            'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
            'skin_dir' => $this->cbconfig->item('skin_cmall'),
            'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    public function ajax_wishlist()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_wishlist';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login();

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_wishlist_model'));
        $this->load->model(array('Cmall_item_model', 'Cmall_item_meta_model', 'Cmall_item_detail_model'));
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = $this->Cmall_wishlist_model->primary_key;
        $forder = 'desc';

        $per_page = $this->cbconfig->item('list_count') ? (int)$this->cbconfig->item('list_count') : 20;
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cmall_wishlist.mem_id'] = $this->member->item('mem_id');
        $where['cit_status'] = 1;
        $result = $this->Cmall_wishlist_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['thumb'] = cover_thumb_name(element('cit_file_1', $val), 'list');
                $result['list'][$key]['item_url'] = cmall_item_url(element('cit_key', $val));
                $result['list'][$key]['waveform'] = json_decode(element('waveform', $val), true);
                $result['list'][$key]['delete_url'] = site_url('cmallact/wishlist_delete/' . element('cwi_id', $val) . '?' . $param->output());
                $result['list'][$key]['num'] = $list_num--;
                $result['list'][$key]['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $val));
                $itemdetails = $this->Cmall_item_detail_model->get_all_detail(element('cit_id', $val));
                foreach ($itemdetails as $itemdetail) {
                    $result['list'][$key]['detail'][$itemdetail['cde_title']] = $itemdetail;
                }
            }
        }

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode($result));
    }


    /**
     * 상품후기 목록을 ajax 로 가져옵니다
     */
    public function reviewlist($cit_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_reviewlist';
        $this->load->event($eventname);

        $cit_id = (int)$cit_id;
        if (empty($cit_id) or $cit_id < 1) {
            show_404();
        }

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_item_model', 'Cmall_review_model'));

        $item = $this->Cmall_item_model->get_one($cit_id);
        if (!element('cit_id', $item)) {
            show_404();
        }

        $mem_id = (int)$this->member->item('mem_id');

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = $this->input->get('findex', null, 'cre_id');
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = '';
        $skeyword = '';

        $per_page = 5;
        $offset = ($page - 1) * $per_page;

        $is_admin = $this->member->is_admin();

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cre_status'] = 1;
        $where['cit_id'] = $cit_id;

        $thumb_width = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_review_mobile_thumb_width')
            : $this->cbconfig->item('cmall_product_review_thumb_width');
        $autolink = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('use_cmall_product_review_mobile_auto_url')
            : $this->cbconfig->item('use_cmall_product_review_auto_url');
        $popup = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_review_mobile_content_target_blank')
            : $this->cbconfig->item('cmall_product_review_content_target_blank');

        $result = $this->Cmall_review_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['display_name'] = display_username(
                    element('mem_userid', $val),
                    element('mem_nickname', $val),
                    element('mem_icon', $val)
                );
                $result['list'][$key]['display_datetime'] = display_datetime(element('cre_datetime', $val), 'full');
                $result['list'][$key]['content'] = display_html_content(
                    element('cre_content', $val),
                    element('cre_content_html_type', $val),
                    $thumb_width,
                    $autolink,
                    $popup
                );
                $result['list'][$key]['can_update'] = false;
                $result['list'][$key]['can_delete'] = false;
                if ($is_admin !== false
                    or (element('mem_id', $val) && $mem_id === (int)element('mem_id', $val))) {
                    $result['list'][$key]['can_update'] = true;
                    $result['list'][$key]['can_delete'] = true;
                }
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $view['view']['data'] = $result;
        $view['view']['cit_id'] = $cit_id;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/reviewlist/' . $cit_id) . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;

        if (!$this->input->get('page')) {
            $_GET['page'] = (string)$page;
        }

        $config['_attributes'] = 'onClick="cmall_review_page(\'' . $cit_id . '\', $(this).attr(\'data-ci-pagination-page\'));return false;"';
        if ($this->cbconfig->get_device_view_type() === 'mobile') {
            $config['num_links'] = 3;
        } else {
            $config['num_links'] = 5;
        }
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('mobile_skin_cmall')
            : $this->cbconfig->item('skin_cmall');
        if (empty($skindir)) {
            $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? $this->cbconfig->item('mobile_skin_default')
                : $this->cbconfig->item('skin_default');
        }
        if (empty($skindir)) {
            $skindir = 'basic';
        }
        $skin = 'cmall/' . $skindir . '/review_list';

        $view['view']['view_skin_url'] = site_url(VIEW_DIR . 'cmall/' . $skindir);

        $this->data = $view;
        $this->view = $skin;
    }


    public function review_write($cit_id = 0, $cre_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_review_write';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login('alert');


        $view = array();
        $view['view'] = array();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_item_model', 'Cmall_review_model'));

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        if ($cre_id) {
            $cre_id = (int)$cre_id;
            if (empty($cre_id) or $cre_id < 1) {
                show_404();
            }
        }
        $cit_id = (int)$cit_id;
        if (empty($cit_id) or $cit_id < 1) {
            show_404();
        }

        $primary_key = $this->Cmall_review_model->primary_key;

        $item = $this->Cmall_item_model->get_one($cit_id);
        if (!element('cit_id', $item) or !element('cit_status', $item)) {
            alert_close('존재하지 않는 상품입니다');
        }

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        $getdata = array();
        if ($cre_id) {
            $getdata = $this->Cmall_review_model->get_one($cre_id);
            if (!element('cre_id', $getdata)) {
                alert_close('잘못된 접근입니다');
            }
            $is_admin = $this->member->is_admin();
            if ($is_admin === false
                && (int)element('mem_id', $getdata) !== $mem_id) {
                alert_close('본인의 글 외에는 접근하실 수 없습니다');
            }
        }

        /**
         * 주문완료 후 사용후기 작성 가능한 경우
         **/
        if (!$this->cbconfig->item('use_cmall_product_review_anytime')) {
            $ordered = $this->cmalllib->is_ordered_item($mem_id, $cit_id);
            if (empty($ordered)) {
                alert_close('주문을 완료하신 후에 상품후기 작성이 가능합니다');
            }

        }

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'cre_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cre_content',
                'label' => '내용',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cre_score',
                'label' => '평점',
                'rules' => 'trim|required|numeric|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[5]',
            ),
        );
        $this->form_validation->set_rules($config);


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;
            $view['view']['data'] = $getdata;
            $view['view']['item'] = $item;

            /**
             * 레이아웃을 정의합니다
             */
            $page_title = $this->cbconfig->item('site_meta_title_cmall_review_write');
            $meta_description = $this->cbconfig->item('site_meta_description_cmall_review_write');
            $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_review_write');
            $meta_author = $this->cbconfig->item('site_meta_author_cmall_review_write');
            $page_name = $this->cbconfig->item('site_page_name_cmall_review_write');

            $searchconfig = array(
                '{컨텐츠몰명}',
                '{상품명}',
                '{판매가격}',
                '{기본설명}',
            );
            $replaceconfig = array(
                $this->cbconfig->item('cmall_name'),
                element('cit_name', $item),
                element('cit_price', $item),
                element('cit_summary', $item),
            );

            $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
            $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
            $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
            $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
            $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

            $layoutconfig = array(
                'path' => 'cmall',
                'layout' => 'layout_popup',
                'skin' => 'review_write',
                'layout_dir' => $this->cbconfig->item('layout_cmall'),
                'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
                'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
                'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
                'skin_dir' => $this->cbconfig->item('skin_cmall'),
                'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
                'page_title' => $page_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'meta_author' => $meta_author,
                'page_name' => $page_name,
            );
            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            Events::trigger('formruntrue', $eventname);

            $content_type = $this->cbconfig->item('use_cmall_product_review_dhtml') ? 1 : 0;
            $updatedata = array(
                'cit_id' => $cit_id,
                'cre_title' => $this->input->post('cre_title', null, ''),
                'cre_content' => $this->input->post('cre_content', null, ''),
                'cre_content_html_type' => $content_type,
                'cre_score' => $this->input->post('cre_score', null, 0),
            );

            /**
             * 게시물을 수정하는 경우입니다
             */
            $param =& $this->querystring;
            $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;

            if ($cre_id) {

                // 이벤트가 존재하면 실행합니다
                Events::trigger('before_update', $eventname);

                $this->Cmall_review_model->update($cre_id, $updatedata);
                $cntresult = $this->cmalllib->update_review_count($cit_id);
                $jresult = json_decode($cntresult, true);
                $cnt = element('cit_review_count', $jresult);
                echo '<script type="text/javascript">window.opener.view_cmall_review("viewitemreview", ' . $cit_id . ', ' . $page . ');window.opener.cmall_review_count_update(' . $cnt . ');</script>';
                alert_close('정상적으로 수정되었습니다.');
            } else {

                // 이벤트가 존재하면 실행합니다
                Events::trigger('before_insert', $eventname);

                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['cre_datetime'] = cdate('Y-m-d H:i:s');
                $updatedata['mem_id'] = $mem_id;
                $updatedata['cre_ip'] = $this->input->ip_address();

                if (!$this->cbconfig->item('use_cmall_product_review_approve')) {
                    $updatedata['cre_status'] = 1;
                }

                $_cre_id = $this->Cmall_review_model->insert($updatedata);

                $this->cmalllib->review_alarm($_cre_id);

                $cntresult = $this->cmalllib->update_review_count($cit_id);
                $jresult = json_decode($cntresult, true);
                $cnt = element('cit_review_count', $jresult);
                if ($this->cbconfig->item('use_cmall_product_review_approve')) {
                    echo '<script type="text/javascript">window.opener.view_cmall_review("viewitemreview", ' . $cit_id . ', ' . $page . ');window.opener.cmall_review_count_update(' . $cnt . ');</script>';
                    alert_close('정상적으로 입력되었습니다. 관리자의 승인 후 출력됩니다');
                } else {
                    echo '<script type="text/javascript">window.opener.view_cmall_review("viewitemreview", ' . $cit_id . ', ' . $page . ');window.opener.cmall_review_count_update(' . $cnt . ');</script>';
                    alert_close('정상적으로 입력되었습니다.');
                }
            }
        }
    }


    /**
     * Q&A 목록을 ajax 로 가져옵니다
     */
    public function qnalist($cit_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_qnalist';
        $this->load->event($eventname);

        $cit_id = (int)$cit_id;
        if (empty($cit_id) or $cit_id < 1) {
            show_404();
        }

        $view = array();
        $view['view'] = array();

        $mem_id = (int)$this->member->item('mem_id');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_item_model', 'Cmall_qna_model'));

        $item = $this->Cmall_item_model->get_one($cit_id);
        if (!element('cit_id', $item)) {
            show_404();
        }

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;
        $findex = $this->input->get('findex') ? $this->input->get('findex') : 'cre_id';
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = '';
        $skeyword = '';

        $per_page = 5;
        $offset = ($page - 1) * $per_page;

        $is_admin = $this->member->is_admin();

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        $where['cit_id'] = $cit_id;

        $thumb_width = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_qna_mobile_thumb_width')
            : $this->cbconfig->item('cmall_product_qna_thumb_width');
        $autolink = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('use_cmall_product_qna_mobile_auto_url')
            : $this->cbconfig->item('use_cmall_product_qna_auto_url');
        $popup = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('cmall_product_qna_mobile_content_target_blank')
            : $this->cbconfig->item('cmall_product_qna_content_target_blank');

        $result = $this->Cmall_qna_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['display_name'] = display_username(
                    element('mem_userid', $val),
                    element('mem_nickname', $val),
                    element('mem_icon', $val)
                );
                $result['list'][$key]['display_datetime'] = display_datetime(element('cqa_datetime', $val), 'full');
                $result['list'][$key]['content'] = display_html_content(
                    element('cqa_content', $val),
                    element('cqa_content_html_type', $val),
                    $thumb_width,
                    $autolink,
                    $popup
                );
                $result['list'][$key]['reply_content'] = display_html_content(
                    element('cqa_reply_content', $val),
                    element('cqa_reply_html_type', $val),
                    $thumb_width,
                    $autolink,
                    $popup
                );
                if (element('cqa_secret', $val)) {
                    if ($mem_id && ($is_admin !== false or (int)element('mem_id', $val) === $mem_id)) {
                        $result['list'][$key]['content'] = '<div class="label label-warning">비밀글입니다</div> ' . $result['list'][$key]['content'];
                        $result['list'][$key]['reply_content'] = '<div class="label label-warning">비밀글입니다</div>' . $result['list'][$key]['reply_content'];
                    } else {
                        $result['list'][$key]['content'] = '<div class="label label-warning">비밀글입니다</div>';
                        $result['list'][$key]['reply_content'] = '<div class="label label-warning">비밀글입니다</div>';
                    }
                }
                if (!element('cqa_reply_content', $val)) {
                    $result['list'][$key]['reply_content'] = '아직 답변이 완료되지 않았습니다';
                }

                $result['list'][$key]['can_update'] = false;
                $result['list'][$key]['can_delete'] = false;
                if ($is_admin !== false or (element('mem_id', $val) && $mem_id === (int)element('mem_id', $val))) {
                    $result['list'][$key]['can_update'] = true;
                    $result['list'][$key]['can_delete'] = true;
                }
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $view['view']['data'] = $result;
        $view['view']['cit_id'] = $cit_id;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = site_url('cmall/qnalist/' . $cit_id) . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;

        if (!$this->input->get('page')) {
            $_GET['page'] = (string)$page;
        }

        $config['_attributes'] = 'onClick="cmall_qna_page(\'' . $cit_id . '\', $(this).attr(\'data-ci-pagination-page\'));return false;"';
        if ($this->cbconfig->get_device_view_type() === 'mobile') {
            $config['num_links'] = 3;
        } else {
            $config['num_links'] = 5;
        }
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? $this->cbconfig->item('mobile_skin_cmall')
            : $this->cbconfig->item('skin_cmall');
        if (empty($skindir)) {
            $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? $this->cbconfig->item('mobile_skin_default')
                : $this->cbconfig->item('skin_default');
        }
        if (empty($skindir)) {
            $skindir = 'basic';
        }
        $skin = 'cmall/' . $skindir . '/qna_list';

        $this->data = $view;
        $this->view = $skin;
    }


    public function qna_write($cit_id = 0, $cqa_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_cmall_qna_write';
        $this->load->event($eventname);

        /**
         * 로그인이 필요한 페이지입니다
         */
        required_user_login('alert');

        $mem_id = (int)$this->member->item('mem_id');

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_item_model', 'Cmall_qna_model'));

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        if ($cqa_id) {
            $cqa_id = (int)$cqa_id;
            if (empty($cqa_id) or $cqa_id < 1) {
                show_404();
            }
        }
        $cit_id = (int)$cit_id;
        if (empty($cit_id) or $cit_id < 1) {
            show_404();
        }
        $primary_key = $this->Cmall_qna_model->primary_key;

        $item = $this->Cmall_item_model->get_one($cit_id);
        if (!element('cit_id', $item) or !element('cit_status', $item)) {
            alert_close('존재하지 않는 상품입니다');
        }

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        $getdata = array();
        if ($cqa_id) {
            $getdata = $this->Cmall_qna_model->get_one($cqa_id);
            if (!element('cqa_id', $getdata)) {
                alert_close('잘못된 접근입니다');
            }
            $is_admin = $this->member->is_admin();
            if ($is_admin === false && (int)element('mem_id', $getdata) !== $mem_id) {
                alert_close('본인의 글 외에는 접근하실 수 없습니다');
            }
        }

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'cqa_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cqa_content',
                'label' => '내용',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cqa_secret',
                'label' => '비밀글여부',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'cqa_receive_email',
                'label' => '답변시 메일받기',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'cqa_receive_sms',
                'label' => '답변시 문자받기',
                'rules' => 'trim|numeric',
            ),
        );
        $this->form_validation->set_rules($config);


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;
            $view['view']['data'] = $getdata;
            $view['view']['item'] = $item;

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

            /**
             * 레이아웃을 정의합니다
             */
            $page_title = $this->cbconfig->item('site_meta_title_cmall_qna_write');
            $meta_description = $this->cbconfig->item('site_meta_description_cmall_qna_write');
            $meta_keywords = $this->cbconfig->item('site_meta_keywords_cmall_qna_write');
            $meta_author = $this->cbconfig->item('site_meta_author_cmall_qna_write');
            $page_name = $this->cbconfig->item('site_page_name_cmall_qna_write');

            $searchconfig = array(
                '{컨텐츠몰명}',
                '{상품명}',
                '{판매가격}',
                '{기본설명}',
            );
            $replaceconfig = array(
                $this->cbconfig->item('cmall_name'),
                element('cit_name', $item),
                element('cit_price', $item),
                element('cit_summary', $item),
            );

            $page_title = str_replace($searchconfig, $replaceconfig, $page_title);
            $meta_description = str_replace($searchconfig, $replaceconfig, $meta_description);
            $meta_keywords = str_replace($searchconfig, $replaceconfig, $meta_keywords);
            $meta_author = str_replace($searchconfig, $replaceconfig, $meta_author);
            $page_name = str_replace($searchconfig, $replaceconfig, $page_name);

            $layoutconfig = array(
                'path' => 'cmall',
                'layout' => 'layout_popup',
                'skin' => 'qna_write',
                'layout_dir' => $this->cbconfig->item('layout_cmall'),
                'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_cmall'),
                'use_sidebar' => $this->cbconfig->item('sidebar_cmall'),
                'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_cmall'),
                'skin_dir' => $this->cbconfig->item('skin_cmall'),
                'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_cmall'),
                'page_title' => $page_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'meta_author' => $meta_author,
                'page_name' => $page_name,
            );
            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            $content_type = $this->cbconfig->item('use_cmall_product_qna_dhtml') ? 1 : 0;
            $cqa_secret = $this->input->post('cqa_secret') ? 1 : 0;
            $cqa_receive_email = $this->input->post('cqa_receive_email') ? 1 : 0;
            $cqa_receive_sms = $this->input->post('cqa_receive_sms') ? 1 : 0;

            $updatedata = array(
                'cit_id' => $cit_id,
                'cqa_title' => $this->input->post('cqa_title', null, ''),
                'cqa_content' => $this->input->post('cqa_content', null, ''),
                'cqa_content_html_type' => $content_type,
                'cqa_secret' => $cqa_secret,
                'cqa_receive_email' => $cqa_receive_email,
                'cqa_receive_sms' => $cqa_receive_sms,
            );

            /**
             * 게시물을 수정하는 경우입니다
             */
            $param =& $this->querystring;
            $page = (((int)$this->input->get('page')) > 0) ? ((int)$this->input->get('page')) : 1;

            if ($cqa_id) {
                $this->Cmall_qna_model->update($cqa_id, $updatedata);
                $cntresult = $this->cmalllib->update_qna_count($cit_id);
                $jresult = json_decode($cntresult, true);
                $cnt = element('cit_qna_count', $jresult);
                echo '<script type="text/javascript">window.opener.view_cmall_qna("viewitemqna", ' . $cit_id . ', ' . $page . ');window.opener.cmall_qna_count_update(' . $cnt . ');</script>';
                alert_close('정상적으로 수정되었습니다.');
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['cqa_datetime'] = cdate('Y-m-d H:i:s');
                $updatedata['mem_id'] = $mem_id;
                $updatedata['cqa_ip'] = $this->input->ip_address();

                $_cqa_id = $this->Cmall_qna_model->insert($updatedata);

                $this->cmalllib->qna_alarm($_cqa_id);

                $cntresult = $this->cmalllib->update_qna_count($cit_id);
                $jresult = json_decode($cntresult, true);
                $cnt = element('cit_qna_count', $jresult);
                echo '<script type="text/javascript">window.opener.view_cmall_qna("viewitemqna", ' . $cit_id . ', ' . $page . ');window.opener.cmall_qna_count_update(' . $cnt . ');</script>';
                alert_close('정상적으로 입력되었습니다.');
            }
        }
    }

    public function ajax_cancel()
    {
        ajax_required_user_login();

        $this->load->model(array('Cmall_order_model', 'Cmall_order_detail_model', 'Cmall_item_detail_model'));

        $cor_id = $this->input->post('cor_id');
        $cod_ids = $this->input->post('cod_ids');

        $this->load->model(array('Cmall_order_model', 'Cmall_item_detail_model', 'Cmall_order_detail_model'));

        $order = $this->Cmall_order_model->get_one($cor_id);

        $total_refunds = 0;
        $total_refund_point = 0;
        foreach ($cod_ids as $cod_id) {
            $orderdetail = $this->Cmall_order_detail_model->get_one($cod_id);
            if ($orderdetail) {
                $itemdetail = $this->Cmall_item_detail_model->get_one($orderdetail['cde_id']);
                if ($order['cor_pg'] == 'paypal') {
                    $total_refunds += floatval($itemdetail['cde_price_d']);
                } else {
                    $total_refunds += floatval($itemdetail['cde_price']);
                }
                $total_refund_point += intval($orderdetail['cod_point']);
                $this->Cmall_order_detail_model->update($cod_id, [
                    'cod_status' => 'cancel'
                ]);
            }
        }

        if ($order) {
            $this->Cmall_order_model->update($cor_id, [
                'cor_status' => 2,
                'cor_refund_price' => $total_refunds - $total_refund_point,
                'cor_refund_point' => $total_refund_point,
                'cor_memo' => $this->input->post('cor_memo'),
                'cor_admin_memo' => $this->input->post('cor_admin_memo'),
                'cor_cancel_datetime' => cdate('Y-m-d H:i:s')
            ]);
        }


        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode([
            'message' => '결제가 취소되었습니다.'
        ]));
    }

    public function ajax_refund_complete()
    {
        ajax_required_user_login();

        $this->load->model('Cmall_order_model');
        $cor_id = $this->input->post('cor_id');

        $this->Cmall_order_model->update($cor_id, [
            'cor_memo' => $this->input->post('cor_memo'),
            'cor_admin_memo' => $this->input->post('cor_admin_memo'),
        ]);

        $this->output->set_content_type('text/json');
        $this->output->set_output(json_encode([
            'message' => 'Success'
        ]));
    }

    public function ajax_salehistory()
    {
        $this->output->set_content_type('text/json');

        ajax_required_user_login();

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $forder = $this->input->get('forder');
        $status = $this->input->get('status');
        $is_download = $this->input->get('is_download');
        if ($status == '') $status = '%%';

        $mem_id = (int)$this->member->item('mem_id');

        $sql_total_money = "SELECT SUM(total_money) as total FROM (SELECT 
                       CASE
                           WHEN co.cor_pg = 'allat' THEN cid.cde_price
                           WHEN co.cor_pg = 'paypal' THEN cid.cde_price_d
                           ELSE cid.cde_price END                               as total_money
                FROM cb_cmall_order_detail AS cod
                         LEFT JOIN cb_cmall_order as co ON co.cor_id = cod.cor_id
                         LEFT JOIN cb_cmall_item as ci ON cod.cit_id = ci.cit_id
                         LEFT JOIN cb_cmall_item_detail as cid ON cid.cde_id = cod.cde_id
                         LEFT JOIN cb_member as m ON m.mem_id = ci.mem_id
                         LEFT JOIN cb_member_group_member as mgm ON m.mem_id = mgm.mem_id
                         LEFT JOIN cb_member_group as mg ON mg.mgr_id = mgm.mgr_id 
                WHERE ci.mem_id = ? AND cod.cod_status=? AND co.cor_datetime >= ? AND co.cor_datetime <=? AND co.cor_pg = ?) as sale_data";

        $sql_count = "SELECT COUNT(*) as rownum
                FROM cb_cmall_order_detail AS cod
                         LEFT JOIN cb_cmall_order as co ON co.cor_id = cod.cor_id
                         LEFT JOIN cb_cmall_item as ci ON cod.cit_id = ci.cit_id
                         LEFT JOIN cb_cmall_item_detail as cid ON cid.cde_id = cod.cde_id
                         LEFT JOIN cb_member as m ON m.mem_id = ci.mem_id
                         LEFT JOIN cb_member_group_member as mgm ON m.mem_id = mgm.mem_id
                         LEFT JOIN cb_member_group as mg ON mg.mgr_id = mgm.mgr_id 
                WHERE ci.mem_id = ? AND cod.cod_status LIKE ? AND co.cor_datetime >= ? AND co.cor_datetime <=?";

        $sql_list = "SELECT cod.cod_id, cod.cor_id, co.cor_datetime,
                       co.mem_nickname                                          as buyer_nickname,
                       m.mem_nickname                                           as seller_nickname,
                       ci.cit_file_1, ci.cit_name, ci.cit_key,
                       cid.cde_title, cid.cde_id,
                       co.cor_pg,
                       CASE
                           WHEN co.cor_pg = 'allat' THEN cid.cde_price
                           WHEN co.cor_pg = 'paypal' THEN cid.cde_price_d
                           ELSE cid.cde_price END                               as total_money,
                       cod.cod_status,
                      DATE_ADD(co.cor_approve_datetime, INTERVAL 60 DAY) as expired_date,
                      DATEDIFF(DATE_ADD(co.cor_approve_datetime, INTERVAL 60 DAY), NOW()) as remain_days
                FROM cb_cmall_order_detail AS cod
                         LEFT JOIN cb_cmall_order as co ON co.cor_id = cod.cor_id
                         LEFT JOIN cb_cmall_item as ci ON cod.cit_id = ci.cit_id
                         LEFT JOIN cb_cmall_item_detail as cid ON cid.cde_id = cod.cde_id
                         LEFT JOIN cb_member as m ON m.mem_id = ci.mem_id
                         LEFT JOIN cb_cmall_download_log cdl ON co.cor_id = cdl.cor_id
                WHERE ci.mem_id = ? AND co.cor_datetime >= ? AND co.cor_datetime <=? AND cod.cod_status LIKE ?
                ORDER BY co.cor_datetime " . $forder;

        $deposit_money = ($this->db->query($sql_total_money, [$mem_id, 'deposit', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'allat'])->row_array())['total'];
        $deposit_money_d = ($this->db->query($sql_total_money, [$mem_id, 'deposit', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'paypal'])->row_array())['total'];

        $order_funds = ($this->db->query($sql_total_money, [$mem_id, 'order', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'allat'])->row_array())['total'];
        $order_funds_d = ($this->db->query($sql_total_money, [$mem_id, 'order', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'paypal'])->row_array())['total'];

        $refunds = ($this->db->query($sql_total_money, [$mem_id, 'cancel', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'allat'])->row_array())['total'];
        $refunds_d = ($this->db->query($sql_total_money, [$mem_id, 'cancel', $start_date . ' 00:00:00', $end_date . ' 23:59:59', 'paypal'])->row_array())['total'];

        $total_rows = ($this->db->query($sql_count, [$mem_id, '%%', $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array())['rownum'];
        $deposit_rows = ($this->db->query($sql_count, [$mem_id, 'deposit', $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array())['rownum'];
        $order_rows = ($this->db->query($sql_count, [$mem_id, 'order', $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array())['rownum'];
        $refund_rows = ($this->db->query($sql_count, [$mem_id, 'cancel', $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array())['rownum'];

        $sp_list = $this->db->query($sql_list, [$mem_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59', $status, ])->result_array();

        $this->output->set_output(json_encode([
            'message' => 'Success',
            'waiting_funds' => $deposit_money,
            'waiting_funds_d' => $deposit_money_d,
            'order_funds' => $order_funds,
            'order_funds_d' => $order_funds_d,
            'refunds' => $refunds,
            'refunds_d' => $refunds_d,
            'total_rows' => $total_rows,
            'deposit_rows' => $deposit_rows,
            'order_rows' => $order_rows,
            'refund_rows' => $refund_rows,
            'sp_list' => $sp_list
        ]));
        return true;
    }
}