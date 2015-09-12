

module.exports = {

    template: require('./map.template.html'),

    data: function() {
        return {
            charXPercent: 5,
            site: null,
            characters: [],
            state: null
        };
    },

    methods: {
        sendStatus: function() {
            var facingRight = ($(this.$$.character).attr('data-state').indexOf('RIGHT') > -1);
            socket.emit("map_status", {m: this.site.id, l: Math.floor(this.charXPercent * 100) / 100, r: facingRight});
        },

        getCharArrayPos: function(id) {
            var keys = Object.keys(this.characters);
            var char_array_pos = null;
            for(var x = 0; x < keys.length; x++)
            {
                if(id == this.characters[keys[x]].i)
                    char_array_pos = keys[x];
            }
            return char_array_pos;
        },

        getLeftPos: function(percent) {
            return ($(window).width() - $(this.$$.character).width()) * percent / 100;
        }
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

            var left = self.getLeftPos(self.charXPercent);

            $(self.$$.character).stop(true, true).animate({
                'left':  left
            }, 50);
        });

        this.$on('character_created', function (char) {
            var char_array_pos = self.getCharArrayPos(char.data('id'));
            var data = self.characters[char_array_pos];
            var left = self.getLeftPos(data.l);
            var state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";

            self.characters[char_array_pos].char = char;
            char.attr('data-state', state);

            char.css({
                'left':  left
            });
        });

        this.$http.get('/api/map').success(function(site) {

            for(var x = 0; x < site.items.length; x++)
            {
                site.items[x].left = this.getLeftPos(Math.floor((Math.random() * 65) + 25));
            }

            self.site = site;

            setInterval(this.sendStatus, 1000);

            socket.on('map_status', function (data) {
                var char_array_pos = self.getCharArrayPos(data.i);

                if(char_array_pos)
                {
                    var oldData = self.characters[char_array_pos];
                    if(oldData.l != data.l)
                    {
                        var state = (data.l > oldData.l) ? "WALK_RIGHT" : "WALK_LEFT";
                        var left = self.getLeftPos(data.l);

                        var time = Math.abs(data.l - oldData.l) / .7 * 65;
                        oldData.char.attr('data-state', state);

                        oldData.char.stop(true).animate({
                            'left':  left
                        }, time, 'linear', function() {
                            state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                            oldData.char.attr('data-state', state);
                        });
                    }
                    else if(oldData.r != data.r)
                    {
                        var state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                        oldData.char.attr('data-state', state);
                    }

                    self.characters[char_array_pos].l = data.l;
                    self.characters[char_array_pos].r = data.r;
                }
                else
                    self.characters.push(data);
            });

        }).error(function() {

        });
    }
};
