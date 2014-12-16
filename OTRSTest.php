<?php

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

