module.exports = {

    template: require('./teams.template.html'),

    data: function() {
        return {
            teams: null,
            myTeam: null,
            newTeam: {
                active: false,
                name: '',
                description: '',
                posting: false
            },
            viewTeam: {
                team: null,
                active: false,
                data: null,
                destroy: false,
                leave: false,
                join: false,
                loadingMessage: ""
            }
        };
    },

    filters: {
        notMine: function(teams) {
            var self = this;

            if(!this.myTeam)
                return teams;

            return teams.filter(function(team) {
                return team.id != self.myTeam.id;
            });
        }
    },

    methods: {
        openTeam: function(e, team) {
            var self = this;

            e.preventDefault();

            this.viewTeam.active = true;
            this.viewTeam.team = team;

            this.$http.get('/api/teams/' + team.id).success(function(data) {

                self.viewTeam.data = data;

            }).error(function() {

            });
        },

        backToList: function() {
            this.viewTeam.active = false;
            this.viewTeam.team = null;
            this.viewTeam.data = null;
        },

        isOwner: function() {
            return window.session_id == this.viewTeam.data.team.owner_id;
        },

        leaveTeam: function(e) {
            e.preventDefault();

            this.viewTeam.leave = true;
        },

        cancelLeave: function() {
            this.viewTeam.leave = false;
        },

        confirmLeave: function() {
            this.cancelLeave();
            this.viewTeam.loadingMessage = "LEAVING TEAM";

            var self = this;

            this.$http.post('/api/teams/leave').success(function() {

                self.viewTeam.loadingMessage = "";
                self.viewTeam.team.user_count--;
                self.myTeam = null;
                self.backToList();
            });
        },

        destroyTeam: function(e) {
            e.preventDefault();

            this.viewTeam.destroy = true;
        },

        cancelDestroy: function() {
            this.viewTeam.destroy = false;
        },

        confirmDestroy: function() {
            this.cancelDestroy();
            this.viewTeam.loadingMessage = "DESTROYING TEAM";

            var self = this;

            this.$http.delete('/api/teams').success(function() {

                self.viewTeam.loadingMessage = "";
                self.teams.$remove(self.viewTeam.team);
                self.myTeam = null;
                self.backToList();
            });
        },

        createTeam: function() {
            this.newTeam.active = true;
        },

        cancelCreate: function() {
            this.newTeam.active = false;
            this.newTeam.name = "";
            this.newTeam.description = "";
        },

        confirmCreate: function() {
            this.newTeam.posting = true;

            var self = this;

            this.$http.post('/api/teams/new', {'name': this.newTeam.name, 'description': this.newTeam.description}).success(function(site) {

                self.teams.push(site);
                self.myTeam = site;

                self.cancelCreate();
                this.newTeam.posting = false;
            });
        },

        openProfile: function(event, user) {
            event.preventDefault();
            this.$dispatch('open-profile', {name: user.name, id: user.id});
        }
    },

    ready: function() {
        var self = this;

        this.$http.get('/api/teams').success(function(data) {

            self.teams = data.teams;

            if(data.my_team) {
                $.each(data.teams, function(team) {
                    if(data.teams[team].id == data.my_team)
                        self.myTeam = data.teams[team];
                });
            }

        }).error(function() {

        });
    }
};
