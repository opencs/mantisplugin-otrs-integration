<?php

//[date][userid][username][field][type][old_value][new_value]
class OTRSStatusChange {

	private $old_status;
	private $old_status_name;
	private $new_status;
	private $new_status_name;

	public function __construct($old_status, $new_status) {
		$this->old_status = $old_status;
		$this->old_status_name = get_enum_element( 'status', $old_status);
		$this->new_status = $new_status;
		$this->new_status_name = get_enum_element( 'status', $new_status);
	}	
	
	public function __get($name) {
		$v = $this->$name;
		return $v;
	}
}


/**
 * Recover the status change field.
 * @param int $bugid
 * @param int $time
 */
function OTRSGetStatusChange($bug) {
	$t_mantis_bug_history_table = $t_mantis_bug_history_table = db_get_table( 'mantis_bug_history_table');
	
	$bugid = $bug->id;
	$time = $bug->last_updated;
	
	$query = "SELECT old_value, new_value FROM $t_mantis_bug_history_table WHERE bug_id=? and field_name=? and date_modified=?";
	$result = db_query_bound( $query, Array( $bugid,  'status', $time));
	$result_count = db_num_rows( $result );
	if ($result_count == 0) {
		return null;
	} else {
		$t_row = db_fetch_array( $result );
		return new OTRSStatusChange($t_row['old_value'], $t_row['new_value']);
	}
}

class OTRSIntegrationPlugin extends MantisPlugin {
    function register() {
        $this->name = 'OTRS Integration';    # Proper name of plugin
        $this->description = 'OTRS integration plugin.';    # Short description of the plugin
        $this->page = '';           # Default plugin page

        $this->version = '1.0';     # Plugin version string
        $this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '1.2.0',#   Should always depend on an appropriate version of MantisBT
            );

        $this->page		= 'config_page';
        $this->author = 'Fabio Jun Takada Chino';         # Author/team name
        $this->contact = '';        # Author/team e-mail address
        $this->url = '';            # Support webpage
    }

    function hooks() {
        return array(
            'EVENT_UPDATE_BUG' => 'updateBug',
        );
    }

    function config() {
        return array(
            'otrs_field' => 'OTRS Ticket',
        );
    }
    
    function getOTRSTicket($p_bug) {
    	$t_field_name = plugin_config_get( 'otrs_field', 'OTRS Ticket', true);
   	
    	$t_ticket = bug_get_field($p_bug, $t_field_name);
    	if (($t_ticket != null) && ($t_ticket == '')){
    		$t_ticket = null;
    	}
    	return $t_ticket;
    }
    
    function processBug($p_bug, $p_statusChange){
    	
    	// Recover the OTRS ticket number
    	$t_ticket = $this->getOTRSTicket($p_bug);
    	log_event(LOG_EMAIL, $t_ticket);
    }

    function updateBug( $p_event, $p_chained_param ) {
    	
    	// Search for the status change
    	$p_statusChange = OTRSGetStatusChange($p_chained_param);
    	if ($p_statusChange != null) {
    		$this->processBug($p_chained_param, $p_statusChange);
    	}

        return $p_chained_param;
    }

}
