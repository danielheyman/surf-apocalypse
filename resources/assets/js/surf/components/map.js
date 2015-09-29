module.exports = {

    template: require('./map.template.html'),

    data: function() {
        return {
            charXPercent: 5,
            site: null,
            characters: [],
            state: "IDLE_RIGHT",
            name: "",
        };
    },

    methods: {
        sendStatus: function() {
            if(!this.site) return;

            var facingRight = (this.state.indexOf('RIGHT') > -1);
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
        },

        createCharacter: function(el, id) {

            var char_array_pos = this.getCharArrayPos(id);
            var data = this.characters[char_array_pos];
            var left = this.getLeftPos(data.l);

            this.characters[char_array_pos].el = el;

            el.css({
                'left':  left
            });
        },

        moveCharacter: function(state) {
            if(state == 'WALK_LEFT') {
                this.charXPercent -= .7;
            }
            else if(state == 'WALK_RIGHT') {
                this.charXPercent += .7;
            }

            if(this.charXPercent < 0)
                this.charXPercent = 0;
            else if(this.charXPercent > 100)
            {
                var self = this;
                var itemsFound = [];

                this.site.items.forEach(function(item) {
                    if(!item.pickedUp) return;
                    itemsFound.push(item.id);
                });

                this.$http.post('/api/map', {'id': this.site.id, 'items': itemsFound}).success(function(site) {

                    self.processSite(site);
                });

                this.site = null;

                return;
            }

            var left = this.getLeftPos(this.charXPercent);

            $(this.$$.character).stop(true, true).animate({
                'left':  left
            }, 50);
        },

        processSite: function(site) {
            for(var x = 0; x < site.items.length; x++)
            {
                site.items[x].left = this.getLeftPos(Math.floor((Math.random() * 65) + 25));
                site.items[x].pickedUp = false;
            }

            this.site = site;
        }
    },

    components: {
        'character': require('./character.js')
    },

    filters: {
        removeFoundItems: function(items) {
            return items.filter(function(item) {
                return !item.pickedUp;
            });
        }
    },

    ready: function() {
        var self = this;

        this.name = window.session_name;

        this.$http.get('/api/map').success(function(site) {
            self.processSite(site);
        });

        setInterval(this.sendStatus, 500);

        $("body").keydown(function(e) {
            if(!self.site) return;

            if(e.keyCode != 38) return;

            var myLocationStart = self.getLeftPos(self.charXPercent) + 30;
            var myLocationEnd = myLocationStart + $(self.$$.character).width() - 30;

            for(var x = 0; x < self.site.items.length; x++)
            {
                if(myLocationEnd > self.site.items[x].left && myLocationStart < self.site.items[x].left + 30)
                {
                    self.site.items[x].pickedUp = true;
                }
            }
        });

        socket.on('map_status', function (data) {
            if(!self.site) return;

            var char_array_pos = self.getCharArrayPos(data.i);

            if(char_array_pos)
            {
                var oldData = self.characters[char_array_pos];
                if(oldData.l != data.l)
                {
                    var state = (data.l > oldData.l) ? "WALK_RIGHT" : "WALK_LEFT";
                    var left = self.getLeftPos(data.l);

                    var time = Math.abs(data.l - oldData.l) / .7 * 65;
                    oldData.state = state;

                    oldData.el.stop(true).animate({
                        'left':  left
                    }, time, 'linear', function() {
                        state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                        oldData.state = state;
                    });
                }
                else if(oldData.r != data.r)
                {
                    var state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                    oldData.state = state;
                }

                oldData.l = data.l;
                oldData.r = data.r;
            }
            else
            {
                data['state'] = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                self.characters.push(data);
            }
        });
    }
};
