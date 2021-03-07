<?php
/**
 * All template hooks for LearnPress H5P Content templates.
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Hooks
 * @version 3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
* @see learn_press_content_item_h5p_title
 * @see learn_press_content_item_h5p_intro
 */
add_action( 'learn-press/before-content-item-summary/lp_h5p', 'learn_press_content_item_h5p_title', 10 );
add_action( 'learn-press/before-content-item-summary/lp_h5p', 'learn_press_content_item_h5p_intro', 15 );

/**
 * @see learn_press_content_item_h5p_buttons
 * @see learn_press_course_finish_button
 */
add_action( 'learn-press/after-content-item-summary/lp_h5p', 'learn_press_content_item_h5p_buttons', 15 );
add_action( 'learn-press/after-content-item-summary/lp_h5p', 'learn_press_course_finish_button', 15 );

/**
 * @see learn_press_content_item_h5p_content
 */
add_action( 'learn-press/content-item-summary/lp_h5p', 'learn_press_content_item_h5p_content', 5 );
add_action( 'learn-press/content-item-summary/lp_h5p', 'learn_press_content_item_h5p_condition', 10 );

/**
 * @see learn_press_h5p_result
 * @see learn_press_h5p_complete
 * @see learn_press_h5p_summary
 */
add_action( 'learn-press/h5p-buttons', 'learn_press_h5p_result', 15 );
add_action( 'learn-press/h5p-buttons', 'learn_press_h5p_summary', 10 );
add_action( 'learn-press/h5p-buttons', 'learn_press_h5p_complete', 20 );

/**
 * @see learn_press_h5p_fe_setting
 * @see learn_press_h5p_fe_fields
 * @see lp_h5ps_setup_shortcode_page_content
 */
/*add_action( 'learn-press/frontend-editor/item-settings-after', 'learn_press_h5p_fe_setting' );
add_action( 'learn-press/frontend-editor/form-fields-after', 'learn_press_h5p_fe_fields' );
add_action( 'learn-press/frontend-editor/item-extra-action', 'learn_press_h5p_fe_manager_link' );
add_filter( 'the_content', 'lp_h5ps_setup_shortcode_page_content' );*/

