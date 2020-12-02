<?php $this->managelayout->add_css('/dist/admin_cmallitem_write.css'); ?>
<?php $this->managelayout->add_js('/dist/chunk-vendors.js'); ?>
<?php $this->managelayout->add_js('/dist/chunk-common.js'); ?>
<?php $this->managelayout->add_js('/dist/admin_cmallitem_write.js'); ?>
<?php $this->managelayout->add_script('window.vm.$children[0].$data.cit_id = "' . element('cit_id', element('data', $view)) . '";'); ?>

<div class="box">
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open_multipart(current_full_url(), $attributes);
        ?>
        <input type="hidden" name="<?php echo element('primary_key', $view); ?>" value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>"/>
        <div class="box-table-header">
            <h4><a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2">기본정보</a></h4>
            <a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2"><i class="fa fa-chevron-up pull-right"></i></a>
        </div>
        <div class="collapse in" id="cmalltab2">
            <div class="form-group">
                <label class="col-sm-2 control-label">음원상세 주소</label>
                <div class="col-sm-10 form-inline">
                    <?php echo cmall_item_url(); ?> <input type="text" class="form-control" name="cit_key" value="<?php echo set_value('cit_key', element('cit_key', element('data', $view))); ?>"/> 페이지주소를 입력해주세요
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">판매자 정보</label>
                <div class="col-sm-10 form-inline">
                    <input type="hidden" name="seller_mem_userid" value="<?php echo set_value('seller_mem_userid', element('seller_mem_userid', element('data', $view))); ?>"/>
                    <?php
                    echo element('seller_mem_userid', element('data', $view)) . ' / ';
                    echo element('mem_email', element('sellerInfo', element('data', $view))) . ' / ';
                    echo element('mem_username', element('sellerInfo', element('data', $view)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">트랙명</label>
                <div class="col-sm-10 form-inline">
                    <input type="text" class="form-control" name="cit_name" value="<?php echo set_value('cit_name', element('cit_name', element('data', $view))); ?>"/>
                </div>
            </div>
            <input type="hidden" class="form-control" name="cit_order" value="<?php echo set_value('cit_order', element('cit_order', element('data', $view))) ?? 0; ?>"/>

            <div class="form-group">
                <label class="col-sm-2 control-label">태그</label>
                <div class="col-sm-10 form-inline">
                    <input type="text" class="form-control" name="info_content_7" value="<?php echo set_value('info_content_7', element('info_content_7', element('data', $view))); ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">트랙유형</label>
                <div class="col-sm-10 form-inline">
                    <select name="info_content_6" class="form-control">
                        <?php
                        $list = [
                            'Beats',
                            'Beats with chorus',
                            'Vocals',
                            'Song reference',
                            'Songs'
                        ];
                        for ($i = 0; $i < sizeof($list); $i++) {
                            $checked = element('info_content_6', element('data', $view)) == $list[$i];
                            ?>
                            <option value="<?php echo $list[$i] ?>" <?php echo $checked == 1 ? 'selected' : '' ?>><?php echo $list[$i] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">보이스 여부</label>
                <div class="col-sm-10 form-inline">
                    <input type="checkbox" name="info_content_8" value="1" <?php echo set_checkbox('info_content_8', '1', (element('info_content_8', element('data', $view)) ? true : false)); ?> />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">유통 개시일자</label>
                <div class="col-sm-10 form-inline">
                    <input type="text" class="form-control" name="cit_start_datetime" value="<?php echo set_value('cit_start_datetime', element('cit_start_datetime', element('data', $view))); ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">판매상태</label>
                <div class="col-sm-10">
                    <label for="cit_status" class="checkbox-inline">
                        <input type="checkbox" name="cit_status" id="cit_status" value="1" <?php echo set_checkbox('cit_status', '1', (element('cit_status', element('data', $view)) ? true : false)); ?> /> 판매합니다
                    </label>
                    <div class="help-inline">체크를 해제하시면 상품 리스트에서 사라지며, 구매할 수 없습니다.</div>
                </div>
            </div>
            <div class="collapse in" id="cmalltab6">
                <div class="form-group">
                    <label class="col-sm-2 control-label">트랙 표지 이미지</label>
                    <div class="col-sm-10 form-inline">
                        <?php
                        if (element('cit_file_1', element('data', $view))) {
                            ?>
                            <a href="/uploads/cmallitem/<?php echo element('cit_file_1', element('data', $view)); ?>" target="_blank">
                                <img src="<?php echo thumb_url('cmallitem', element('cit_file_1', element('data', $view)), 80); ?>"
                                     alt="<?php echo isset($detail) ? html_escape(element('cde_title', $detail)) : ''; ?>"
                                     title="<?php echo isset($detail) ? html_escape(element('cde_title', $detail)) : ''; ?>"/>
                            </a>
                            <label for="cit_file_1_del">
                                <input type="checkbox" name="cit_file_1_del" id="cit_file_1_del" value="1" <?php echo set_checkbox('cit_file_1' . '_del', '1'); ?> /> 삭제
                            </label>
                            <?php
                        }
                        ?>
                        <input type="file" name="cit_file_1" id="cit_file_1"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-table-header">
            <h4><a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3">음원 및 옵션관리</a></h4>
            <a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3"><i class="fa fa-chevron-up pull-right"></i></a>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">음원설정</label>
            <div class="col-sm-10">
                <label>
                    <input type="checkbox" name="cit_freebeat" id="cit_freebeat" value="1"
                        <?php echo set_checkbox('cit_freebeat', '1', (element('cit_freebeat', element('data', $view)) ? true : false)); ?> />
                    무료비트
                </label>
                <label>
                    <input type="checkbox" name="cit_include_copyright_transfer" id="cit_include_copyright_transfer" value="1"
                        <?php echo set_checkbox('cit_include_copyright_transfer', '1', (element('cit_include_copyright_transfer', element('data', $view)) ? true : false)); ?> />
                    저작권 양도 포함
                </label>
                <label>
                    <input type="checkbox" name="cit_officially_registered" id="cit_officially_registered" value="1"
                        <?php echo set_checkbox('cit_officially_registered', '1', (element('cit_officially_registered', element('data', $view)) ? true : false)); ?> />
                    저작권 등록된 음원
                </label>
                <label>
                    <input type="checkbox" name="cit_org_content" id="cit_org_content" value="1"
                        <?php echo set_checkbox('cit_org_content', '1', (element('cit_org_content', element('data', $view)) ? true : false)); ?> />
                    오리지널 콘텐츠
                </label>
            </div>
            <label class="col-sm-2 control-label">유통형태</label>
            <div class="col-sm-10">
                <label>
                    <input type="checkbox" name="cit_lease_license_use" id="cit_lease_license_use" value="1"
                        <?php echo set_checkbox('cit_lease_license_use', '1', (element('cit_lease_license_use', element('data', $view)) ? true : false)); ?> />
                    임대
                </label>
                <label>
                    <input type="checkbox" name="cit_mastering_license_use" id="cit_mastering_license_use" value="1"
                        <?php echo set_checkbox('cit_mastering_license_use', '1', (element('cit_mastering_license_use', element('data', $view)) ? true : false)); ?> />
                    판매
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">음원파일</label>
            <div class="col-sm-10">
                <div class="collapse in" id="cmalltab5">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>파일구분 <a href="javascript:;" class="btn btn-xs btn-danger" onClick="add_option();">옵션추가</a></th>
                                <th>첨부파일</th>
                                <th>KRW금액</th>
                                <th>USD금액</th>
                                <th>판매수량</th>
                                <th>사용여부</th>
                            </tr>
                            </thead>
                            <tbody id="item_option_wrap">
                            <?php
                            if (element('item_detail', element('data', $view))) {
                                foreach (element('item_detail', element('data', $view)) as $detail) {
                                    ?>
                                    <tr>
                                        <td>
                                            <select name="cde_title_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" class="form-control">
                                                <?php
                                                $list = array('LEASE', 'STEM', 'TAGGED', 'PREVIEW');
                                                for ($i = 0; $i < sizeof($list); $i++) {
                                                    $checked = element('cde_title', $detail) == $list[$i];
                                                    ?>
                                                    <option value="<?php echo $list[$i] ?>" <?php echo $checked == 1 ? 'selected' : '' ?>><?php echo $list[$i] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <!--                                    <input type="text" class="form-control" name="cde_title_update[--><?php //echo html_escape(element('cde_id', $detail)); ?><!--]" value="--><?php //echo html_escape(element('cde_title', $detail)); ?><!--" />-->
                                        </td>
                                        <td class="form-inline">
                                            <input type="file" class="form-control" name="cde_file_update[<?php echo html_escape(element('cde_id', $detail)); ?>]"/>
                                            <?php if (element('cde_filename', $detail)) { ?>
                                                <a href="<?php echo admin_url('cmall/itemdownload/download/' . element('cde_id', $detail)); ?>" class="ct_file_name"><?php echo html_escape(element('cde_originname', $detail)); ?></a>
                                            <?php } ?>
                                        </td>
                                        <td><input type="number" class="form-control" name="cde_price_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="<?php echo (int)element('cde_price', $detail); ?>"/>원</td>
                                        <td>$<input type="number" step="0.01" class="form-control" name="cde_price_d_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="<?php echo (float)element('cde_price_d', $detail); ?>"/></td>
                                        <td><input type="number" class="form-control" name="cde_quantity_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="<?php echo (int)element('cde_quantity', $detail); ?>"/>개</td>
                                        <td><input type="checkbox" name="cde_status_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="1" <?php echo (element('cde_status', $detail)) ? ' checked="checked" ' : ''; ?> /></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-table-header">
            <h4><a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3">트랙 정보</a></h4>
            <a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3"><i class="fa fa-chevron-up pull-right"></i></a>
        </div>
        <?php
        $genreList = [
            'Hip hop',
            'K-pop',
            'Pop',
            'R&B',
            'Dance',
            'Rock',
            'Electronic',
            'Jazz',
            'Acoustic',
            'Indie',
            'Reggae',
            'World',
            'Free Beats',
        ];
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">주 장르</label>
            <div class="col-sm-10">
                <select name="info_content_1" class="form-control">
                    <?php
                    for ($i = 0; $i < sizeof($genreList); $i++) {
                        $checked = element('info_content_1', element('data', $view)) == $genreList[$i];
                        ?>
                        <option value="<?php echo $genreList[$i] ?>" <?php echo $checked == 1 ? 'selected' : '' ?>><?php echo $genreList[$i] ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">보조장르</label>
            <div class="col-sm-10">
                <select name="info_content_4" class="form-control">
                    <?php
                    for ($i = 0; $i < sizeof($genreList); $i++) {
                        $checked = element('info_content_4', element('data', $view)) == $genreList[$i];
                        ?>
                        <option value="<?php echo $genreList[$i] ?>" <?php echo $checked == 1 ? 'selected' : '' ?>><?php echo $genreList[$i] ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">무드</label>
            <div class="col-sm-10">
                <select name="info_content_5" class="form-control">
                    <?php
                    $moodsList = [
                        'Angry',
                        'Annoyed',
                        'Anxious',
                        'Bouncy',
                        'Calm',
                        'Chill',
                        'Confident',
                        'Crazy',
                        'Dark',
                        'Depressed',
                        'Dirty',
                        'Dope',
                        'Energetic',
                        'Enraged',
                        'Evil',
                        'Giddy',
                        'Gloomy',
                        'Groovy',
                        'Happy',
                        'Hyper',
                        'Kitsch',
                        'Lazy',
                        'Lo-fi',
                        'Lonely',
                        'Loved',
                        'Majestic',
                        'Mellow',
                        'Peaceful',
                        'Rebellious',
                        'Relaxed',
                        'Sad',
                        'Sensual',
                        'Scared',
                        'Soulful',
                    ];
                    for ($i = 0; $i < sizeof($moodsList); $i++) {
                        $checked = element('info_content_5', element('data', $view)) == $moodsList[$i];
                        ?>
                        <option value="<?php echo $moodsList[$i] ?>" <?php echo $checked == 1 ? 'selected' : '' ?>><?php echo $moodsList[$i] ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">BPM</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="info_content_2" value="<?php echo set_value('info_content_2', element('info_content_2', element('data', $view))); ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">트랙설명</label>
            <div class="col-sm-10">
                <?php echo display_dhtml_editor('cit_content', set_value('cit_content', element('cit_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_cmall_product_dhtml'), $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
            </div>
        </div>


        <div class="collapse in" id="cmalltab3">
            <input type="hidden" class="form-control" name="cit_price" value="<?php echo set_value('cit_price', element('cit_price', element('data', $view))) ?? 0; ?>"/>
            <div class="form-group">
                <label class="col-sm-2 control-label">상품유형</label>
                <div class="col-sm-10">
                    <label for="cit_type1" class="checkbox-inline">
                        <input type="checkbox" name="cit_type1" id="cit_type1" value="1" <?php echo set_checkbox('cit_type1', '1', (element('cit_type1', element('data', $view)) ? true : false)); ?> /> 추천
                    </label>
                    <label for="cit_type2" class="checkbox-inline">
                        <input type="checkbox" name="cit_type2" id="cit_type2" value="1" <?php echo set_checkbox('cit_type2', '1', (element('cit_type2', element('data', $view)) ? true : false)); ?> /> 인기
                    </label>
                    <label for="cit_type3" class="checkbox-inline">
                        <input type="checkbox" name="cit_type3" id="cit_type3" value="1" <?php echo set_checkbox('cit_type3', '1', (element('cit_type3', element('data', $view)) ? true : false)); ?> /> 신상품
                    </label>
                    <label for="cit_type4" class="checkbox-inline">
                        <input type="checkbox" name="cit_type4" id="cit_type4" value="1" <?php echo set_checkbox('cit_type4', '1', (element('cit_type4', element('data', $view)) ? true : false)); ?> /> 할인
                    </label>
                    <label for="cit_type5" class="checkbox-inline">
                        <input type="checkbox" name="cit_type5" id="cit_type5" value="1" <?php echo set_checkbox('cit_type5', '1', (element('cit_type5', element('data', $view)) ? true : false)); ?> /> 구독
                    </label>
                    <div class="help-inline">체크하시면, 메인페이지에 각 카테고리에 출력됩니다</div>
                </div>
            </div>
            <input type="hidden" class="form-control" name="cit_download_days" value="<?php echo set_value('cit_download_days', (int)element('cit_download_days', element('data', $view))); ?>"/>
        </div>
        <div class="box-table-header">
            <h4><a data-toggle="collapse" href="#cmalltab7" aria-expanded="true" aria-controls="cmalltab7">관련상품</a></h4>
            <a data-toggle="collapse" href="#cmalltab7" aria-expanded="true" aria-controls="cmalltab7"><i class="fa fa-chevron-up pull-right"></i></a>
        </div>
        <?php if (element('cit_id', element('data', $view))) { ?>
        <div class="collapse in" id="cmalltab7">
            <div id="app">

            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <colgroup>
                        <col style="width: 110px;">
                        <col style="width: 130px;">
                        <col>
                        <col style="width: 150px;">
                    </colgroup>
                    <thead>
                    <tr>
                        <th></th>
                        <th>상품ID</th>
                        <th>상품명</th>
                        <th>기능</th>
                    </tr>
                    </thead>
                    <tbody id="item_option_wrap">
                    <?php
                    if (element('relation', element('data', $view))) {
                        foreach (element('relation', element('data', $view)) as $detail) {
                            ?>
                            <tr>
                                <td>
                                    <img class="thumbnail mg0" style="width:80px;" src="/uploads/cmallitem/<?php echo element('img', $detail) ?>">
                                </td>
                                <td>
                                    <?php echo html_escape(element('cit_id_r', $detail)); ?>
                                </td>
                                <td>
                                    <?php echo html_escape(element('cit_name', $detail)); ?>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-xs btn-danger" onClick="remove_relation(<?php echo html_escape(element('cir_id', $detail)) ?>)">삭제</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <!--                    --><?php //echo print_r(element('data',$view),true) ?>
            </div>
            <?php } else { ?>
                <div class="collapse in" id="cmalltab7">
                    관련상품은 신규 저장한 다음 추가할 수 있습니다
                </div>
            <?php } ?>

            <script type="text/javascript">
                //<![CDATA[
                function add_option() {
                    //$('#item_option_wrap').append('<tr><td><input type="text" class="form-control" name="cde_title[]" value="" /></td><td class="form-inline"><input type="file" class="form-control" name="cde_file[]" /></td><td><input type="number" class="form-control" name="cde_price[]" value="0" />원</td><td><input type="checkbox" name="cde_status[]" value="1" checked="checked" /></td></tr>');
                    $('#item_option_wrap').append('<tr><td><select class="form-control" name="cde_title[]"><option value="LEASE">LEASE</option><option value="STEM">STEM</option><option value="TAGGED">TAGGED</option><option value="PREVIEW">PREVIEW</option></select></td><td class="form-inline"><input type="file" class="form-control" name="cde_file[]" /></td><td><input type="number" class="form-control" name="cde_price[]" value="0" />원</td><td><input type="checkbox" name="cde_status[]" value="1" checked="checked" /></td></tr>');
                }

                function add_relation() {
                    console.log('CIT_ID : ' + $('#t_cit_id').val());

                    if (!$('#t_cit_id').val()) return;

                    var jqxhr = $.ajax({
                        method: 'POST',
                        url: "/admin/cmall/cmallitem/ajax_add_relation",
                        data: $.param({cit_id: '<?php echo element('cit_id', element('data', $view)) ?>', cit_id_r: $('#t_cit_id').val()})

                    })
                        .done(function () {
                            window.location.reload();
                        })
                        .fail(function () {
                            alert("등록 중 오류가 발생 하였습니다");
                        });

                }

                function remove_relation(rid) {
                    var jqxhr = $.ajax({
                        method: 'POST',
                        url: "/admin/cmall/cmallitem/ajax_remove_relation",
                        data: $.param({cir_id: rid})

                    })
                        .done(function () {
                            window.location.reload();
                        })
                        .fail(function () {
                            alert("삭제 중 오류가 발생 하였습니다");
                        });
                }

                //]]>
            </script>
        </div>
        <div class="btn-group pull-right" role="group" aria-label="...">
            <button type="button" class="btn btn-default btn-sm" onclick="location.href='/admin/cmall/cmallitem?<?= 'sfield=' . $this->input->get('sfield') . '&skeyword=' . $this->input->get('skeyword') . '&page=' . $this->input->get('page') ?>';">목록으로</button>
            <button type="submit" class="btn btn-success btn-sm">저장하기</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    //<![CDATA[
    jQuery(function ($) {
        $('#fadminwrite').validate({
            rules: {
                cit_key: {required: true, minlength: 3, maxlength: 50, alpha_dash: true},
                cit_name: 'required',
                // cit_order: 'required',
                // cit_price: { required:true, number:true },
			cit_content : {<?php echo ($this->cbconfig->item('use_cmall_product_dhtml')) ? 'required_' . $this->cbconfig->item('cmall_product_editor_type') : 'required'; ?> : true },
			cit_mobile_content : {<?php echo ($this->cbconfig->item('use_cmall_product_dhtml')) ? 'valid_' . $this->cbconfig->item('cmall_product_editor_type') : ''; ?> : true },
			header_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			footer_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			mobile_header_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			mobile_footer_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true }
    },
        submitHandler: function (form) {

            // 옵션 입력 체크
            var option_count = 0;
            var option_file = 0;
            var io_price = 0;
            var max_io_price = 0;
            var is_price_chk = false;

            $("select[name^=cde_title]").each(function (index) {

                if ($.trim($(this).val()).length > 0) {
                    option_count++;
                    is_price_chk = false;

                    if (!form.cit_id.value) {
                        if ($.trim($("input[name^=cde_file]").eq(index).val()).length > 0) {
                            option_file++;
                            is_price_chk = true;
                        }
                    } else {
                        if ($(".ct_file_name").eq(index).length > 0) {
                            option_file++;
                            is_price_chk = true;
                        } else {
                            if ($.trim($("input[name^=cde_file]").eq(index).val()).length > 0) {
                                option_file++;
                                is_price_chk = true;
                            }
                        }
                    }

                    if (is_price_chk) {
                        io_price = parseInt($.trim($("input[name^=cde_price_update]").eq(index).val()));
                        if (max_io_price < io_price)
                            max_io_price = io_price;
                    }
                }
            });

            if (option_count == 0) {
                alert("상품옵션을 하나이상 입력해 주십시오.");
                $("#cmalltab5").focus();
                return false;
            }

            if (option_count > 0 && (option_file == 0 || option_count > option_file)) {
                alert("입력하신 상품옵션의 파일을 업로드해 주십시오.");
                $("#cmalltab5").focus();
                return false;
            }

            form.submit();
        }
    });
});
    //]]>
</script>
