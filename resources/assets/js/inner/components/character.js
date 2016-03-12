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
            stateKey: '',
            intervals: [],
            height: 110,
            width: 110,
            loaded: false,
            name: '',
            equipsCss: '',
        };
    },

    props: {
        onCreate: {
            type: Function
        },
        setState: {
            type: String
        },
        mine: {
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
            
            $(".character", this.$el).css('background-position', -((this.frame - 1) * this.width) + 'px ' + -((this.state.line - 1) * this.height) + 'px');

            if (--this.frame < 1) this.frame = this.state.frames;
        },

        initNewState: function(state) {
            if(this.stateKey == state) return;
            this.stateKey = state;
            if (this.currentState) this.currentState = state;
            this.state = this.states[state];
            this.interval = 1;
            this.frame = this.state.reverse ? this.state.frames : 1;
        },

        keyDownListener: function(e) {
            if (e.keyCode == 39)
                this.initNewState('WALK_RIGHT');

            else if (e.keyCode == 37)
                this.initNewState('WALK_LEFT');
        },

        keyUpListener: function(e) {
            if (e.keyCode == 39 && this.state == this.states.WALK_RIGHT)
                this.initNewState('IDLE_RIGHT');

            else if (e.keyCode == 37 && this.state == this.states.WALK_LEFT)
                this.initNewState('IDLE_LEFT');
        },
        
        buildEquips: function(equips) {
            var self = this;
            var style = 'background:';
            var equipsLength = equips.length;

            $.each(equips, function(index) {
                style += 'url("../api/equips/' + this + '.png")';
                if(index < equipsLength - 1) style += ',';
            });
            style += ';background-size: 900% 400%;';
            this.equipsCss = style;
        }
    },
    
    attached: function() {
        var self = this;
        
        this.initNewState('IDLE_RIGHT');
        
        if (this.mine) {
            $(document).on('keydown', this.keyDownListener);
            $(document).on('keyup', this.keyUpListener);
        }
        
        this.intervals.push(setInterval(this.drawCharacter, 50));

        var preload = function() {
            $(".character", self.$el).preload(function() {                
                self.loaded = true;
            });
        };

        if(this.mine) {
            this.name = window.session_name;
            this.buildEquips(window.session_equips);
            this.$nextTick(preload);
        } else {
            socket.emit('char_info', this.charId);
        }

        socket.on('char_info', function(data) {
            if(data.i != self.charId) return;
            
            if(typeof data.e == "string")
                data.e = data.e.split(",");
            if(data.e[0] === "") data.e = [];
            self.name = data.n; 
            self.buildEquips(data.e);
                
            self.$nextTick(preload);
        });
        
        if(self.onCreate) self.onCreate($(".character", self.$el), self.charId);
    },

    detached: function() {
        var self = this;
        
        $(document).off('keydown', this.keyDownListener);
        $(document).off('keyup', this.keyUpListener);

        $.each(this.intervals, function(key) {
            clearInterval(self.intervals[key]);
        });
    }
};
