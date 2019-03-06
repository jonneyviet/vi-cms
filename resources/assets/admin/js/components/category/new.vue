<template>
        <transition  name="slide-fade">
            <div class="_modal" v-if="show">
                <div class="_modal_wrapper">
                    <div class="_modal_wrapper_container">
                        <div class="title">
                            <i class="material-icons">playlist_add</i><span>{{trans("category.new")}}</span>
                            <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                        </div>
                        <div class="body">
                            <div class="form">
                                <input-text
                                        v-model="name"
                                        :placeholder="trans('category.name')"
                                        :isLoading="isLoading"
                                        @keyup.enter.one="create()"
                                ></input-text>
                                <lang-select v-model="lang_value"
                                             track-by="name"
                                             label="name"
                                              :options="lang_options"
                                              :close-on-select="true"
                                              :searchable="false"
                                              :show-labels="false"
                                             :allow-empty="false"
                                              v-bind:placeholder="trans('category.language')"
                                             v-if="keyCategory==null"
                                >
                                </lang-select>
                                <type-select v-model="type_value"
                                             track-by="name"
                                             label="name"
                                             :options="type_options"
                                             :close-on-select="true"
                                             :searchable="false"
                                             :show-labels="false"
                                             :allow-empty="false"
                                             v-bind:placeholder="trans('category.type')"
                                             v-if="keyCategory==null"
                                >
                                </type-select>
                                <div :class="[$style.errors]" v-html="message"></div>
                                <button class="_btn" v-on:click.one="create()"><i class="material-icons">add</i>{{trans('category.submit')}}</button>
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
        props:["show","keyCategory"],
        data(){
            return {
                name:null,
                message:null,
                lang_value:null,
                lang_options:[],
                type_value:null,
                type_options:[],
                isLoading:false,
            }
        },
        created:function(){
            this.get_lang_options(this.lang_options);
            this.get_type_options(this.type_options);
        },
        watch:{
        },
        methods:{
            create(){
                this.isLoading=true;
                axios.post(this.pathApiCategory+"/create",{
                    key:this.keyCategory,
                    name:this.name,
                    lang:(this.lang_value) ? this.lang_value.id : null,
                    type:(this.type_value) ? this.type_value.id : null,
                }).then(response=>{
                    this.name=null;
                    this.$emit('refresh',true);
                    this.isLoading=false;
                    if(this.keyCategory){
                        this.$router.push({ name: 'category', params: { id: this.keyCategory }});
                    }else{
                        this.$router.push({ name: 'categoryDefault'});
                    }
                })
                .catch(e=>{
                    this.message=this.errors(e.response.data.message);
                });
                return false;
            },
            get_lang_options(e){
                    const value = this.setting("langList");
                    for (var key in value) {
                        e.push(value[key]);
                    }
            },
            get_type_options(e){
                    const value = this.setting("typeCategory");
                    for (var key in value) {
                        if (value.hasOwnProperty(key)) {
                            e.push(value[key]);
                        }
                    }
            }
        },
        components: {
            inputText:InputText,
            langSelect: Multiselect,
            typeSelect: Multiselect,
        }
    }
</script>
<style lang="scss" scoped module>
    
</style>