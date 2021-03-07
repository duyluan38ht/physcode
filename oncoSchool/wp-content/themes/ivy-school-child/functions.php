<?php
function thim_child_enqueue_styles() {
	if ( is_multisite() ) {
		wp_enqueue_style( 'thim-child-style', get_stylesheet_uri() );
	} else {
		wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 100 );

// Remove paid membership pro waninrg
add_action('init', function () {
    remove_action('admin_notices', 'pmpro_license_nag');
});

// Fix for broken password reset feature
if ( !function_exists( 'thim_replace_retrieve_password_message' ) ) {
        function thim_replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
				$reset_page = '/account/';
                $reset_link = add_query_arg(
                        array(
                                'action' => 'rp',
                                'key'    => $key,
                                'login'  => rawurlencode( $user_login )
                        ), $reset_page
                );

                // Create new message
                $message = thim_get_login_page_url(). "\r\n\r\n";
                $message = __( 'Foi solicitava uma nova senha para a sua conta no site Onco School.<o>' ) . "\n";
                $message .= sprintf( __( '' ), network_home_url( '/' ) ) . "\n";
                $message .= sprintf( __( '<p><b>Utilizador</b>: %s', 'eduma' ), $user_login ) . "\n";
                $message .= __( '', 'eduma' ) . "\n";
                $message .= __( '<p>Para efetuar o reset da password visite o seguinte link: <p> https://www.onco.school', 'eduma' ) . "";
                $message .= $reset_link . "\n";

                return $message;
        }
}

if ( !function_exists( 'is_wpe' ) && !function_exists( 'is_wpe_snapshot' ) ) {
        add_filter( 'retrieve_password_message', 'thim_replace_retrieve_password_message', 10, 4 );
}

