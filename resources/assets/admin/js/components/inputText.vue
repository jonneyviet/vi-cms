<template>
        <div :class="[$style.input_text,(showIcon) ? $style.input_icon : '']">
            <input type="text"
                   v-model="inputVal"
                   v-on="$listeners"
                   :placeholder="placeholder"
                   :readonly="readonly"
                   :class="{ active: isActive}"
            >
            <i class="material-icons" v-if="showIcon">{{material_icon}}</i>
            <div :class="$style.lds_ring" v-if="isLoading"><div></div><div></div><div></div><div></div></div>
            <p :class="$style.errors" v-if="errors_text!=null" v-html="errors_text"></p>
        </div>
</template>
<script>
    export default{
        props:['placeholder','value',"readonly",'material_icon',"isLoading","errors_text"],
        data(){
            return {
                inputVal: this.value,
                isActive:true
            }
        },
        mounted() {
            const vm = this
        },
        watch:{
            inputVal(val) {
                this.$emit('input', val);
            },
            value(e){
                this.inputVal=e;
            },
            errors_text(e){
                this.errors_text=e;
            }

        },
        created:function(){
            this.inputVal=this.value;
        },
        methods:{

        },
        computed:{
            showIcon:function(){
                if(!this.isLoading){
                    if(this.material_icon!=null){return true}return false
                }
            }
        },
        components: {

        },
    }
</script>
<style lang="scss" scoped module>
    @import "../../sass/variables";
    input[type="text"]{
        width: 100%;
        height: 35px;
        padding: 0px 10px;
        color: $text-color;
        border:solid 1px $input-border;
        &:focus{
            box-shadow: none;
            outline-offset: 0px;
            outline:none;
            border:solid 1px $input-border-focus;
        }
        &:-moz-read-only { /* For Firefox */
            &:focus{
                box-shadow: none;
                outline-offset: 0px;
                outline:none;
                border: solid 1px $input-border-focus;
            }
        }
        &:read-only {
            &:focus{
                box-shadow: none;
                outline-offset: 0px;
                outline:none;
                border: solid 1px $input-border-focus;
            }
        }
    }
    .input_text{
        width: 100%;
        position: relative;
        margin-bottom: 10px;
    }
    .errors{
        color: red;
        margin: 10px 0px;
        font-size: 14px;
    }
    .input_icon{
        >input{
            padding-right: 35px;
            &:focus + i{
                color: $input-border-focus;
            }
        }
        >i{
            position: absolute;
            top: 5px;
            right: 5px;
            color: $input-border;
        }
    }
    .lds_ring {
        display: inline-block;
        position: absolute;
        width: 35px;
        height: 35px;
        right: 0px;
        top: 0px;
    }
    .lds_ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 23px;
        height: 23px;
        margin: 6px;
        border: 2px solid $input-border;
        border-radius: 50%;
        animation: lds_ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: $input-border transparent transparent transparent;
    }
    .lds_ring div:nth-child(1) {
        animation-delay: -0.45s;
    }
    .lds_ring div:nth-child(2) {
        animation-delay: -0.3s;
    }
    .lds_ring div:nth-child(3) {
        animation-delay: -0.15s;
    }
    @keyframes lds_ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }


</style>