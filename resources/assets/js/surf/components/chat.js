module.exports = {

    template: require('./chat.template.html'),

    data: function() {
        return {
            messages: [
                { name: "System", text: "Welcome to SurfApocalypse!"}
            ],
            message: "",
            channel: "map"
        }
    },

    methods: {
        sendMessage: function() {

            if(!this.message) return;

            this.$dispatch('chat-sent', this.message);
            socket.emit("chat", {c: this.channel, m: this.message});

            this.addMessage({c: this.channel, n: window.session_name, m: this.message});

            this.message = "";
        },

        addMessage: function(data) {
            var messages = $(this.$$.messages);

            var scrolledToBottom = messages.scrollTop() + messages.innerHeight() + 1 >= messages.prop('scrollHeight');

            this.messages.push({name: data.n, text: data.m});

            var self = this;

            if(scrolledToBottom) {
                setTimeout(function() {
                    messages.animate({ scrollTop: messages.prop('scrollHeight') - messages.innerHeight()}, 100, function() { self.removeOldMessages(); });
                }, 10);
            }

            if(data.c == "map" && data.i != null)
                this.$dispatch('chat-received', {text: data.m, id: data.i});
        },

        removeOldMessages: function() {
            if(this.messages.length > 20) {
                this.messages.shift();
            }
        }
    },

    ready: function() {
        socket.on('chat', this.addMessage);
    }
};
