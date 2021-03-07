<?php
/**
 * Template for displaying H5p after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/buttons/result.php.
 *
 * @author  ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$course              = LP_Global::course();
$current_h5p       = LP_Global::course_item();
$user                = learn_press_get_current_user();
$current_useritem_id = learn_press_get_user_item_id( $user->get_id(), $current_h5p->get_id(), $course->get_id() );
if( ! $current_useritem_id ){
	$course_data  = $user->get_course_data( $course->get_id() );
	$current_useritem_id = $course_data->get_item( $current_h5p->get_id() )->get_user_item_id();
}
$result_grade    = learn_press_h5p_get_result( $current_h5p->get_id(), $user->get_id(), $course->get_id() );
?>

<div class="h5p-result <?php echo esc_attr( $result_grade['grade'] ); ?>">

    <h5><?php _e( 'Congratulation, you already completed this!', 'learnpress-h5p' ); ?></h5>

    <div class="result-grade">
        <span class="result-achieved"><?php echo $result_grade['user_mark']; ?></span>
        <span class="result-require"><?php echo $result_grade['mark']; ?></span>
        <p class="result-message"><?php echo sprintf( __( 'Your result is <strong>%s</strong>', 'learnpress-h5p' ), $result_grade['grade'] == '' ? __( 'Ungraded', 'learnpress-h5p' ) : $result_grade['grade'] ); ?> </p>
    </div>
</div>
