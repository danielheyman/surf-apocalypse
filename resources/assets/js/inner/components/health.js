module.exports = {

    template: require('./health.template.html'),

    data: function() {
        return {
            width: 5,
            shared: window.store
        };
    },

    methods: {
        
    },

    ready: function () {
        this.width = 5 + this.shared.user.health * 0.95;
    }
};
