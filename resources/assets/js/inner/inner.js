let Vue = require('vue');
Vue.use(require('vue-resource'));
Vue.use(require('vue-validator'));

window.socket = io('http://surf.local:3000');

Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");

window.store = {
    items: {
        desc: require('./items/desc.js'),
        decimal: require('./items/decimal.js')
    },
    user: user,
    unread_pm: unread_pm
};

$(document).ready(function() {
    (require('./draggable'))(Vue);

    const Billboard = Vue.extend({
        template: require('./components/billboard.template.html')
    });

    Vue.component('billboard', Billboard);

    new Vue({
        el: '#app',

        data: {
            currentView: 'map',
            loading: true,
            notifications: [],
            openProfiles: [],
            unreadPm: [],
            items: [],
            shared: window.store
        },

        components: {
            'chat': require('./components/chat'),
            'map': require('./components/map'),
            'sites': require('./components/sites'),
            'teams': require('./components/teams'),
            'profile': require('./components/profile'),
            'equip': require('./components/equip'),
            'health': require('./components/health')
        },

        methods: {
            navigate: function(to) {
                this.currentView = to;
            },

            closeProfile: function(id) {
                this.openProfiles.splice(this.profileIndex(id), 1);
            },

            profileIndex: function(id) {
                for (let i = 0; i < this.openProfiles.length; i++)
                    if (this.openProfiles[i].id == id)
                        return i;

                return -1;
            }
        },

        ready: function() {
            // Init
            $(this.$els.main).removeClass('hidden');

            // Item loading and updating
            const items = this.shared.user.items;
            let new_item_list = {};
            Object.keys(items).forEach((key,index) => {
                if(this.shared.items.decimal[key]) {
                    const split = this.shared.items.decimal[key];
                    new_item_list[key + "/" + split[0]] = parseInt(items[key]);
                    new_item_list[key + "/" + split[1]] = Math.round(items[key] * 100) % 100;
                } else {
                    new_item_list[key] = parseFloat(items[key]);
                }
            });
            this.shared.user.items = new_item_list;
            
            socket.on('App\\Events\\UpdatedItem', data => {
                if(this.shared.items.decimal[data.type]) {
                    const split = this.shared.items.decimal[data.type];
                    this.shared.user.items[data.type + "/" + split[0]] = parseInt(data.amount);
                    this.shared.user.items[data.type + "/" + split[1]] = Math.round(data.amount * 100) % 100;
                } else {
                    this.shared.user.items[data.type] = parseFloat(data.amount);
                }
            });
            
            // Preloading
            let count = 0;
            const inc = () => {
                if(++count == 2) this.loading = false;
            };

            const images = [
                '../../img/surf/bg.jpg',
                '../../img/surf/bill-bg.jpg',
                '../../img/surf/bill-bg2.jpg'
            ];

            $.preload(images, 3, last => {
                if (!last) return;
                loading.inc();
            });

            $(".wrapper").preload(() => { loading.inc(); });
            
            // Profile private messages
            this.unreadPm = this.shared.unread_pm.split(",");
            if(this.unreadPm[0] === "") this.unreadPm = [];
            
            socket.on('pm', data => {
                if(data.from !== this.shared.user.id && this.unreadPm.indexOf(data.from) === -1) this.unreadPm.push(data.from);
                
                this.$broadcast('received-pm', data);
            });

            this.$on('seen-pm', id => {
                const index = this.unreadPm.indexOf(id);
                if(index !== -1) this.unreadPm.splice(index, 1);
                this.$http.put('/api/pms/seen/' + id);          
                return false;
            });
            
            this.$on('open-profile', data => {
                if(!data.id || this.shared.user.id == data.id || this.profileIndex(data.id) != -1)
                    return;

                this.openProfiles.push(data);
                return false;
            });

            // Resizing footer
            $(".footer").mouseenter(function() {
                $(".wrapper").removeClass("small-footer");
            }).mouseleave(function() {
                $(".wrapper").addClass("small-footer");
            });

            
            // Chat system
            this.$on('chat-sent', message => {
                this.$broadcast('chat-sent', message);
                return false;
            });

            this.$on('chat-received', message => {
                this.$broadcast('chat-received', message);
                return false;
            });


            this.$on('notification', message => {
                this.notifications.push(message);
                
                setTimeout(() => {
                    this.notifications.shift();
                }, 5000);

                return false;
            });
        }
    });

});
