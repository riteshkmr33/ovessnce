<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino 2.5                                                        # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

if (is_numeric($_GET['id'])) {

	echo '<div class="padded-box"><form method="post" action="index.php">';
	        echo '<p>Are you sure you want to end this conversation?</p>';
	        echo '
	        <input type="hidden" name="id" id="id" value="'.$_GET['id'].'" />
	        <button class="btn btn-primary" type="submit" name="delete_conv" value="delete">Yes</button>
	        </form></div>
	';
}
?>