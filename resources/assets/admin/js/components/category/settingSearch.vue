<template>
    <transition  name="slide-fade">
        <div class="_modal" v-if="show">
            <div class="_modal_wrapper">
                <div class="_modal_wrapper_container">
                    <div class="title">
                        <i class="material-icons">find_replace</i><span>{{trans("category.setting_search")}}</span>
                        <span :class="[$style.close]" @click="$emit('close')"><i class="material-icons">close</i></span>
                    </div>
                    <div class="body">
                        <div class="form">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
    export default {
        props:["show"],
        data(){
            return {
                isLoading:false,
            }
        },
        watch:{
        },
        methods:{
        },
        components: {
        }
    }
</script>
<style lang="scss" scoped module>

</style>