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

    var Billboard = Vue.extend({
        template: require('./components/billboard.template.html')
    });

    Vue.component('billboard', Billboard);

    new Vue({
        el: '#app',

        data: {
            currentView: 'map',
            loading: true,
            notifications: [],
            coins: 0,
            openProfiles: []
        },

        components: {
            'chat': require('./components/chat'),
            'map': require('./components/map'),
            'sites': require('./components/sites'),
            'teams': require('./components/teams'),
            'profile': require('./components/profile')
        },

        methods: {
            navigate: function(to) {
                this.currentView = to;
            },

            closeProfile: function(id) {
                this.openProfiles.$remove(this.profileIndex(id));
            },

            profileIndex: function(id) {
                for (var i = 0; i < this.openProfiles.length; i++)
                    if (this.openProfiles[i].id == id)
                        return i;

                return -1;
            }
        },

        ready: function() {
            $(this.$$.main).removeClass('hidden');

            var self = this;

            this.coins = window.session_coins;

            var loading = { 
                count: 0, 
                inc: function() { 
                    if(++this.count === 2) self.loading = false; 
                } 
            };

            var images = [
                '../../img/surf/bg.jpg',
                '../../img/surf/bill-bg.jpg',
                '../../img/surf/bill-bg2.jpg'
            ];

            $.preload(images, 3, function(last) {
                if (!last) return;
                loading.inc();
            });

            $(".wrapper").preload(function() { loading.inc(); });
            
            socket.on('App\\Events\\UpdatedCoins', function(data) {
                self.coins = data.coins;
            });

            socket.on('App\\Events\\SentPM', function(data) {
                if(self.profileIndex(data.from) == -1)
                    return;

                self.$broadcast('received-pm', data);
            });

            $(".footer").mouseenter(function() {
                $(".wrapper").removeClass("small-footer");
            }).mouseleave(function() {
                $(".wrapper").addClass("small-footer");
            });

            this.$on('open-profile', function(data) {
                if(!data.id || window.session_id == data.id || this.profileIndex(data.id) != -1)
                    return;

                self.openProfiles.push(data);
                return false;
            });

            this.$on('chat-sent', function(message) {
                self.$broadcast('chat-sent', message);
                return false;
            });

            this.$on('chat-received', function(message) {
                self.$broadcast('chat-received', message);
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
