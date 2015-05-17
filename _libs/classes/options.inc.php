<?php
	class Options{
		function GetMonths($type){
			$months = array();
			
			switch($type){
				case 1:
					$months[sizeof($months)] = "Jan";
					$months[sizeof($months)] = "Feb";
					$months[sizeof($months)] = "Mar";
					$months[sizeof($months)] = "Apr";
					$months[sizeof($months)] = "May";
					$months[sizeof($months)] = "Jun";
					$months[sizeof($months)] = "Jul";
					$months[sizeof($months)] = "Aug";
					$months[sizeof($months)] = "Sep";
					$months[sizeof($months)] = "Oct";
					$months[sizeof($months)] = "Nov";
					$months[sizeof($months)] = "Dec";
					break;
				case 2:
					$months[sizeof($months)] = "January";
					$months[sizeof($months)] = "February";
					$months[sizeof($months)] = "March";
					$months[sizeof($months)] = "April";
					$months[sizeof($months)] = "May";
					$months[sizeof($months)] = "June";
					$months[sizeof($months)] = "July";
					$months[sizeof($months)] = "August";
					$months[sizeof($months)] = "September";
					$months[sizeof($months)] = "October";
					$months[sizeof($months)] = "November";
					$months[sizeof($months)] = "December";
					break;
			}
			
			return $months;
		}
	}