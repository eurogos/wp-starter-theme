var $ = jQuery.noConflict();

//Debounce Function
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) {
                func.apply(context, args);
            }
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) {
            func.apply(context, args);
        }
    };
}


//Start of document READY
$(document).ready(function(){

    //Cache window with to avoid resize trigger problem on webkit scroll
    var cachedWidth = $(window).width();

    //Debounce the window resize event
    $(window).resize(debounce(function(){
        var newWidth = $(window).width();

        //Stuff to do in a window resize HERE

        if(newWidth !== cachedWidth){
            //Typically close the mobile menu HERE
            cachedWidth = newWidth;
        }
    }, 100));

});