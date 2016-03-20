module.exports = {

    template: require('./health.template.html'),

    data: function() {
        return {
            width: 5,
            health: 10
        };
    },

    methods: {
        
    },

    ready: function () {
        this.health = window.session_health;
        this.width = 5 + this.health * 0.95;
    }
};
