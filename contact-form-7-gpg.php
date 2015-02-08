<?php
/*
Plugin Name: Contact Form 7 GPG
Plugin URI: http://pugstaller.com/contact-form-7-gpg/
Description: This Plugin adds GnuPG encryption to Contact Form 7.
Author: Lukas Pugstaller
Version: 0.1
Author URI: http://pugstaller.com/
License: GPL
*/

/*  Copyright 2015 Lukas Pugstaller (email: web@pugstaller.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 1, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die("No script kiddies please!");



//* ==================== ADMIN PAGE ==================== */

function wpcf7_gpg_menu() {
    add_options_page( "Contact Form 7 GPG", "Contact Form 7 GPG", "manage_options", __FILE__, "wpcf7_gpg_options" );
}

if ( is_admin() ) {
	include_once( "contact-form-7-gpg-admin.php" );
	add_action( "admin_menu", "wpcf7_gpg_menu" );
}



//* ==================== MAIN SCRIPT ==================== */

function wpcf7_gpg() {
	$public_key = get_option( "wpcf7_gpg_public_key" );
	if ( !empty( $public_key ) ) {
		try {
			$wpcf7 = WPCF7_ContactForm::get_current();
			$submission = WPCF7_Submission::get_instance();
			require_once 'libs/GPG.php';
			$gpg = new GPG();
			$pub_key = new GPG_Public_Key( $public_key );
			if ( $submission ) {
				$posted_data = $submission->get_posted_data();
				if ( empty( $posted_data ) ) return;
				$mail = $wpcf7->prop('mail');
				$mail['body'] = $gpg->encrypt($pub_key,$mail['body']);
				$wpcf7->set_properties( array(
					"mail" => $mail
				) );
				return $wpcf7;
			}
		} catch (Exception $e) {
			// send message without encryption and inform the admin
			wp_mail( get_option( 'admin_email' ), "Error - Contact Form 7 GPG", "The message you will receive couldn't be encrypted. Please check your settings or contact me: web@pugstaller.com." );
		}
	}
}
add_action( "wpcf7_before_send_mail", "wpcf7_gpg");



//* ==================== INSTALL ==================== */

function wpcf7_gpg_install() {
	$public_key = get_option( "wpcf7_gpg_public_key" );
	if ( $public_key == FALSE ) add_option( "wpcf7_gpg_public_key", "" );
}
register_activation_hook( __FILE__, "wpcf7_gpg_install" );



//* ==================== UNINSTALL ==================== */

function wpcf7_gpg_uninstall() {

	$option_name = 'wpcf7_gpg_public_key';

	delete_option( $option_name );

	// For site options in multisite
	delete_site_option( $option_name );

	//drop a custom db table
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpcf7_gpg_public_key" );

	//note in multisite looping through blogs to delete options on each blog does not scale. You'll just have to leave them.
}
register_uninstall_hook( __FILE__, "wpcf7_gpg_uninstall" );

?>