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
            },
            shared: window.store
        };
    },

    validators: {
        minLength: function (val, rule) {
            return val.length >= rule;
        }
    },

    filters: {
        notMine: function(teams) {
            if(!this.myTeam) return teams;

            return teams.filter(team => {
                return team.id != this.myTeam.id;
            });
        }
    },

    methods: {
        openTeam: function(e, team) {
            e.preventDefault();

            this.viewTeam.active = true;
            this.viewTeam.team = team;

            this.$http.get('/api/teams/' + team.id).then(result => {
                this.viewTeam.data = result.data;
            });
        },

        backToList: function() {
            this.viewTeam.active = false;
            this.viewTeam.team = null;
            this.viewTeam.data = null;
        },

        isOwner: function() {
            return this.shared.user.id == this.viewTeam.data.team.owner_id;
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

            this.$http.post('/api/teams/leave').then(() => {

                this.viewTeam.loadingMessage = "";
                this.viewTeam.team.user_count--;
                this.myTeam = null;
                this.backToList();
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

            this.$http.delete('/api/teams').then(() => {

                this.viewTeam.loadingMessage = "";
                this.teams.$remove(this.viewTeam.team);
                this.myTeam = null;
                this.backToList();
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

            this.$http.post('/api/teams/new', {'name': this.newTeam.name, 'description': this.newTeam.description}).then(result => {
                var site = result.data;
                this.teams.push(site);
                this.myTeam = site;

                this.cancelCreate();
                this.newTeam.posting = false;
            });
        },

        openProfile: function(event, user) {
            event.preventDefault();
            this.$dispatch('open-profile', {name: user.name, id: user.id});
        }
    },

    ready: function() {
        this.$http.get('/api/teams').then(result => {
            var data = result.data;
            this.teams = data.teams;

            if(data.my_team) {
                $.each(data.teams, team => {
                    if(data.teams[team].id == data.my_team)
                        this.myTeam = data.teams[team];
                });
            }

        });
    }
};
