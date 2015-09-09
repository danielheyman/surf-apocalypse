module.exports = {

    template: require('./chat.template.html'),

    data: function() {
        return {
            messages: [],
            message: ""
        }
    },

    methods: {
        sendMessage: function() {

            if(!this.message) return;

            socket.emit("chat", {c: "global", m: self.message});
            self.message = "";
        },

        removeOldMessages: function() {
            if(self.messages.length > 20) {
                self.messages.shift();
            }
        }
    },

    ready: function() {
        self = this;

        socket.on('chat', function (data) {
            var messages = $(self.$$.messages);

            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() >= messages.prop('scrollHeight');

            self.messages.push({name: data.n, text: data.m});

            if(scrolledToBottom) {
                setTimeout(function() {
                    messages.animate({ scrollTop: messages.prop('scrollHeight') - messages.innerHeight()}, 100, function() { self.removeOldMessages(); });
                }, 10);
            }
        });

        socket.emit("chat", {c: "global", m: "Hey dude how are you today?"});
    }
};
