jQuery(function($){
    $(".map").each(function(){

        var mapDomElement = this,
            address = $(mapDomElement).data("address"),
            override = $(mapDomElement).data("override"),
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
            map: map,
            icon: themeData.childThemeDir + '/img/pin.png'
        };

        if( lat && lng ){

            loc = new google.maps.LatLng(lat,lng);

            map.setCenter(loc);

            markerOpts.position = loc;

            marker = new google.maps.Marker(markerOpts);

        } else {
            //Create geocoder object
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode( { "address": address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    map.setCenter(results[0].geometry.location);

                    markerOpts.position = results[0].geometry.location;

                    marker = new google.maps.Marker(markerOpts);
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
});
