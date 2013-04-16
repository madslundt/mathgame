<?php
	if ($view == 'group') {
		if ($_POST['find'] > -1) { // Specified group
		
		} else { // ALL groups
		
		}
		
		
	} else if ($view == 'user') {
		if ($_POST['find']) { // Specified user
			
		} else { // ALL users
			
		}
		
		
	} else {
		
		if ($_POST['find'] > -1) { // Specified level
			echo 'test1';
		} else { // All levels
			echo 'test2';
		}
	}
?>