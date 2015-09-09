module.exports = {

    template: require('./map.template.html'),

    data: function() {
        return {
            charXPercent: 5
        };
    },

    components: {
        'character': require('./character.js')
    },

    ready: function() {
        var self = this;

        this.$on('character_moving', function (state) {

            if(state == 'WALK_LEFT') {
                self.charXPercent -= .7;
            }
            else if(state == 'WALK_RIGHT') {
                self.charXPercent += .7;
            }

            if(self.charXPercent < 0)
                self.charXPercent = 0;
            else if(self.charXPercent > 100)
                self.charXPercent = 100;

            var left = ($(window).width() - $(self.$$.character).width()) * self.charXPercent / 100;

            $(self.$$.character).css({
                'left':  left
            });
        });
    }
};
