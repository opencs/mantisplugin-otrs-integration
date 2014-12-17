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

function OTRSGetOTRSConnectPath() {
	return realpath(dirname(__FILE__)).'/otrsconnect/otrsconnect.py';
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
        	'EVENT_REPORT_BUG' => 'newBug',
        );
    }

    function config() {
        return array(
            'otrs_field' => 'OTRS Ticket',
        );
    }
    
    function getOTRSTicket($p_bug) {
    	
    	$t_field_name = plugin_config_get( 'otrs_field', 'OTRS Ticket', true);
    	
    	$t_field_id = custom_field_get_id_from_name($t_field_name);
    	
    	$t_ticket = custom_field_get_value($t_field_id, $p_bug->id);    	
    	if ($t_ticket == false) {
    		return null;
    	}
   		
    	// Trim data
    	$t_ticket = trim($t_ticket);
   		if ($t_ticket == '') {
   			$t_ticket = null;
   		}
   		
    	return $t_ticket;
    }
    
    function processBug($p_bug, $p_statusChange){
    	
    	// Recover the OTRS ticket number
    	$t_ticket = $this->getOTRSTicket($p_bug);
    	if ($t_ticket != null) {
    		$t_subject = 'Mantis bug #'.$p_bug->id.' status changed to '.$p_statusChange->new_status_name;
    		$t_body = 'Mantis bug #'.$p_bug->id.' status changed from '.$p_statusChange->old_status_name.' to '.$p_statusChange->new_status_name.'.';
    		$t_cmd = OTRSGetOTRSConnectPath().
      			' "'.escapeshellcmd($t_ticket).'"'.
    			' "'.$t_subject.'"'.
    			' "'.$t_body.'" >/dev/null';
    		log_event(LOG_EMAIL, $t_cmd);
    		log_event(LOG_EMAIL, exec($t_cmd));
    	}
    }

    function updateBug( $p_event, $p_bug ) {
    	
    	// Search for the status change
    	$p_statusChange = OTRSGetStatusChange($p_bug);
    	if ($p_statusChange != null) {
    		$this->processBug($p_chained_param, $p_statusChange);
    	}

        return $p_chained_param;
    }
    
    function processNewBug($p_bug){
    	 
    	// Recover the OTRS ticket number
    	$t_ticket = $this->getOTRSTicket($p_bug);
    	if ($t_ticket != null) {
    		$t_subject = 'Mantis bug #'.$p_bug->id.' added to this ticket';
    		$t_body = 'The Mantis bug #'.$p_bug->id.' has been created for this ticket.';
    		$t_cmd = OTRSGetOTRSConnectPath().
    		' "'.escapeshellcmd($t_ticket).'"'.
    		' "'.$t_subject.'"'.
    		' "'.$t_body.'" >/dev/null';
    		log_event(LOG_EMAIL, $t_cmd);
    		log_event(LOG_EMAIL, exec($t_cmd));
    	}
    }    

    
    function newBug( $p_event, $p_bug, $i_bugid) {
    	 
    	// Search for the status change
    	$this->processNewBug($p_bug);
    
    	return $p_chained_param;
    }
}
