function enable_drag_croll() {
    if ($('.dragscroll').length <= 0) {
        $('.dragscroll1').addClass('dragscroll');
        dragscroll.reset();
    }
}
function disable_drag_croll() {
    if ($('.dragscroll').length > 0) {
        $('.dragscroll1').removeClass('dragscroll');
        dragscroll.reset();
    }
}