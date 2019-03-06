<template>
    <div>
        seo
    </div>
</template>
<script>
    export default{
        data(){
            return {

            }
        },
        watch: {

        },
        created:function(){

        },
        methods:{

        },
        computed:{

        },
        components: {

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
    .breadcrumb_folder{
        padding: 0px;
        margin: 0px;
        list-style: none;
        li{
            display: inline-block;
            a{
                color: $text-color;
                text-decoration: none;
                display: flex;
                align-items: center;
                i{
                    margin-right: 5px;
                }
            }
            &:nth-child(n+2){
                a{
                    position: relative;
                }
            }
        }
    }
    .panel_heading{
        padding: 5px 10px;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 44px;
        box-sizing: border-box;
        .title{
            .icon{
                color: $text-color;
            }
        }
    }
    .panel_tag{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #e9ebee;
    }
    .panel_body{
        display: flex;
        padding: 10px;
    }
    .content_space{
        height: calc(100% - 103px);
        overflow: scroll;
    }
    .icon{
        display: flex;
        align-items: center;
        i{
            margin-right: 5px;
        }
        span{
            font-weight: bold;
        }
    }
    .btn{
        display: flex;
        align-items: center;
        border:solid 1px $border-color;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 50px;
        color: $text-color;
        i{
            margin-right: 5px;
        }
        &_group{
            list-style: none;
            margin:0px;
            padding: 0px;
            li{
                display: inline-block;
            }
        }
    }
    .icon_file{
        min-height: 50px;
    }
    .more{
        width: 50px;
    }
</style>
