/**
 * Address to Coordinates Helper
 * This script helps convert addresses to latitude and longitude coordinates
 * for use in property listings
 */

function initAddressToCoordinates() {
    // Check if the address input field exists
    const addressInput = document.getElementById('property-address');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const geocodeButton = document.getElementById('geocode-address');
    
    if (!addressInput || !latitudeInput || !longitudeInput || !geocodeButton) {
        return;
    }
    
    // Add click event to the geocode button
    geocodeButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        const address = addressInput.value;
        if (!address) {
            alert('Please enter an address first');
            return;
        }
        
        // Use Google Maps Geocoding API
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                const lat = results[0].geometry.location.lat();
                const lng = results[0].geometry.location.lng();
                
                // Update the latitude and longitude fields
                latitudeInput.value = lat;
                longitudeInput.value = lng;
                
                // Show success message
                alert('Coordinates found!\nLatitude: ' + lat + '\nLongitude: ' + lng);
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    });
}

// Initialize when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initAddressToCoordinates);
