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
            style: {
                background: '',
                'background-size': ''
            },
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
        },
        equip: {
            type: String
        },
        name: {
            type: String
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
            
            $(this.$els.character).css('background-position', -((this.frame - 1) * this.width) + 'px ' + -((this.state.line - 1) * this.height) + 'px');

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
            if(!this.loaded) return;
            
            if (e.keyCode == 39)
                this.initNewState('WALK_RIGHT');

            else if (e.keyCode == 37)
                this.initNewState('WALK_LEFT');
        },

        keyUpListener: function(e) {
            if(!this.loaded) return;

            if (e.keyCode == 39 && this.state == this.states.WALK_RIGHT)
                this.initNewState('IDLE_RIGHT');

            else if (e.keyCode == 37 && this.state == this.states.WALK_LEFT)
                this.initNewState('IDLE_LEFT');
        },
        
        buildEquips: function(equips) {
            if(typeof equips == "string") equips = equips.split(",");
            
            var self = this;
            var style = '';
            var equipsLength = equips.length;

            $.each(equips, function(index) {
                style += 'url("../../img/surf/equips/' + this + '.png")';
                if(index < equipsLength - 1) style += ',';
            });
            this.style.background = style;
            this.style['background-size'] = '900% 400%';
        },
        
        openProfile: function() {
            this.$dispatch('open-profile', {name: this.name, id: this.charId});
        },

    },
    
    attached: function() {
        var self = this;
        this.initNewState('IDLE_RIGHT');        
        this.intervals.push(setInterval(this.drawCharacter, 50));

        var preload = function() {
            $(this.$els.character).preload(function() {                
                self.loaded = true;
            });
        };

        if(this.mine) {
            this.name = window.session_name;
            this.buildEquips(window.session_equips);
            this.$nextTick(preload);
            $(document).on('keydown', this.keyDownListener);
            $(document).on('keyup', this.keyUpListener);
        } else if(this.equip && this.name) {
            this.buildEquips(this.equip);
            this.$nextTick(preload);
            $(document).on('keydown', this.keyDownListener);
            $(document).on('keyup', this.keyUpListener);
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
        
        if(this.onCreate) this.onCreate($(this.$el), this.charId);
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
