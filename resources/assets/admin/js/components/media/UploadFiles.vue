<!-- template for the modal upload files -->
<template>
    <div>
        <a href="javascript:;" :class="[$style.btn]">
             <input class="input-file" type="file" multiple="multiple" id="attachments" @change="uploadFieldChange">
             <i class="material-icons">cloud_upload</i><span>{{trans('media.uploads')}}</span>
        </a>
        <transition name="uploadFile" >
            <div v-if="showLoading" :class="[$style.modal]">
                <div :class="[$style.content_modal]">
                    <div :class="[$style.title]" v-if="errors_up.length>0">
                        <span :class="[$style.close]" @click="closeErrors()"><i class="material-icons">close</i></span>
                    </div>
                    <div :class="[$style.process_content]">
                        <div :class="[$style.process]">
                          <div :class="[$style.process_bar]" role="progressbar" v-bind:aria-valuenow="percentCompleted"
                          aria-valuemin="0" aria-valuemax="100" v-bind:style="{width: percentCompleted+'%'}">{{percentCompleted}}%</div>
                        </div>
                    </div>
                    <div :class="[$style.list_errors]" v-if="errors_up.length>0">
                        <ul>
                            <li v-for="i in errors_up">{{i}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
    export default{
        props: [
            'targetFolder',"close",
        ],
        data(){
            return {
                data: new FormData(),
                errors_up:[],
                percentCompleted: 0,
                showLoading:false,
            }
        },
        watch:{
        },
        methods:{
            uploadFieldChange(e){
                this.resetData();
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.lengthFiles=files.length;
                this.showLoading=true;
                this.data.append('folder_upload',this.targetFolder);
                for (let i = 0; i < files.length; i++) {
                    this.data.append('input_name[]', files[i]);
                }
                this.uploadFile(this.data).then(data => {

                },error=>{
                    this.errors_up=(error);
                });

                this.data = new FormData();

            },
            resetData() {
                this.data = new FormData(); // Reset it completely
                this.attachments = [];
                this.errors_up=[];
                this.showLoading=false;
                this.$emit('refreshUpload',false);
            },
            uploadFile(file){
                return new Promise((resolve, reject) => {
                   var config = {
                        headers: { 'Content-Type': 'multipart/form-data' } ,
                        onUploadProgress: function(progressEvent) {
                            this.percentCompleted=parseInt((progressEvent.loaded*100)/progressEvent.total)
                            this.$forceUpdate();
                        }.bind(this)
                    };
                    // Make HTTP request to store announcement
                    axios.post(this.pathApiMedia+'/uploadFile',file, config)
                        .then(function (response) {
                            resolve(response);
                            this.$emit('refreshUpload',true);
                            this.showLoading=false;
                        }.bind(this))
                        .catch(function (error) {
                            reject(error.response.data.message);

                        });
                    })   
            },
            closeErrors(){
                this.showLoading=false;
                this.$emit('refreshUpload',true);
            }
        }

    }
</script>
<style lang="scss" scoped module>
    $body-bg: #f5f8fa;
    $color-primary:#D32F2F;
    $color-primary-b:#B71C1C;
    $bg-color: #f5f8fa;
    $border-color: #d6d6d7;
    $text-color: #636b6f;
    .btn{
        display: flex;
        align-items: center;
        border:solid 1px $border-color;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 50px;
        color: $text-color;
        position: relative;
        overflow: hidden;
        i{
            margin-right: 5px;
        }
        input{
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            height: 100%;
            width: 100%;
        }
    }
    .modal{
        position: fixed;
        top:0px;
        left: 0px;
        background-color: rgba(0,0,0,0.5);
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        text-align: center;
        z-index: 2;
    }
    .content_modal{
        width: 60%;
        margin: auto;
        background-color: #fff;

    }
    .process_content{
        padding: 10px;
        .process{
            height: 20px;
            margin-bottom: 20px;
            overflow: hidden;
            background-color: #f5f5f5;
            border-radius: 10px;
            -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
            box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
            width: 100%;
            margin: auto;
        }
        .process_bar{
            float: left;
            width: 0;
            height: 100%;
            font-size: 12px;
            line-height: 20px;
            color: #fff;
            text-align: center;
            background-color: $color-primary-b;
            -webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
            -webkit-transition: width .6s ease;
            -o-transition: width .6s ease;
            transition: width .6s ease;
        }
    }
    .list_errors{
        padding: 10px;
        ul{
            margin: 0px;
            padding: 0px;
            text-align: left;
            li{
                color: red;
                font-size: 14px;
                text-align: left;
            }
        }
    }
    .title{
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #e9ebee;
        display: flex;
        align-items: center;
        color: $text-color;
        position: relative;
        display: flex;
        justify-content: flex-end;
        font-weight: bold;
        i{
            margin-right: 0px;
        }
        .close{
            cursor: pointer;
        }
    }
</style>