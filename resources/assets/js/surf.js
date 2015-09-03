var done = false;
var images = [
    '../../img/surf/bg.jpg'
];

$.preload(images, 1, function (last) {
    if (!last) return;
    if (done) show();
    done = true;
});

$(".wrapper").preload(function () {
    if (done) show();
    done = true;
});

function show() {
    $(".loading").hide();
    $("body").addClass("bg");
    $(".wrapper").show();
    $('.character').sprite({fps: 3, no_of_frames: 6});
}




var socket = io('http://surf.local:3000');
socket.emit("join");

// socket.on("global:App\\Events\\SentGlobalMessage", function(message){
//     console.log(message.data);
// });


new Vue({
    el: '#chat',

    data: {
        messages: [],
        message: ""
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

            messages = $(self.$el).find(".messages");
            scrolledToBottom = messages.scrollTop() + messages.innerHeight() >= messages.prop('scrollHeight');

            self.messages.push({name: data.n, text: data.m});

            if(scrolledToBottom) {
                setTimeout(function() {
                    messages.animate({ scrollTop: messages.prop('scrollHeight') - messages.innerHeight()}, 100, function() { self.removeOldMessages(); });
                }, 10);
            }
        });

        socket.emit("chat", {c: "global", m: "Hey dude how are you today?"});
    }
});
