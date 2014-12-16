<?php
/* OpenCS's MantisBT OTRS Integration Plugin - An OTRS integration plugin for MantisBT
 * Copyright (C) 2014 Open Communications Security
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */ 
require_once( '../../core.php' );
require_once('OTRSIntegration.php');

$bugid = $_GET['bugid'];
?>
<form method="get" action="OTRSTest.php">
	Bug ID: <input type="text" name="bugid" value="<?php echo($bugid); ?>">
	<input type="submit">
</form>
<?php 
if (($bugid != null) && ($bugid != '')) {
	
	$bug = bug_get($bugid);
	$test = OTRSGetStatusChange($bug);
	echo($test->old_status_name.'<br>');
	echo($test->new_status_name.'<br>');
}
?>

