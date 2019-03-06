<template>
    <div :class="$style.body_text">
        <div>
            <div :class="$style.left">
                <div :class="$style.row">
                    <label>{{trans('category.name')}}</label>
                    <div :class="$style.input_name">
                        <input-text
                                v-model="nameCategory.name"
                                :placeholder="trans('category.name')"
                                :isLoading="nameCategory.isLoading"
                                :errors_text="nameCategory.errors_name"
                                @keyup.enter="updateName()"
                        ></input-text>
                    </div>
                    <label>{{trans('category.content')}}</label>
                    <div>
                        <textarea v-model="text" :class="$style.textarea"></textarea>
                    </div>
                </div>
            </div>
            <div  :class="$style.right">
                <div :class="$style.row">
                    <label>{{trans('post.created_at')}}</label>
                        <div :class="$style.input_name">
                            <date-created type="datetime" v-model="date_time" format="dd-MM-yyyy HH:mm"></date-created>
                    </div>
                     <label>{{trans('post.avatar')}}</label>
                    <div>
                        <avatar-post
                                :imgUrl = "avatar.imgUrl"
                                :imgName="nameCategory.name"
                                :urlUpload="avatar.urlUpload"
                                :name_title = "trans('category.avatar')"
                                :viewport_with="setting('sizeAvatar.width')"
                                :viewport_height="setting('sizeAvatar.height')"
                                :route="avatar.route"
                        ></avatar-post>
                    </div>
                    <div class="_checkbox_content">
                        <div class="_checkbox">
                            <input type="checkbox" id="isPublic" v-model="isPublic"><label></label>
                        </div>
                        <label for="isPublic" class="label_checkbox">{{trans('category.public')}}</label>
                    </div>
                </div>
                <button class="_btn" @click="update()" v-bind:disabled="loadding"><i class="material-icons">update</i>{{loading_uppdate}}</button>
            </div>
        </div>
    </div>
</template>
<script>
    import InputText from '../../components/inputText.vue'
    import {Datetime} from 'vue-datetime'
    import avatarImage from '../../components/avatarImage'
    export default{
        data(){
            return {
                text:null,
                loadding:false,
                nameCategory:{
                    name:null,
                    isLoading:false,
                    errors_name:null,
                },
                addTitleText:{
                    isActive:false,
                    icon:"add",
                    name:""
                },
                date_time:null,
                avatar:{
                    imgUrl:"",
                    urlUpload:this.pathApiCategory+"/uploadAvatar",
                    route:this.$route.params.id,
                },
                isPublic:0,
            }
        },
        watch:{
            htmlForEditor:function (e) {
                this.text.forEach((e, i) => {
                    if (e.key == this.keyText) {
                        this.text[i].content = this.htmlForEditor;
                    }
                });
            },
        },
        created:function(){
            this.getItem('text,name');
        },
        methods:{
            getItem(option){
                axios.get(this.pathApiCategory+"/getItem/"+this.$route.params.id)
                    .then(response=>{
                        this.nameCategory.name=(response.data.name);
                        this.date_time=(response.data.date);
                        this.text=(response.data.text);
                        this.isPublic=(response.data.is_public);
                        this.avatar.imgUrl=(response.data.avatar_url!=null)?response.data.avatar_url:null;
                    })
                    .catch(e=>{})
            },
            editText(key){
                this.keyText=key;
                this.text.forEach((e, i) => {
                    if (e.key == key) {
                        this.htmlForEditor=this.text[i].content;
                    }
                });
            },
            updateName(){
                    this.nameCategory.isLoading=true;
                    this.nameCategory.errors_name=null;
                    axios.post(this.pathApiCategory+"/update/"+this.$route.params.id,{
                        data:{name:this.nameCategory.name}
                    }).then(response=>{
                        this.nameCategory.isLoading=false;
                        this.$emit("updateText",true);
                    }).catch(e=>{
                        this.nameCategory.errors_name=(this.errors(e.response.data.message));
                    });
            },
            update(){
                this.loadding=true;
                this.nameCategory.errors_name=null;
                axios.post(this.pathApiCategory+"/update/"+this.$route.params.id,{
                        data:{
                            text:this.text,
                            name:this.nameCategory.name,
                            created_at:this.date_time,
                            is_public:this.isPublic,
                        }
                    }).then(response=>{
                        this.loadding=false;
                        this.$emit("updateText",true);
                    }).catch(e=>{
                        this.nameCategory.errors_name=(this.errors(e.response.data.message));
                    });
                return false;
            }
        },
        computed:{
            loading_uppdate: function(){
                if(this.loadding){
                     return this.trans('category.btn_updating');
                }
                 return this.trans('category.btn_update');
            }
        },
        components: {
            inputText:InputText,
            dateCreated:Datetime,
            avatarPost:avatarImage,
        },
    }
</script>
<style lang="scss" module>
     @import "../../../sass/variables";
     .body_text{
        >div{
            &:after{
                content: "";
                display: table;
                clear: both;
            }
            .left{
                width: calc(100% - 290px);
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                float: left;
            }
            .right{
                width: 290px;
                float: right;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                padding-left: 10px;
                padding-right: 10px;

                .title{
                    background-color: #dbdbdb;
                    height: 48px;
                    margin-top: 10px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    position: relative;
                    >div{
                        display: flex;
                        justify-content: start;
                        align-items: center;
                    }
                    i{
                        font-size: 28px;
                        margin-right: 10px;
                        margin-left: 10px;
                    }
                    .form_search{
                        position: absolute;
                        top: 48px;
                        left: 0px;
                        width: 100%;
                        background-color: #dbdbdb;
                        padding: 10px;
                        box-sizing: border-box;
                        height:0px;
                        z-index: -1;
                        opacity: 0;
                        transition: all 500ms ease;
                    }
                }

            }
        }
    }
    .right{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }
    .row{
        margin-bottom: 10px;
        label{
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            width: 100%;
        }
    }
    .input_name{
        display: flex;
        justify-content: start;
        align-items: center;
        a{
            color:#636b6f;
            width: 50px;
            text-align: center;
        }
    }
    .textarea{
        height: 200px;
    }
</style>
