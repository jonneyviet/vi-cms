<template>
        <div>
            <div :class="[$style.checkbox]">
                <input type="checkbox" name="choice" v-bind:value="item.share" v-model="folders.select"><label></label>
            </div>
            <router-link :to="{ name: 'media', params: { id: item.share }}">
            <i class="material-icons">folder</i> 
            <div :class="[$style.name_file]">{{item.name}}</div>
            </router-link>
        </div>
</template>
<script>
    export default {
        props:["show"],
        data(){
            return {
                
            }
        },
        methods:{
            
        },
        components:{
            
        }
    }
</script>
<style lang="scss" scoped module>
</style>