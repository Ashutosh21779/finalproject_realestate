$(document).ready(function() {
    // Initialize view style based on saved preference or default to list
    let viewStyle = localStorage.getItem('propertyViewStyle') || 'list';
    setViewStyle(viewStyle);

    // View style toggle (list/grid)
    $('#view-style').on('change', function() {
        const viewStyle = $(this).val();
        setViewStyle(viewStyle);
        localStorage.setItem('propertyViewStyle', viewStyle);
    });

    function setViewStyle(style) {
        if (style === 'grid') {
            $('.wrapper').removeClass('list').addClass('grid');
            $('#view-style').val('grid');
        } else {
            $('.wrapper').removeClass('grid').addClass('list');
            $('#view-style').val('list');
        }
    }

    // Price range inputs validation and formatting
    $('.input-min, .input-max').on('input', function() {
        // Allow only numbers and commas
        let value = $(this).val().replace(/[^0-9,]/g, '');
        $(this).val(value);
    });

    // Format price inputs on blur
    $('.input-min, .input-max').on('blur', function() {
        let value = $(this).val().replace(/,/g, ''); // Remove existing commas
        if (value && !isNaN(value)) {
            // Format with commas for thousands
            $(this).val(Number(value).toLocaleString());
        }
    });

    // Handle form submission to ensure clean values are sent
    $('form').on('submit', function() {
        $('.input-min, .input-max').each(function() {
            // Store the original value with commas for display
            let displayValue = $(this).val();

            // Set the value to a clean number without commas for submission
            if (displayValue) {
                let cleanValue = displayValue.replace(/,/g, '');
                $(this).val(cleanValue);
            }
        });

        // Form will submit with clean values
        return true;
    });

    // Initialize select2 for better dropdown experience if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.wide').select2({
            width: '100%',
            minimumResultsForSearch: 7
        });
    }

    // Handle price range slider if it exists
    if ($('.price-range-slider').length) {
        const minPriceInput = $('input[name="min_price"]');
        const maxPriceInput = $('input[name="max_price"]');

        // Initialize the price range slider
        $('.price-range-slider').slider({
            range: true,
            min: 0,
            max: 1000000,
            values: [
                minPriceInput.val() ? parseInt(minPriceInput.val()) : 0,
                maxPriceInput.val() ? parseInt(maxPriceInput.val()) : 1000000
            ],
            slide: function(event, ui) {
                minPriceInput.val(ui.values[0]);
                maxPriceInput.val(ui.values[1]);
            }
        });
    }

    // Set selected values in filters if they exist in URL or from PHP variables
    function setFilterValues() {
        // First check for PHP variables passed from controller
        if (typeof propertyFilters !== 'undefined') {
            if (propertyFilters.property_status) {
                $('select[name="property_status"]').val(propertyFilters.property_status);
            }

            if (propertyFilters.ptype_id) {
                $('select[name="ptype_id"]').val(propertyFilters.ptype_id);
            }

            if (propertyFilters.state) {
                $('select[name="state"]').val(propertyFilters.state);
            }

            if (propertyFilters.bedrooms) {
                $('select[name="bedrooms"]').val(propertyFilters.bedrooms);
            }

            if (propertyFilters.bathrooms) {
                $('select[name="bathrooms"]').val(propertyFilters.bathrooms);
            }

            if (propertyFilters.min_price) {
                $('input[name="min_price"]').val(propertyFilters.min_price);
            }

            if (propertyFilters.max_price) {
                $('input[name="max_price"]').val(propertyFilters.max_price);
            }
        } else {
            // Fall back to URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Set property status
            if (urlParams.has('property_status')) {
                $('select[name="property_status"]').val(urlParams.get('property_status')).trigger('change');
            }

            // Set property type
            if (urlParams.has('ptype_id')) {
                $('select[name="ptype_id"]').val(urlParams.get('ptype_id')).trigger('change');
            }

            // Set state/location
            if (urlParams.has('state')) {
                $('select[name="state"]').val(urlParams.get('state')).trigger('change');
            }

            // Set bedrooms
            if (urlParams.has('bedrooms')) {
                $('select[name="bedrooms"]').val(urlParams.get('bedrooms')).trigger('change');
            }

            // Set bathrooms
            if (urlParams.has('bathrooms')) {
                $('select[name="bathrooms"]').val(urlParams.get('bathrooms')).trigger('change');
            }

            // Set price range
            if (urlParams.has('min_price')) {
                $('input[name="min_price"]').val(urlParams.get('min_price'));
            }

            if (urlParams.has('max_price')) {
                $('input[name="max_price"]').val(urlParams.get('max_price'));
            }
        }

        // Trigger change events for select2 dropdowns if select2 is available
        if (typeof $.fn.select2 !== 'undefined') {
            $('select.wide').trigger('change.select2');
        }
    }

    // Call the function to set filter values
    setFilterValues();

    // Clear all filters button
    $('.clear-filters').on('click', function(e) {
        e.preventDefault();
        $('select.wide').val('').trigger('change');
        $('input[name="min_price"], input[name="max_price"]').val('');

        // If on the search results page, redirect to all properties
        if (window.location.href.includes('property_search')) {
            window.location.href = '/properties';
        }
    });
});
