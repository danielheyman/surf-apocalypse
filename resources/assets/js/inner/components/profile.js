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
            noMessages: true,
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
            if (this.onClose)
                this.onClose(this.userId);
        },

        sendMessage: function() {
            if (!this.message || this.sending)
                return;

            var self = this;

            this.sending = true;

            this.$http.post('/api/pms/' + this.userId, {message: this.message}).success(function(data) {
                self.messages.push(data);
                self.noMessages = false;
                self.message = "";
                self.sending = false;
                self.scrolledToBottom();
                self.$nextTick(function () {
                    self.$$.message.focus();
                });
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

            var messages = $(this.$$.messages);
            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() + 1 >= messages.prop('scrollHeight');

            if (scrolledToBottom || !messages.length) {
                this.$nextTick(function () {
                    if(!messages.length)
                        messages = $(this.$$.messages);

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

        this.$http.get('/api/pms/' + this.userId).success(function(data) {
            self.gravatar = data.gravatar;
            self.messages = data.messages;
            self.loaded = true;
            self.noMessages = (data.messages.length === 0);
            self.scrolledToBottom();
        });

        this.$on('received-pm', function(data) {
            if(data.from != self.userId) return;

            self.messages.push(data.message);
            self.scrolledToBottom();

            return false;
        });

        $(this.$el).draggable();

    },

    detached: function() {
        $(self.$el).undraggable();
    }
};
