<template>
    <div >
        <master-top v-if="masterTopshow"></master-top>
        <div class="page">
            <master-menu v-if="masterMenushow" ></master-menu>
            <div class="container-fluid clearfix">
                    <div :class="[$style.mainboard]">
                        <transition name="fade">
                        <router-view class="view"></router-view>
                        </transition>
                    </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import MasterTop from './Top.vue';
    import MasterMenu from './Menu.vue';

    import Media from './components/media/index.vue';
    //category
    import CategoryText from './components/category/text.vue';
    import CategoryList from './components/category/list.vue';
    //post
    import PostList from './components/post/list.vue';
    import PostText from './components/post/text.vue';

    const configRouter=[
        {
            name:"mediaDefault",
            path:"/admin/media/",
            component: Media,
        },
        {
            name:"media",
            path:"/admin/media/:id",
            component: Media,
        },
       // category
        {
            name:"categoryDefault",
            path:"/admin/category",
            component: CategoryList,
        },
        {
            name:"category",
            path:"/admin/category/:id",
            component: CategoryList,
            children:[
                {
                    path: 'text',
                    name: 'categoryText',
                    component: CategoryText,
                }
            ]
        },
        //post
        {
            name:"postDefault",
            path:"/admin/post",
            component: PostList,
            children:[
                {
                    name:"postText",
                    path:"/admin/post/:id",
                    component: PostText,
                    children:[

                    ]
                },
            ]
        },
    ];
    var router = new VueRouter({
        mode: 'history',
        routes:configRouter,
    });
    export default {
        data(){
            return {

            }
        },
        mounted(){
           
        },
        watch:{

        },
        created:function () {

        },
        methods:{
        },
        computed:{
            masterTopshow: function(){
                if(this.$route.name=='mediaDefault'||this.$route.name=='media'){
                    return false;
                }else{
                    return true;
                }
            },
            masterMenushow:function(){
                if(this.$route.name=='mediaDefault'||this.$route.name=='media'){
                    return false;
                }else{
                    return true;
                }
            },
        },
        components: {
            masterTop:MasterTop,
            masterMenu:MasterMenu,
            appMedia: Media,
        },
        router
    }



</script>

<style lang="scss" scoped module>
    .mainboard {
        
    }
</style>