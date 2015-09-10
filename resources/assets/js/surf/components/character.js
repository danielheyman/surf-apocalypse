module.exports = {

    template: require('./character.template.html'),

    data: function () {
        return {
            frame: 13,
            interval: 1,
            name: '',
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
            state: null,
            stateKey: null
        }
    },

    methods: {
        drawCharacter: function() {
            if(!$(this.$el).data('main') && $(this.$el).attr('data-state') != this.stateKey)
                this.initNewState($(this.$el).attr('data-state'));

            if($(this.$el).data('main') && this.state.moving)
                this.$dispatch('character_moving', this.stateKey);

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

        initNewState: function(state) {
            this.stateKey = state;
            this.state = this.states[state];
            this.interval = 1;
            this.frame = this.state.reverse ? this.state.frames : 1;
        }
    },

    ready: function() {
        if($(this.$el).data('main'))
        {
            this.name = window.session_name;
            this.initNewState('IDLE_RIGHT');

            var self = this;
            $("body").keydown(function(e) {
                if(e.keyCode == 39 && self.state != self.states.WALK_RIGHT)
                    self.initNewState('WALK_RIGHT');

                else if(e.keyCode == 37 && self.state != self.states.WALK_LEFT)
                    self.initNewState('WALK_LEFT');
            });
            $("body").keyup(function() {
                if(self.state == self.states.WALK_RIGHT)
                    self.initNewState('IDLE_RIGHT');

                else if(self.state == self.states.WALK_LEFT)
                    self.initNewState('IDLE_LEFT');
            });
        }
        else {
            this.name = $(this.$el).data('name');
            
            this.$dispatch('character_created', $(this.$el));
        }

        setInterval(this.drawCharacter, 50);
    }
};
