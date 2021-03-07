<?php
/**
 * Template for displaying default template St-list-categories element.
 *
 * This template can be overridden by copying it to yourtheme/builderpress/st-list-categories/layout-1.php.
 *
 * @author      ThimPress
 * @package     BuilderPress/Templates
 * @version     1.0.0
 * @author      Thimpress, vinhnq
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

?>

<div class="wrap-element">
    <div class="heading-post">
        <div class="text">
            <h3 class="title">
                <?php echo $title ? $title : esc_html__( 'Trending Categories', 'builderpress' );?>
            </h3>

            <div class="description">
                <?php echo $sub_title ? $sub_title : '';?>
            </div>
        </div>
    </div>

    <div class="list-posts">


        <div class="slide-posts js-call-slick-cats" data-numofshow="3" data-numofscroll="1" data-loopslide="0" data-autoscroll="0" data-speedauto="6000" data-responsive="[3, 1], [3, 1], [2, 1], [2, 1], [1, 1]">
            <div class="wrap-arrow-slick">
                <div class="arow-slick prev-slick">
                    <i class="ion ion-ios-arrow-left"></i>
                </div>

                <div class="arow-slick next-slick">
                    <i class="ion ion-ios-arrow-right"></i>
                </div>
            </div>

            <div class="slide-slick">
                <div class="item-slick">
                    <?php $i=0; foreach( $categories as $category ) { $i++; ?>
                        <?php
                        $size = ($i==3 || $i==6 || $i==9) ? '390x588' : '390x294';
                        $image = get_term_meta( $category->term_id, 'thim_cat_image', true );
                        ?>
                        <div class="post-item">
                            <?php builder_press_get_attachment_image( $image['id'], $size ); ?>
                            <div class="content">
                                <span class="title"><?php echo esc_html( $category->name );?></span>
                                <?php echo $category->category_count;?> <?php echo ($category->category_count==0 || $category->category_count==1) ? esc_html__('Movie','video-blog') : esc_html__('Movies','video-blog');?>
                            </div>
                        </div>
                        <?php
                        if($i==2 || $i==3 || $i==5 || $i==7 || $i==8 || $i==10) echo '</div><div class="item-slick">';
                        ?>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>