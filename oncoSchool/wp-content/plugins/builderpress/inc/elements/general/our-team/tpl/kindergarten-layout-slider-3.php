<?php
/**
 * Template for displaying template Our team element kindergarten-layout-slider-2.
 *
 * This template can be overridden by copying it to yourtheme/builderpress/our-team/kindergarten-layout-slider-2.php.
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

?>
<div class="wrap-element">
    <div class="slide-teacher js-call-slick-col" data-numofslide="3" data-numofscroll="1" data-loopslide="1" data-autoscroll="0" data-speedauto="6000" data-respon="[3, 1], [3, 1], [2, 1], [2, 1], [1, 1]">
        <div class="slide-slick">
            <?php foreach ( $members as $member ) { ?>
                <?php $avatar_id = ! empty( $member['photo'] ) ? (int) $member['photo'] : ''; ?>
                <div class="item-slick">
                    <div class="teacher-item">
                        <a href="#" class="teacher-image">
                            <?php
                                $sizes = apply_filters( 'builder-press/our-team/kindergarten-layout-slider-3/image-size', '146x146' );
                                builder_press_get_attachment_image($avatar_id, $sizes);
                            ?>
                        </a>

                        <div class="teacher-text">
                            <h4 class="title">
                                <a href="<?php echo esc_url($member['link']) ?>" class="name"><?php echo esc_html( $member['name'] ); ?></a><?php if ( ! empty( $member['job'] ) ) { ?>/<span class="info"><?php echo esc_html( $member['job'] ); ?></span><?php } ?>
                            </h4>

                            <?php if ( ! empty( $member['description'] ) ) { ?>
                                <div class="description">
                                        <?php echo esc_html($member['description']); ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="teacher-social">
                            <?php if ( ! empty( $member['contacts'] ) ) { ?>
                                <?php if ( ! empty( $member['contacts'] ) ) {
                                    $contacts = $member['contacts'];
                                    if ( is_string( $contacts ) ) {
                                        $el_contacts = explode('|', $contacts);
                                        $contacts = array();
                                        $i = 0;
                                        while ( $i < $el_contacts[0] ) {
                                            $i++;
                                            $fields_contact = explode( ',', $el_contacts[1] );
                                            $contact_item = array();
                                            foreach ( $fields_contact as $value_c ) {
                                                $key = $value_c.$i;
                                                if ( isset( $member[$value_c.$i] ) ){
                                                    $contact_item[$value_c] = $member[$value_c.$i];
                                                }
                                            }
                                            $contacts[] = $contact_item;
                                        }
                                    }
                                    foreach ( $contacts as $contact ) {
                                        switch ( $contact['icon'] ) {
                                            case 'font_ionicons':
                                                if ( ! empty( $contact['font_ionicons'] ) ) { ?>
                                                    <a href="<?php echo esc_url( $contact['url'] ); ?>" class="social-item">
                                                        <i class="<?php echo esc_attr( $contact['font_ionicons'] ); ?> icon-ionicons"
                                                           aria-hidden="true"></i>
                                                    </a>
                                                <?php }
                                                break;
                                            default:
                                                if ( ! empty( $contact['font_awesome'] ) && ! empty( $contact['url'] ) ) { ?>
                                                    <a href="<?php echo esc_url( $contact['url'] ); ?>" class="social-item">
                                                        <i class="<?php echo esc_attr( $contact['font_awesome'] ); ?> icon-fontawesome"
                                                           aria-hidden="true"></i></a>
                                                    <?php
                                                }
                                                break;
                                        }
                                    }
                                } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="wrap-dot-slick"></div>
    </div>
</div>
