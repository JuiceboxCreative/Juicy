jQuery(document).ready(function(){

    jQuery('.hamburger').on('click',function(){
        jQuery(this).toggleClass('is-active');
        jQuery('.main-navigation').toggleClass('is-active');

    });

});
