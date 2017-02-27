module.exports = function(Vue) {
    var move = null;
    var el = null;

    $(window).on('mouseup', function() {
        $("body").css("pointer-events", "auto");
        move = null;
    });

    $(document).on('mousemove', function(event) {
        if(!move) return;

        var x = event.pageX - move.mouseX + move.locX;
        var y = event.pageY - move.mouseY + move.locY;
        
        if(x < 0)
            x = 0;
        else if(x > $(document).width() - el.width())
            x = $(document).width() - el.width();
        if(y < 0)
            y = 0;
        else if(y > $(document).height() - el.height())
            y = $(document).height() - el.height();

        el.css('left', x);
        el.css('top', y);
    });

    Vue.directive('draggable', {
        bind: function() {
            $(this.el).find('[data-draggable=draggable]').bind('mousedown', {el: $(this.el)}, this.mousedown);
            $(this.el).css({
                position: 'absolute',
                zIndex: 10,
                left: ($(document).width() - $(this.el).width()) / 2,
                top: ($(document).height() - 400) / 2,
                boxShadow: "0 0 10px #000"
            });
        },
        
        mousedown: function(e) {
            $("body").css("pointer-events", "none");
            el = e.data.el;
            var offset = el.offset();
            move = {mouseX: event.pageX, mouseY: event.pageY, locX: offset.left, locY: offset.top};
        },
        
        unbind: function() {
            $(this.el).find('[data-draggable=draggable]').unbind('mousedown', this.mousedown);
        }
    });
};
