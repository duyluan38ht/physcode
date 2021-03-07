<?php
/**
 * Template for displaying default template Brands element layout 1.
 *
 * This template can be overridden by copying it to yourtheme/builderpress/brands/layout-1.php.
 *
 * @author      ThimPress
 * @package     BuilderPress/Templates
 * @version     1.0.0
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var $params array - shortcode params
 * @var $brands_title
 * @var $pagination
 */

if ( isset( $params['items-title'])) {

?>

<div class="js-call-slick-col" data-pagination="<?php echo esc_attr( $params['pagination'] ); ?>" data-numofslide="<?php echo esc_attr( $items_visible ); ?>" data-numofscroll="1"
     data-loopslide="0" data-autoscroll="0" data-speedauto="6000"
     data-respon="[<?php echo esc_attr( $items_visible ); ?>, 1], [<?php echo esc_attr( $items_visible ); ?>, 1], [<?php echo esc_attr( $items_tablet ); ?>, 1], [<?php echo esc_attr( $items_mobile ); ?>, 1], [<?php echo esc_attr( $items_mobile ); ?>, 1]">

    <div class="slide-slick">
		<?php foreach ( $params['items-title'] as $key => $brand ) { ?>
            <div class="item-slick">
                <div class="item-brands">
					<?php if ( isset( $brand['img'] ) ) {
						$logo = wp_get_attachment_image_src( $brand['img'], 'full' );
						$img  = '';

						if ( $logo ) {
							$img = '<img src="' . $logo[0] . '" width="' . $logo[1] . '" height="' . $logo[2] . '" alt="' . esc_attr__( 'Logo', 'builderpress' ) . '">';
						}

						if ( $img ) { ?>
                            <button data-balloon="<?php echo $brand['title']; ?>" data-balloon-pos="up">
                                <?php echo ent2ncr( $img ); ?>
                            </button>
                        <?php }
					} ?>
                </div>
            </div>
		<?php } ?>
    </div>

    <?php if ($params['pagination']) { ?>
        <div class="wrap-arrow-slick">
            <div class="arow-slick prev-slick">
                <i class="ion-ios-arrow-back"></i>
            </div>

            <div class="arow-slick next-slick">
                <i class="ion-ios-arrow-forward"></i>
            </div>
        </div>
    <?php } ?>
</div>


<?php }