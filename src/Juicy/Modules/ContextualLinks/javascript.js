jQuery(function($){
    $('.module-cta-boxes.carousel .module-cta-boxes__post-wrapper').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
            {
                breakpoint: 1160,
                settings: {
                    slidesToScroll: 2,
                    slidesToShow: 2
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToScroll: 1,
                    slidesToShow: 1
                },
            }
        ]
    });
});
