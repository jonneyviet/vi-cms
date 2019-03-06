<template>
	<div :class="[$style.search]">
		<input type="text" name="serach" v-bind:placeholder="langData.search" v-model="searchQuery">
	</div>
</template>
<script>
	 export default{
        props:["recycleFind","langData"],
        data(){
        	return {
		        results: [],
                searchQuery: null,
        	}
        },
        watch:{
        	searchQuery: _.debounce(function(){
                this.searchKey(this.searchQuery);
        	},1000),

        },
        methods:{
        	searchKey(key){
                if(key==""){
                    this.$emit('refreshFind',true);
                }else{
                    if(!this.recycleFind){
                        var path=this.pathApiMedia+"/search?value="+key
                    }else{
                        var path=this.pathApiMedia+"/search?rcy=true&value="+key
                    }
                    axios.get(path)
                        .then(response=>{
                            this.results=response.data.folders.data;
                            this.$emit("resultSearchFolder",this.results);
                            this.$emit("resultSearchFile",response.data.files.data);
                            this.$emit("resultNextPage",response.data.files.next_page_url);
                        })
                        .catch(e=>{

                        })
                }
            },
            loadMore(){
                var container = this.$el.querySelector("#infinite_list");
                if(container){
					if (container.scrollTop + container.clientHeight == container.scrollHeight) {
						this.getData(this.results);
					}
                }
            },
			getData(results){
                var path = this.next_page;
                if (path != null) {
                    axios.get(path)
                        .then(response => {
                            $.each(response.data.folders.data, function(key, value) {
                                results.push(value);
                            });
                            this.next_page = response.data.next_page_url;
                            this.isLoading = false;
                        })
                        .catch(e => {
                        });
                }
			},

        }

    }
</script>
<style lang="scss" scoped module>
    .search{
        input{
            width: 320px;
            height: 35px;
            box-shadow: none;
            outline: none;
            border-radius: 30px;
            padding: 0px 10px;
            border:none;
        }
    }
</style>