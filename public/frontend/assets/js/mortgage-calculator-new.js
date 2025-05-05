$(document).ready(function() {
    // Format currency
    function formatCurrency(value) {
        return 'NPR ' + new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // Format percentage
    function formatPercentage(value) {
        return value + '%';
    }

    // Handle input formatting
    $('.currency-input').on('input', function() {
        // Remove non-numeric characters except decimal point
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
    });

    $('.percentage-input').on('input', function() {
        // Remove non-numeric characters except decimal point
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));

        // Ensure percentage is not greater than 100
        if (parseFloat($(this).val()) > 100) {
            $(this).val(100);
        }
    });

    // Calculate mortgage payment
    function calculateMortgage() {
        // Get input values
        let totalAmount = parseFloat($('#total-amount').val()) || 0;
        let downPayment = parseFloat($('#down-payment').val()) || 0;
        let interestRate = parseFloat($('#interest-rate').val()) || 0;
        let loanTerm = parseFloat($('#loan-term').val()) || 30;
        let paymentFrequency = $('#payment-frequency').val();

        // Calculate loan amount
        let loanAmount = totalAmount - (totalAmount * (downPayment / 100));

        // Convert annual interest rate to monthly
        let monthlyInterestRate = (interestRate / 100) / 12;

        // Calculate number of payments
        let numberOfPayments = loanTerm * 12;

        // Calculate monthly payment using the mortgage formula
        let monthlyPayment = 0;

        if (interestRate > 0) {
            monthlyPayment = loanAmount *
                (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) /
                (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
        } else {
            // If interest rate is 0, simple division
            monthlyPayment = loanAmount / numberOfPayments;
        }

        // Adjust payment based on frequency
        let payment = monthlyPayment;
        let paymentLabel = "Monthly Payment";

        if (paymentFrequency === 'biweekly') {
            payment = (monthlyPayment * 12) / 26;
            paymentLabel = "Bi-Weekly Payment";
        } else if (paymentFrequency === 'weekly') {
            payment = (monthlyPayment * 12) / 52;
            paymentLabel = "Weekly Payment";
        } else if (paymentFrequency === 'yearly') {
            payment = monthlyPayment * 12;
            paymentLabel = "Yearly Payment";
        }

        // Calculate total payment and interest
        let totalPayment = payment * (paymentFrequency === 'monthly' ? numberOfPayments :
                                     paymentFrequency === 'biweekly' ? 26 * loanTerm :
                                     paymentFrequency === 'weekly' ? 52 * loanTerm : loanTerm);
        let totalInterest = totalPayment - loanAmount;

        // Display results
        $('#payment-amount').text(formatCurrency(payment));
        $('#payment-label').text(paymentLabel);
        $('#loan-amount').text(formatCurrency(loanAmount));
        $('#total-interest').text(formatCurrency(totalInterest));
        $('#total-payment').text(formatCurrency(totalPayment));

        // Show results
        $('.mortgage-results').slideDown();
    }

    // Handle calculate button click
    $('#calculate-mortgage').on('click', function(e) {
        e.preventDefault();
        calculateMortgage();
    });

    // Handle property price auto-fill if available
    if ($('#property-price').length) {
        let propertyPrice = parseFloat($('#property-price').data('price')) || 0;
        if (propertyPrice > 0) {
            $('#total-amount').val(propertyPrice);
        }
    }

    // Handle reset button
    $('#reset-calculator').on('click', function(e) {
        e.preventDefault();
        $('#mortgage-calculator-form')[0].reset();
        $('.mortgage-results').slideUp();
    });
});
