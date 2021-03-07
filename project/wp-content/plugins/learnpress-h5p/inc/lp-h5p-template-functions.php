<?php
/**
 * All functions for LearnPress H5P Content templates.
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Functions
 * @version 3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'learn_press_content_item_h5p_duration' ) ) {
	/**
	 * H5p duration.
	 */
	function learn_press_content_item_h5p_duration() {
		$course        = LP_Global::course();
		$user          = learn_press_get_current_user();
		$h5p           = LP_Global::course_item();
		$h5p_data      = $user->get_item_data( $h5p->get_id(), $course->get_id() );
		$status        = $user->get_item_status( $h5p->get_id(), $course->get_id() );
		$duration      = learn_press_h5p_get_time_remaining( $h5p_data );
		$duration_time = get_post_meta( $h5p->get_id(), '_lp_duration', true );

		if ( in_array( $status, array( 'started', 'h5p_doing', 'completed' ) ) ) {
			learn_press_h5p_get_template( 'content-h5p/duration.php', array(
				'duration'      => $duration,
				'duration_time' => $duration_time
			) );
		}
	}
}

if ( ! function_exists( 'learn_press_content_item_h5p_title' ) ) {
	/**
	 * H5p title.
	 */
	function learn_press_content_item_h5p_title() {
		learn_press_h5p_get_template( 'content-h5p/title.php' );
	}
}

if ( ! function_exists( 'learn_press_content_item_h5p_intro' ) ) {
	/**
	 * H5p introduction.
	 */
	function learn_press_content_item_h5p_intro() {
		$h5p = LP_Global::course_item();

		if ( ! lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/intro.php' );
		}
	}
}

if ( ! function_exists( 'learn_press_content_item_h5p_buttons' ) ) {
	/**
	 * H5p buttons.
	 */
	function learn_press_content_item_h5p_buttons() {
		$h5p = LP_Global::course_item();
		if ( lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/buttons.php' );
		}
	}
}

if ( ! function_exists( 'learn_press_content_item_h5p_content' ) ) {
	/**
	 * H5p content.
	 */
	function learn_press_content_item_h5p_content() {
		learn_press_h5p_get_template( 'content-h5p/content.php' );
	}
}

if ( ! function_exists( 'learn_press_content_item_h5p_condition' ) ) {
	/**
	 * H5p content.
	 */
	function learn_press_content_item_h5p_condition() {
		$h5p = LP_Global::course_item();
		if ( lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/condition.php' );
		}
	}
}

if ( ! function_exists( 'learn_press_h5p_summary' ) ) {
	/**
	 * H5p attachment.
	 */
	function learn_press_h5p_summary() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || $user->has_item_status( array(
				'completed',
			), $h5p->get_id(), $course->get_id() )
		) {
			$conditional_h5p = get_post_meta( $h5p->get_id(), '_lp_h5p_interact', true );
			$plugin          = H5P_Plugin::get_instance();
			$content         = $plugin->get_content( $conditional_h5p );
			$library         = ! empty( $content['library']['name'] ) ? $content['library']['name'] : '';
			if ( $library != '' && in_array( $library, lp_h5p_can_summary_types_list() ) ) {
				$library_file_name = strtolower( str_replace( 'H5P.', '', $library ) ) . '.php';
				learn_press_h5p_get_template( 'content-h5p/summary/' . $library_file_name, array( 'h5p_content' => $content ) );
			}
		}

	}
}

if ( ! function_exists( 'learn_press_h5p_start_button' ) ) {
	/**
	 * Start button.
	 */
	function learn_press_h5p_start_button() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || ! $user->has_course_status( $course->get_id(), array( 'enrolled' ) ) || $user->has_item_status( array(
				'started',
				'h5p_doing',
				'completed',
				'evaluated'
			), $h5p->get_id(), $course->get_id() )
		) {
			return;
		}
		learn_press_h5p_get_template( 'content-h5p/buttons/start.php' );
	}
}


if ( ! function_exists( 'learn_press_h5p_nav_buttons' ) ) {
	/**
	 * Nav button.
	 */
	function learn_press_h5p_nav_buttons() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( ! $user->has_item_status( array(
			'started',
			'h5p_doing'
		), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/controls.php' );
	}
}


if ( ! function_exists( 'learn_press_h5p_after_sent' ) ) {
	/**
	 * Sent button.
	 */
	function learn_press_h5p_after_sent() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( ! $user->has_item_status( array(
			'completed'
		), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/sent.php' );
	}
}

if ( ! function_exists( 'learn_press_h5p_result' ) ) {
	/**
	 * Result button.
	 */
	function learn_press_h5p_result() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( ! $user->has_item_status( array(
			'completed'
		), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/result.php' );
	}
}

if ( ! function_exists( 'learn_press_h5p_complete' ) ) {
	/**
	 * Retake button.
	 */
	function learn_press_h5p_complete() {
		$course = LP_Global::course();
		$user   = LP_Global::user();
		$h5p    = LP_Global::course_item();
		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || ! $user->has_course_status( $course->get_id(), array( 'enrolled' ) ) || $user->has_item_status( array(
				'completed'
			), $h5p->get_id(), $course->get_id() )
		) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/complete.php' );
	}
}

add_filter( 'learn-press/can-view-h5p', 'learn_press_h5p_filter_can_view_item', 10, 4 );

function learn_press_h5p_filter_can_view_item( $view, $h5p_id, $user_id, $course_id ) {
	$user           = learn_press_get_user( $user_id );
	$_lp_submission = get_post_meta( $course_id, '_lp_submission', true );
	if ( $_lp_submission === 'yes' ) {
		if ( ! $user->is_logged_in() ) {
			return 'not-logged-in';
		} else if ( ! $user->has_enrolled_course( $course_id ) ) {
			return 'not-enrolled';
		}
	}

	return $view;
}

if ( ! function_exists( 'learn_press_h5p_fe_setting' ) ) {
	function learn_press_h5p_fe_setting() {
		learn_press_h5p_get_template( 'frontend-editor/item-settings.php' );
	}
}

if ( ! function_exists( 'learn_press_h5p_fe_fields' ) ) {
	function learn_press_h5p_fe_fields() {
		learn_press_h5p_get_template( 'frontend-editor/form-fields.php' );
	}
}

if ( ! function_exists( 'learn_press_h5p_fe_manager_link' ) ) {
	function learn_press_h5p_fe_manager_link() {
		$manager_page = get_option( 'h5p_students_man_page_id' );
		if ( $manager_page ) {
			$url  = get_page_link( $manager_page );
			$args = array(
				'manager_page' => $manager_page,
				'page_url'     => $url
			);
			learn_press_h5p_get_template( 'frontend-editor/manager-link.php', $args );
		} else {
			return;
		}
	}
}

if ( ! function_exists( 'lp_h5ps_setup_shortcode_page_content' ) ) {

	function lp_h5ps_setup_shortcode_page_content( $content ) {
		global $post;

		$page_id = $post->ID;

		if ( ! $page_id ) {
			return $content;
		}

		if ( get_option( 'h5p_students_man_page_id' ) == $page_id ) {
			$current_content = get_post( $page_id )->post_content;
			if ( strpos( $current_content, '[h5p_students_manager' ) === false ) {
				$content = '[' . apply_filters( 'h5p_students_manager_shortcode_tag', 'h5p_students_manager' ) . ']';
			}
		} elseif ( get_option( 'h5p_evaluate_page_id' ) == $page_id ) {
			$current_content = get_post( $page_id )->post_content;
			if ( strpos( $current_content, '[h5p_evaluate_form' ) === false ) {
				$content = '[' . apply_filters( 'h5p_students_evaluate_shortcode_tag', 'h5p_evaluate_form' ) . ']';
			}
		}

		return do_shortcode( $content );
	}

}