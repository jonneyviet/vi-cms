<template>
	<div>
	    <transition name="menu">
	        <div id="menu" class="menu" v-bind:class="{'active': show}">
				<div :class="$style.title" class="_title">
					<a href="javascript:;" @click="showMenu"><i class="material-icons">apps</i></a>
				</div>
				<div :class="$style.list" class="_list">
					<div :class="$style.title_list"><span>Basic</span></div>
					<ul>
						<li><router-link :to="{ name: 'categoryDefault'}"><i class="material-icons">filter_none</i><span>Category</span></router-link></li>
						<li><router-link :to="{ name: 'postDefault'}"><i class="material-icons">border_color</i><span>Post</span></router-link></li>
						<li><a href="javascipt:;"><i class="material-icons">insert_drive_file</i><span>Page</span></a></li>
					</ul>
					<div :class="$style.title_list"><span>Advance</span></div>
					<ul>
						<li><router-link :to="{ name: 'mediaDefault'}"><i class="material-icons">photo_library</i><span>Media</span></router-link></li>
						<li><router-link :to="{ name: 'media',params: { id: 'recycle' }}"><i class="material-icons">delete</i><span>Recycle bin</span></router-link></li>
					</ul>

				</div>
	        </div>
	    </transition>
    </div>
</template>
<script>
	import axios from 'axios';
	export default{
	    props:['dataLang'],
        data(){
            return {
            	show:false,
            }
        },
        methods:{
        	showMenu: function(){
        		this.show = !this.show
        	}
        }
    }
</script>
<style lang="scss" scoped module>
    
    .title{
    	width: 100%;
    	height: 44px;
    	display: flex;
        align-items: center;
        padding: 0px 10px;
        box-sizing: border-box;
        justify-content: flex-end;
        text-align: center;
        a{
        	color: #636b6f;
        	&:hover,&:focus{
        		color: #636b6f;
        	}
        }
    	i{
    		font-size: 28px;
			color: #D32F2F;
    	}
    }
	.list{
		overflow-y: scroll;
		height: calc(100% - 45px);
		width: auto;
		&::-webkit-scrollbar {
			width: 0px;
		}

		&::-webkit-scrollbar-track {
			-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			border-radius: 0px;
		}
		&::-webkit-scrollbar-thumb {
			border-radius: 0px;
			-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
		}
		.title_list{
			height: 38px;
			display: flex;
			align-items: center;
			padding: 0px 10px;
			border-top: 1px solid #e9ebee;
			text-transform: uppercase;
			font-size: 14px;
			color: #636b6f;
		}
		ul {
			padding: 0px;
			margin: 0px;
			list-style: none;

		}
		li{
			background-color: #f5f5f5;
			a{
				background-color: #f5f5f5;
				display: flex;
				align-items: center;
				color: #636b6f;
				text-align: center;
				position: relative;
				text-decoration: none;
				&:hover,&:focus{
					color: #636b6f;
					background-color: hsl(0, 0%, 92%);
				}
				i{
					display: inline-block;
					font-size: 21px;
					min-width:60px ;
				}
				span{
					display: inline-block;
					position: relative;
					line-height: 38px;
					z-index: 99;
					color: #110f37;
					white-space: nowrap;
				}
			}
		}
	}
</style>