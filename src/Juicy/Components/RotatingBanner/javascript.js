jQuery(function($){
    var $slideshow = $('.rotating-banner.rotating-banner--slideshow');

    if ( !$slideshow.length ) {
        return;
    }

    $slideshow.cycle({
        slides: '.rotating-banner__slide',
        next: '.rotating-banner__nav.next',
        prev: '.rotating-banner__nav.prev',
        timeout: 10000
    });
});
