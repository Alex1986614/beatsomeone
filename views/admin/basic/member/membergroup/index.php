<style>
    .row {
        display: flex;
    }
    .align-items-center {
        align-items: center !important;
    }
</style>
<div class="box">
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'flist', 'id' => 'flist');
        echo form_open(current_full_url(), $attributes);
        ?>
        <input type="hidden" name="s" value="1"/>
        <div class="box-table-header">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <button type="submit" class="btn btn-outline btn-danger btn-sm">저장하기</button>
            </div>
        </div>
        <div class="row"><?php echo element('total_rows', element('data', $view), 0); ?>개의 그룹이 존재합니다</div>
        <div class="list-group">
            <div class="form-group list-group-item">
                <div class="col-sm-1">순서변경</div>
                <div class="col-sm-3">그룹명</div>
                <div class="col-sm-1">월금액</div>
                <div class="col-sm-1">연금액</div>
                <div class="col-sm-1">할인율 & 수수료</div>
                <div class="col-sm-1">업로드 트랙 제한</div>
                <div class="col-sm-1">메시지 채팅 기능</div>
                <div class="col-sm-1">기본그룹</div>
                <div class="col-sm-1">회원수</div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-outline btn-primary btn-xs btn-add-rows">추가</button>
                </div>
            </div>
            <div id="sortable">
                <?php
                if (element('list', element('data', $view))) {
                    foreach (element('list', element('data', $view)) as $result) {
                        ?>
                        <div class="form-group list-group-item row align-items-center">
                            <div class="col-sm-1">
                                <div class="fa fa-arrows" style="cursor:pointer;"></div>
                                <input type="hidden" name="mgr_id[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo element('mgr_id', $result); ?>"/>
                            </div>
                            <div class="col-sm-3">
                                <label>그룹명</label>
                                <input type="text" class="form-control"
                                       name="mgr_title[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_title', $result)); ?>"/>
                                <label>설명</label>
                                <input type="text" class="form-control"
                                       name="mgr_description[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_description', $result)); ?>"/>
                            </div>
                            <div class="col-sm-1">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="mgr_monthly_cost_d[<?php echo element('mgr_id', $result); ?>]"
                                           value="<?php echo html_escape(element('mgr_monthly_cost_d', $result)); ?>"/>
                                    <span class="input-group-addon">$</span>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="mgr_monthly_cost_w[<?php echo element('mgr_id', $result); ?>]"
                                           value="<?php echo html_escape(element('mgr_monthly_cost_w', $result)); ?>"/>
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="mgr_year_cost_d[<?php echo element('mgr_id', $result); ?>]"
                                           value="<?php echo html_escape(element('mgr_year_cost_d', $result)); ?>"/>
                                    <span class="input-group-addon">$</span>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="mgr_year_cost_w[<?php echo element('mgr_id', $result); ?>]"
                                           value="<?php echo html_escape(element('mgr_year_cost_w', $result)); ?>"/>
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <label>월 할인율</label>
                                <input type="text" class="form-control"
                                       name="mgr_monthly_discount[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_monthly_discount', $result)); ?>"/>
                                <label>연 할인율</label>
                                <input type="text" class="form-control"
                                       name="mgr_year_discount[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_year_discount', $result)); ?>"/>
                                <label>수수료</label>
                                <input type="text" class="form-control"
                                       name="mgr_commission[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_commission', $result)); ?>"/>
                            </div>

                            <div class="col-sm-1">
                                <label>월 트랙개수</label>
                                <input type="text" class="form-control"
                                       name="mgr_monthly_upload_limit[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_monthly_upload_limit', $result)); ?>"/>
                                <label>연 트랙개수</label>
                                <input type="text" class="form-control"
                                       name="mgr_year_upload_limit[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_year_upload_limit', $result)); ?>"/>
                            </div>

                            <div class="col-sm-1">
                                <label>월 메시지</label>
                                <input type="text" class="form-control"
                                       name="mgr_monthly_msg_limit[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_monthly_msg_limit', $result)); ?>"/>
                                <label>연 메시지</label>
                                <input type="text" class="form-control"
                                       name="mgr_year_msg_limit[<?php echo element('mgr_id', $result); ?>]"
                                       value="<?php echo html_escape(element('mgr_year_msg_limit', $result)); ?>"/>
                            </div>

                            <div class="col-sm-1"><input type="checkbox"
                                                         name="mgr_is_default[<?php echo element('mgr_id', $result); ?>]"
                                                         value="1" <?php echo element('mgr_is_default', $result) ? ' checked="checked" ' : ''; ?> />
                            </div>
                            <div class="col-sm-1"><?php echo element('member_count', $result); ?></div>
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-outline btn-default btn-xs btn-delete-row">삭제
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="row">기본그룹에 체크하시면, 회원가입시 해당 그룹에 자동으로 추가됩니다. 그룹삭제시, 복구가 불가하므로 주의하여 주시기 바랍니다.</div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script type="text/javascript">
    //<![CDATA[
    $(document).on('click', '.btn-add-rows', function () {
        $('#sortable').append(
            '<div class="form-group list-group-item row align-items-center">' +
                '<div class="col-sm-1"><div class="fa fa-arrows" style="cursor:pointer;"></div><input type="hidden" name="mgr_id[]" /></div>' +
                '<div class="col-sm-3">' +
                    '<label>그룹명</label><input type="text" class="form-control" name="mgr_title[]"/>' +
                    '<label>설명</label><input type="text" class="form-control" name="mgr_description[]"/>' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<div class="input-group">' +
                        '<input type="text" class="form-control" name="mgr_monthly_cost_d[]"/>' +
                        '<span class="input-group-addon">$</span>' +
                    '</div>' +
                    '<div class="input-group">' +
                        '<input type="text" class="form-control" name="mgr_monthly_cost_w[]"/>' +
                        '<span class="input-group-addon">원</span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<div class="input-group">' +
                        '<input type="text" class="form-control" name="mgr_year_cost_d[]"/>' +
                        '<span class="input-group-addon">$</span>' +
                    '</div>' +
                    '<div class="input-group">' +
                        '<input type="text" class="form-control" name="mgr_year_cost_w[]"/>' +
                        '<span class="input-group-addon">원</span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<label>월 할인율</label>' +
                    '<input type="text" class="form-control" name="mgr_monthly_discount[]" />' +
                    '<label>연 할일율</label>' +
                    '<input type="text" class="form-control" name="mgr_year_discount[]" />' +
                    '<label>수수료</label>' +
                    '<input type="text" class="form-control" name="mgr_commission[]" />' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<label>월 트랙개수</label>' +
                    '<input type="text" class="form-control" name="mgr_monthly_upload_limit[]" />' +
                    '<label>연 트랙개수</label>' +
                    '<input type="text" class="form-control" name="mgr_year_upload_limit[]" />' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<label>월 메시지</label>' +
                    '<input type="text" class="form-control" name="mgr_monthly_msg_limit[]" />' +
                    '<label>연 메시지</label>' +
                    '<input type="text" class="form-control" name="mgr_year_msg_limit[]" />' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<input type="checkbox" name="mgr_is_default[]" value="1" />' +
                '</div>' +
                '<div class="col-sm-1"></div>' +
                '<div class="col-sm-1">' +
                    '<button type="button" class="btn btn-outline btn-default btn-xs btn-delete-row" >삭제</button>' +
                '</div>' +
            '</div>');
    });
    $(document).on('click', '.btn-delete-row', function () {
        $(this).parents('div.list-group-item').remove();
    });
    $(function () {
        $('#sortable').sortable({
            handle: '.fa-arrows'
        });
    })
    //]]>
</script>
