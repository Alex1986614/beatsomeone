<?php

defined('BASEPATH') or exit('No direct script access allowed');

use maximal\audio\Waveform;

class Waveformlib
{
    private $CI;

    /**
     * This is a static class, do not instantiate it
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * 웨이브폼 생성
     */
    public function getWaveform($cit_id = 0, $width = 200)
    {
        $cit_id = (int)$cit_id;
        if (empty($cit_id)) {
            return null;
        }

        $this->CI->load->model(array('Cmall_item_detail_model'));
        $file = $this->CI->Cmall_item_detail_model->preview_by_cit_id($cit_id);
        if (empty(element('cde_id', $file))) {
            return null;
        }

        $waveform = new Waveform(config_item('uploads_dir') . '/cmallitemdetail/' . element('cde_filename', $file));
        $data = $waveform->getWaveformData($width);

        return json_encode($data['lines1']);
    }

    /**
     * 웨이브폼 등록
     * @param int $cit_id
     * @param int $width
     */
    public function setWaveform($cit_id = 0, $width = 200)
    {
        $this->CI->load->model(array('Cmall_item_model'));
        $waveform = $this->getWaveform($cit_id, $width);
        $this->CI->Cmall_item_model->update_waveform($cit_id, $waveform);
    }
}
