module.exports = {

    template: require('./teams.template.html'),

    data: function() {
        return {
            teams: null,
            myTeam: null,
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
