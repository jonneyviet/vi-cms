<template>
    <div>
        <div class="container" v-if="showComponent">
            <div class="panel panel-default">
                <div class="_panel_heading">
                    <div class="title">
                        <div class="icon">
                            <i class="material-icons">filter_none</i>
                            <span>{{trans('category.category')}}
                            </span></div>
                    </div>
                </div>
                <div class="_panel_tag" >
                    <ul class="_breadcrumb_folder">
                        <li>
                            <router-link :to="{ name: 'categoryDefault'}">
                                <i class="material-icons">more_vert</i>
                            </router-link>
                        </li>
                        <li v-for="item in breadcrumb">
                            <router-link :to="{ name: 'category', params: { id: item.key }}">
                                <i class="material-icons">arrow_right</i> {{item.name}}
                            </router-link>
                        </li>
                         <li v-if="showEditText">
                             <router-link v-bind:title="trans('category.more')" :to="{ name: 'categoryText'}"><i class="material-icons">create</i></router-link>
                        </li>
                    </ul>
                    <ul class="_btn-group">
                        <li v-if="show_table">
                            <div class="_search">
                                <a href="javascript:;" @click.prevent="settingSearch.show=true" class="setting_search"><i class="material-icons">settings</i></a>
                                <input-search
                                        v-model="keySearch"
                                        :placeholder="trans('category.find')"
                                        :isLoading="isLoading"
                                        @keyup.enter.one=""
                                        :material_icon="'search'"
                                ></input-search>
                            </div>
                        </li>
                        <li>
                            <a href="javascript:;" class="_btn" @click="openNew()" v-bind:title="trans('category.new')">
                                <i class="material-icons">playlist_add</i><span>{{trans('category.new')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="_panel_body">
                        <div v-if="show_table">
                            <div class="table_type_01" id="infinite_list" @scroll="loadMore(category.list)">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="on">#</th>
                                            <th class="name">
                                                {{trans('category.name')}}
                                            </th>
                                            <th class="lang"><i class="material-icons">translate</i></th>
                                            <th class="type">{{trans('category.type')}}</th>
                                            <th class="status">{{trans('category.status')}}</th>
                                            <th class="child">{{trans('category.number_category_child')}}</th>
                                            <th class="date">{{trans('category.date_update')}}</th>
                                            <th class="function"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="category.list.length>0">
                                        <tr v-for="item,i in category.list">
                                            <td>{{++i}}</td>
                                            <td>{{item.name}}</td>
                                            <td>{{item.lang}}</td>
                                            <td>{{item.type}}</td>
                                            <td>
                                                <label class="switch">
                                                <input type="checkbox" v-bind:rel="item.key" v-model="item.is_public" v-on:change="updateIsPublic(item.key,item.is_public)">
                                                <span class="slider round"></span>
                                            </label>
                                            </td>
                                            <td>
                                                <router-link v-if="parseInt(item.child)>0" v-bind:title="trans('caetegry.more')" :to="{ name: 'category', params: { id: item.key }}">{{item.child}}</router-link>
                                            </td>
                                            <td>{{item.date}}</td>
                                            <td>
                                                <ul class="_list_btn">
                                                    <li :class="$style.more">
                                                         <router-link v-bind:title="trans('category.more')" :to="{ name: 'categoryText', params: { id: item.key }}"><i class="material-icons">create</i></router-link>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody class="_not_data" v-else >
                                            <tr><td colspan="9">{{trans('category.not_found')}}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <router-view @updateText="refreshBreadcrumb=$event"></router-view>
                </div>
            </div>
        </div>
        <!-- New Folder   -->
        <new-category
                v-bind:show="showNew"
                v-bind:keyCategory="keyCategory"
                @close="showNew = false"
                @refresh="refresh = $event">
        </new-category>
        <!-- End new Folder     -->
        <!-- New Folder   -->
        <setting-search
                v-bind:show="settingSearch.show"
                @close="settingSearch.show = false"
                @refresh="refresh = $event">
        </setting-search>
        <!-- End new Folder     -->
    </div>
</template>
<script>
    import axios from 'axios';
    import New from './new.vue';
    import SettingSearch from './settingSearch.vue';
    import InputText from '../../components/inputText.vue'
    export default {
        data() {
            return {
                showComponent:true,
                refresh: false,
                breadcrumb: null,
                refreshBreadcrumb:false,
                type: null,
                langData: [],
                keyCategory:null,
                category: {
                    list: [],
                    selects: [],
                    nextPage:null,
                },
                showNew: false,
                update: false,
                isLoading:false,
                keySearch:null,
                settingSearch:{
                    show:false
                }
            }
        },
        watch: {
            '$route.params.id': function(id) {
                this.all(this.$route.params.id)
                this.getBreadcrumb();
            },
            refreshBreadcrumb:function(e){
                if(e){
                    this.getBreadcrumb();
                    this.refreshBreadcrumb=false;
                }
            },
            refresh: function(id) {
                this.showNew = false;
                if (this.refresh) {
                    this.all(this.$route.params.id)
                }
                this.refresh = false;
            },
            keySearch: _.debounce(function(){
                this.searchKey(this.keySearch);
            },1000),
        },
        created: function() {
            this.all(this.$route.params.id);
            this.getBreadcrumb();
        },
        methods: {
            all(key, option = null) {
                var path = this.pathApiCategory + "/get?";
                if (key != null) {
                    path = path + "&key=" + key
                }
                axios.get(path)
                    .then(response => {
                        this.category.list = response.data.data;
                        this.category.nextPage=response.data.next_page_url;
                        this.keyCategory = this.$route.params.id;
                    })
                    .catch(e => {this.showComponent=false})
            },
            openNew() {
                if (this.$route.params.id == null) {
                    this.keyCategory = null;
                }
                return this.showNew = true;
            },
            loadMore(result=[]){
                var container = this.$el.querySelector("#infinite_list");
                if(this._loadAction(container)){
                    if(this.category.nextPage!=null){
                        axios.get(this.category.nextPage)
                            .then(response=>{
                                response.data.data.forEach(function(item,index) {
                                    result.push(item);
                                });
                                this.category.nextPage=response.data.next_page_url;
                            })
                            .catch(e=>{});
                    }
                }
            },
            searchKey(key){
                var path=this.pathApiCategory+"/get?search="+key;
                if(this.$route.params.id){
                    path=path+"&key="+this.$route.params.id
                }
                this.isLoading=true;
                axios.get(path)
                    .then(response=>{
                        this.isLoading=false;
                        this.category.list = response.data.data;
                        this.category.nextPage=response.data.next_page_url;
                    }).catch(e=>{})
            },
            getBreadcrumb(){
                var path=this.pathApiCategory+"/getBreadcrumb";
                if(this.$route.params.id){
                    path=path+"?key="+this.$route.params.id;
                }
                axios.get(path)
                    .then(response=>{
                       this.breadcrumb=(response.data)
                    }).catch(e=>{})
            },
            updateIsPublic(key,isPublic){
                axios.post(this.pathApiCategory+"/update/"+key,{
                    data:{
                        is_public:isPublic,
                    }
                }).then(response=>{
                }).catch(e=>{});
            }
        },
        computed: {
            show_table: function() {
                if (this.$route.name == 'category' || this.$route.name == 'categoryDefault') {
                     return true;
                } else {
                    return false;
                }
            },
            showEditText:function(){
                 if (this.$route.name == 'category'){
                    return true;
                 }
                 return false;
            }
        },
        components: {
            newCategory: New,
            settingSearch: SettingSearch,
            inputSearch:InputText,
        }
    }
</script>

<style lang="scss" module>
    
</style>

<style>
    
</style>