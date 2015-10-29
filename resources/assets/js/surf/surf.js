var Vue = require('vue');
Vue.use(require('vue-resource'));
Vue.use(require('vue-validator'));

var socket = io('http://surf.local:3000');
window.socket = socket;

// socket.on("global:App\\Events\\SentGlobalMessage", function(message){
//     console.log(message.data);
// });

Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");

$(document).ready(function() {

    new Vue({
        el: '#app',

        data: {
            currentView: 'teams',
            loading: true,
            notifications: [],
            coins: 0
        },

        components: {
            'chat': require('./components/chat'),
            'map': require('./components/map'),
            'sites': require('./components/sites'),
            'teams': require('./components/teams')
        },

        methods: {
            navigate: function(to) {
                this.currentView = to;
            }
        },

        ready: function() {
            $(this.$$.main).removeClass('hidden');

            var self = this;

            this.coins = window.session_coins;

            var doneLoading = false;

            var images = [
                '../../img/surf/bg.jpg',
                '../../img/surf/bill-bg.jpg',
                '../../img/surf/bill-bg2.jpg'
            ];

            $.preload(images, 1, function(last) {
                if (!last) return;
                if (doneLoading) self.loading = false;
                doneLoading = true;
            });

            $(".wrapper").preload(function() {
                if (doneLoading) self.loading = false;
                doneLoading = true;
            });

            socket.on('App\\Events\\UpdatedCoins', function(data) {
                self.coins = data.coins;
            });

            $(".footer").mouseenter(function() {
                $(".wrapper").removeClass("small-footer");
            }).mouseleave(function() {
                $(".wrapper").addClass("small-footer");
            });

            this.$on('chat-sent', function(message) {
                this.$broadcast('chat-sent', message);
                return false;
            });

            this.$on('chat-received', function(message) {
                this.$broadcast('chat-received', message);
                return false;
            });

            this.$on('notification', function(message) {
                self.notifications.push(message);
                setTimeout(function() {
                    self.notifications.shift();
                }, 5000);

                return false;
            });
        }
    });

});
