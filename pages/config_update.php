<?php
form_security_validate( 'plugin_otrsintegration_config_update' );

$f_ticket_field_name = gpc_get_string( 'ticket_field_name' );

if ($f_ticket_field_name != null) {
	plugin_config_set( 'otrs_field', $f_ticket_field_name);
}

form_security_purge( 'plugin_otrsintegration_config_update' );
print_successful_redirect( plugin_page( 'config_page', true ) );
?>
