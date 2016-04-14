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
            var messages = $(this.$els.messages);
            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() + 1 >= messages.prop('scrollHeight');

            if (scrolledToBottom || !messages.length) {
                this.$nextTick(() => {
                    if(!messages.length)
                        messages = $(this.$els.messages);

                    messages.animate({
                        scrollTop: messages.prop('scrollHeight') - messages.innerHeight()
                    }, 100, () => {
                        this.removeOldMessages();
                    });
                });
            }
        }
    },

    ready: function () {
        this.$http.get('/api/pms/' + this.userId).then(result => {
            var data = result.data;
            this.gravatar = data.gravatar;
            this.messages = data.messages;
            this.loaded = true;
            this.scrolledToBottom();
            this.$dispatch('seen-pm', this.userId);
        });

        this.$on('received-pm', data => {
            this.messages.push({
                side: data.from == window.session_id ? 'right' : 'left',
                message: data.message.message,
                info: data.message.info
            });
            
            if(data.from == window.session_id) {
                this.message = "";
                this.sending = false;
                this.$nextTick(() => {
                    this.$els.message.focus();
                });
            }

            this.scrolledToBottom();
            this.$dispatch('seen-pm', data.from);
            return false;
        });

    },
};
