<?php
/**
 * Template for displaying loop course of section.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/loop-section.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $section ) ) {
	return;
}
$course = learn_press_get_the_course();

if ( ! apply_filters( 'learn-press/section-visible', true, $section, $course ) ) {
	return;
}

$user        = learn_press_get_current_user();
$user_course = $user->get_course_data( get_the_ID() );
$items       = $section->get_items();

?>

<li <?php $section->main_class(); ?> id="section-<?php echo esc_attr( $section->get_slug() ); ?>" data-id="<?php echo esc_attr( $section->get_slug() ); ?>" data-section-id="<?php echo esc_attr( $section->get_id() ); ?>">
	<?php do_action( 'learn-press/before-section-summary', $section, $course->get_id() ); ?>

	<div class="section-header">
		<div class="section-left">
			<span class="section-toggle">
				<i class="fas fa-minus"></i>
				<i class="fas fa-plus"></i>
			</span>
			<h5 class="section-title">
				<?php
				$title = $section->get_title();
				echo ! $title ? _x( 'Untitled', 'template title empty', 'course-builder' ) : $title;
				?>
				<?php $description = $section->get_description(); ?>
				<?php if ( $description ) : ?>
					<p class="section-desc"><?php echo $description; ?></p>
				<?php endif; ?>
			</h5>	
		</div>
		<?php $percent = $user_course->get_percent_completed_items( '', $section->get_id() ); ?>
		<div class="section-meta">
			<div class="learn-press-progress" title="<?php echo esc_attr( sprintf( __( 'Section progress %s%%', 'course-builder' ), round( $percent, 2 ) ) ); ?>">
			<span class="number"><?php printf( '%d/%d', $user_course->get_completed_items( '', false, $section->get_id() ), $section->count_items( '', true ) ); ?></span>
			</div>
		</div>
		
	</div>

	<?php do_action( 'learn-press/before-section-content', $section, $course->get_id() ); ?>

	<?php if ( ! $items ) : ?>
		<?php learn_press_display_message( __( 'No items in this section', 'course-builder' ) ); ?>
	<?php else : ?>

		<ul class="section-content">

			<?php foreach ( $items as $item ) : ?>
				<?php 
					$post_type = str_replace( 'lp_', '', $item->get_item_type() );
					if ( empty( $count[$post_type] ) ) {
						$count[$post_type] = 1;
					} else {
						$count[$post_type] ++;
					}
				?>
					<li class="<?php echo implode( ' ', $item->get_class() ); ?>" data-id="<?php echo esc_attr( $item->get_id() ); ?>">

						<?php do_action( 'learn-press/before-section-loop-item', $item, $section, $course ); ?>

						<?php $item_link = $user->can_view_item( $item->get_id() ) ? $item->get_permalink() : 'javascript:void(0);'; ?>
                        
                       
                        
                        <div class="meta-rank">
						<div class="rank"><?php echo $section->get_position() . '.' . $count[ $post_type ]; ?></div>
						</div>
						<?php do_action( 'learn-press/begin-section-loop-item', $item ); ?>
						<a class="section-item-link" href="<?php echo apply_filters( 'learn-press/section-item-permalink', $item_link, $item, $section, $course ); ?>">

							<?php
							do_action( 'learn-press/before-section-loop-item-title', $item, $section, $course );

							learn_press_get_template(
								'single-course/section/' . $item->get_template(),
								array(
									'item'    => $item,
									'section' => $section,
								)
							);

							do_action( 'learn-press/after-section-loop-item-title', $item, $section, $course );
							?>
						</a>

						<?php do_action( 'learn-press/after-section-loop-item', $item, $section, $course ); ?>
					</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php do_action( 'learn-press/after-section-summary', $section, $course->get_id() ); ?>
</li>
