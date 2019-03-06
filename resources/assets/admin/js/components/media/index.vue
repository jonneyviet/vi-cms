vue<template>
    <div>
    <div class="container">
        <div class="panel panel-default">
            <div :class="[$style.panel_heading]">
                <div :class="[$style.title]">
                    <div :class="[$style.icon]"><i class="material-icons">storage</i><span>{{trans('media.media')}}</span></div>
                </div>
                <div :class="[$style.search]">
                    <media-find v-bind:recycleFind="type" v-bind:langData="langData"
                        @resultSearchFolder="folders.list=$event"
                        @resultSearchFile="files.list=$event"
                        @resultNextPage="files.nextPage=$event"
                        @refreshFind="refresh = $event"
                     ></media-find>
                </div>
            </div>
            <div :class="[$style.panel_tag]">
                <ul :class="[$style.breadcrumb_folder]">
                    <li v-for="item in breadcrumb">
                        <router-link :to="{ name: 'mediaDefault'}"  v-if="item.share==''">
                        <i class="material-icons">folder</i>
                         {{item.name}}
                        </router-link>
                         <router-link :to="{ name: 'media', params: { id: item.share }}" v-else>
                        <i class="material-icons">folder</i>
                         {{item.name}}
                        </router-link>
                    </li>
                </ul>
                <ul :class="[$style.btn_group]" v-if="type==''">
                    <li>
                         <upload-files 
                             v-bind:targetFolder="$route.params.id"
                                v-bind:close="files.upload"
                             @refreshUpload="refresh = $event"
                         ></upload-files>
                    </li>

                    <li>
                        <a href="javascript:;" :class="[$style.btn]" v-on:click="folders.showNew=true">
                            <i class="material-icons">create_new_folder</i><span>{{trans('media.new')}}</span>
                        </a>
                    </li>
                    <li v-if="showButton01" v-on:click="showMoveMedia=true">
                        <a href="javascript:;" :class="[$style.btn]">
                            <i class="material-icons">low_priority</i><span>{{trans('media.move')}}</span>
                        </a>
                    </li>
                    <li v-if="showButton01">
                        <a href="javascript:;" :class="[$style.btn]"  v-on:click="recycleAction()">
                            <i class="material-icons">delete</i><span>{{trans('media.delete')}}</span>
                        </a>
                    </li>
                </ul>
                <ul :class="[$style.btn_group]" v-else>
                    <li v-if="showButton01">
                        <a href="javascript:;"  :class="[$style.btn]" v-on:click="restoreAction()">
                            <i class="material-icons">restore_from_trash</i><span>{{trans('media.restore')}}</span>
                        </a>
                    </li>
                    <li v-if="showButton01">
                        <a href="javascript:;" :class="[$style.btn]" v-on:click="showDeleteMedia=true">
                            <i class="material-icons">delete_forever</i><span>{{trans('media.delete')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div :class="[$style.panel_body]">
                <div :class="$style.left" v-if="folders.list.length>0">
                    <div :class="[$style.list,$style.list_folders]">
                        <ul>
                            <li v-for="item in folders.list" >
                              <div>
                                <div :class="[$style.checkbox]">
                                    <input type="checkbox" name="choice" v-bind:value="item.share" v-model="folders.select"><label></label>
                                </div>
                                <router-link :to="{ name: 'media', params: { id: item.share }}">
                                <i class="material-icons">folder</i> 
                                <div :class="[$style.name_file]">{{item.name}}</div>
                                </router-link>
                              </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="infinite_list" @scroll="loadMore(files.list)" :class="[$style.content_space]" @click.left="closeMenu">
                    <div :class="[$style.list,$style.list_images]">
                        <ul>
                            <li v-for="item in files.list">
                                <div>
                                    <div :class="[$style.checkbox]">
                                         <input type="checkbox" name="choice" v-bind:value="item.share" v-model="files.select"><label></label>
                                    </div>
                                    <figure v-if="item.type=='image'"><img v-bind:src="item.path"></figure>
                                    <div :class="[$style.icon_file]" v-else><i v-bind:class="item.type"></i></div>
                                    <div  :class="[$style.name_file]">{{item.name}}</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <a href="javascript:;" class="_btn" :class="[$style.btn_tinymce]" v-on:click="choiceTinyMce()">
                <i class="material-icons">add</i><span>{{trans('media.btn_ok')}}</span>
            </a>
        </div>
    </div>
    <!-- New Folder   -->
    <new-folder 
        v-bind:show="folders.showNew"
        @close="folders.showNew = false"
        @refresh="refresh = $event"
    >
    </new-folder>
    <!-- End new Folder     -->
     <!-- New Folder   -->
    <move-media
        v-bind:show="showMoveMedia"
        v-bind:foldersSelect="folders.select"
        v-bind:filesSelect="files.select"
        v-bind:langData="langData"
        @close="showMoveMedia = false"
        @refreshMove="refreshMove = $event"
        @desFolder="desFolder = $event"
    >
    </move-media>
    <!-- End new Folder     -->
    <!-- New Folder   -->
    <delete-media
            v-bind:show="showDeleteMedia"
            v-bind:foldersSelect="folders.select"
            v-bind:filesSelect="files.select"
            v-bind:langData="langData"
            @close="showDeleteMedia = false"
            @refreshDelete="refresh = $event"
    >
    </delete-media>
    <!-- End new Folder     -->
    </div>
</template>
<script>
    import Search from './MediaFind.vue';
    import Upload from './UploadFiles.vue';
    import NewFolder from './NewFolder.vue';
    import MoveMedia from './MediaMove.vue';
    import DeleteMedia from './MediaDelete.vue';
    import tinymce from '@tinymce/tinymce-vue';
    export default{
        data(){
            return {
                breadcrumb:[],
                type:null,
                folders:{
                    select:[],
                    list:[],
                    showNew:false
                },
                files:{
                    select:[],
                    list:[],
                    upload:false,
                    nextPage:null,
                },
                isOpenSearch:false,
                loading:false,
                refresh:false,
                refreshMove:false,
                resultSearch:null,
                showMoveMedia:false,
                desFolder:null,
                showDeleteMedia:false,
                recycleFind:false,
                langData:[],
                fileTinymce:"null",
            }
        },
        watch: {
            '$route.params.id': function (id) {
                  this.getAll(this.$route.params.id)
                },
            openUpload:function(id){
                if(!this.openUpload){
                    this.getAll(this.$route.params.id)
                }
            },
            refresh:function(id){
                if(this.refresh){
                    this.getAll(this.$route.params.id)
                }
                this.refresh=false;
                this.folders.showNew=false;
                this.files.upload=true;
                this.showDeleteMedia=false;
            },
            desFolder:function(){
                if(this.refreshMove){
                    const folderId = this.desFolder;
                    this.$router.push({ name: 'media', params: { id: folderId }});
                }else{
                    this.$router.push({ name: 'mediaDefault'});
                }
                this.refreshMove=false;
                this.showMoveMedia=false;
                this.folders.select=this.files.select=[];
            },
            'files.select':function(e){
                var t=null;
                this.files.list.forEach(function(item){
                    if(item.share==e[0]){
                       t=item.link;
                    }
                });
                this.fileTinymce=t;
            }
        },
        created:function(){
            this.getAll(this.$route.params.id);
        },
        methods:{
            getAll(key){
                this.folders.select=[];
                this.files.select=[];
                if(key==null){
                    var path=this.pathApiMedia+"/getAll";
                }else{
                    var path=this.pathApiMedia+"/getAll?share="+key
                }
                axios.get(path)
                    .then(response=>{
                        this.breadcrumb=response.data.breadcrumb
                        this.type=response.data.type

                        this.folders.list=response.data.folders
                        this.files.list=response.data.files.data
                        this.files.nextPage=response.data.files.next_page_url
                    })
                    .catch(e=>{})
            },
            loadMore(result=[]){
                var container = this.$el.querySelector("#infinite_list");
                if(container){
                    if (container.scrollTop + container.clientHeight == container.scrollHeight) {
                        if(this.files.nextPage!=null){
                            axios.get(this.files.nextPage)
                                .then(response=>{
                                    response.data.files.data.forEach(function(item,index) {
                                        result.push(item);
                                    });
                                    this.files.nextPage=response.data.files.next_page_url;
                                })
                                .catch(e=>{});
                        }
                    }
                }
            },
            recycleAction(){
               axios.post(this.pathApiMedia+"/recycle",{
                        folders:this.folders.select,
                        files:this.files.select,
                    }).then(response=>{
                            this.getAll(this.$route.params.id);
                            this.folders.select=this.files.select=[];
                    })
                    .catch(e=>{});
            },
            closeMenu: function(){
                this.isOpenSearch=false;
                this.isOpenMenu=false;
            },
            restoreAction(){
                axios.post(this.pathApiMedia+"/restore",{
                    folders:this.folders.select,
                    files:this.files.select,
                }).then(response=>{
                    this.$router.push({ name: 'mediaDefault'});
                }).catch(e=>{});
            },
            choiceTinyMce(){
                var args = top.tinymce.activeEditor.windowManager.getParams();
                var win = (args.window);
                var input = (args.input);
                win.document.getElementById(input).value = this.fileTinymce;
                top.tinymce.activeEditor.windowManager.close();
            }
        },
        computed:{
            showButton01:function(){
                if(this.folders.select.length>0 || this.files.select.length>0){
                    return true
                }else{
                    return false
                }
            }
        },
        components: {
            MediaFind:Search,
            UploadFiles:Upload,
            NewFolder:NewFolder,
            MoveMedia:MoveMedia,
            DeleteMedia:DeleteMedia,
        }
    }
</script>
<style lang="scss" module>
    @import "../../../sass/variables";
    .breadcrumb_folder{
        padding: 0px;
        margin: 0px;
        list-style: none;
        li{
            display: inline-block;
            a{
                color: $text-color;
                text-decoration: none;
                display: flex;
                align-items: center;
                padding-right: 15px;
                i{
                    margin-right: 5px;
                }
            }
            &:nth-child(n+2){
                a{
                    position: relative;
                   &:before{
                        content: "/";
                        position: absolute;
                        top: 4px;
                        left: -9px;
                    }
                }
            }
        }
    }
    .panel_heading{
        padding: 5px 10px;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        .title{
           .icon{
            color: $text-color;
           }
        }
        .search{

        }
    }
    .panel_tag{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #e9ebee;
    }
    .panel_body{
        display: flex;
        .left{
            min-width:220px;
            height: calc(100% - 55px);
            border-right: solid 1px $border-color;
            overflow-y: scroll;
        }
    }
    .content_space{
        height: calc(100% - 55px);
        overflow-y: scroll;
        overflow-x: hidden;
        width: 100%;
    }
    .list{
      width: 100%;
      display: block;
      >div{
        position: relative;
      }
      .checkbox{
            position: absolute;
            right: 0px;
            top: 0px;
            width: 30px;
            height: 30px;
            background-color: #fff;
            border-bottom-left-radius: 45px;
            text-align: right;
           input[type=checkbox] {
                position: absolute;
                right: 0px;
                top: 0px;
                height: 20px;
                width: 20px;
                opacity: 0;
            }
            input[type=checkbox] + label
            {
                background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAdElEQVR4Ae2XAQbAMAwA87MB0O1FKfQDG/1Rf7bVQAApTSl3HCBxFBqBQODoardMVv/dg9TuG2wVJ2d0jPESB9kMtIAna2Z/FgfFDBQZJWA/QQQRRBBBBBFEEEEEbRakCz/5Kg7SwjMoiZNnQcy98SntB+ADkcYWisQoWCcAAAAASUVORK5CYII=');
                background-size: 100%;
                height: 20px;
                width: 20px;
                display:inline-block;
                padding: 0 0 0 0px;
            }
            input[type=checkbox]:checked + label
            {
                background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAQAAABLCVATAAAAyElEQVR4Ae3VgQaDUBiG4RfYIt1AYNhVDA1A2xWdoBvY6IoCQdAlbAV0FQ1m4bP9dTJC7wdwPID/MLOtBEc+cY6ELxUMM1eAdmHw2BUp84IypNwLylcBNZzpl0MNEXCkWwbVRADEtEugmnBk/CBh/KFKGAPqOdGgTKDML6jjAETUBmNCLTEAIdXncamMDY1U8KZKdsJYkFClMpMgofbsUcaGlFJmMiSUMjaklDJ+Z+TJY/2HzYacF+SQUi8oReM+m7n94cu223oBwHx/AH2GJB8AAAAASUVORK5CYII=');
                background-size: 100%;
            }
        }
      &_folders{
        ul{
          display: block;
          list-style: none;
          padding: 0px;
          margin: 0px;
            >li{
              box-sizing: border-box;
              display: block;
              >div{
                position: relative;
                padding: 10px;
                border-radius: 3px;
                border-bottom: solid 1px $border-color;
                  .checkbox{
                      position: absolute;
                      top: 50%;
                      right: 12px;
                      margin-top: -12px;
                  }
                  .name_file{
                      width: 100%;
                      white-space: nowrap;
                      padding-right: 30px;
                  }
                a{
                  color: $text-color;
                  text-decoration: none;
                  display: flex;
                  align-items: center;
                  i{
                    font-size: 24px;
                    display: inline-block;
                    color: #6bccf9;
                    padding-right: 10px;
                  }
                  .name{
                    white-space: nowrap;
                    overflow: hidden;
                    display: inline-block;
                    padding-left: 10px;
                  }
                }
              }
          }
        }
      }
      &_images{
        ul{
          display: flex;
          justify-content: left;
          flex-wrap: wrap;
          list-style: none;
          padding: 0px;
          >li{
            width: 20%;
            padding: 10px;
            box-sizing: border-box;
            >div{
              position: relative;
              -webkit-box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.05);
              -moz-box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.05);
              box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.05);
              &:hover{
                -webkit-box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.12);
                -moz-box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.12);
                box-shadow: 1px 1px 4px 3px rgba(0,0,0,0.12);
              }
            }
            figure{
              border-radius: 3px;
              overflow: hidden;
              margin: 0px;
              img{
                width: 100%;
              }
            }
            .name_file{
              padding: 5px;
              overflow: hidden;
              min-height: 47px;
                overflow-wrap: break-word;
            }
          }
        }
      }
    }
    .icon{
        display: flex;
        align-items: center;
        i{
            margin-right: 5px;
        }
        span{
            font-weight: bold;
        }
    }
    .btn{
        display: flex;
        align-items: center;
        border:solid 1px $border-color;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 50px;
        color: $text-color;
        i{
            margin-right: 5px;
        }
        &_group{
            list-style: none;
            margin:0px;
            padding: 0px;
            li{
                display: inline-block;
            }
        }
    }
    .btn_tinymce{
        position: fixed;
        bottom: 10px;
        right: 10px;
        background-color:red;
        color: #fff;
    }
    .icon_file{
        min-height: 50px;
    }
</style>




