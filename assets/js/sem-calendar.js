jQuery(function ($) {
    $('.sem-calendar-month').hide();
    var currentYear = $('#current_year').val();
    var currentMonth = $('#current_month').attr('value').replace('month-', '');

    var allYear = document.getElementById('current_year').options;
    var minYear = allYear[0].value;
    var maxYear = allYear[allYear.length - 1].value;


    $('#' + currentYear + '-' + currentMonth).show();

    $('#current_year').change(function () {
        refreshMonth();
    });
    $('#current_month').change(function () {
        refreshMonth();
    });
    $('#previous').click(function () {
        var prevYear;
        var prevMonth;

        if (currentMonth == 1) {
            prevMonth = 12;
            prevYear = parseInt(currentYear) - 1;
        }
        else {
            prevMonth = parseInt(currentMonth) - 1;
            prevYear = currentYear;
        }

        if (minYear <= prevYear) {
            $('#next').prop('disabled', false);
            changeMonth(prevMonth, prevYear);
            $('#current_year').val(prevYear);
            $('#current_month').attr('value', 'month-' + prevMonth);
        } else {
            $('#previous').prop('disabled', true);
        }
    });
    $('#next').click(function () {
        var nextYear;
        var nextMonth;

        if (currentMonth == 12) {
            nextMonth = 1;
            nextYear = parseInt(currentYear) + 1;
        }
        else {
            nextMonth = parseInt(currentMonth) + 1;
            nextYear = currentYear;
        }

        if (maxYear >= nextYear) {
            $('#previous').prop('disabled', false);
            changeMonth(nextMonth, nextYear);
            $('#current_year').val(nextYear);
            $('#current_month').attr('value', 'month-' + nextMonth);
        } else {
            $('#next').prop('disabled', true);
        }
    });


    function refreshMonth() {

        var year = $('#current_year').val();
        var month = $('#current_month').attr('value').replace('month-', '');

        changeMonth(month, year);

    }

    function changeMonth(month, year) {
        if ((month != currentMonth) || (year != currentYear)) {
            $('#' + currentYear + '-' + currentMonth).slideUp();
            $('#' + year + '-' + month).slideDown();

            currentMonth = month;
            currentYear = year;
        }
    }


});

