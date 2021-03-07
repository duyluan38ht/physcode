<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime( d.getTime() + ( exdays * 24 * 60 * 60 * 1000 ) );
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}

function getCookie( cname ) {
	var name = cname + "=";
	var ca = document.cookie.split(";");
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while ( c.charAt(0) == " " ) {
			c = c.substring( 1 );
		}
		if ( c.indexOf( name ) == 0 ) {
			return c.substring( name.length, c.length );
		}
	}
	return "";
}
</script>

<?php
global $wpdb;

if ( isset( $_COOKIE['accessProtectedWebsite'] ) && !empty( $_COOKIE['accessProtectedWebsite'] ) ) {

	// se tem cookie, valida se o cookie esta correcto
	// se nao esta correcto, invalida o cookie e mostra ecra de login
	if ( ! $this->validate_cookie( $_COOKIE['accessProtectedWebsite'] ) ) {
		echo '<script type="text/javascript">setCookie("accessProtectedWebsite", "", 0);</script>';
		$this->access_website_popup_container();
		die();
	}

} else {
	// se preencheu o form de login
	if ( isset( $_POST['submitform'] ) && $_POST['submitform'] == 'login' && isset( $_POST['security_admin_nonce'] ) && wp_verify_nonce( $_POST['security_admin_nonce'], 'security_admin_action_nonce' ) ) {

		$check = $this->check_password( $_POST['password'], true );

		// password rerrada
		if ( ! $check ) {
			$_SESSION['error'] = $this->options['wrong_password_text'];
			$this->access_website_popup_container();
			die();
		}

		// se a password esta certo, tem de confirmar se é para alterar ou nao
		if ( $this->options['change_required'] == 'yes' ) {
			// se for para alterar, invoca form de alteraçaõ, senão passa a frente
			if ( !isset( $check->upassword ) || empty( $check->upassword ) ) {
				$_SESSION['passwordUsed'] = $this->encode_password( $_POST['password'] );
				$this->set_user_password_popup();
				die();
			}
		}

		// se esta certa, grava o cookie e avança
		echo '<script type="text/javascript">setCookie("accessProtectedWebsite", "' . $check->id . $check->password . '", ' . $this->options['cookie_duration'] . ');</script>';
		return;

	}

	// se adicionou nova password
	if ( isset( $_POST['submitform'] ) && $_POST['submitform'] == 'define_password' && isset( $_POST['security_user_nonce'] ) && wp_verify_nonce( $_POST['security_user_nonce'], 'security_user_action_nonce' ) ) {

		$userPassword = $this->encode_password( $_POST['password'] );

		// Actualiza a nova password na tabela
		$up = $wpdb->update(
			$this->table_name,
			array(
				'password' => $userPassword,
				'upassword' => $_SESSION['passwordUsed'],
				'is_pass_change' => 1,
				'change_ip' => esc_sql( $_SERVER['REMOTE_ADDR'] ),
				'change_time' => current_time( 'Y-m-d H:i:s')
			),
			array( 'password' => esc_sql( $_SESSION['passwordUsed'] ) )
		);

		$getRow = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE password = %s AND upassword = %s", $userPassword, esc_sql( $_SESSION['passwordUsed'] ) ) );

		if( $getRow ) {
			// se validou, grava cookie e evança
			unset( $_SESSION['passwordUsed'] );
			echo '<script type="text/javascript">setCookie("accessProtectedWebsite", "' . $getRow->id . $getRow->password . '", ' . $this->options['cookie_duration'] . ');</script>';
			return;
		} else {
			$this->set_user_password_popup();
			die();
		}

	}

	$this->access_website_popup_container();
	die();

}
