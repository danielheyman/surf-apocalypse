var Vue = require('vue');
Vue.use(require('vue-resource'));
Vue.use(require('vue-validator'));

window.socket = io('http://surf.local:3000');

Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");

$(document).ready(function() {
    require('./draggable');

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
            openProfiles: [],
            unreadPm: [],
        },

        components: {
            'chat': require('./components/chat'),
            'map': require('./components/map'),
            'sites': require('./components/sites'),
            'teams': require('./components/teams'),
            'profile': require('./components/profile'),
            'equip': require('./components/equip')
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
            // Init
            $(this.$$.main).removeClass('hidden');
            var self = this;
            this.coins = window.session_coins;

            // Preloading
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
            
            // Update coins
            socket.on('App\\Events\\UpdatedCoins', function(data) {
                self.coins = data.coins;
            });

            // Profile private messages
            this.unreadPm = window.unread_pm.split(",");
            if(this.unreadPm[0] === "") this.unreadPm = [];
            
            socket.on('App\\Events\\SentPM', function(data) {
                if(data.from == window.session_id) return;
                
                if(self.unreadPm.indexOf(data.from) === -1) self.unreadPm.push(data.from);
                
                self.$broadcast('received-pm', data);
            });

            this.$on('seen-pm', function(id) {
                var index = self.unreadPm.indexOf(id);
                if(index !== -1) self.unreadPm.$remove(index);      
                this.$http.put('/api/pms/seen/' + id);          
                return false;
            });
            
            this.$on('open-profile', function(data) {
                if(!data.id || window.session_id == data.id || this.profileIndex(data.id) != -1)
                    return;

                self.openProfiles.push(data);
                return false;
            });

            // Resizing footer
            $(".footer").mouseenter(function() {
                $(".wrapper").removeClass("small-footer");
            }).mouseleave(function() {
                $(".wrapper").addClass("small-footer");
            });

            
            // Chat system
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
