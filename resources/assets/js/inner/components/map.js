module.exports = {

    template: require('./map.template.html'),

    data: function() {
        return {
            charXPercent: 5,
            walkingSpeed: 0.8,
            site: null,
            siteLoaded: false,
            characters: [],
            state: 'IDLE_RIGHT',
            intervals: [],
            message: '',
            characterEl: null,
        };
    },
    
    filters: {
        notFound: function(items) {
            return items.filter(function(item) {
                return !item.pickedUp;
            });
        }

    },

    methods: {
        sendStatus: function() {
            if (!this.site || !this.siteLoaded || !this.characterEl) return;

            var facingRight = (this.state.indexOf('RIGHT') > -1);
            socket.emit('map_status', {
                m: this.site.id,
                l: Math.floor(this.charXPercent * 100) / 100,
                r: facingRight
            });
        },

        getCharArrayPos: function(id) {
            var keys = Object.keys(this.characters);
            var char_array_pos = -1;
            for (var x = 0; x < keys.length; x++) {
                if (id == this.characters[keys[x]].i)
                    char_array_pos = keys[x];
            }
            return char_array_pos;
        },

        getLeftPos: function(percent) {
            return ($(window).width() - 110) * percent / 100;
        },
        
        createMyCharacter: function(el, id) {
            this.characterEl = el;
        },
        
        createMapCharacter: function(el, id) {
            el.css("left", this.getLeftPos(95));
            this.site.user_info.el = el;
        },

        createCharacter: function(el, id) {
            
            var char_array_pos = this.getCharArrayPos(id);
            var data = this.characters[char_array_pos];
            var left = this.getLeftPos(data.l);

            this.characters[char_array_pos].el = el;

            el.css({
                'left': left
            });
        },

        moveCharacter: function(state) {
            if (!this.site) return;

            if (state == 'WALK_LEFT') {
                this.charXPercent -= this.walkingSpeed;
            } else if (state == 'WALK_RIGHT') {
                this.charXPercent += this.walkingSpeed;
            }

            if (this.charXPercent < 0)
                this.charXPercent = 0;
            else if (this.charXPercent > 100) {
                var self = this;
                var itemsFound = [];

                socket.emit('map_leave', this.site.id);

                this.site.items.forEach(function(item) {
                    if (!item.pickedUp) return;
                    itemsFound.push(item.id);
                });

                this.$http.post('/api/map', {
                    'id': this.site.id,
                    'items': itemsFound
                }).then(function(result) {
                    self.processSite(result.data);
                });

                this.site = null;
                this.siteLoaded = false;
                this.charXPercent = 5;
                this.characters = [];

                return;
            }

            var left = this.getLeftPos(this.charXPercent);

            this.characterEl.stop(true, true).animate({
                'left': left
            }, 50);
        },

        processSite: function(site) {
            for (var x = 0; x < site.items.length; x++) {
                site.items[x].left = this.getLeftPos(Math.floor((Math.random() * 65) + 25));
                site.items[x].pickedUp = false;
            }
            
            this.site = site;
        },

        loadedSite: function() {
            this.siteLoaded = true;
        },

        getItemSrc: function(icon) {
            var id = Math.floor(icon / 10);
            var str_id = "000" + id;
            str_id = str_id.substr(str_id.length - 3);
            var ext = (icon % 1000 ? 'jpg' : 'png');
            return '../img/surf/icons/' + str_id + "." + ext;
        }
    },

    components: {
        'character': require('./character.js')
    },

    attached: function() {
        var self = this;

        this.$http.get('/api/map').then(function(result) {
            self.processSite(result.data);
        });

        this.intervals.push(setInterval(this.sendStatus, 500));

        $(document).keydown(function(e) {
            if (!self.site || e.keyCode != 38 || !self.characterEl) return;

            var myLocationStart = self.getLeftPos(self.charXPercent) + self.characterEl.width() / 2 - 30;
            var myLocationEnd = myLocationStart + 60;
            
            var characterMapEl = $(self.site.user_info.el);
            if (myLocationEnd > characterMapEl.offset().left && myLocationStart < characterMapEl.offset().left + characterMapEl.width() && !self.site.user_info.attacked) {
                self.site.user_info.attacked = true;
                self.$dispatch('notification', "You attacked <span>" + self.site.user_info.name + "</span> (-5 HP)");
            }

            for (var x = 0; x < self.site.items.length; x++) {
                if (myLocationEnd > self.site.items[x].left && myLocationStart < self.site.items[x].left + 30 && !self.site.items[x].pickedUp) {
                    self.site.items[x].pickedUp = true;
                    self.$dispatch('notification', "You have gained <span>" + self.site.items[x].name + "s</span> (+" + self.site.items[x].count + ")");
                }
            }
        });

        socket.on('map_status', function(data) {
            if (!self.site) return;
            
            var char_array_pos = self.getCharArrayPos(data.i);
            var el = (char_array_pos > -1) ? self.characters[char_array_pos] : null;
            
            if (char_array_pos > -1 && self.characters[char_array_pos].el !== undefined) {
                var state;

                if (el.l != data.l) {
                    state = (data.l > el.l) ? "WALK_RIGHT" : "WALK_LEFT";
                    var left = self.getLeftPos(data.l);

                    var time = Math.abs(data.l - el.l) / self.walkingSpeed * 65;
                    el.state = state;

                    el.el.stop(true).animate({
                        'left': left
                    }, time, 'linear', function() {
                        state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                        el.state = state;
                    });
                } else if (el.r != data.r) {
                    state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                    el.state = state;
                }

                el.l = data.l;
                el.r = data.r;
            } else if(char_array_pos > -1) {
                el.message = '';
                el.state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
            } else {
                data.message = '';
                data.state = (data.r) ? "IDLE_RIGHT" : "IDLE_LEFT";
                self.characters.push(data);
            }
            
        });
        
        socket.on('map_leave', function(id) {
            if (!self.site) return;
            var char_array_pos = parseInt(self.getCharArrayPos(id));
            if (char_array_pos > -1) self.characters.splice(char_array_pos, 1);
        });

        this.$on('chat-sent', function(message) {
            self.message = message;

            setTimeout(function() {
                if (self.message == message) self.message = "";
            }, 5000);
        });

        this.$on('chat-received', function(message) {
            var char_array_pos = self.getCharArrayPos(message.id);

            if (char_array_pos == -1)
                return;

            self.characters[char_array_pos].message = message.text;

            setTimeout(function() {
                var char_array_pos = self.getCharArrayPos(message.id);

                if (char_array_pos == -1) return;

                if (self.characters[char_array_pos].message == message.text)
                    self.characters[char_array_pos].message = "";
            }, 5000);
        });
    },

    detached: function() {
        var self = this;

        $.each(this.intervals, function(key) {
            clearInterval(self.intervals[key]);
        });
    }
};
