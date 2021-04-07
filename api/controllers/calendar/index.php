<?php
	header("Content-Type: text/html");
	use benhall14\phpCalendar\Calendar as Calendar;
	$calendar = new Calendar();

    # if needed, add event

	$calendar->addEvent(
	    '2017-01-14',   # start date in Y-m-d format
	    '2017-01-14',   # end date in Y-m-d format
	    'My Birthday',  # event name text
	    true,           # should the date be masked - boolean default true
	    ['myclass', 'abc']   # (optional) additional classes in either string or array format to be included on the event days
	);

    # or for multiple events

	$events = array();

	$events[] = array(
		'start' => '2017-01-14',
		'end' => '2017-01-14',
		'summary' => 'My Birthday',
		'mask' => true,
		'classes' => ['myclass', 'abc']
	);

	$events[] = array(
		'start' => '2017-12-25',
		'end' => '2017-12-25',
		'summary' => 'Christmas',
		'mask' => true
	);

	$calendar->addEvents($events);

	# finally, to draw a calendar

	echo $calendar->draw(date('Y-m-d')); # draw this months calendar

	# this can be repeated as many times as needed with different dates passed, such as:

	echo $calendar->draw(date('Y-01-01')); # draw a calendar for January this year

	echo $calendar->draw(date('Y-02-01')); # draw a calendar for February this year

	echo $calendar->draw(date('Y-03-01')); # draw a calendar for March this year

	echo $calendar->draw(date('Y-04-01')); # draw a calendar for April this year

	echo $calendar->draw(date('Y-05-01')); # draw a calendar for May this year

	echo $calendar->draw(date('Y-06-01')); # draw a calendar for June this year

	# to use the pre-made color schemes, include the calendar.css stylesheet and pass the color choice to the draw method, such as:

	echo $calendar->draw(date('Y-m-d'));            # print a (default) turquoise calendar

	echo $calendar->draw(date('Y-m-d'), 'purple');  # print a purple calendar

	echo $calendar->draw(date('Y-m-d'), 'pink');    # print a pink calendar

	echo $calendar->draw(date('Y-m-d'), 'orange');  # print a orange calendar

	echo $calendar->draw(date('Y-m-d'), 'yellow');  # print a yellow calendar

	echo $calendar->draw(date('Y-m-d'), 'green');   # print a green calendar

	echo $calendar->draw(date('Y-m-d'), 'grey');    # print a grey calendar

	echo $calendar->draw(date('Y-m-d'), 'blue');    # print a blue calendar
    
?>