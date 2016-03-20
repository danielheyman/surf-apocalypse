module.exports = {

    template: require('./chat.template.html'),

    data: function() {
        return {
            messages: {
                "global": [{
                    name: "System",
                    text: "Welcome to SurfApocalypse! The global chat will be seen by all players online."
                }],
                "map": [{
                    name: "System",
                    text: "The map chat will only be seen by players surfing the same map."
                }]
            },
            message: '',
            channels: ['global', 'map'],
            unseen: {'global': false, 'map': false},
            channel: 'global'
        };
    },

    methods: {
        sendMessage: function() {

            if (!this.message) return;

            if (this.channel == 'map') this.$dispatch('chat-sent', this.message);

            socket.emit("chat", {
                c: this.channel,
                m: this.message
            });

            this.addMessage({
                c: this.channel,
                n: window.session_name,
                m: this.message
            });

            this.message = "";
        },

        addMessage: function(data) {
            var messages = $(this.$$.messages);

            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() + 1 >= messages.prop('scrollHeight');

            this.messages[data.c].push({
                name: data.n,
                text: data.m,
                id: data.i
            });

            var self = this;

            if(data.c != this.channel)
                this.unseen[data.c] = true;

            if (scrolledToBottom && data.c == this.channel) {
                this.$nextTick(function () {
                    messages.animate({
                        scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                    }, 100, function() {
                        self.removeOldMessages(data.c);
                    });
                });
            }

            if (data.c == "map" && data.i) {
                this.$dispatch('chat-received', {
                    text: data.m,
                    id: data.i
                });
            }
        },

        removeOldMessages: function(channel) {
            if (this.messages[channel].length > 20) {
                this.messages[channel] = this.messages[channel].slice(-20);
            }
        },

        toggleChannel: function(channel) {
            this.channel = channel;

            this.unseen[channel] = false;

            var messages = $(this.$$.messages);

            this.$nextTick(function() {
                messages.animate({
                    scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                }, 100);
            });
        },

        currentChannel: function(channel) {
            return this.channel == channel;
        },

        openProfile: function(event, name, id) {
            event.preventDefault();

            if(id) this.$dispatch('open-profile', {name: name, id: id});
        }
    },

    ready: function() {
        socket.on('chat', this.addMessage);
    }
};
