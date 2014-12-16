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
auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( lang_get( 'plugin_otrsintegration_title' ) );

print_manage_menu( );

?>

<br />
<form action="<?php echo plugin_page( 'config_update' )?>" method="post">
<?php echo form_security_field( 'plugin_otrsintegration_config_update' ) ?>

<table align="center" class="width50" cellspacing="1">
<tr>
	<td class="form-title" colspan="3">
		<?php echo lang_get( 'plugin_otrsintegration_title' ) . ': ' . lang_get( 'plugin_otrsintegration_config' )?>
	</td>
</tr>
<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="60%">
		<?php echo lang_get( 'plugin_otrsintegration_ticket_field_name' )?>
		<br /><span class="small"><?php echo lang_get( 'plugin_otrsintegration_ticket_field_name_desc' )?></span>
	</td>
	<td class="center" width="40%" colspan="2">
		<label><input type="text" name="ticket_field_name" 
			value="<?php  echo plugin_config_get( 'otrs_field', 'OTRS Ticket') ?>"/></label>
	</td>
</tr>
<tr>
	<td class="center" colspan="3">
		<input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' )?>" />
	</td>
</tr>

</table>
</form>

<?php
html_page_bottom();
