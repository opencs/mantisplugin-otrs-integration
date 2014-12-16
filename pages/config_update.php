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
form_security_validate( 'plugin_otrsintegration_config_update' );

$f_ticket_field_name = gpc_get_string( 'ticket_field_name' );

if ($f_ticket_field_name != null) {
	plugin_config_set( 'otrs_field', $f_ticket_field_name);
}

form_security_purge( 'plugin_otrsintegration_config_update' );
print_successful_redirect( plugin_page( 'config_page', true ) );
?>
