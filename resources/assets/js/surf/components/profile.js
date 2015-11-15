module.exports = {

    template: require('./profile.template.html'),

    data: function() {
        return {
            move: null
        };
    },

    props: {
        userId: {
            type: Number
        }
    },

    methods: {
        startMove: function(event) {
            var offset = $(this.$el).offset();
            this.move = {mouseX: event.pageX, mouseY: event.pageY, locX: offset.left, locY: offset.top};
        }
    },

    ready: function () {
        var self = this;

        $('body').on('mouseup', function() {
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
