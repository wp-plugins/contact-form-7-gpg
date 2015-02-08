<?php

defined('ABSPATH') or die("No script kiddies please!");

function wpcf7_gpg_options() {
	echo '<div class="wrap">
          <h2><abbr title="GNU Privacy Guard">GnuPG</abbr> for Contact Form 7</h2>';

	# update options
	if ( isset( $_POST["wpcf7_gpg_public_key"] ) ) {
		update_option( "wpcf7_gpg_public_key", $_POST["wpcf7_gpg_public_key"] );
		echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
	}

	# get options
	$wpcf7_gpg_public_key = get_option( "wpcf7_gpg_public_key" );

	echo '<div id="mainblock">';
	echo '<form action="" method="post">';
	echo '<p><label>Your public GPG key:<br>';
	echo '<textarea name="wpcf7_gpg_public_key" cols="65" rows="18" style="font-family:monospace; font-size:11px;">' . $wpcf7_gpg_public_key . '</textarea></label></p>';
	echo '<input type="submit" value="Save Settings" class="button-primary">';
	echo '</form>';
	echo '</div>';
}

?>