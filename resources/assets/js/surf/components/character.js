module.exports = {

    template: require('./character.template.html'),

    data: function() {
        return {
            frame: 13,
            interval: 1,
            states: {
                IDLE_LEFT: {
                    frames: 2,
                    intervals: 15,
                    line: 1
                },
                IDLE_RIGHT: {
                    frames: 2,
                    intervals: 15,
                    line: 2
                },
                WALK_LEFT: {
                    frames: 9,
                    intervals: 3,
                    line: 3,
                    moving: true
                },
                WALK_RIGHT: {
                    frames: 9,
                    intervals: 3,
                    line: 4,
                    moving: true
                }
            },
            state: null,
            stateKey: 'IDLE_RIGHT',
            intervals: [],
            height: 110,
            width: 110
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
        highlight: {
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


            if (--this.frame < 1)
                this.frame = this.state.frames;
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
