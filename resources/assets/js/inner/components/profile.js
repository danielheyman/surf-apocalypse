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
        startMove: function(event) {
            var offset = $(this.$el).offset();
            this.move = {mouseX: event.pageX, mouseY: event.pageY, locX: offset.left, locY: offset.top};
        },

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
            if(data.from != self.userId)
                return;

            self.messages.push(data.message);
            self.scrolledToBottom();

            return false;
        });

        var el = $(self.$el);

        el.css('left', ($(document).width() - el.width()) / 2);
        el.css('top', ($(document).height() - 400) / 2);

        $(window).on('mouseup', function() {
            if(self.move) self.move = null;
        });

        $('body').on('mousemove', function(event) {
            if(!self.move)
                return;

            var el = $(self.$el);
            var x = event.pageX - self.move.mouseX + self.move.locX;
            var y = event.pageY - self.move.mouseY + self.move.locY;
            if(x < 0)
                x = 0;
            else if(x > $(document).width() - el.width())
                x = $(document).width() - el.width();
            if(y < 0)
                y = 0;
            else if(y > $(document).height() - el.height())
                y = $(document).height() - el.height();

            el.css('left', x);
            el.css('top', y);
        });

    }
};
