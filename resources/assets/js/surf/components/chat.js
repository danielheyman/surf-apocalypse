module.exports = {

    template: require('./chat.template.html'),

    data: function() {
        return {
            messages: {
                "global": [{
                    name: "System",
                    text: "Welcome to SurfApocalypse! The global chat will be seen by all users online."
                }],
                "map": [{
                    name: "System",
                    text: "The map chat will only be seen by users surfing the same map."
                }]
            },
            message: '',
            channels: ['global', 'map'],
            channel: 'global',
            channelPicker: false
        };
    },

    methods: {
        sendMessage: function() {

            if (!this.message) return;

            if(this.channel == 'map')
                this.$dispatch('chat-sent', this.message);

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
                text: data.m
            });

            var self = this;

            if (scrolledToBottom && data.c == this.channel) {
                setTimeout(function() {
                    messages.animate({
                        scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                    }, 100, function() {
                        self.removeOldMessages(data.c);
                    });
                }, 10);
            }

            if (data.c == "map" && data.i) {
                this.$dispatch('chat-received', {
                    text: data.m,
                    id: data.i
                });
            }
        },

        removeOldMessages: function(channel) {
            if(this.messages[channel].length > 20) {
                this.messages[channel] = this.messages[channel].slice(-20);
            }
        },

        toggleChannels: function() {
            this.channelPicker = !this.channelPicker;
        },

        toggleChannel: function(channel) {
            this.channel = channel;

            this.$nextTick(function () {
                console.log(messages.prop('scrollHeight') - messages.innerHeight());
                messages.animate({
                    scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                }, 100);
            });
        }
    },

    ready: function() {
        socket.on('chat', this.addMessage);
    }
};
