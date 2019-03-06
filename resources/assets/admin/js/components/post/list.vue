<template>
    <div>
        <div class="container">
            <div class="panel panel-default">
                <div class="_panel_heading" :class="[$style.panel_heading]">
                    <div :class="[$style.title]">
                        <div :class="[$style.icon]"><i class="material-icons">border_color</i><span>{{trans('post.post')}}</span></div>
                    </div>
                    <div :class="[$style.search]">
                        <!-- serach -->
                    </div>
                </div>
                <div class="_panel_tag" :class="[$style.panel_tag]">
                   <ul :class="[$style.breadcrumb_folder]">
                       <li><router-link :to="{ name: 'postDefault'}">
                           <i class="material-icons">more_vert</i>
                            </router-link>
                        </li>
                       <li>
                           <a href="javascript:;"><i class="material-icons">arrow_right</i>{{namePost}}</a>
                       </li>
                   </ul>
                    <ul :class="[$style.btn_group]">
                        <li>
                            <a href="javascript:;" :class="[$style.btn]" @click="showNew=!showNew">
                                <i class="material-icons">playlist_add</i><span>{{trans('post.new')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="_panel_body" :class="[$style.panel_body]">
                    <div style="width: 100%">
                        <div v-if="show_table">
                            <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{trans('post.name')}}</th>
                            <th>{{trans('post.language')}}</th>
                            <th>{{trans('post.status')}}</th>
                            <th>{{trans('post.date_update')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item,i in dataPost.list">
                            <td>{{++i}}</td>
                            <td>{{item.name}}</td>
                            <td>{{item.lang}}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{item.date}}</td>
                            <td>
                                <ul class="_list_btn">
                                    <li :class="$style.more">
                                        <router-link v-bind:title="trans('post.more')" :to="{ name: 'postText', params: { id: item.key }}"><i class="material-icons">more_horiz</i></router-link>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                        </div>
                        <router-view @update="refresh=$event" @namePost="namePost=$event"></router-view>
                    </div>
                </div>
            </div>
        </div>
        <!-- New Folder   -->
        <new-post
                v-bind:show="showNew"
                @close="showNew = false"
                @refresh="refresh = $event">
        </new-post>
        <!-- End new Folder     -->
    </div>
</template>

<script>
    import New from './new.vue';
    export default {
        props:['lang'],
        data() {
            return {
                refresh:false,
                showNew:false,
                namePost:null,
                dataPost:{
                    list:[]
                }
            }
        },
        watch: {
            refresh: function(id) {
                this.showNew = false;
                if(this.refresh){
                    this.all();
                }
            },
            namePost:function(e){
                this.namePost=e;
            }
        },
        created: function() {
            this.all();
        },
        methods: {
            all(option = null) {
                var path = this.pathApiPost + "/getAll";
                if (option != null) {
                    path = path + "&o=" + option
                }
                axios.get(path)
                    .then(response => {
                        this.dataPost.list = response.data.data;
                        this.breadcrumb = response.data.breadcrumb;
                    })
                    .catch(e => {})
            },
        },
        computed: {
            show_table: function() {
                if (this.$route.name == 'postDefault') {
                    return true
                }else {
                    return false;
                }

            }
        },
        components: {
            newPost:New
        }
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
    .breadcrumb_folder {
        padding: 0px;
        margin: 0px;
        list-style: none;
        li {
            display: inline-block;
            a {
                color: $text-color;
                text-decoration: none;
                display: flex;
                align-items: center;
                i {
                    margin-right: 5px;
                }
            }
            &:nth-child(n+2) {
                a {
                    position: relative;
                }
            }
        }
    }

    .panel_heading {
        padding: 5px 10px;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 44px;
        box-sizing: border-box;
        .title {
            .icon {
                color: $text-color;
            }
        }
    }

    .panel_tag {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #e9ebee;
    }

    .panel_body {
        display: flex;
        padding: 10px;
    }

    .content_space {
        height: calc(100% - 103px);
        overflow: scroll;
    }

    .icon {
        display: flex;
        align-items: center;
        i {
            margin-right: 5px;
        }
        span {
            font-weight: bold;
        }
    }

    .btn {
        display: flex;
        align-items: center;
        border: solid 1px $border-color;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 50px;
        color: $text-color;
        i {
            margin-right: 5px;
        }
        &_group {
            list-style: none;
            margin: 0px;
            padding: 0px;
            li {
                display: inline-block;
            }
        }
    }

    .icon_file {
        min-height: 50px;
    }

    .more {
        width: 50px;
    }
</style>

<style>

</style>