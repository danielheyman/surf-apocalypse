module.exports = {

    template: require('./character.template.html'),

    data: function() {
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
            state: null,
            stateKey: 'IDLE_RIGHT',
            intervals: [],
            height: 0,
            width: 0
        };
    },

    props: {
        onCreate: {
            type: Function
        },
        setState: {
            type: String
        },
        movable: {
            type: Boolean
        },
        name: {
            type: String
        },
        message: {
            type: String
        },
        onMove: {
            type: Function
        },
        charId: {
            type: Number
        },
        currentState: {
            type: String,
            twoWay: true
        }
    },

    methods: {
        drawCharacter: function() {
            if (this.setState && this.setState != this.stateKey)
                this.initNewState(this.setState);

            if (this.onMove && this.state.moving)
                this.onMove(this.stateKey);

            if (this.interval++ != 1) {
                if (this.interval > this.state.intervals)
                    this.interval = 1;
                return;
            }

            $(this.$el).css('background-position', -((this.frame - 1) * this.width) + 'px ' + -((this.state.line - 1) * this.height) + 'px');

            if (!this.state.reverse) {
                if (++this.frame > this.state.frames)
                    this.frame = 1;
            } else {
                if (--this.frame < 1)
                    this.frame = this.state.frames;
            }
        },

        initNewState: function(state) {
            this.stateKey = state;
            if (this.currentState) this.currentState = state;
            this.state = this.states[state];
            this.interval = 1;
            this.frame = this.state.reverse ? this.state.frames : 1;
        },

        keyDownListener: function(e) {
            if (e.keyCode == 39 && this.state != this.states.WALK_RIGHT)
                this.initNewState('WALK_RIGHT');

            else if (e.keyCode == 37 && this.state != this.states.WALK_LEFT)
                this.initNewState('WALK_LEFT');
        },

        keyUpListener: function(e) {
            if (e.keyCode == 39 && this.state == this.states.WALK_RIGHT)
                this.initNewState('IDLE_RIGHT');

            else if (e.keyCode == 37 && this.state == this.states.WALK_LEFT)
                this.initNewState('IDLE_LEFT');
        }
    },

    attached: function() {
        this.initNewState(this.stateKey);

        this.height = $(this.$el).height();
        this.width = $(this.$el).width();

        if (this.movable) {
            $(document).on('keydown', this.keyDownListener);
            $(document).on('keyup', this.keyUpListener);
        }

        if (this.onCreate)
            this.onCreate($(this.$el), this.charId);

        this.intervals.push(setInterval(this.drawCharacter, 50));
    },

    detached: function() {
        var self = this;

        $.each(this.intervals, function(key) {
            clearInterval(self.intervals[key]);
        });
    }
};
