module.exports = {

    template: require('./sites.template.html'),

    data: function() {
        return {
            sites: null,
            delete: null,
            newSite: {
                active: false,
                name: '',
                url: '',
                posting: false
            }
        };
    },

    validators: {
        url: function (val) {
            return /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)$/.test(val);
        }, 
        minLength: function (val, rule) {
            return val.length >= rule;
        }
    },

    computed: {
        noSites: function() {
            if(!this.sites) return false;

            return !this.sites.length;
        }
    },

    methods: {
        toggleSite: function(e, site) {
            e.preventDefault();
            site.enabled = !site.enabled;

            this.$http.post('/api/sites/' + site.id, {'enabled': site.enabled});
        },

        deleteSite: function(e, site) {
            e.preventDefault();
            this.delete = site;
        },

        confirmDelete: function() {
            this.sites.$remove(this.delete);

            this.$http.delete('api/sites/' + this.delete.id);

            this.delete = null;
        },

        cancelDelete: function() {
            this.delete = null;
        },

        addSite: function() {
            this.newSite.active = true;
        },

        cancelAdd: function() {
            this.newSite.active = false;
            this.newSite.name = '';
            this.newSite.url = '';
        },

        confirmAdd: function() {
            this.newSite.posting = true;

            this.$http.post('/api/sites/new', {'name': this.newSite.name, 'url': this.newSite.url}).then(result => {
                this.sites.push(result.data);

                this.newSite.active = false;
                this.newSite.posting = false;
                this.newSite.name = '';
                this.newSite.url = '';
            });
        }


    },

    ready: function() {
        this.$http.get('/api/sites').then(result => {
            this.sites = result.data;
        });
    }
};
