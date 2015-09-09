module.exports = {

    template: require('./sites.template.html'),

    data: function() {
        return {
            sites: null,
            delete: null,
            addingNew: false,
            newName: '',
            newUrl: '',
            postingNew: false
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
            this.addingNew = true;
        },

        cancelAdd: function() {
            this.addingNew = false;
        },

        confirmAdd: function() {
            this.postingNew = true;

            var self = this;

            this.$http.post('/api/sites/new', {'name': this.newName, 'url': this.newUrl}).success(function(site) {

                self.sites.push(site);

                this.addingNew = false;
                this.postingNew = false;
                this.newName = '';
                this.newUrl = '';
            });
        }


    },

    ready: function() {
        var self = this;

        this.$http.get('/api/sites').success(function(sites) {

            self.sites = sites;

        }).error(function() {

        });
    }
};
