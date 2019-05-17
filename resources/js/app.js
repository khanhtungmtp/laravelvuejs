
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// thư viện thời gian
import moment from 'moment';
// class gate.js
import gate from './gate';
// gate( bên class gate có contructor), $gate là tên của prototype đặt gì cũng được
Vue.prototype.$gate = new gate(window.user);

// thư viện vform cho vuejs đẹp
import { Form, HasError, AlertError } from 'vform';
window.Form = Form;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

// sử dụng thư viện laravel vue pagination
Vue.component('pagination', require('laravel-vue-pagination'));

import VueRouter from 'vue-router'
// thư viện thanh trạng thái
import VueProgressBar from 'vue-progressbar'

// thư viện hiện thông báo cảnh báo khi xóa
import swal from 'sweetalert2'
window.swal= swal;

const options = {
    color: '#bffaf3',
    failedColor: '#874b4b',
    thickness: '5px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'top',
    inverse: false
};

const toast = swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});
window.toast= toast;

Vue.use(VueProgressBar, options)

Vue.use(VueRouter);

// tạo filter , giống define trong php
Vue.filter('textUppercase',function(text){
    // viết hoa chữ cái đầu tiên
    return text.charAt(0).toUpperCase(0) + text.slice(1);
});

Vue.filter('carbonDate',function (created_at) {
    return moment(created_at).startOf('hour').fromNow();
})

const routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default },
    { path: '/profile',   component: require('./components/Profile.vue').default },
    { path: '/developer',   component: require('./components/Developer.vue').default },
    { path: '/users',     component: require('./components/Users.vue').default },
    { path: '*',     component: require('./components/NotFound.vue').default }
];

// tạo xe bus phát hiện thay đổi all user trong database
window.EventBus = new Vue();

const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'not-found',
    require('./components/NotFound.vue').default
);

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data:{
        search:''
    },
    methods:{
        searchit(){
            // phát thông báo cho user.vue biết đang search
            EventBus.$emit('searching');
        }
    }
});
