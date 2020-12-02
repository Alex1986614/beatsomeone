Beat Someone
============

씨아이보드 기반의 음악 거래 사이트

## 실행방법

* 라이브러리 설치 : npm i
* 개발 : npm run dev
* 빌드 : npm run build

## 씨아이보드 설치시 유의사항
* 테이블 Prefix는 CB로 고정
* Layout은 일반으로 사용

## DB 작업
* SKIN 추가
    *     insert into cb_config values ('layout_beatsomeone','beatsomeone_basic');
    *     insert into cb_config values ('mobile_layout_beatsomeone','beatsomeone_mobile');
    
* 연관음반 테이블 추가
    *       create table cb_cmall_item_relation
            (
                cir_id   int auto_increment
                    primary key,
                cit_id   int null,
                cit_id_r int null
            )
                comment '컨텐츠몰 관련상품';

* 멤버타입 (일반 : 1 / 뮤지션 : 2) 컬럼 추가
    *     alter table cb_member ADD mem_usertype int DEFAULT 1;
    
* 멤버 그룹 추가
    *     delete from cb_member_group where cb_member_group.mgr_id > 1;
    *     insert into cb_member_group(mgr_id,mgr_title,mgr_is_default,mgr_datetime,mgr_order,mgr_description) values (2,'뮤지션그룹',0,now(),2,'');

* 2차 변경데이터
    *       	CREATE VIEW cb_cmall_item_meta_v AS
            	select
            	    i.cit_id,
            	    m1.cim_value AS genre,
            	    m2.cim_value AS bpm,
            	    m3.cim_value AS musician,
            	    m4.cim_value AS subgenre,
            	    m5.cim_value AS moods,
            	    m6.cim_value AS trackType,
            	    m7.cim_value AS hashTag,
            	    m8.cim_value AS voice,
            	    d1.cde_id AS cde_id,
            	    d1.cde_price AS cde_price,
            	    d1.cde_price_d AS cde_price_d,
            	    d1.cde_quantity AS cde_quantity,
            	    d1.cde_download AS cde_download,
            	    d1.cde_originname AS cde_originname,
            	    d2.cde_id AS cde_id_2,
            	    d2.cde_price AS cde_price_2,
            	    d2.cde_price_d AS cde_price_d_2,
            	    d2.cde_quantity AS cde_quantity_2,
            	    d2.cde_download AS cde_download_2,
            	    d2.cde_originname AS cde_originname_2,
            	    d3.cde_id AS cde_id_3,
            	    d3.cde_price AS cde_price_3,
            	    d3.cde_price_d AS cde_price_d_3,
            	    d3.cde_quantity AS cde_quantity_3,
            	    d3.cde_download AS cde_download_3,
            	    d3.cde_originname AS cde_originname_3
            	from cb_cmall_item as i
            	         left outer join cb_cmall_item_meta as m1 on m1.cit_id = i.cit_id and m1.cim_key = 'info_content_1'
            	         left outer join cb_cmall_item_meta as m2 on m2.cit_id = i.cit_id and m2.cim_key = 'info_content_2'
            	         left outer join cb_cmall_item_meta as m3 on m3.cit_id = i.cit_id and m3.cim_key = 'info_content_3'
            	         left outer join cb_cmall_item_meta as m4 on m4.cit_id = i.cit_id and m4.cim_key = 'info_content_4'
            	         left outer join cb_cmall_item_meta as m5 on m5.cit_id = i.cit_id and m5.cim_key = 'info_content_5'
            	         left outer join cb_cmall_item_meta as m6 on m6.cit_id = i.cit_id and m6.cim_key = 'info_content_6'
            	         left outer join cb_cmall_item_meta as m7 on m7.cit_id = i.cit_id and m7.cim_key = 'info_content_7'
            	         left outer join cb_cmall_item_meta as m8 on m8.cit_id = i.cit_id and m8.cim_key = 'info_content_8'
            	         left outer join cb_cmall_item_detail as d1 on d1.cit_id = i.cit_id and d1.cde_title = 'LEASE'
            	         left outer join cb_cmall_item_detail as d2 on d2.cit_id = i.cit_id and d2.cde_title = 'STEM'
            	         left outer join cb_cmall_item_detail as d3 on d3.cit_id = i.cit_id and d3.cde_title = 'TAGGED'

    *           	create table cb_bs_register_plan_cost
                	(
                		plan varchar(100) not null
                			primary key,
                		monthly decimal(7,3) default 0.000 null,
                		monthly_d decimal(7,3) default 0.000 null,
                		yearly decimal(7,3) default 0.000 null,
                		yearly_d decimal(7,3) default 0.000 null,
                		yearly_discount_pc decimal(7,3) default 0.000 null,
                		yearly_discount_amt decimal(7,3) default 0.000 null,
                		yearly_discount_amt_d decimal(7,3) default 0.000 null
                	)
                	comment 'BS 유료 플랜 가격 테이블';
    
    *       	alter table cb_cmall_item add cit_start_datetime datetime null;
            	alter table cb_cmall_item add cit_share_count int default 0 null;
                alter table cb_cmall_item_detail add cde_price_d decimal(7,2) default 0.0 null;
                alter table cb_cmall_item_detail add cde_quantity int default 0 null;
                INSERT INTO beatsomeone.cb_bs_register_plan_cost (plan, monthly, monthly_d, yearly, yearly_d, yearly_discount_pc, yearly_discount_amt, yearly_discount_amt_d) VALUES ('MARKETPLACE', 0.000, 9.990, 0.000, 95.880, 20.000, 0.000, 24.000);
                INSERT INTO beatsomeone.cb_bs_register_plan_cost (plan, monthly, monthly_d, yearly, yearly_d, yearly_discount_pc, yearly_discount_amt, yearly_discount_amt_d) VALUES ('PRO PAGE', 0.000, 19.990, 0.000, 179.880, 25.000, 0.000, 24.000);

* 3차 변경 데이터               
    *           create table cb_cmall_item_show_history
                (
                    ish_id int auto_increment,
                    cit_id int not null,
                    mem_id int not null,
                    show_dt datetime null,
                    constraint cb_cmall_item_show_history_pk
                        primary key (ish_id)
                )
                comment '조회 이력';

* 4차 변경 데이터 // 2020-12-02
	*		alter table cb_member_group ADD mgr_monthly_upload_limit int;
	*		alter table cb_member_group ADD mgr_year_upload_limit int;
	*		alter table cb_member_group ADD mgr_monthly_msg_limit int;
	*		alter table cb_member_group ADD mgr_year_msg_limit int;
       
## 환경설정
* 뮤지션 회원 추가정보 (계좌)
    * 환경설정 - 회원가입설정 - 가입폼 관리 (추가)
        * mem_musician_bank
            * 은행
          	* 단일선택(select)
          	    *     국민
                      신한
          		      우리
          		      농협
          		      카카오뱅크
                      케이뱅크
                      
        * mem_musician_account
            * 계좌번호
            * 한줄입력형식
            
        * mem_musician_account_nm
            * 예금주
            * 한줄입력형식
            
      
## 설정파일
* /vue.config.js
    * vuecli webpack 환경설정
    * 페이지 추가될 경우 configureWebpack 의 entry 에 추가
    
* /package.json
    * eslintConfig 에 eslint 변경사항 적용

## 실서버 배포시
* SQL Log hook 제거
    * hooks/Log_queries.php 에 SQL Log 저장 부분을 비활성화 
