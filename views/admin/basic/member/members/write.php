<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message(element('message', $view), '<div class="alert alert-warning">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
        <input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
        <div class="form-group">
            <label class="col-sm-2 control-label">회원등급</label>
            <div class="col-sm-10">
                <?php
                $memUserType = [
                    1 => '일반회원',
                    2 => '판매회원(FREE)',
                    3 => '판매회원(Platinum)',
                    4 => '판매회원(Master)'
                ];
                ?>
                <?php foreach ($memUserType as $key => $val) { ?>
                    <label for="mem_usertype_<?= $key ?>" class="checkbox-inline">
                        <input type="radio" name="mem_usertype" id="mem_usertype_<?= $key ?>" value="<?= $key ?>"
                            <?php echo set_checkbox('mem_usertype', $key, (element('mem_usertype', element('data', $view)) == $key ? true : false)); ?> />
                        <?= $val ?>
                </label>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">유저타입</label>
            <div class="col-sm-10">
                <?php
                $memType = [
                    'Music Lover',
                    'Recording Artist',
                    'Music Producer',
                    'Artist/Producer'
                ];
                ?>
                <?php foreach ($memType as $key => $val) { ?>
                    <label for="mem_type_<?= $key + 1 ?>" class="checkbox-inline">
                        <input type="radio" name="mem_type" id="mem_type_<?= $key + 1 ?>" value="<?= $val ?>"
                            <?php echo set_checkbox('mem_type', $val, (element('mem_type', element('data', $view)) == $val ? true : false)); ?> />
                        <?= $val ?>
                </label>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">회원아이디</label>
            <div class="col-sm-10 form-inline">
                <input type="hidden" name="mem_userid" value="<?php echo set_value('mem_userid', element('mem_userid', element('data', $view))); ?>"/>
                <input type="text" class="form-control" name="mem_id" value="<?php echo set_value('mem_id', element('mem_id', element('data', $view))); ?>" disabled/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">닉네임</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="mem_nickname" value="<?php echo set_value('mem_nickname', element('mem_nickname', element('data', $view))); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">이메일</label>
            <div class="col-sm-8">
                <input type="email" class="form-control" name="mem_email" value="<?php echo set_value('mem_email', element('mem_email', element('data', $view))); ?>" />
            </div>
            <div class="col-sm-2">
                <label for="mem_email_cert" class="checkbox-inline">
                    <input type="checkbox" name="mem_email_cert" id="mem_email_cert" value="1" <?php echo set_checkbox('mem_email_cert', '1', (element('mem_email_cert', element('data', $view)) ? true : false)); ?> /> 인증
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">패스워드</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="mem_password" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">실명</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="mem_firstname" value="<?php echo set_value('mem_firstname', element('mem_firstname', element('data', $view))); ?>" />
                <input type="text" class="form-control" name="mem_lastname" value="<?php echo set_value('mem_lastname', element('mem_lastname', element('data', $view))); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">국가 및 도시</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="mem_address1" value="<?php echo set_value('mem_address1', element('mem_address1', element('data', $view))); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">소셜연동</label>
            <div class="col-sm-10">
                <?php if ($this->cbconfig->item('use_sociallogin_facebook') && element('facebook_id', element('social', element('data', $view)))) { ?>
                    <a href="javascript:;" onClick="social_open('facebook', '<?php echo element('mem_id', element('data', $view)); ?>');"><img src="<?php echo base_url('assets/images/social_facebook.png'); ?>" width="15" height="15" alt="페이스북 로그인" title="페이스북 로그인" /></a>
                <?php } ?>
                <?php if ($this->cbconfig->item('use_sociallogin_twitter') && element('twitter_id', element('social', element('data', $view)))) { ?>
                    <a href="javascript:;" onClick="social_open('twitter', '<?php echo element('mem_id', element('data', $view)); ?>');"><img src="<?php echo base_url('assets/images/social_twitter.png'); ?>" width="15" height="15" alt="트위터 로그인" title="트위터 로그인" /></a>
                <?php } ?>
                <?php if ($this->cbconfig->item('use_sociallogin_google') && element('google_id', element('social', element('data', $view)))) { ?>
                    <a href="javascript:;" onClick="social_open('google', '<?php echo element('mem_id', element('data', $view)); ?>');"><img src="<?php echo base_url('assets/images/social_google.png'); ?>" width="15" height="15" alt="구글 로그인" title="구글 로그인" /></a>
                <?php } ?>
                <?php if ($this->cbconfig->item('use_sociallogin_naver') && element('naver_id', element('social', element('data', $view)))) { ?>
                    <a href="javascript:;" onClick="social_open('naver', '<?php echo element('mem_id', element('data', $view)); ?>');"><img src="<?php echo base_url('assets/images/social_naver.png'); ?>" width="15" height="15" alt="네이버 로그인" title="네이버 로그인" /></a>
                <?php } ?>
                <?php if ($this->cbconfig->item('use_sociallogin_kakao') && element('kakao_id', element('social', element('data', $view)))) { ?>
                    <a href="javascript:;" onClick="social_open('kakao', '<?php echo element('mem_id', element('data', $view)); ?>');"><img src="<?php echo base_url('assets/images/social_kakao.png'); ?>" width="15" height="15" alt="카카오 로그인" title="카카오 로그인" /></a>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">포인트</label>
            <div class="col-sm-10">
                <?php echo element('mem_point', element('data', $view)); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">프로필사진</label>
            <div class="col-sm-10">
                <?php
                if (element('mem_photo', element('data', $view))) {
                    ?>
                    <img src="<?php echo member_photo_url(element('mem_photo', element('data', $view))); ?>" alt="회원 사진" title="회원 사진" />
                    <label for="mem_photo_del">
                        <input type="checkbox" name="mem_photo_del" id="mem_photo_del" value="1" <?php echo set_checkbox('mem_photo_del', '1'); ?> /> 삭제
                    </label>
                    <?php
                }
                ?>
                <input type="file" name="mem_photo" id="mem_photo" />
                <p class="help-block">가로길이 : <?php echo $this->cbconfig->item('member_photo_width'); ?>px, 세로길이 : <?php echo $this->cbconfig->item('member_photo_height'); ?>px 에 최적화되어있습니다, gif, jpg, png 파일 업로드가 가능합니다</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">프로필 문구</label>
            <div class="col-sm-10">
                <textarea class="form-control" rows="5" name="mem_profile_content"><?php echo set_value('mem_profile_content', element('mem_profile_content', element('data', $view))); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">은행</label>
            <div class="col-sm-10">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">계좌번호</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">예금주</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">마케팅 동의</label>
            <div class="col-sm-10">
                메일받기
                <label for="mem_receive_email" class="checkbox-inline">
                    <input type="checkbox" name="mem_receive_email" id="mem_receive_email" value="1" <?php echo set_checkbox('mem_receive_email', '1', (element('mem_receive_email', element('data', $view)) ? true : false)); ?> /> 사용합니다
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                SMS
                <label for="mem_receive_sms" class="checkbox-inline">
                    <input type="checkbox" name="mem_receive_sms" id="mem_receive_sms" value="1" <?php echo set_checkbox('mem_receive_sms', '1', (element('mem_receive_sms', element('data', $view)) ? true : false)); ?> /> 사용합니다
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">관리자용 메모</label>
            <div class="col-sm-10">
                <textarea class="form-control" rows="5" name="mem_adminmemo"><?php echo set_value('mem_adminmemo', element('mem_adminmemo', element('data', $view))); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">승인상태</label>
            <div class="col-sm-10 form-inline">
                <select name="mem_denied" class="form-control">
                    <option value="0" <?php echo set_select('mem_denied', '0', ( ! element('mem_denied', element('data', $view)) ? true : false)); ?>>승인</option>
                    <option value="1" <?php echo set_select('mem_denied', '1', (element('mem_denied', element('data', $view)) ? true : false)); ?>>차단</option>
                </select>
            </div>
        </div>
        <?php if($this->member->item('mem_id') == 1) { ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">최고관리자</label>
                <div class="col-sm-10 form-inline">
                    <select name="mem_is_admin" class="form-control">
                        <option value="0" <?php echo set_select('mem_is_admin', '0', (element('mem_is_admin', element('data', $view)) !== '1' ? true : false)); ?>>아닙니다</option>
                        <option value="1" <?php echo set_select('mem_is_admin', '1', (element('mem_is_admin', element('data', $view)) === '1' ? true : false)); ?>>최고관리자입니다</option>
                    </select>
                </div>
            </div>
        <?php } ?>
        <input type="hidden" name="mem_level" value="<?php echo (int) element('mem_level', element('data', $view)) ?>">
			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
    function social_open(stype, mem_id) {
        var pop_url = cb_admin_url + '/member/members/socialinfo/' + stype + '/' + mem_id;
        window.open(pop_url, 'win_socialinfo', 'left=100,top=100,width=730,height=500,scrollbars=1');
        return false;
    }

$(function() {
	$('#fadminwrite').validate({
		rules: {
			mem_userid: { required: true, minlength:3, maxlength:20 },
			mem_username: {minlength:2, maxlength:20 },
			mem_nickname: {required :true, minlength:2, maxlength:20 },
			mem_email: {required :true, email:true },
			mem_password: {minlength :4 }
		}
	});
});
//]]>
</script>
