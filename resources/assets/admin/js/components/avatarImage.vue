<template>
	<div>
	<div :class="[$style.content_image]" @click="show=true" v-bind:style="{'min-height': heightAvatar + 'px' }">
		<img v-bind:src="image" alt="">
	</div>
	<transition  name="slide-fade">
		<div class="_modal" v-show="show">
			<div class="_modal_wrapper">
				<div class="_modal_wrapper_container _modal_wrapper_container_sm">
					<div class="title">
						<i class="material-icons">add_photo_alternate</i><span>{{name_title}}</span>
						<span :class="[$style.close]" @click="show=!show"><i class="material-icons">close</i></span>
					</div>
					<div class="body">
						<div id="crop_image"></div>
						<ul :class="$style.list_btn">
							<li>
								<label class="_btn" :class="$style.btn_uppload">
									<input type="file" id="upload" value="Choose a file" accept="image/*" @change="onFileChange">
									<i class="material-icons">add_to_photos</i><span>{{trans('media.choice')}}</span>
								</label>
							</li>
							<li v-if="canUpload"><button class="_btn" @click.once="uploadFile()" ><i class="material-icons">cloud_upload</i><span>{{name_button}}</span></button></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</transition>
	</div>
</template>
<script>
	import { Croppie } from 'croppie/croppie';

    export default {
    	props:['route','imgUrl','imgName','urlUpload','name_title','viewport_with','viewport_height'],
    	mounted(){
    	    this.$on('imageUploaded',function(imageData){
				this.image=imageData
                if(this.croppie) {
                    this.croppie.destroy()
                }
                this.modalVisible=true;
                this.setUpCroppie(imageData)
			});
           // this.image = this.imgUrl;
            //this.setUpCroppie();
        },
		watch:{
            imgUrl:function(e){
    	        this.image=e;
    	        if(e!=null){
                	this.setUpCroppie(e)
                }
			},
            image:function(e){
                this.image=e;
			}
		},
        data(){
        	return {
        	    show:false,
        		croppie:null,
                modalVisible: false,
        		image:null,
                name_button:this.trans('media.uploads')
        	}
        },
        methods:{
            uploadFile () {
                this.name_button =this.trans('media.uploading')
                this.croppie.result({
                    type: 'canvas',
                    size: 'viewport'
                }).then((response) => {
                    this.image = response
                    axios.post(this.urlUpload,
						{
						    image: this.image,
							name:this.imgName,
                            key: this.route,
						})
                        .then((response) => {
                            this.image=response.data.url;
                            this.name_button =this.trans('media.uploads')
                            this.modalVisible=false;
                            this.show=false;
                        })
                });
                return false;
            },
        	setUpCroppie(){
                    var el = document.getElementById('crop_image');
                    const viewport_height = this.viewport_height;
                    const viewport_width = this.viewport_with;
                    const boundary_height = this.viewport_height;
                    this.croppie = new Croppie(el, {
                        enableExif: true,
                        viewport: {width: viewport_width, height: viewport_height},
                        boundary: {width: viewport_width, height: boundary_height},
                        showZoomer: true,
                    });
                    this.croppie.bind({
                        url: this.image
                    })
        	},
            onFileChange(e){
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
			},
            createImage(file){
                var image = new Image();
                var reader = new FileReader();
                var vm = this;
                reader.onload = (e) => {
                    vm.image = e.target.result;
                    vm.$emit('imageUploaded',e.target.result);
                };
                reader.readAsDataURL(file);
			}
        },
		computed:{
            canUpload:function(){
                if(this.modalVisible){
                    return true;
				}
                return false;
			},
			heightAvatar:function(){
                if(this.image!=null){
                    return 142;
				}else {
                    return 200;
                }
			}
		}
    }
</script>
<style lang="scss" module>
	@import "../../sass/variables";
	.content_image{
		min-height: 100px;
		width: 100%;
		max-width: 300px;
		background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAQAAAD9CzEMAAAA6klEQVR4Ae3WAQYCQRSH8UmIkEIpEBBCdwg6xxLQKbpEQChAACUE3SEAkaANlQQK6SsoPRp2Zyeh9wPg+fgPa41SvlFmwh5XeyaUjR1FQpLaUrAHBvgwtAd2+LCzBzzRgAYi04AGNNBn8M3AggxZlt8KHKmaB+qckwf69JButF4XgVtATpFmzruuuBm5BeQUOVY8zUiJmyxLt4CcosYJgDV5cSJeIm5ATtHkyoXGx7sgRkBMIdChbb0cxQ3IKezkS0QOiCkikC8RIdA2Dgj+4Wu6wYfND3/fS4QkFVI0dlSYcsDVgTEV45lSd1/M0dCnEPtZAAAAAElFTkSuQmCC
") no-repeat;
		background-position: center;
		background-color: $text-color;
		position: relative;
		overflow: hidden;
		vertical-align: middle;
		margin-bottom: 10px;
		img{
			max-width: 100%;
		}
		&:hover{
			opacity: 0.7;
			cursor: pointer;
		}
	}
	.list_btn{
		list-style: none;
		margin: 0px;
		padding: 0px;
		li{
			display: inline-block;
			label.btn{
				width: 100%;
			}
		}
	}
	label.btn{
		cursor: pointer;
		margin: 0px;
		display: flex;
		justify-content: center;
		vertical-align: middle;
		box-sizing: border-box;
		width: 100%;
		height: 100px;
		border-radius: 0px;
		border:none;
		input[type="file"]{
			display: none;
		}
	}
	label.btn_uppload{
		cursor: pointer;
		margin: 0px;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-align: center;
		-ms-flex-align: center;
		align-items: center;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		input[type="file"]{
			display: none;
		}
	}
</style>