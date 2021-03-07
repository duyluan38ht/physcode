<?php
/**
 * Template for displaying template Our team element layout 1.
 *
 * This template can be overridden by copying it to yourtheme/builderpress/our-team/layout-1.php.
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

<div class="owl-carousel owl-theme our-team">
    <?php foreach ( $members as $member ) { ?>

        <?php $avatar_id = ! empty( $member['photo'] ) ? (int) $member['photo'] : ''; ?>

        <div class="item">
            <div class="item-inner">
                <!--                    photo-->
                <?php if ( $avatar_id ) { ?>
                    <div class="avatar-contact">
                        <div class="avatar">
                            <?php $image = wp_get_attachment_image_src( $avatar_id, 'full' ); ?>
                            <img src="<?php echo esc_attr( $image[0] ); ?>"
                                 width="<?php echo esc_attr( $image[1] ); ?>"
                                 height="<?php echo esc_attr( $image[2] ); ?>"
                                 alt="<?php esc_attr__( 'photo', 'builderpress' ); ?>">
                        </div>
                    </div>
                <?php } ?>

                <div class="information">
                    <!--                        name-->
                    <?php if ( ! empty( $member['name'] ) ) { ?>
                        <h3 class="name">
                            <?php if ( ! empty( $member['link'] ) ) { ?>
                            <a href="<?php echo esc_url( $member['link'] ); ?>" class="url">
                                <?php } ?>
                                <?php echo esc_html( $member['name'] ); ?>
                                <?php if ( ! empty( $member['link'] ) ) { ?>
                            </a>
                        <?php } ?>
                        </h3>
                    <?php } ?>

                    <!--                        job-->
                    <?php if ( ! empty( $member['job'] ) ) { ?>
                        <span class="job"><?php echo esc_html( $member['job'] ); ?></span>
                    <?php } ?>

                    <!--                        contact-->
                    <?php if ( ! empty( $member['contacts'] ) ) { ?>
                        <div class="contact">
                            <?php
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
                                                <a href="<?php echo esc_url( $contact['url'] ); ?>">
                                                    <i class="<?php echo esc_attr( $contact['font_ionicons'] ); ?> icon-ionicons"
                                                       aria-hidden="true"></i>
                                                </a>
                                            <?php }
                                            break;
                                        default:
                                            if ( ! empty( $contact['font_awesome'] ) && ! empty( $contact['url'] ) ) { ?>
                                                <a href="<?php echo esc_url( $contact['url'] ); ?>">
                                                    <i class="<?php echo esc_attr( $contact['font_awesome'] ); ?> icon-fontawesome"
                                                       aria-hidden="true"></i></a>
                                                <?php
                                            }
                                            break;
                                    }
                                }
                            ?>

                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    <?php } ?>
</div>