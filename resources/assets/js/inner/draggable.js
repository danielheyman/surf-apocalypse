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

var startmove = function(e) {
    $("body").css("pointer-events", "none");
    el = e.data.el;
    var offset = el.offset();
    move = {mouseX: event.pageX, mouseY: event.pageY, locX: offset.left, locY: offset.top};
};

var add = function(el) {
    el.find('[data-draggable=draggable]').bind('mousedown', {el: el}, startmove);
    el.css({
        position: 'absolute',
        zIndex: 10,
        left: ($(document).width() - el.width()) / 2,
        top: ($(document).height() - 400) / 2,
        boxShadow: "0 0 10px #000"
    });
};

var remove = function(el) {
    el.find('[data-draggable=draggable]').unbind('mousedown', startmove);
};

$.fn.draggable = function() {
    add($(this));
};

$.fn.undraggable = function() {
    remove($(this));
};
