<template>
    <div class="search search_folder">
        <div class="input">
            <input type="text" name="serach" placeholder="Search folder here..." v-model="searchQuery">
        </div>
        <div id="folder_result" class="result" v-if="isOpen">
            <div class="title" v-html="msg"></div>
            <ul v-if="choice" id="infinite-list" @scroll="loadMore()">
                <li v-for="result in results">
                    <a href="javascript:;">
                        <span class="name">{{result.name}}</span>
                        <span class="path">{{result.path}}</span>
                        <div class="checkbox">
                             <input type="radio" name="choice" v-bind:value="result.key" v-model="selectFolderMove">
                             <span></span>
                        </div>
                    </a>
                </li>
            </ul>
            <ul v-else id="infinite_list" @scroll="loadMore()">
                <li v-for="result in results"  @click="isOpen=false">
                    <router-link   :to="{ name: result.route, params: { id: result.key }}">
                        <span class="name">{{result.name}}</span>
                        <span class="path">{{result.path}}</span>
                    </router-link>
                </li>
            </ul>
            
        </div>
    </div>
</template>
<script>
    import _ from 'lodash';
     import axios from 'axios';
     export default{
        props:['choiceOne','isOpenParent'],
        data(){
            return {
                isOpen:false,
                results: [],
                searchQuery: null,
                isLoading: false,
                total:null,
                msg:null,
                showInside: false,
                next_page:null,
                choice:this.choiceOne,
                selectFolder:null,
                selectFolderMove:null,
            }
        },
        watch:{
            isOpenParent:function(e){
                this.isOpen=e&&this.isOpenParent
            },
            searchQuery: _.debounce(function(){
                if(this.searchQuery.length<=0){
                    this.isOpen=false;
                    this.isLoading=false;
                }else{
                    this.searchKey(this.searchQuery);
                }
            },1000),
            selectFolderMove:function(){
                this.$emit('folderDestination', this.selectFolderMove);
            }

        },
        methods:{
            searchKey(key,page=false){
                this.isLoading=true;
                this.isOpen=true;
                this.$emit("updateSearch",this.isOpen);
                var path="api/search?value="+key
                axios.get(path)
                    .then(response=>{
                        this.results=response.data.data;
                        this.total=response.data.total;
                        this.msg=response.data.msg;
                        this.next_page=response.data.next_page_url;
                        this.isLoading=false;
                    })
                    .catch(e=>{

                    })
            },
            loadMore(){
                var container = this.$el.querySelector("#infinite_list");
                if(container){
                    if (container.scrollTop + container.clientHeight == container.scrollHeight) {
                        this.getData(this.results);
                    }
                }
            },
            getData(results){
                this.isLoading = true;
                var path = this.next_page;
                if (path != null) {
                    axios.get(path)
                        .then(response => {
                            //this.results.push(response.data.data);
                            $.each(response.data.data, function(key, value) {
                                results.push(value);
                            });
                            this.next_page = response.data.next_page_url;
                            this.isLoading = false;
                        })
                        .catch(e => {
                            this.isLoading = false;
                        });
                } else {
                    this.isLoading = false;
                }
            },

        }

    }
</script>