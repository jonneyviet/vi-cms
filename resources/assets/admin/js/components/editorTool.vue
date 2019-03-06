<template>
    <div>
        <editor v-model="html" api-key="e62dwx662k7m9tvzx07iq46n55rxog846xx7hdvrv6ck9iyi" :init="option"></editor>
    </div>
</template>
<script>
    //e62dwx662k7m9tvzx07iq46n55rxog846xx7hdvrv6ck9iyi
    import Editor from '@tinymce/tinymce-vue';

    export default {
        props: ['htmlEditor'],
        data() {
            return {
                html:this.htmlEditor,
                showManage:false,
                option:{
                    plugins: 'image,media,link',
                    height : 278,
                    image_advtab: true,
                    images_upload_url:'test',
                    automatic_uploads: true,
                    file_browser_callback: function(field_name, url, type, win){
                        var filebrowser = "/admin/media";
                        var width = window.innerWidth-30;
                        var height = window.innerHeight-60;
                        if(width > 1800) width=1800;
                        if(height > 1200) height=1200;
                        if(width>600){
                            var width_reduce = (width - 20) % 138;
                            width = width - width_reduce + 10;
                        }
                        tinymce.activeEditor.windowManager.open({
                            title : "Media",
                            width : width,
                            height : height,
                            url : filebrowser
                        }, {
                            window : win,
                            input : field_name
                        });
                        return false;
                    },
                },
            }
        },
        mounted() {

        },
        created:function(){
        },
        watch:{
            html(val) {
                this.$emit('updateText', val);
            },
            htmlEditor(e){
                this.html=e;
            },
            test(){
                console.log(test);
            }
        },
        methods:{
            myFileBrowser:function(){
                console.log('test');
            }
        },
        components:{
            editor: Editor
        }
    }
</script>