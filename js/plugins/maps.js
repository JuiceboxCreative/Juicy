/**
*
*   Script uses google maps and geocoder to create map
*
*   Requires a div with:
*   * data-title="example"
*   * data-address="example"
*   * class="map"
*/
jQuery(function($){
    // Only run script if there is a .map div on the page
    if ( ! $(".map").length )
        return;

    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyDBkfJltZseT_Mjjm4kTFBrAw2LKN-s5gk&sensor=false&callback=createMap";
    document.body.appendChild(script);

});

/*
    Create google maps
*/
function createMap(){

    var $ = jQuery;

    $(".map").each(function(){

        var mapDomElement = this,
            address = $(mapDomElement).data("address"),
            lat = parseFloat($(mapDomElement).data('lat')),
            lng = parseFloat($(mapDomElement).data('lng')),
            zoom = $(mapDomElement).data('zoom'),
            loc = null;

        //Sets HTML5 maps
        google.maps.visualRefresh = true;

        //Set map options
        var mapOptions = {
            zoom: zoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
            mapTypeControl: false,
            backgroundColor: '#2b2b2b',
            scrollwheel: false,
            draggable: false
        };

        //Create map object
        var map = new google.maps.Map(mapDomElement, mapOptions);

        var markerOpts = {
            map: map
        };

        if( lat && lng ){

            loc = new google.maps.LatLng(lat,lng);

            map.setCenter(loc);

            markerOpts.position = loc;

            marker = new google.maps.Marker(markerOpts);

            addClickEventToMarker(marker, 'http://maps.google.com.au/maps?q='+encodeURIComponent(address));

        } else {
            //Create geocoder object
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode( { "address": address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    map.setCenter(results[0].geometry.location);

                    markerOpts.position = results[0].geometry.location;

                    marker = new google.maps.Marker(markerOpts);

                    addClickEventToMarker(marker, 'http://maps.google.com.au/maps?q='+encodeURIComponent(address));
                }
            });
        }

        // Set new center point on map as window resizes
        google.maps.event.addDomListener(window, "resize", function() {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });

    });
}
/*
    Adds click event to marker,
    if user clicks then opens a new window
    at https://maps.google.com.au/maps
*/
function addClickEventToMarker(marker, link) {
    google.maps.event.addListener(marker, "click", function() {
        window.open(link, '_blank');
    });
}