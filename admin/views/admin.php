<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
	        <?php
		// settings_errors( );
		// This print out all hidden setting fields
		settings_fields( 'twitter-fire_settings' );
		do_settings_sections( 'twitter_api_keys' );
?>
	        <?php submit_button(); ?>
	    </form>

</div>
