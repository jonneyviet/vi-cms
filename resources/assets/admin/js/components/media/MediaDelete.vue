<template>
    <transition  name="slide-fade">
        <div :class="[$style.modal_mask]" v-if="show">
            <div :class="[$style.modal_wrapper]">
                <div :class="[$style.modal_container]">
                    <div :class="[$style.title]">
                        <i class="material-icons">delete_forever</i><span>{{langData.delete}}</span>
                        <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                    </div>
                    <div :class="[$style.body]">
                        <div :class="$style.message">
                            {{langData.notification_delete}}
                        </div>
                        <button :class="$style.btn_delete" @click="deleteAction()"><i class="material-icons">report</i>{{langData.btn_ok}}</button>
                        <div :class="[$style.errors]" v-html="message">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
    export default {
        props:["show","foldersSelect","filesSelect","langData"],
        data(){
            return {
                name_folder:null,
                message:"",
            }
        },
        methods:{
            deleteAction(){
                axios.post(this.pathApiMedia+"/delete",{
                    folders:this.foldersSelect,
                    files:this.filesSelect,
                }).then(response=>{
                    this.$emit('refreshDelete',true);
                }).catch(e=>{
                    this.message=e.message;
                })
            },
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
    .modal_mask{
        position: fixed;
        top:0px;
        left: 0px;
        background-color: rgba(0,0,0,0.5);
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        z-index: 999999;
        .modal_wrapper{
            display: block;
            .modal_container{
                width: 320px;
                margin: auto;
                background-color: #fff;
                .title{
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #e9ebee;
                    display: flex;
                    align-items: center;
                    color: $text-color;
                    position: relative;
                    display: flex;
                    justify-content: space-between;
                    font-weight: bold;
                    i{
                        margin-right: 0px;
                    }
                    .close{
                        cursor: pointer;
                    }
                }
                .body{
                    padding: 10px;
                    text-align: center;
                    .message{

                    }
                    .btn_delete{
                        display: flex;
                        align-items: center;
                        border:solid 1px $border-color;
                        padding: 5px 10px;
                        text-decoration: none;
                        border-radius: 50px;
                        color: $text-color;
                        margin: auto;
                        margin-top: 10px;
                        &:hover,&:focus{
                            outline: none;
                            box-shadow: none;
                        }
                        i{
                            margin-right: 5px;
                        }
                    }
                    .errors{
                        color: red;
                        font-size: 14px;
                        margin: 10px 0px;
                        text-align: left;
                    }
                }
            }
        }
    }
</style>
<style type="text/css">
    .slide-fade-enter-active {
        transition: all .2s ease;
    }
    .slide-fade-leave-active {
        transition: all .2s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
    {
        transform: translateX(10px);
        opacity: 0;
    }
</style>