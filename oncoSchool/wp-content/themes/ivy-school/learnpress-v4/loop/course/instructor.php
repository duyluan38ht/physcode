<?php
/**
 * Template for displaying instructor of course within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/loop/course/instructor.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$course = LP_Global::course();
?>
<div class="instructor">
    <div class="ava">
        <?php echo ent2ncr($course->get_instructor()->get_profile_picture('',68)); ?>
    </div>
    <div class="name">
        <?php echo ent2ncr($course->get_instructor_html()); ?>
    </div>
</div>
