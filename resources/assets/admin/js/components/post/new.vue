<template>
        <transition  name="slide-fade">
            <div class="_modal" v-if="show">
                <div class="_modal_wrapper">
                    <div class="_modal_wrapper_container">
                        <div class="title">
                            <i class="material-icons">playlist_add</i><span>{{trans('post.new')}}</span>
                            <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                        </div>
                        <div class="body">
                            <div class="form">
                                <input-text
                                        v-model="name"
                                        :placeholder="trans('post.name')"
                                        :isLoading="isLoading"
                                        @keyup.enter.one="create()"
                                ></input-text>
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
                                <div :class="[$style.errors]" v-html="message"></div>
                                <button class="_btn" v-on:click.one="create()"><i class="material-icons">add</i>{{trans('post.submit')}}</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </transition>
</template>
<script>
    import Multiselect from 'vue-multiselect';
    import InputText from '../../components/inputText.vue'
    export default {
        props:["show"],
        data(){
            return {
                message:null,
                name:null,
                category:[],
                selectedCategory:[],
                isLoading: false
            }
        },
        watch:{
            show:function(e){
                if(e){
                    this.asyncFindCategory();
                }
                this.show=e;
            }
        },
        created(){
        },
        methods:{
            asyncFindCategory(query=null){
                this.isLoading=true;
                var path=this.pathApiCategory+"/search";
                if(query!=null){
                    path=path+"?keyword="+query;
                }
                _.debounce(axios.get(path)
                    .then(response=>{
                        this.category=response.data.data;
                        this.selectedCategory=this.category[0];
                        this.isLoading=false;
                    }).catch(e=>{})
                );
                    return false;
            },
            create(){
                axios.post(this.pathApiPost+"/create",{
                    name:this.name,
                    category:this.selectedCategory,
                }).then(response=>{
                    this.name=null;
                    this.$emit('refresh',true);
                    this.$router.push({ name: 'postDefault'});

                })
                .catch(e=>{
                    this.message=this.errors(e.response.data.message);
                });
                return false;
            }
        },
        components: {
            categorySelect: Multiselect,
            inputText:InputText,
        }
    }
</script>
<style lang="scss" scoped module>
    .errors{
        color: red;
        -ms-word-break: break-word;
        word-break: break-word;
        text-align: left;
    }
</style>