<?php
add_filter( 'option_blog_public', '__return_zero' );

add_action( 'admin_notices', function() {
    if ( current_user_can( 'manage_options' ) && 'index.php' === $GLOBALS['pagenow'] ) {
        $user = get_user_by( 'login', 'demo' );
        if ( $user->token ) {
            $url = add_query_arg( 'auto', $user->token, trailingslashit( home_url() ) );
?>
<script type="text/javascript">
function copyAutoLoginLink() {
    var copyText = document.getElementById( 'autoLoginLink' );
    copyText.select();
    document.execCommand("copy");
    return false;
}
</script>
<div class="notice notice-success is-dismissible">
    <p>
        <strong><?php _e( 'Automatic Login:', 'sandboxes' ); ?></strong>
        <input id="autoLoginLink" style="width: 580px; border: none; background: white" type="text" class="regular-text" value="<?php echo esc_attr( $url ); ?>" readonly />
        <button class="button-primary" onclick="copyAutoLoginLink()"><?php _e( 'Copy', 'sandboxes' ); ?></button>
    </p>
</div>
<?php
        }
    }
} );

add_action( 'wp_login', function( $login ) {
    $parts = explode( '.', wp_parse_url( WP_HOME, PHP_URL_HOST ), 2 );
    wp_remote_post( 'https://sandbox.otgs.work/wp-json/sandboxes/v1/login/' . reset( $parts ) );
} );

add_action( 'init', function() {
    if ( ! empty( $_GET['auto'] ) ) {
        $user  = get_user_by( 'login', 'demo' );
        $token = $_GET['auto'];
        if ( $user && $user->token && $user->token === $token ) {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );
            do_action( 'wp_login', $user->user_login, $user );
            wp_redirect( get_dashboard_url() );
            exit;
        }
    }
}, -PHP_INT_MAX );
