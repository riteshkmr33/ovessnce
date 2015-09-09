$(function() {
    /* Change status code starts here */
	$(document.body).on('click', 'button.mediachangeStatus', function(){
		
		var status_id = $('select.action').val();
		var rowIds = Array();
		
		$('input.checkboxes:checked').each(function(){
			rowIds.push($(this).val());
		});
		
		$('div#error').html('').removeClass('note-warning note-success note-danger').hide('slow');
		
		if (status_id != '') {
			$('input#status_value').val(status_id);
			if (rowIds.length > 0) {
				if (confirm("Are sure want to change the status of selected record..??")) {
					$('form#statusForm').submit();
				}
			} else {
				$('div#error').html('Please select at least 1 record to change the status..!!').addClass('note-warning').show('slow');
			}
		} else {
			$('div#error').html('Please select status to apply..!!').addClass('note-warning').show('slow');
		}
		
	});
	/* Change status code ends here */
});
