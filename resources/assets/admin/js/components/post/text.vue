<template>
    <div :class="$style.body_text">
        <div>
            <div :class="$style.left">
                <div :class="$style.row">
                    <label>{{trans('post.name')}}</label>
                    <div :class="$style.input_name">
                        <input-text
                                v-model="namePost.name"
                                :isLoading="namePost.isLoading"
                                :material_icon="'done'"
                                :errors_text="namePost.e"
                                @keyup.enter="updateName()"
                        ></input-text>
                    </div>
                    <p :class="$style.errors_name" v-html="errors_name"></p>
                </div>
                <div :class="$style.row">
                    <div :class="$style.editor">
                        <label>{{trans('post.content')}}</label>
                        <editor-tool :htmlEditor="htmlForEditor" @updateText="htmlForEditor = $event"></editor-tool>
                    </div>
                </div>
            </div>
            <div  :class="$style.right">
                <div :class="$style.row">
                    <div  :class="$style.title">
                        <div><i class="material-icons">list</i>{{trans('category.list_content')}}</div>
                    </div>
                    <div  :class="$style.list">
                        <ul>
                            <li v-for="item in text">
                                <div>
                                    <span>{{item.title}}</span>
                                </div>
                                <div>
                                    <i class="material-icons" @click="editText(item.key)">edit</i>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div :class="$style.row">
                        <label>{{trans('category.name')}}</label>
                        <category-select
                                v-model="selectedCategory"
                                :placeholder="trans('category.find')"
                                label="name"
                                :close-on-select="true"
                                :show-labels="false"
                                :allow-empty="false"
                                :options="category"
                                @search-change="asyncFindCategory"
                        >
                            <span slot="noResult">{{trans('post.not_found')}}</span>
                        </category-select>
                    </div>
                    <div :class="$style.row">
                        <label>{{trans('post.created_at')}}</label>
                        <div :class="$style.input_name">
                            <date-created type="datetime" v-model="date_time" format="dd-MM-yyyy HH:mm:ss"></date-created>
                        </div>
                        <label>{{trans('post.avatar')}}</label>
                        <div>
                            <avatar-post
                                    :imgUrl = "avatar.imgUrl"
                                    :imgName="namePost.name"
                                    :urlUpload="avatar.urlUpload"
                                    :name_title = "trans('post.avatar')"
                                    :viewport_with="'350'"
                                    :viewport_height="'350'"
                                    :route="avatar.route"
                            ></avatar-post>
                        </div>
                    </div>
                    <button class="_btn" @click="updateAll()"><i class="material-icons">update</i>{{loading_uppdate}}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import EditorTool from '../../components/editorTool.vue'
    import InputText from '../../components/inputText.vue'
    import {Datetime} from 'vue-datetime'
    import avatarImage from '../../components/avatarImage'
    import Multiselect from 'vue-multiselect';
    export default{
        data(){
            return {
                htmlForEditor:null,
                keyText:null,
                text:[],
                loading:false,
                errors_name:null,
                namePost:{
                    name:null,
                    isLoading:false,
                    e:null,
                },
                addTitleText:{
                    isActive:false,
                    icon:"add",
                    name:""
                },
                category:[],
                selectedCategory:[],
                avatar:{
                    imgUrl:"",
                    urlUpload:this.pathApiPost+"/uploadAvatar",
                    route:this.$route.params.id,
                },
                date_time:null,
            }
        },
        mounted() {
        },
        watch:{
            htmlForEditor:function(e){
                this.htmlForEditor=e;
                this.text.forEach((e, i) => {
                    if (e.key == this.keyText) {
                        this.text[i].content = this.htmlForEditor;
                    }
                });

            },
            selectedCategory:function(e){
                this.selectedCategory=e;
            }
        },
        created:function(){
            this.getRow('text,name,date_time,avatar,category');
            this.asyncFindCategory();
        },
        methods:{
            getRow(option){
                axios.get(this.pathApiPost+"/getType?key="+this.$route.params.id+"&option="+option)
                    .then(response=>{
                        this.text=response.data.data.text;
                        this.namePost.name=response.data.data.name;
                        this.htmlForEditor=(Object.values(this.text[0])[2]);
                        this.keyText=(Object.values(this.text[0])[0]);
                        this.date_time=response.data.data.date_time;
                        this.avatar.imgUrl=response.data.data.avatar;
                        this.selectedCategory.push(response.data.data.category);
                        this.$emit("namePost",this.namePost.name);
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
                if(this.namePost.name.trim()){
                    this.namePost.isLoading=true;
                    axios.post(this.pathApiPost+"/update",{
                        data:{key:this.$route.params.id,name:this.namePost.name.trim()}
                    }).then(response=>{
                        this.$emit("update",true);
                        this.$emit("namePost",this.namePost.name);
                        this.namePost.isLoading=false;
                    }).catch(e=>{
                        this.namePost.errors=(this.errors(e.response.data.message));
                    });
                }
            },
            update(){
                this.loading=true;
                axios.post(this.pathApiPost+"/update",{
                    data:{
                        key:this.$route.params.id,
                        text:this.text,
                        category_id:this.selectedCategory.id,
                        name:this.namePost.name,
                        created_at:this.date_time,
                    }
                }).then(response=>{
                    this.$emit("update",true);
                    this.loading=false;
                }).catch(e=>{});
                return false;
            },
            updateAll(){
                this.update();
            },
            asyncFindCategory(query=null){
                var path=this.pathApiCategory+"/get?option=1";
                if(query!=null){
                    path=path+"&search="+query;
                }
                axios.get(path)
                    .then(response=>{
                        this.category=response.data.data;
                    }).catch(e=>{});
                return false;
            },
        },
        computed:{
            loading_uppdate: function(){
                if(this.loading){
                    return this.trans('post.btn_updating');
                }
                return this.trans('post.btn_update');
            }
        },
        components: {
            inputText:InputText,
            dateCreated:Datetime,
            avatarPost:avatarImage,
            editorTool:EditorTool,
            categorySelect:Multiselect,
        },
    }
</script>
<style lang="scss" module>
    // Body
    $body-bg: #f5f8fa;
    $color-primary:#D32F2F;
    $color-primary-b:#B71C1C;
    $bg-color: #f5f8fa;
    $border-color: #d6d6d7;
    $text-color: #636b6f;
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
    .list{
        border: 1px solid #e9ebee;
        margin-bottom: 10px;
        ul{
            list-style: none;
            padding: 0px;
            margin: 0px;
            li{
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 10px;
                border-bottom: 1px solid #e9ebee;
                position: relative;
                span{
                    padding-right: 10px;
                    color: #7f7d7d;
                    display: inline-block;
                    &.icon{
                        width: 50px;
                        i{
                            font-size: 28px;
                            line-height: 14px;
                            &:first-child{
                                position: absolute;
                                top: 7px;
                                left: 0px;
                            }
                            &:last-child{
                                position: absolute;
                                bottom: 7px;
                                top: auto;
                                left: 0px;
                            }
                        }

                    }
                }

                i{
                    color: #dbdbdb;
                    min-width: 40px;
                    text-align: center;
                    &:hover{
                        cursor: pointer;
                    }
                }
                &:hover{
                    i,span{
                        color: $text-color;
                    }
                }
            }
        }
    }
    button{
        margin-top: 10px;
        margin-right: auto;
        margin-left: auto;
    }
    .errors_name{
        margin: 5px 0px;
        color: red;
        font-size: 12px;
    }
</style>
<style>
    .ql-toolbar.ql-snow+.ql-container.ql-snow{
        height: calc(100% - 205px);
    }
    ._active ._form_search{
        z-index: 2!important;
        opacity: 1!important;
        height: auto!important;
    }
</style>
