// REMOVE LOGO //
$("#btn-RemoveLogo").click(function() {
    $('#ConfirmRemove').modal('show');
});

$("#btn-RemoveLogoNow").click(function() {
    App.startPageLoading({animate: true});
    var id              = $('#id').val();
    window.location.href= BASE_URL + "/setting/delete_logo/" + id;
});
// END REMOVE LOGO //


// REMOVE ICON //
$("#btn-RemoveIcon").click(function() {
    $('#ConfirmRemoveIcon').modal('show');
});

$("#btn-RemoveIconNow").click(function() {
    App.startPageLoading({animate: true});
    var id              = $('#id').val();
    window.location.href= BASE_URL + "/setting/delete_icon/" + id;
});
// END REMOVE ICON //

$('#autocomplete').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
      console.log("enter");
    e.preventDefault();
    return false;
  }
});



        // This example displays an address form, using the autocomplete feature
        // of the Google Places API to help users fill in the information.

        // This example requires the Places library. Include the libraries=places
        // parameter when you first load the API. For example:
        // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

        var placeSearch, autocomplete;


        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            var str = JSON.stringify(place.geometry.location);
            var a = str.split(",");
            var b = a[0].substring(7);
            var c = a[1].substring(6);
            var d = c.replace("}","");
//console.log(d);
            showPosition(b,d);

        }

        // Bias the autocomplete object to the user's geographical location,
        // as supplied by the browser's 'navigator.geolocation' object.
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }

        function showPosition(lat,lng)
        {
            $("#lat").val(lat);
            $("#long").val(lng);
            latlon=new google.maps.LatLng(lat,lng)
            mapholder=document.getElementById('mapholder')
            mapholder.style.height='250px';
            mapholder.style.width='100%';

            var myOptions={
                center:latlon,zoom:14,
                mapTypeId:google.maps.MapTypeId.ROADMAP,
                mapTypeControl:false,
                navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
            };
            var map=new google.maps.Map(document.getElementById("mapholder"),myOptions);
            var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
        }
