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
            width: 110,
            loaded: false
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
        message: {
            type: String
        },
        onMove: {
            type: Function
        },
        currentState: {
            type: String,
            twoWay: true
        },
        charId: {
            type: Number
        },
        name: {
            type: String
        },
        equips: {
            type: Array
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
        },
        
        buildEquips: function() {
            var self = this;
            var style = 'background:';
            var equipsLength = this.equips.length;

            $.each(this.equips, function(index) {
                style += 'url("../api/equips/' + this + '.png")';
                if(index < equipsLength - 1) style += ',';
            });
            style += ';background-size: 900% 400%;';
            this.equipsCss = style;
        }
    },
    
    created: function() { 
        this.buildEquips();
    },

    attached: function() {
        var self = this;
        $(this.$el).preload(function() {
            self.loaded = true;
            
            self.initNewState(self.stateKey);
            
            if (self.movable) {
                $(document).on('keydown', self.keyDownListener);
                $(document).on('keyup', self.keyUpListener);
            }

            self.intervals.push(setInterval(self.drawCharacter, 50));
        });
        
        if(self.onCreate) self.onCreate($(self.$el), self.charId);
    },

    detached: function() {
        var self = this;

        $.each(this.intervals, function(key) {
            clearInterval(self.intervals[key]);
        });
    }
};
