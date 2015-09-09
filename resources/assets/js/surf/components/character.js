module.exports = {

    template: require('./character.template.html'),

    data: function () {
        return {
            frame: 13,
            interval: 1,
            states: {
                IDLE_RIGHT: {
                    frames: 6,
                    intervals: 5,
                    line: 1
                },
                IDLE_LEFT: {
                    frames: 6,
                    intervals: 5,
                    revese: true,
                    line: 2
                },
                WALK_RIGHT: {
                    frames: 14,
                    intervals: 1,
                    line: 3,
                    moving: true
                },
                WALK_LEFT: {
                    frames: 14,
                    intervals: 1,
                    reverse: true,
                    line: 4,
                    moving: true
                }
            },
            state: null
        }
    },

    methods: {
        drawCharacter: function() {
            if(this.state.moving)
                this.$dispatch('character_moving', this.getStateKey(this.state));

            if(this.interval++ != 1) {
                if(this.interval > this.state.intervals)
                    this.interval = 1;
                return;
            }

            var height = $(this.$el).height();
            var width = $(this.$el).width();

            $(this.$el).css('background-position', -((this.frame - 1) * width) + 'px ' + -((this.state.line - 1) * height) + 'px');

            if(!this.state.reverse) {
                if(++this.frame > this.state.frames)
                    this.frame = 1;
            } else {
                if(--this.frame < 1)
                    this.frame = this.state.frames;
            }
        },

        getStateKey: function(state) {
            var keys = Object.keys(this.states);
            for(var x = 0; x < keys.length; x++) {
                var key = keys[x];
                if(this.states[key] == state)
                    return key;
            }
        },

        initNewState: function(state) {
            this.state = state;
            this.interval = 1;
            this.frame = state.reverse ? state.frames : 1;
        }
    },

    ready: function() {
        this.initNewState(this.states.IDLE_RIGHT);

        setInterval(this.drawCharacter, 50);

        var self = this;

        $("body").keydown(function(e) {
            if(e.keyCode == 39 && self.state != self.states.WALK_RIGHT)
                self.initNewState(self.states.WALK_RIGHT);

            else if(e.keyCode == 37 && self.state != self.states.WALK_LEFT)
                self.initNewState(self.states.WALK_LEFT);
        });
        $("body").keyup(function() {
            if(self.state == self.states.WALK_RIGHT)
                self.initNewState(self.states.IDLE_RIGHT);

            else if(self.state == self.states.WALK_LEFT)
                self.initNewState(self.states.IDLE_LEFT);
        });
    }
};
