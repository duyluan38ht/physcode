<?php
/**
 * Template for displaying h5p tab in user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/profile/tabs/h5p_items.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();
$user    = $profile->get_user();

$filter_status = LP_Request::get_string( 'filter-status' );
$curd          = new LP_H5p_CURD();
$query         = $curd->profile_query_h5p_items( $profile->get_user_data( 'id' ), array( 'status' => $filter_status ) );
?>

<div class="learn-press-subtab-content">
	<h3 class="profile-heading"><?php _e( 'My H5P Items', 'learnpress-h5p' ); ?></h3>

	<?php if ( $filters = $curd->get_h5p_items_filters( $profile, $filter_status ) ) { ?>
		<ul class="lp-sub-menu">
			<?php foreach ( $filters as $class => $link ) { ?>
				<li class="<?php echo $class; ?>"><?php echo $link; ?></li>
			<?php } ?>
		</ul>
	<?php } ?>

	<?php if ( $query['items'] ) { ?>
		<table class="lp-list-table profile-list-h5p profile-list-table">
			<thead>
			<tr>
				<th class="column-course"><?php _e( 'Course', 'learnpress-h5p' ); ?></th>
				<th class="column-h5p"><?php _e( 'H5P Items', 'learnpress-h5p' ); ?></th>
				<th class="column-padding-grade"><?php _e( 'Passing Grade', 'learnpress-h5p' ); ?></th>
				<th class="column-status"><?php _e( 'Status', 'learnpress-h5p' ); ?></th>
				<th class="column-mark"><?php _e( 'Mark', 'learnpress-h5p' ); ?></th>
				<th class="column-time-interval"><?php _e( 'Interval', 'learnpress-h5p' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $query['items'] as $user_h5p ) { ?>
				<?php
				/**
				 * @var $user_h5p LP_User_Item_h5p
				 */
				$h5p = learn_press_get_h5p( $user_h5p->get_id() );
				$courses    = learn_press_get_item_courses( array( $user_h5p->get_id() ) );

				// for case h5p was un-assign from course
				if ( ! $courses ) {
					continue;
				}

				$course       = learn_press_get_course( $courses[0]->ID );
				$course_data  = $user->get_course_data( $course->get_id() );
				$user_item_id = $course_data->get_item( $h5p->get_id() )->get_user_item_id();
				if( ! $user_item_id ){
					$user_item_id = learn_press_get_user_item_id( $user->get_id(), $h5p->get_id(), $course->get_id() );
                }
				$mark         = learn_press_get_user_item_meta( $user_item_id, 'score', true );
				$completed    = $user->has_item_status( array( 'completed' ), $h5p->get_id(), $course->get_id() ); ?>
				<tr>
					<td class="column-course">
						<?php if ( $courses ) { ?>
							<a href="<?php echo $course->get_permalink() ?>">
								<?php echo $course->get_title( 'display' ); ?>
							</a>
						<?php } ?>
					</td>
					<td class="column-h5p">
						<?php if ( $courses ) { ?>
							<a href="<?php echo $course->get_item_link( $user_h5p->get_id() ) ?>">
								<?php echo $h5p->get_title( 'display' ); ?>
							</a>
						<?php } ?>
					</td>
					<td class="column-padding-grade">
						<?php echo $h5p->get_data( 'passing_grade' ); ?>
					</td>
					<td class="column-status">
						<?php echo $completed ? __( 'Completed', 'learnpress-h5p' ) : __( 'Not completed', 'learnpress-h5p' ); ?>
					</td>
					<td class="column-mark">
						<?php
						if ( $completed ) {
							echo $mark . '/' . learn_press_get_user_item_meta( $user_item_id, 'max_score', true );

							if ( ! $completed ) {
								$status = __( 'completed', 'learnpress-h5p' );
							} else {
								$status = ( $mark / learn_press_get_user_item_meta( $user_item_id, 'max_score', true ) ) * 100 >= $h5p->get_data( 'passing_grade' ) ? __( 'passed', 'learnpress-h5p' ) : __( 'failed', 'learnpress-h5p' );
							} ?>
							<span class="lp-label label-<?php echo esc_attr( $status ); ?>"><?php esc_html_e( $status ); ?></span>
						<?php } else {
							echo '-';
						} ?>
					</td>
					<td class="column-time-interval">
						<?php echo( $user_h5p->get_time_interval( 'display' ) ); ?>
					</td>
				</tr>
				<?php continue; ?>
				<tr>
					<td colspan="4"></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr class="list-table-nav">
				<td colspan="2" class="nav-text">
					<?php echo $query->get_offset_text(); ?>
				</td>
				<td colspan="4" class="nav-pages">
					<?php $query->get_nav_numbers( true ); ?>
				</td>
			</tr>
			</tfoot>
		</table>

	<?php } else { ?>
		<?php learn_press_display_message( __( 'No h5p items!', 'learnpress-h5p' ) ); ?>
	<?php } ?>
</div>
