module.exports = {

    template: require('./profile.template.html'),

    data: function() {
        return {
            move: null,
            // messages: [{
            //     side: 'left',
            //     message: 'Actually I\'ll probably have to leave before you make it.',
            //     info: 'Nov 11, 11:22 AM'
            // }, {
            //     side: 'right',
            //     message: 'Actually I\'ll probably have to leave before you make it.',
            //     info: 'Nov 11, 11:22 AM'
            // }],
            messages: [],
            loaded: false,
            message: '',
            gravatar: '',
            sending: false
        };
    },

    props: {
        onClose: {
            type: Function
        },
        userId: {
            type: Number
        },
        userName: {
            type: String
        }
    },

    methods: {
        close: function() {
            if (this.onClose) this.onClose(this.userId);
        },

        sendMessage: function(e) {
            if (!this.message || this.sending)
                return;

            var self = this;

            this.sending = true;

            this.$http.post('/api/pms/' + this.userId, {message: this.message});

            socket.emit("pm", {
                id: this.userId,
                m: this.message
            });

            this.message = "Sending...";
        },

        removeOldMessages: function(channel) {
            if (this.messages.length > 20) {
                this.messages = this.messages.slice(-20);
            }
        },

        scrolledToBottom: function() {
            var self = this;

            var messages = $(this.$els.messages);
            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() + 1 >= messages.prop('scrollHeight');

            if (scrolledToBottom || !messages.length) {
                this.$nextTick(function () {
                    if(!messages.length)
                        messages = $(this.$els.messages);

                    messages.animate({
                        scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                    }, 100, function() {
                        self.removeOldMessages();
                    });
                });
            }
        }
    },

    ready: function () {
        var self = this;

        this.$http.get('/api/pms/' + this.userId).then(function(result) {
            var data = result.data;
            self.gravatar = data.gravatar;
            self.messages = data.messages;
            self.loaded = true;
            self.scrolledToBottom();
            self.$dispatch('seen-pm', this.userId);
        });

        this.$on('received-pm', function(data) {
            self.messages.push({
                side: data.from == window.session_id ? 'right' : 'left',
                message: data.message.message,
                info: data.message.info
            });
            
            if(data.from == window.session_id) {
                self.message = "";
                self.sending = false;
                self.$nextTick(function () {
                    self.$els.message.focus();
                });
            }

            self.scrolledToBottom();
            self.$dispatch('seen-pm', data.from);
            return false;
        });

    },
};
