<template>
    <div>
        <h5 class="mb-4">{{$t('chat')}}</h5>
        <div class="row">
            <div class="col">
                <div class="row pb-2 align-items-end" style="height: 65px;">
                    <div class="col-auto">
                        <v-select :clearable="false" :searchable="false" v-model="dateType" :options="dateTypes"></v-select>
                    </div>
                    <!--            <div class="col-10">-->
                    <!--                <div class="sort">-->
                    <!--                    <div class="input_wrap line" >-->
                    <!--                        <input type="text" :placeholder="$t('enterYourSearchword')" :value="searchUser" @input="searchUser=$event.target.value" @keypress.enter="goSearchUser">-->
                    <!--                        <button @click="goSearchUser">-->
                    <!--                            <img src="/assets/images/icon/searchicon.png">-->
                    <!--                        </button>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                </div>
                <ul class="list-group">
                    <li v-for="(m, i) in messageList" v-bind:key="i" class="list-group-item" :class="mid == m.mem_id ? 'active' : ''" @click="goMChat($event, m)">
                        <div class="row">
                            <div class="col-auto">
                                <div class="mem-icon">
                                    <img v-if="isEmpty(m.mem_photo)" src="/assets/images/portait.png"/>
                                    <img v-else :src="'/uploads/member_photo/' + m.mem_photo" alt="">
                                </div>
                            </div>
                            <div class="col">
                                <h5>{{ m.mem_nickname }}</h5>
                                <p class="text-secondary">{{ formatConName(m.nte_content, 50) }}</p>
                            </div>
                            <div class="col text-right">
                                <p class="text-secondary">{{ m.nte_datetime }}</p>
                                <span v-show="m.unread != 0" class="badge badge-warning">{{ m.unread }}</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col" :style="'display: '+mchat">
                <div class="row" style="height: 65px;">
                    <div class="col-auto">
                        <div class="mem-icon">
                            <img v-if="isEmpty(mchatUserPhoto)" src="/assets/images/portait.png"/>
                            <img v-else :src="'/uploads/member_photo/' + mchatUserPhoto" alt="">
                        </div>
                    </div>
                    <div class="col">
                        <h5>{{ mchatUser }}</h5>
                        <p class="text-muted">{{ mchatUserBio }}</p>
                    </div>
                </div>
                <div class="bg-secondary mb-3" id="messageDisplay">
                    <!-- 스크롤 박스위치 -->
                    <div id="message-list" style="overflow-y: scroll;">
                        <div v-for="(m, i) in messageDetail" v-bind:key="m.nte_id" class="chatBalloon" :class="mem_id === m.send_mem_id ? 'me' : ''">
                            <div>{{m.nte_content}}
                                <button v-if="m.nte_filename != ''" class="btn btn--glass" @click="filedown(m.nte_filename, m.nte_originname)">
                                    <img src="/assets/images/icon/file.png"/>{{ m.nte_originname }}
                                </button>
                            </div>
                            <div class="date">
                                <!-- <span v-if="m.nte_read_datetime != '' && mem_id != m.send_mem_id" class="active">Seen</span> -->
                                {{ m.nte_datetime }}
                                <span v-if="mem_id === m.send_mem_id"></span>

                            </div>
                        </div>
                    </div>

                    <div class="attached" :class="attfilename != '' ? 'active' : ''">
                        <div class="btn btn--glass">
                            <img src="/assets/images/icon/file.png"/>{{attfilename}}
                            <button class="close" @click="attfilename = ''">
                                <img src="/assets/images/icon/x-white.png"/>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-group-prepend mr-2">
                        <label class="btn btn-secondary mb-0" for="file">
                            <input type="file" id="file" ref="file" style="display:none;" @change="attfile" @drop="attfile">
                            <i class="fa fa-file"></i>
                        </label>
                    </div>
                    <input class="form-control" type="text" placeholder="Enter your message..." v-model="goMessText" @keypress.enter="sendMess">
                    <div class="input-group-append">
                        <button class="btn btn-primary" :disabled="!sendBtnYn" @click="sendMess">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import $ from "jquery"
    import FileUpload from 'vue-simple-upload/dist/FileUpload'
    import "jquery.nicescroll"

    export default {
        components: {
            FileUpload
        },
        data: function() {
            return {
                isLogin: false,
                isLoading: false,
                group_title: 'SELLER',
                product_status: 'PENDING',
                mem_id: '',
                mem_photo: '',
                mem_usertype: '',
                mem_nickname: '',
                mem_address1: '',
                mem_type: '',
                mem_lastname: '',
                mem_profile_content: '',
                popup_filter:0,
                dateType: 'All',
                goMessText: '',
                search_date_option: 0,
                searchword: 0,
                messageList: [],
                tempList: [],
                messageDetail: [],
                mchat: 'none',
                mid: '',
                mchatUser: '',
                mchatUserPhoto: '',
                mchatUserBio: '',
                attfilename: '',
                attfileurlname: '',
                searchUser: '',
                sendBtnYn: false,
                timerId: '',
                dateTypes: [
                    {
                        label: this.$t('all'),
                        code: 'All'
                    },
                    {
                        label: this.$t('read'),
                        code: 'Read'
                    },
                    {
                        label: this.$t('unread'),
                        code: 'Unread'
                    },
                ]
            };
        },
        watch:{
            messageList:()=>{
                let ele = document.getElementById('message-list');
                ele.scrollTop = ele.scrollHeight;
            },
            goMessText: function (e) {
                if(this.goMessText.length == 0){
                    this.sendBtnYn = false;
                }else{
                    this.sendBtnYn = true;
                }
            },
        },
        mounted(){
            // 커스텀 셀렉트 옵션
            $(".bs-select").on("click", function() {

                $(this)
                    .siblings(".bs-select")
                    .removeClass("active")
                    .find(".options")
                    .hide();
                $(this).toggleClass("active");
                $(this)
                    .find(".options")
                    .toggle();
            });

            // 커스텀 스크롤
            $("#message-list").niceScroll({
                cursorborder: "none"
            });
        },
        created() {
            this.ajaxMessageList();
            this.ajaxUserInfo();
        },
        methods:{
            async ajaxUserInfo () {
                try {
                    this.isLoading = true;
                    const { data } = await axios.get(
                        '/beatsomeoneApi/get_user_info', {}
                    );
                    //console.log(data);
                    this.mem_id = data[0].mem_id;
                    this.mem_photo = data[0].mem_photo;
                    this.mem_usertype = data[0].mem_usertype;
                    this.mem_nickname = data[0].mem_nickname;
                    this.mem_address1 = data[0].mem_address1;
                    this.mem_type = data[0].mem_type;
                    this.mem_lastname = data[0].mem_lastname;
                    this.mem_profile_content = data[0].mem_profile_content;

                    if(this.mem_usertype == 1){
                        this.group_title = "CUSTOMER";
                    }else{
                        this.group_title = "SELLER";
                    }
                } catch (err) {
                    console.log('ajaxUserInfo error');
                } finally {
                    this.isLoading = false;
                }
            },
            async ajaxMessageList () {
                try {
                    this.isLoading = true;
                    const { data } = await axios.get(
                        '/beatsomeoneApi/message_list', {}
                    );
                    console.log(data);
                    this.messageList = data.result;
                } catch (err) {
                    console.log('ajaxMessageList error');
                } finally {
                    this.isLoading = false;
                }
            },
            async ajaxMessageDetail(mid) {
                try {
                    this.isLoading = true;
                    var param = new FormData();
                    param.append('mid', JSON.stringify(mid));
                    const { data } = await axios.post(
                        '/beatsomeoneApi/message_detail', param,{
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                    console.log(data);
                    this.messageDetail = data.result;
                } catch (err) {
                    console.log('ajaxMessageDetail error');
                } finally {
                    this.isLoading = false;
                }
            },
            async ajaxMessageRead(mid) {
                try {
                    this.isLoading = true;
                    var param = new FormData();
                    param.append('mid', JSON.stringify(mid));
                    const { data } = await axios.post(
                        '/beatsomeoneApi/message_read', param,{
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                    console.log(data);
                    return data.result;
                } catch (err) {
                    console.log('ajaxMessageRead error');
                } finally {
                    this.isLoading = false;
                }
            },
            formatConName: function(data, limitLth){
                let rst;
                if(limitLth < data.length){
                    rst = data.substring(0,limitLth)  + '...';
                }else{
                    rst = data
                }
                return rst;
            },
            goPage: function(page){
                window.location.href = '/mypage/'+page;
            },
            calcSeq: function(size, i){
                return parseInt(size) - parseInt(i);
            },
            formatCitName: function(data){
                var rst;
                var limitLth = 50
                if(limitLth < data.length && data.length <= limitLth*2){
                    rst = data.substring(0,limitLth) + '<br>' + data.substring(limitLth,limitLth*2);
                }else if(limitLth < data.length && limitLth*2 < data.length){
                    rst = data.substring(0,limitLth) + '<br>' + data.substring(limitLth,limitLth*2) + '...';
                }else{
                    rst = data
                }
                return rst;
            },
            productEditBtn: function(key){
                console.log("productEditBtn:" +key);
                window.location.href = 'https://beatsomeone.com/detail/'+key;
            },
            funcDateType: function(t){
                if(this.dateType == t){
                    return;
                }else{
                    if(t === "All"){
                        this.search_date_option = 0
                        this.dateType = t;
                        this.ajaxMessageList();
                    }else if(t === "Read"){
                        this.search_date_option = 1
                        this.dateType = t;
                        this.ajaxMessageList().then(()=>{
                            this.messageList = this.messageList.filter(item => item.unread == 0);
                        });
                    }else{
                        this.search_date_option = 2
                        this.dateType = t;
                        this.ajaxMessageList().then(()=>{
                            this.messageList = this.messageList.filter(item => item.unread > 0);
                        });
                    }
                }
            },
            goMChat: function(e, m){
                if(this.mchat == "none"){
                    this.mchat = "block";
                }

                if(!this.isEmpty(this.timerId)){
                    clearInterval(this.timerId);
                }

                this.ajaxMessageDetail(m.mem_id).then(()=>{
                    this.mid = m.mem_id;
                    this.mchatUser = m.mem_nickname;
                    this.mchatUserPhoto = m.mem_photo;
                    if(this.isEmpty(m.mem_type) && this.isEmpty(m.mem_lastname)){
                        this.mchatUserBio = '';
                    }else{
                        this.mchatUserBio = m.mem_profile_content;
                    }

                    var messageDisplay = document.getElementById("messageDisplay");
                    messageDisplay.scrollTop = messageDisplay.scrollHeight;
                });
                this.ajaxMessageRead(m.mem_id).then((data)=>{
                    if(data){
                        this.ajaxMessageList().then(()=>{
                            //console.log(this.tempList.length);
                            for(let i in this.tempList){
                                this.messageList.push(this.tempList[i]);
                            }
                            this.tempList = [];
                        });
                    }
                });

                this.timerId = setInterval(function(){
                    console.log(m.mem_nickname);
                    this.ajaxMessageDetail(m.mem_id).then(()=>{
                        this.mid = m.mem_id;
                        this.mchatUser = m.mem_nickname;
                        this.mchatUserPhoto = m.mem_photo;
                        if(this.isEmpty(m.mem_type) && this.isEmpty(m.mem_lastname)){
                            this.mchatUserBio = '';
                        }else{
                            this.mchatUserBio = m.mem_profile_content;
                        }

                        var messageDisplay = document.getElementById("messageDisplay");
                        messageDisplay.scrollTop = messageDisplay.scrollHeight;
                    });
                    this.ajaxMessageRead(m.mem_id).then((data)=>{
                        if(data){
                            this.ajaxMessageList().then(()=>{
                                //console.log(this.tempList.length);
                                for(let i in this.tempList){
                                    this.messageList.push(this.tempList[i]);
                                }
                                this.tempList = [];
                            });
                        }
                    });
                }.bind(this, m), 3000);
                //console.log(e);
            },
            attfile: function(e){
                //console.log(e);
                this.attfilename = e.target.files[0].name;
                const formData = new FormData();
                formData.append('file', this.$refs.file.files[0]);
                const url = "/beatsomeoneApi/upload_message_file";
                axios.post(url, formData).then(r => {
                    console.log(r.data);
                    if(0 < r.data.filesize){
                        //
                    }else{
                        alert('파일을 다시 확인해주세요');
                    }
                    this.attfileurlname = r.data.filename;
                })
            },
            sendMess: function(e){
                if(this.goMessText.length == 0){
                    alert('메세지를 입력하세요');
                    return;
                }

                let fn = '';
                let fnurl = '';
                if(!this.isEmpty(this.attfilename)){
                    fn = this.attfilename;
                    fnurl = this.attfileurlname;
                }
                const formData = new FormData();
                formData.append('rmid', JSON.stringify(this.mid));
                formData.append('message', JSON.stringify(this.goMessText));
                formData.append('filename', JSON.stringify(fn));
                formData.append('fileurlname', JSON.stringify(fnurl));
                const url = "/beatsomeoneApi/message_send";
                axios.post(url, formData).then(r => {
                    console.log(r);
                    if(r.data.message == 'ok'){
                        this.attfilename = '';
                        this.attfileurlname = '';
                        this.goMessText = '';
                        this.ajaxMessageDetail(this.mid);
                        this.ajaxMessageList().then(()=>{
                            for(let i in this.messageList){
                                if(this.messageList[i].mem_id == this.mid){
                                    this.messageList[i].unread = 0;
                                }
                            }
                        });
                    }

                });
            },
            isEmpty: function(str){
                if(typeof str == "undefined" || str == null || str == "")
                    return true;
                else
                    return false;
            },
            forceFileDownload(r, on){
                const blob = new Blob([r.data], { type: 'application/octet-stream' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = on;
                link.click();
                URL.revokeObjectURL(link.href);
            },
            filedown : function(fn, on){
                let param = {"fn":fn, "on":on};
                axios.get('/cmallact/download_messfile', {params: param, responseType: 'arraybuffer'})
                    .then(r => {
                        console.log(r);
                        this.forceFileDownload(r, on);
                    })
                    .catch((e) => console.log(e));
            },
            goSearchUser : function(){
                console.log(this.searchUser);

                for(let l in this.messageList){
                    if(this.messageList[l].mem_nickname == this.searchUser){
                        this.goMChat('', this.messageList[l]);
                        return;
                    }
                }

                let param = {'searchUser': JSON.stringify(this.searchUser)};
                axios.get('/beatsomeoneApi/get_userid_info', {params: param})
                    .then(r => {
                        console.log(r.data.length);
                        if(r.data.length == 0){
                            alert('유저 아이디를 다시 확인해주세요');
                        }else{
                            let data = {'mem_id':r.data[0].mem_id,'mem_nickname':r.data[0].mem_nickname,
                                'nte_content':'','nte_datetime':'','unread':0};
                            this.tempList.push(data);
                            this.messageList.push(data);
                        }
                    })
                    .catch((e) => console.log(e));

            },
        },
        updated(){
            let ele = document.getElementById('message-list');
            ele.scrollTop = ele.scrollHeight;
        }
    }
</script>

<style scoped="scoped" lang="scss">
    .list-group-item {
        background-color: #1b1b1e;
        height: 80px;

        &.active {
            background-color: #252629;
            border: 0;
        }
    }

    .mem-icon {
        img {
            width: 60px;
            height: 60px;
            border-radius: 50px;
        }
    }

    .btn-primary:disabled {
        background-color: #272727;
        border: 0;
    }

    .v-select {
        width: 200px;
    }
</style>
