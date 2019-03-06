
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
window.axios=require('axios');
window._=require('lodash');

export const eventBus = new Vue();
Vue.prototype.trans = string => _.get(window.i18n, string);
Vue.prototype.setting = string => _.get(window.i18n_setting, string);

//setting language
import { Settings } from 'luxon';
Settings.defaultLocale =  _.get(window.i18n_setting, 'locale');

/**
 *et Next, we will create a fresh Vue application instance and attach it to
 * the page. Thoppieou may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.prototype.pathApiCore   = "/api/core";
Vue.prototype.pathApiMedia    = "/api/media";
Vue.prototype.pathApiCategory = "/api/category";
Vue.prototype.pathApiPost = "/api/post";

Vue.component('app',require('./App.vue'));
Vue.mixin({
    methods:{
        errors: function(message){
            var errorString = '';
            if((typeof message === "object") && (message !== null) ){
                Object.keys(message).map(function(objectKey, index) {
                    var value = message[objectKey];
                    errorString +=value+"<BR>"

                });
            }else{
                return message;
            }
            return errorString;
        },
        swap:function(input,index_A,index_B){
            const temp = input[index_A];
            input[index_A] = input[index_B];
            input[index_B] = temp;
            return input;
        },
        _loadAction(container){
                if(container){
                    if (container.scrollTop + container.clientHeight == container.scrollHeight) {
                       return true;
                    }
                }
                return false;
        },
    }
});
var app = new Vue({
    el: '#app',
    data: {},
    components: {
    },
});

//temaplate js
