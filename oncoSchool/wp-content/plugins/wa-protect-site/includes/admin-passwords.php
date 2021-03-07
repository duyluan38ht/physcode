<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
global $wpdb;

if ( isset( $_POST['action'] ) && $_POST['action'] == 'delete' ) {
  if(isset( $_POST['delete_nonce'] ) && wp_verify_nonce( $_POST['delete_nonce'], 'security_delete_nonce' )) {

    $encryptedpass = $this->encode_password( $_POST['password'] );

    // $wpdb->delete( $this->table_name, array( 'id' => sanitize_text_field( $_POST['id'] ), array( '%d' ) ) );
    $wpdb->delete( $this->table_name, array( 'id' => $_POST['id'] ), array( '%d' ) );
    $successDelete = true;
  }
}

if ( isset( $_POST['action'] ) && $_POST['action'] == 'generate' ) {
  if(isset( $_POST['generate_nonce'] ) && wp_verify_nonce( $_POST['generate_nonce'], 'security_generate_nonce' )) {

    $generated = $this->generate_random_password( $_POST['password_count'] );

  }
}

if ( isset( $_POST['action'] ) && $_POST['action'] == 'createbulk' ) {
  if(isset( $_POST['createbulk_nonce'] ) && wp_verify_nonce( $_POST['createbulk_nonce'], 'security_createbulk_nonce' )) {

    $passwords = explode( PHP_EOL, $_POST['passwords'] );

    if ( isset( $passwords ) && !empty( $passwords ) ) {
      $created = array();
      foreach ($passwords as $password) {
        if ( !empty( $password ) ) {
          $created[] = $this->add_new_password( $password );
        }
      }
    }

  }
}

$passwords = $wpdb->get_results( "SELECT * FROM $this->table_name" );

?>

<div class="wrap">

  <h1>WA Protect Site</h1>

  <?php if ( $error == true ) : ?>
    <div class="notice notice-error"><p><strong><?php _e( 'Error:', 'wa-protect-site' ); ?></strong> <?php echo $message; ?></p></div>
  <?php endif; ?>

  <?php if ( $successDelete == true ): ?>
    <div class="notice notice-success"><p><strong><?php _e( 'Success:', 'wa-protect-site' ); ?></strong> <?php _e( 'Password deleted successfully.', 'wa-protect-site' ); ?></p></div>
  <?php endif; ?>

  <?php if ( $generated ): ?>
    <div class="notice notice-success">
      <p><strong><?php _e( 'Success:', 'wa-protect-site' ); ?></strong> <?php echo count( $generated ); ?> <?php _e( 'password(s) generated successfully.', 'wa-protect-site' ); ?></p>
    </div>
  <?php endif; ?>

  <?php if ( $created ): ?>
    <div class="notice notice-success">
      <p><strong><?php _e( 'Success:', 'wa-protect-site' ); ?></strong> <?php echo count( $created ); ?> <?php _e( 'password(s) created successfully.', 'wa-protect-site' ); ?></p>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label><?php _e( 'Generate Bulk Passwords', 'wa-protect-site' ); ?></label></th>
          <td>
            <?php wp_nonce_field( 'security_generate_nonce', 'generate_nonce' ); ?>
            <input type="number" name="password_count" value="10" class="regular-text ltr" step="1">
            <input type="hidden" name="action" value="generate">
            <input type="submit" value="<?php _e('Generate', 'wa-protect-site'); ?>" class="button">
          </td>
        </tr>
      </tbody>
    </table>
  </form>

  <form method="post" action="">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="passwords"><?php _e( 'Create passwords', 'wa-protect-site' ); ?></label>
          </th>
          <td>
            <?php wp_nonce_field( 'security_createbulk_nonce', 'createbulk_nonce' ); ?>
            <textarea name="passwords" id="passwords" class="large-text code" placeholder="<?php _e('One password per line', 'wa-protect-site'); ?>"></textarea>
            <input type="hidden" name="action" value="createbulk">
            <input type="submit" value="<?php _e('Create', 'wa-protect-site'); ?>" class="button">
          </td>
        </tr>
      </tbody>
    </table>
  </form>

  <table class="table widefat">
    <thead>
      <tr>
        <th><?php _e( '#', 'wa-protect-site' ); ?></th>
        <th><?php _e( 'Password', 'wa-protect-site' ); ?></th>
        <th><?php _e( 'Old Password', 'wa-protect-site' ); ?></th>
        <th><?php _e( 'IP', 'wa-protect-site' ); ?></th>
        <th><?php _e( 'Time', 'wa-protect-site' ); ?></th>
        <th><?php _e( 'Action', 'wa-protect-site' ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($passwords as $password): ?>
        <tr>
          <td><?php echo $password->id; ?></td>
          <td><?php echo $this->decode_password( $password->password ); ?></td>
          <td><?php echo $this->decode_password( $password->upassword ); ?></td>
          <td><?php echo $password->change_ip; ?></td>
          <td><?php echo $password->change_time; ?></td>
          <td>
            <form method="post" action="">
              <?php wp_nonce_field( 'security_delete_nonce', 'delete_nonce' ); ?>
              <input type="hidden" name="action" value="delete">
              <button type="submit" name="id" value="<?php echo $password->id; ?>" class="button button-primary"><?php _e( 'Delete', 'wa-protect-site' ); ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
