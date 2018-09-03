
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Bus = new Vue();

// import VueChatScroll from 'vue-chat-scroll'
//
// Vue.use(VueChatScroll);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('users-component', require('./components/UsersComponent.vue'));

Vue.component('tabs-component', require('./components/TabsComponent.vue'));

// Vue.component('tab-component', require('./components/TabComponent.vue'));

Vue.component('chat-component', require('./components/ChatComponent.vue'));

Vue.component('message-component', require('./components/MessageComponent.vue'));


Vue.component('rooms-component', require('./components/RoomsComponent.vue'));

Vue.component('room-component', require('./components/RoomComponent.vue'));

Vue.component('time-ago-component', require('./components/TimeAgoComponent.vue'));


Vue.component('create-room-component', require('./components/CreateRoomComponent.vue'));

// var DZ = require('./components/RegisterComponent.js');

Vue.component('profile-component', require('./components/ProfileComponent.vue'));
// require('./components/ProfileComponent.js');

// Vue.component('upload-file-component', require('./components/UploadFileComponent.vue'));
Vue.component('register-component', require('./components/RegisterComponent.vue'));

// import { Dropzone } from "./components/RegisterComponent.js";


// Vue.component('scrollbar', require('vue2-scrollbar'));


// Vue.component('logout-component', require('./components/LogoutComponent.vue'));

// window.addEventListener('beforeunload', function (event) {
//     console.log(document.getElementById('logout-form'));
//     setInterval(function () {document.getElementById('logout-form').submit();}, 5000);
//     console.log('----------------leaving---------------');
// }, false);

const app = new Vue({
    el: '#app'
});