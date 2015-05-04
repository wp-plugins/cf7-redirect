<?php
/*
  Plugin Name: Contact Form 7 Redirect
  Plugin URI: http://scriptbaker.com
  Description: Easily redirect contact form 7 to any thank you page URL.
  Version: 1.0
  Author: Tahir Yasin
  Author URI: http://scriptbaker.com
  Text Domain: cf7-redirect

  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Sets the additional_settings property
 * on_sent_ok contains the Redirect URL 
 * @param WPCF7_ContactForm $WPCF7_ContactForm
 */

function wpcf7r_additional_settings($WPCF7_ContactForm) {

    if (!$WPCF7_ContactForm->prop('additional_settings')) {
        $wpcf7_redirect_url = get_post_meta($WPCF7_ContactForm->id(), '_redirect_url', true);
        if ($wpcf7_redirect_url) {
            $properties = array('additional_settings' => "on_sent_ok: \"location = '" . $wpcf7_redirect_url . "';\"");
            $WPCF7_ContactForm->set_properties($properties);
        }
    }
}

add_action("wpcf7_before_send_mail", "wpcf7r_additional_settings");

/**
 * Register Redirect URL metabox to each contact form
 */

function wpcf7r_add_form_options() {
    add_meta_box('redirecturldiv', __( 'Redirect URL', 'cf7-redirect' ), 'wpcf7r_redirect_page_meta_box', null, 'mail', 'core');
}

/**
 * Show Redirect URL metabox to each contact form
 */
function wpcf7r_redirect_page_meta_box($post) {
    $wpcf7_redirect_url = get_post_meta($post->id(), '_redirect_url', true);
    ?>
    <input type="text" id="wpcf7-redirect-url" name="wpcf7-redirect-url" cols="100" rows="8" value="<?php echo $wpcf7_redirect_url; ?>" class="wide" />
    <p class="description"><label for="wpcf7-redirect-url">URL of some thank you page. (e.g. www.example.com/thank-you)</label></p>
    <?php
}

add_action('wpcf7_add_meta_boxes', 'wpcf7r_add_form_options');

/**
 * Save Redirect URL
 * @param WPCF7_ContactForm $contact_form
 */
function wpcf7r_save_redirect_url($contact_form) {
	$sanitized_url = sanitize_text_field(trim($_POST['wpcf7-redirect-url']));
    $url = esc_url_raw($sanitized_url);
    update_post_meta($contact_form->id, '_redirect_url', wpcf7_normalize_newline_deep($url));
}

add_action('wpcf7_after_save', 'wpcf7r_save_redirect_url');
?>