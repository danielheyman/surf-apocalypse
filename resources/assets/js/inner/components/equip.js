module.exports = {

    template: require('./equip.template.html'),

    data: function() {
        return {
            
        };
    },

    methods: {
        
    },

    ready: function () {
        $(this.$el).draggable();
    }
};
