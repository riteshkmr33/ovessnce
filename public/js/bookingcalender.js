$(function() {
    $('#sp_id').data('spid', $('#sp_id').val());
    getSlots($('#sp_id').data('spid'), 0, '', updateWorkdays);
    applyCalendar('.datetimepicker');
    var duration = ($('select#duration_list:first').val() != '') ? $('select#duration_list:first').find('option:selected').text().replace(' Mins', '') : 0;
    var address = ($('select[name="service_location"]:first').val() != '') ? $('select[name="service_location"]:first').val() : '';
    $('div.xdsoft_datetimepicker').data({duration: duration, sp: $('#sp_id').data('spid'), address: address});
    $(document.body).on('click', 'input#submitBooking', function() {
        $('select, input').prop('disabled', false);
    });

    $('form').submit(function() {
        if ($('input.datetimepicker').val() == '- Select date -') {
            $('input.datetimepicker').val('');
        }
        return true;
    });
    
    $(document.body).on('change', 'select[name="service_location"]', function(){
        $('div.xdsoft_datetimepicker').data('address', $(this).val());
        getSlots($('#sp_id').data('spid'), 0, $(this).val(), updateWorkdays);
    });
});
