<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( isset( $_POST['security_settings_nonce'] ) && wp_verify_nonce( $_POST['security_settings_nonce'], 'security_settings_action_nonce' )) {
  $options = array();
  foreach ($_POST as $key => $value) {
    if ( substr( $key, 0, 16 ) == $this->id . '_' ) {
      $options[ substr( $key, 16 ) ] = sanitize_text_field( $value );
    }
  }
  if ( isset( $options ) && !empty( $options ) ) {
    update_option( 'wa_protect_site_options', $options );
  }
}
?>
<?php $this->options = get_option( 'wa_protect_site_options' ); ?>

<div class="wrap">
  <h2><?php _e( 'WA Protect Site Settings', 'wa-protect-site' ); ?></h2>

  <form method="post" name="<?php echo $this->id; ?>_popupSettingOptions" id="popupSettingOptions">

    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label><?php _e( 'Popup Title', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_title" name="wa_protect_site_popup_title" value="<?php echo $this->options['popup_title']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Text', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_text" name="wa_protect_site_popup_text" value="<?php echo $this->options['popup_text']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Textbox Placeholder', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_textbox_placeholder" name="wa_protect_site_popup_textbox_placeholder" value="<?php echo $this->options['popup_textbox_placeholder']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Button Label', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_button_label" name="wa_protect_site_popup_button_label" value="<?php echo $this->options['popup_button_label']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'User Popup Tiitle', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="user_popup_title" name="wa_protect_site_user_popup_title" value="<?php echo $this->options['user_popup_title']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'User Popup Text', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="user_popup_text" name="wa_protect_site_user_popup_text" value="<?php echo $this->options['user_popup_text']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'User Popup Textbox Placeholder', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="user_popup_textbox_placeholder" name="wa_protect_site_user_popup_textbox_placeholder" value="<?php echo $this->options['user_popup_textbox_placeholder']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'User Popup Button Label', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="user_popup_button_label" name="wa_protect_site_user_popup_button_label" value="<?php echo $this->options['user_popup_button_label']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Wrong password error text', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="wrong_password_text" name="wa_protect_site_wrong_password_text" value="<?php echo $this->options['wrong_password_text']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Overlay Color', 'wa-protect-site' ); ?></label></th>
          <td>
            <?php
            if ( $this->options['popup_overlay_color'] ) {
              $overlay_background_color = $this->options['popup_overlay_color'];
            } else {
              $overlay_background_color = '#000000';
            }
            ?>
            <input type="text" class="regular-text" id="popup_overlay_color" name="wa_protect_site_popup_overlay_color" value="<?php echo $overlay_background_color; ?>" required />
            <div class="cw-color-picker" rel="popup_overlay_color"></div>
          </td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Overlay Opacity', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_overlay_opacity" name="wa_protect_site_popup_overlay_opacity" value="<?php echo $this->options['popup_overlay_opacity']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Background Color', 'wa-protect-site' ); ?></label></th>
          <td>
            <?php
            if ( $this->options['popup_background_color'] ) { $main_background_color = $this->options['popup_background_color']; }
            else { $main_background_color = __( '#ffffff', 'wpcoders' ); }
            ?>
            <input type="text" class="regular-text" id="popup_background_color" name="wa_protect_site_popup_background_color" value="<?php echo $main_background_color; ?>" required />
            <div class="cw-color-picker" rel="popup_background_color"></div>
          </td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Upload Logo', 'wa-protect-site' ); ?></label></th>
          <td>
            <input type="text" id="the_logo_image" name="wa_protect_site_the_logo_image" value="<?php echo $this->options['the_logo_image']; ?>" style="width:65%;" placeholder="Insert URL here" />
            <input id="btn_1" class="upload_image_button" type="button" value="Upload Logo" style="width:100px;" />
          </td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Popup Background Opacity', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="popup_background_opacity" name="wa_protect_site_popup_background_opacity" value="<?php echo $this->options['popup_background_opacity']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Cookie Duration (days)', 'wa-protect-site' ); ?></label></th>
          <td><input type="text" class="regular-text" id="cookie_duration" name="wa_protect_site_cookie_duration" value="<?php echo $this->options['cookie_duration']; ?>" required /></td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Require password change after first login', 'wa-protect-site' ); ?></label></th>
          <td>
            <select class="" name="wa_protect_site_change_required">
              <option value="no" <?php if ( $this->options['change_required'] == 'no' ) echo 'selected'; ?>>No</option>
              <option value="yes" <?php if ( $this->options['change_required'] == 'yes' ) echo 'selected'; ?>>Yes</option>
            </select>
          </td>
        </tr>

        <tr>
          <th scope="row"><label><?php _e( 'Pages to protect', 'wa-protect-site' ); ?></label></th>
          <td>
            <select class="" name="wa_protect_site_pages_to_protect">
              <option value="all" <?php if ( $this->options['pages_to_protect'] == 'all' ) echo 'selected'; ?>>All</option>
              <option value="postmeta" <?php if ( $this->options['pages_to_protect'] == 'postmeta' ) echo 'selected'; ?>>Only specified by custom post meta</option>
              <option value="none" <?php if ( $this->options['pages_to_protect'] == 'none' ) echo 'selected'; ?>>None</option>
            </select>
          </td>
        </tr>

      </tbody>

    </table>

    <p class="submit">
      <?php wp_nonce_field( 'security_settings_action_nonce', 'security_settings_nonce' ); ?>
      <button class="button button-primary" id="submit"><?php _e( 'Save Settings', 'wa_protect_site' ); ?></button>
    </p>

  </form>

</div>

<script type="text/javascript">
jQuery(document).ready( function() {

  // colorpicker field
  jQuery('.cw-color-picker').each(function(){
    var $this = jQuery(this), id = $this.attr('rel');
    $this.farbtastic('#' + id);
  });

  /* Logo upload button */
  var formfield;
  jQuery('.upload_image_button').click(function() {
    jQuery('html').addClass('Image');
    formfield = jQuery(this).prev().attr('id');
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
    return false;
  });

  window.original_send_to_editor = window.send_to_editor;
  window.send_to_editor = function(html){

    if (formfield) {
      fileurl = jQuery('img',html).attr('src');
      jQuery('#'+formfield).val(fileurl);
      tb_remove();
      jQuery('html').removeClass('Image');
    }
    else {
      window.original_send_to_editor(html);
    }
  };
});
</script>
