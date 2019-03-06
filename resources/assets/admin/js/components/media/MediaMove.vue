<template>
        <transition  name="slide-fade">
            <div :class="[$style.modal_mask]" v-if="show">
                <div :class="[$style.modal_wrapper]">
                    <div :class="[$style.modal_container]">
                        <div :class="[$style.title]">
                            <i class="material-icons">low_priority</i><span>{{langData.move}}</span>
                            <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                        </div>
                        <div :class="[$style.body]">
                            <div :class="[$style.form]">
                                <input type="text" v-bind:placeholder="langData.name_folder" maxlength="255" v-model="name_folder">
                                <button v-on:click="moveFolders()" v-if="desFolder!=null"><i class="material-icons">arrow_right_alt</i></button>
                            </div>
                            <div :class="[$style.errors]" v-html="message">
                            </div>
                            <div id="infinite_list" :class="[$style.result]" @scroll="loadMore(results)">
                                <ul :class="[$style.list_folder]">
                                    <li v-for="item in results">
                                        <div :class="[$style.box]">
                                            <div :class="[$style.name]">{{item.name}}</div>
                                            <div :class="[$style.choice]">
                                                <input type="radio" name="choice" v-bind:value="item.share" v-model="desFolder"><label></label>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
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
                results:[],
                next_page_url:null,
                message:null,
                desFolder:null,
            }
        },
        watch:{
            name_folder: _.debounce(function(){
                    this.searchFolder(this.name_folder);
                },1000)
        },
        methods:{
            searchFolder(name){
                var path=this.pathApiMedia+"/search?of=true&m=true&value="+name+"&folders="+this.foldersSelect;
                axios.get(path)
                .then(response=>{
                    this.results=(response.data.folders.data).concat(response.data.folders.folderRoot);
                    this.next_page_url=response.data.folders.next_page_url;
                })
                .catch(e=>{

                })
            },
            loadMore(result=[]){
                var container = this.$el.querySelector("#infinite_list");
                if(container){
                    if (container.scrollTop + container.clientHeight == container.scrollHeight) {
                        if(this.next_page_url!=null){
                            axios.get(this.next_page_url)
                                .then(response=>{
                                    response.data.files.original.data.forEach(function(item,index) {
                                        result.push(item);
                                    });
                                    this.next_page_url=response.data.files.original.next_page_url;
                                })
                                .catch(e=>{});
                        }
                    }
                }
            },
            moveFolders(){
                axios.post(this.pathApiMedia+"/move",{
                    shareFolders:this.foldersSelect,
                    shareFiles:this.filesSelect,
                    dshareFolder:this.desFolder,
                }).then(response=>{
                    this.$emit("refreshMove",true);
                    this.$emit("desFolder",this.desFolder);
                    this.desFolder=null;
                }).catch(e=>{

                });
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
                    .form{
                        position: relative;
                        width: 100%;
                        button{
                            position: absolute;
                            top: 0px;
                            right: 0px;
                            height:42px;
                            width: 42px;
                            background-color: transparent;
                            &:focus,&:active{
                                outline: none;
                                box-shadow: none;
                            }
                        }
                    }
                    input{
                        width: 100%;
                        padding: 10px 52px 10px 10px;
                        &:focus,&:active{
                            outline: none;
                            box-shadow: none;
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
    .result{
        max-height: 30%;
        overflow-y: scroll;
        overflow-x:hidden;
    }
    .list_folder{
            list-style: none;
            padding: 0px;
            margin: 0px;
            li{
                .box{
                    display: flex;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    width: 100%;
                    padding: 10px;
                    border-bottom: 1px solid #e9ebee;
                    box-sizing: border-box;
                }
                .choice{
                    width: 20px;
                    position: relative;
                    input[type=radio] {
                        position: absolute;
                        right: 0px;
                        top: 0px;
                        height: 20px;
                        width: 20px;
                        opacity: 0;
                    }
                    input[type=radio] + label
                    {
                        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAdElEQVR4Ae2XAQbAMAwA87MB0O1FKfQDG/1Rf7bVQAApTSl3HCBxFBqBQODoardMVv/dg9TuG2wVJ2d0jPESB9kMtIAna2Z/FgfFDBQZJWA/QQQRRBBBBBFEEEEEbRakCz/5Kg7SwjMoiZNnQcy98SntB+ADkcYWisQoWCcAAAAASUVORK5CYII=');
                        background-size: 100%;
                        height: 20px;
                        width: 20px;
                        display:inline-block;
                        padding: 0 0 0 0px;
                    }
                    input[type=radio]:checked + label
                    {
                        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAQAAABLCVATAAAAyElEQVR4Ae3VgQaDUBiG4RfYIt1AYNhVDA1A2xWdoBvY6IoCQdAlbAV0FQ1m4bP9dTJC7wdwPID/MLOtBEc+cY6ELxUMM1eAdmHw2BUp84IypNwLylcBNZzpl0MNEXCkWwbVRADEtEugmnBk/CBh/KFKGAPqOdGgTKDML6jjAETUBmNCLTEAIdXncamMDY1U8KZKdsJYkFClMpMgofbsUcaGlFJmMiSUMjaklDJ+Z+TJY/2HzYacF+SQUi8oReM+m7n94cu223oBwHx/AH2GJB8AAAAASUVORK5CYII=');
                        background-size: 100%;
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
#infinite_list{

}
/* width */
#infinite_list::-webkit-scrollbar {
    width: 2px;
}

/* Track */
#infinite_list::-webkit-scrollbar-track {
    background: #f1f1f1; 
}

/* Handle */
#infinite_list::-webkit-scrollbar-thumb {
    background: #888; 
}

/* Handle on hover */
#infinite_list::-webkit-scrollbar-thumb:hover {
    background: #555; 
}
</style>