$(document).ready(function() {
    $('.grid-table .checkbox-column-label').change(function () {
        var $allCheckboxes = $('.checkbox-column');
        if ($(this).prop('checked')) {
            $allCheckboxes.prop('checked', true);
        } else {
            $allCheckboxes.prop('checked', false);
        }
    });
});