module.exports = {

    template: require('./teams.template.html'),

    data: function() {
        return {
            teams: null,
            myTeam: null,
            viewTeam: {
                active: false,
                data: null,
                destroy: false,
                destroying: false,
                leave: false,
                leaving: false,
                join: false,
                joining: false
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

            this.$http.get('/api/teams/' + team.id).success(function(data) {

                self.viewTeam.data = data;

            }).error(function() {

            });
        },

        backToList: function() {
            this.viewTeam.active = false;
            this.viewTeam.data = null;
        },

        isOwner: function() {
            return window.session_id == this.viewTeam.data.team.owner_id;
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
