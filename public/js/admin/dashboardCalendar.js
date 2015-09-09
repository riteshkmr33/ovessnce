   /* Dashboard calender code starts */
   // Documentation http://arshaw.com/fullcalendar/docs/event_data/events_json_feed/
    var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	var h = {};

	if ($('#calendar').width() <= 400) {
		$('#calendar').addClass("mobile");
		h = {
			left: 'title, prev, next',
			center: '',
			right: 'today,month,agendaWeek,agendaDay'
		};
	} else {
		$('#calendar').removeClass("mobile");
		if (App.isRTL()) {
			h = {
				right: 'title',
				center: '',
				left: 'prev,next,today,month,agendaWeek,agendaDay'
			};
		} else {
			h = {
				left: 'title',
				center: '',
				right: 'prev,next,today,month,agendaWeek,agendaDay'
			};
		}               
	}
	
	$('#calendar').fullCalendar('destroy'); // destroy the calendar
	$('#calendar').fullCalendar({ //re-initialize the calendar
		disableDragging: false,
		header: h,
		editable: false,
		events: 
        {
            url: (OVEconfig.BASEURL+'/admin/admin/bookings'), // use the `url` property
            //color: 'yellow',    // an option!
            //textColor: 'black'  // an option!
        }
	});
	/* Dashboard calender code ends */
