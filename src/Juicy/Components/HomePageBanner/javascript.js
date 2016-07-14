jQuery(function($){
    var $slideshow = $('.banner-home.banner-home-slideshow');

    if ( !$slideshow.length ) {
        return;
    }

    $slideshow.cycle({
        slides: '.banner-home__slide',
        next: '.banner-home__nav.next',
        prev: '.banner-home__nav.prev',
        timeout: 10000
    });
});
