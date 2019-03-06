<template>
        <transition  name="slide-fade">
            <div class="_modal" v-if="show">
                <div class="_modal_wrapper">
                    <div class="_modal_wrapper_container">
                        <div class="title">
                            <i class="material-icons">create_new_folder</i><span>{{trans('media.new_folder')}}</span>
                            <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                        </div>
                        <div class="body">
                            <div <div class="form">
                                <input-text
                                        v-model="name_folder"
                                        :placeholder="trans('media.name_folder')"
                                        :isLoading="isLoading"
                                        :errors_text="message"
                                        @keyup.enter.once="createFolder()"
                                ></input-text>
                                <button class="_btn" v-on:click.once="createFolder()"><i class="material-icons">add</i>{{trans('media.btn_ok')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
</template>
<script>
    import InputText from '../../components/inputText.vue'
    export default {
        props:["show"],
        data(){
            return {
                name_folder:null,
                message:"",
                isLoading:false
            }
        },
        methods:{
            createFolder(){
                this.$emit('refresh',false);
                axios.post(this.pathApiMedia+"/createFolder",{
                        share:this.$route.params.id,
                        name:this.name_folder
                    }).then(response=>{
                        this.name_folder=null;
                        this.$emit('refresh',true);
                    })
                    .catch(e=>{
                        this.message=this.errors(e.response.data.message);
                    });
                return false
            },
        },
        components:{
            inputText:InputText
        }
    }
</script>
<style lang="scss" scoped module>
</style>