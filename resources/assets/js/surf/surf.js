var done = false;
var images = [
    '../../img/surf/bg.jpg'
];

$.preload(images, 1, function (last) {
    if (!last) return;
    if (done) show();
    done = true;
});

$(".wrapper").preload(function () {
    if (done) show();
    done = true;
});

function show() {
    $(".loading").hide();
    $("body").addClass("bg");
    $(".wrapper").show();
}



var Vue = require('vue');
Vue.use(require('vue-resource'));
Vue.use(require('vue-validator'));

var socket = io('http://surf.local:3000');
window.socket = socket;

// socket.on("global:App\\Events\\SentGlobalMessage", function(message){
//     console.log(message.data);
// });

Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");

new Vue({
    el: '#app',

    data: {
        currentView: 'sites'
    },

    components: {
        'chat': require('./components/chat'),
        'map': require('./components/map'),
        'sites': require('./components/sites')
    },

    methods: {

    },

    ready: function() {

    }
});
