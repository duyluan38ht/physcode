<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/H5p/Classes
 * @version  3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_H5p' ) ) {
	/**
	 * Class LP_Addon_Assignment
	 */
	class LP_Addon_H5p extends LP_Addon {

		/**
		 * Addon version
		 *
		 * @var string
		 */
		public $version = LP_ADDON_H5P_VER;

        /**
         * @var int flag to get the error
         */
        protected static $_error = 0;

		/**
		 * Require LP version
		 *
		 * @var string
		 */
		public $require_version = LP_ADDON_H5P_REQUIRE_VER;

		/**
		 * LP_Addon_Assignment constructor.
		 */
		public function __construct() {
			parent::__construct();

			if ( $this->_check_version() && $this->h5p_actived() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'lp_h5p_admin_assets' ), 20 );
				add_action( 'wp_enqueue_scripts', array( $this, 'lp_h5p_enqueue_scripts' ) );
				add_action( 'init', array( $this, 'lp_h5p_init' ) );
				add_action( 'init', array( $this, 'learnpress_h5p_add_rewrite_rules' ), 1000, 0 );
				add_filter( 'learn-press/course-support-items', array( $this, 'lp_h5p_put_type_here' ), 10, 2 );
				add_filter( 'learn-press/new-section-item-data', array( $this, 'new_h5p_item' ), 10, 4 );
				add_filter( 'learn-press/course-item-object-class', array( $this, 'h5p_object_class' ), 10, 4 );
				add_filter( 'learn-press/modal-search-items/exclude', array( $this, 'exclude_h5p_items' ), 10, 4 );
				// update h5p item in single course template
				add_filter( 'learn_press_locate_template', array( $this, 'update_h5p_template' ), 10, 2 );
				add_filter( 'learn-press/can-view-item', array( $this, 'learnpress_h5p_can_view_item' ), 10, 4 );
				add_filter( 'learn-press/evaluate_passed_conditions', array(
					$this,
					'learnpress_h5p_evaluate'
				), 10, 3 );
				add_filter( 'learn-press/get-course-item', array( $this, 'learnpress_h5p_get_item' ), 10, 3 );
				add_filter( 'learn-press/default-user-item-status', array(
					$this,
					'learnpress_h5p_default_user_item_status'
				), 10, 2 );
				add_filter( 'learn-press/user-item-object', array(
					$this,
					'learnpress_h5p_user_item_object'
				), 10, 2 );
				add_filter( 'learn-press/course-item-type', array(
					$this,
					'learnpress_h5p_course_item_type'
				), 10, 1 );
				add_filter( 'learn-press/block-course-item-types', array(
					$this,
					'learnpress_h5p_block_course_item_type'
				), 10, 1 );
				// get grade
				add_filter( 'learn-press/user-item-grade', array( $this, 'learnpress_h5p_get_grade' ), 10, 4 );
				// AJAX for logging results
				add_action('wp_ajax_lph5p_process', array($this, 'lph5p_process'));
				// add filter user access admin view assignment
				add_filter( 'learn-press/filter-user-access-types', array( $this, 'lp_h5p_add_filter_access' ) );
				// add user profile page tabs
				add_filter( 'learn-press/profile-tabs', array( $this, 'lp_h5p_add_profile_tabs' ) );
				// add profile setting publicity fields
				add_filter( 'learn-press/get-publicity-setting', array( $this, 'lp_h5p_add_publicity_setting' ) );
				// check profile setting publicity fields
				add_filter( 'learn-press/check-publicity-setting', array( $this, 'lp_h5p_check_publicity_setting' ), 10, 2 );
				// add user profile page setting publicity fields
				add_action( 'learn-press/end-profile-publicity-fields', array(
					$this,
					'lp_h5p_add_profile_publicity_fields'
				) );
				add_filter( 'learn-press/item/to_array', array( $this, 'lp_h5p_get_more_data' ), 10, 1 );
			} else {
                self::$_error = 1;
                add_action( 'admin_notices', array( __CLASS__, 'lp_h5p_admin_notice' ) );
            }
		}

		public function lp_h5p_get_more_data( $item ) {
		    if ( $item['type'] == LP_H5P_CPT ) {
		        $item['interacted_h5p'] = get_post_meta( $item['id'], '_lp_h5p_interact', true );

		        if ( $item['interacted_h5p'] ) {
		            $selected_option = '<option value="' . $item['interacted_h5p'] . '"  selected="selected">' . lp_h5p_get_content_title( $item['interacted_h5p'] ) . '</option>';
                } else {
			        $selected_option = '';
                }

                $item['element_name'] = 'select_h5p_lp_' . $item['id'];
		        $item['interacted_html'] = '<option value="" >' . __( 'Select a H5P item', 'learnpress-h5p' ) . '</option>' . $selected_option;
            }

            return $item;
        }

        /**
         * Add Admin notices
         */
        public static function lp_h5p_admin_notice() {
            switch ( self::$_error ) {
                case 1:
                    echo '<div class="error">';
                    echo '<p>' . sprintf( __( '<strong>LearnPress H5P Content</strong> addon requires <a href="%s">H5P Content</a> plugin is installed.', 'learnpress-h5p' ), '//wordpress.org/plugins/h5p/' ) . '</p>';
                    echo '</div>';
                    break;
            }
        }

		/**
		 * @param $profile LP_Profile
		 */
		public function lp_h5p_add_profile_publicity_fields( $profile ) {
			if ( LP()->settings()->get( 'profile_publicity.h5p' ) === 'yes' ) { ?>
				<li class="form-field">
					<label for="my-h5p"><?php _e( 'My H5P items', 'learnpress-h5p' ); ?></label>
					<div class="form-field-input">
						<input name="publicity[h5p]" value="yes" type="checkbox"
						       id="my-h5p" <?php checked( $profile->get_publicity( 'h5p' ), 'yes' ); ?>/>
						<p class="description"><?php _e( 'Public your profile H5P items', 'learnpress-h5p' ); ?></p>
					</div>
				</li>
			<?php }
		}

		/**
		 * @param $publicities
		 * @param $profile LP_Profile
		 *
		 * @return mixed
		 */
		public function lp_h5p_check_publicity_setting( $publicities, $profile ) {
			$publicities['view-tab-h5p'] = $profile->get_publicity( 'h5p' ) == 'yes';

			return $publicities;
		}

		/**
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_publicity_setting( $settings ) {
			$settings['h5p'] = LP()->settings()->get( 'profile_publicity.h5p' );

			return $settings;
		}

		/**
		 * Add user profile tabs.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_profile_tabs( $tabs ) {

			$settings = LP()->settings;

			$tabs['h5p'] = array(
				'title'    => __( 'H5P Items', 'learnpress-h5p' ),
				'slug'     => $settings->get( 'profile_endpoints.profile-h5p', 'h5p' ),
				'callback' => array( $this, 'tab_h5p' ),
				'priority' => 25
			);

			return $tabs;
		}

		public function tab_h5p() {
			learn_press_h5p_get_template( 'profile/tabs/h5p_items.php' );
		}

		/**
		 * @param $types
		 *
		 * @return array
		 */
		public function lp_h5p_add_filter_access( $types ) {
			$types[] = LP_H5P_CPT;

			return $types;
		}

		/**
		 *
		 */
		public function lp_h5p_init() {
			$actions = array(
				'complete-h5p'    => 'complete_h5p'
			);

			foreach ( $actions as $action => $function ) {
				LP_Request_Handler::register_ajax( $action, array( __CLASS__, $function ) );
				LP_Request_Handler::register( "lp-{$action}", array( __CLASS__, $function ) );
			}
		}

		public static function complete_h5p() {
			$course_id     = LP_Request::get_int( 'course-id' );
			$h5p_id 	   = LP_Request::get_int( 'h5p-id' );
			$user          = learn_press_get_current_user();
			$result        = array(
				'message'  => '',
				'result'   => __( 'Success', 'learnpress-h5p' ),
				'redirect' => learn_press_get_current_url()
			);

			$current_useritem_id = learn_press_get_user_item_id( $user->get_id(), $h5p_id, $course_id );

			if ( ! $current_useritem_id ) {
				$course_data         = $user->get_course_data( $course_id );
				$current_useritem_id = $course_data->get_item( $h5p_id )->get_user_item_id();
			}

			$h5p = LP_H5p::get_h5p( $h5p_id );

			$score = learn_press_get_user_item_meta( $current_useritem_id, 'score', true );

			$max_score = learn_press_get_user_item_meta( $current_useritem_id, 'max_score', true );

			$mark = floatval( $score / $max_score ) * 100;

			learn_press_update_user_item_meta( $current_useritem_id, 'grade', $mark >= $h5p->get_data( 'passing_grade' ) ? 'passed' : 'failed' );

			learn_press_update_h5p_item( $h5p_id, $course_id, $user, 'completed', $current_useritem_id );
			$result['message'] = __( 'Congratulation! You completed this!', 'learnpress-h5p' );

			learn_press_maybe_send_json( $result );

			if ( ! empty( $result['redirect'] ) ) {
				wp_redirect( $result['redirect'] );
				exit();
			}
		}

		function lph5p_process() {
			$content_id = filter_input(INPUT_POST, 'contentId', FILTER_VALIDATE_INT);
			$data_meta = array(
				'score' => filter_input(INPUT_POST, 'score', FILTER_VALIDATE_INT),
				'max_score' => filter_input(INPUT_POST, 'maxScore', FILTER_VALIDATE_INT)
			);
			$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
			$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
			$user = learn_press_get_current_user();

			$result = array( 'result' => 'success' );

			try {
				$data = learn_press_h5p_start( $user, $item_id, $course_id, 'start', $data_meta, true );
				if ( is_wp_error( $data ) ) {
					throw new Exception( $data->get_error_message() );
				} else {
					$h5p = LP_H5p::get_h5p( $item_id );
					$conditional_grade = $h5p->get_data( 'passing_grade' );
					$result['result']   = floatval( $data_meta['score'] / $data_meta['max_score'] ) * 100 >= $conditional_grade ? 'reached' : 'not_reached';
					$result['reload'] = $data_meta['score'] == $data_meta['max_score'] ? 1 : 0;
					$result['redirect'] = learn_press_get_current_url();
				}
			}
			catch ( Exception $ex ) {
				$result['message']  = $ex->getMessage();
				$result['result']   = 'error';
				$result['redirect'] = learn_press_get_current_url();
			}

			learn_press_maybe_send_json( $result );

			if ( ! empty( $result['message'] ) ) {
				learn_press_add_message( $result['message'] );
			}

			if ( ! empty( $result['redirect'] ) ) {
				H5PCore::ajaxSuccess($result);
				exit();
			}
		}

		/**
		 * Include files.
		 */
		protected function _includes() {
			require_once LP_ADDON_H5P_INC_PATH . 'custom-post-types' . DIRECTORY_SEPARATOR . 'h5pcontent.php';
			require_once LP_ADDON_H5P_INC_PATH . 'class-lp-h5p-curd.php';
			require_once LP_ADDON_H5P_INC_PATH . 'class-lp-h5p.php';
			require_once LP_ADDON_H5P_INC_PATH . 'functions.php';
			require_once LP_ADDON_H5P_INC_PATH . 'lp-h5p-template-functions.php';
			require_once LP_ADDON_H5P_INC_PATH . 'lp-h5p-template-hooks.php';
			require_once LP_ADDON_H5P_INC_PATH . 'user-item/class-lp-user-item-h5p.php';
		}

		public function learnpress_h5p_add_rewrite_rules() {
			$course_type  = LP_COURSE_CPT;
			$post_types   = get_post_types( '', 'objects' );
			$slug         = preg_replace( '!^/!', '', $post_types[ $course_type ]->rewrite['slug'] );
			$has_category = false;
			if ( preg_match( '!(%?course_category%?)!', $slug ) ) {
				$slug         = preg_replace( '!(%?course_category%?)!', '(.+?)/([^/]+)', $slug );
				$has_category = true;
			}
			if ( $has_category ) {
				add_rewrite_rule(
					'^' . $slug . '(?:/' . $post_types[ LP_H5P_CPT ]->rewrite['slug'] . '/([^/]+))/?$',
					'index.php?' . $course_type . '=$matches[2]&course_category=$matches[1]&course-item=$matches[3]&item-type=' . LP_H5P_CPT,
					'top'
				);
			} else {
				add_rewrite_rule(
					'^' . $slug . '/([^/]+)(?:/' . $post_types[ LP_H5P_CPT ]->rewrite['slug'] . '/([^/]+))/?$',
					'index.php?' . $course_type . '=$matches[1]&course-item=$matches[2]&item-type=' . LP_H5P_CPT,
					'top'
				);
			}
		}

		public function learnpress_h5p_get_grade( $grade, $item_id, $user_id, $course_id ) {
			if ( LP_H5P_CPT == get_post_type( $item_id ) ) {
				$result = learn_press_h5p_get_result( $item_id, $user_id, $course_id );
				$grade  = isset( $result['grade'] ) ? $result['grade'] : false;
			}

			return $grade;
		}

		public function learnpress_h5p_block_course_item_type( $types ) {
			$types[] = LP_H5P_CPT;

			return $types;
		}

		public function learnpress_h5p_course_item_type( $item_types ) {
			$item_types[] = 'lp_h5p';

			return $item_types;
		}

		public function learnpress_h5p_user_item_object( $item, $data ) {
			if ( LP_H5P_CPT == get_post_type( $data['item_id'] ) ) {
				$item = new LP_User_Item( $data );
			}

			return $item;
		}

		/**
		 * @param        $exclude
		 * @param        $type
		 * @param string $context
		 * @param null   $context_id
		 *
		 * @return array
		 */
		public function exclude_h5p_items( $exclude, $type, $context = '', $context_id = null ) {
			if ( $type != 'lp_h5p' ) {
				return $exclude;
			}
			global $wpdb;
			$used_items = array();
			$query      = $wpdb->prepare( "
						SELECT item_id
						FROM {$wpdb->prefix}learnpress_section_items si
						INNER JOIN {$wpdb->prefix}learnpress_sections s ON s.section_id = si.section_id
						INNER JOIN {$wpdb->posts} p ON p.ID = s.section_course_id
						WHERE %d
						AND p.post_type = %s
					", 1, LP_COURSE_CPT );
			$used_items = $wpdb->get_col( $query );
			if ( $used_items && $exclude ) {
				$exclude = array_merge( $exclude, $used_items );
			} else if ( $used_items ) {
				$exclude = $used_items;
			}

			return is_array( $exclude ) ? array_unique( $exclude ) : array();
		}

		/**
		 * @param $item
		 * @param $args
		 *
		 * @return int|WP_Error
		 */
		public function new_h5p_item( $item_id, $item, $args, $course_id ) {
			if ( $item['type'] == LP_H5P_CPT ) {
				$h5p_curd = new LP_H5p_CURD();
				$item_id     = $h5p_curd->create( $args );
			}

			return $item_id;
		}

		/**
		 * @param $status
		 * @param $type
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return string
		 */
		public function h5p_object_class( $status, $type, $item_type, $item_id ) {
			if ( $type == 'h5p' ) {
				return 'LP_H5p';
			}
		}

		/**
		 * @param $types
		 * @param $key
		 *
		 * @return array
		 */
		public function lp_h5p_put_type_here( $types, $key ) {
			if ( $key ) {
				$types[] = 'lp_h5p';
			} else {
				$types['lp_h5p'] = __( 'H5P Item', 'learnpress-h5p' );
			}

			return $types;
		}

		/**
		 * H5P Content is actived?
		 * @return boolean
		 */
		public function h5p_actived() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			return is_plugin_active( 'h5p/h5p.php' );
		}

		/**
		 * Define constants.
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_H5P_TEMPLATE', LP_ADDON_H5P_PATH . '/templates/' );
			define( 'LP_INVALID_H5P_OR_COURSE', 270 );
			define( 'LP_H5P_HAS_STARTED_OR_COMPLETED', 280 );
			define( 'LP_H5P_CPT', 'lp_h5p' );
		}

		/**
		 *
		 */
		public function lp_h5p_enqueue_scripts() {
			if ( function_exists( 'learn_press_is_course' ) && learn_press_is_course() ) {
				wp_enqueue_style( 'learn-press-h5p', plugins_url( '/assets/css/h5p.css', LP_ADDON_H5P_FILE ) );
				wp_enqueue_script( 'learn-press-h5p', plugins_url( '/assets/js/lph5p.js', LP_ADDON_H5P_FILE ), array(
					'jquery',
					'plupload-all',
					'h5p-core-js-jquery',
					'h5p-core-js-h5p',
				) );
				$scripts = learn_press_assets();
				$scripts->add_script_data( 'learn-press-h5p', learn_press_h5p_single_args() );
			}
		}

		/**
		 * Admin asset and localize script.
		 */
		public function lp_h5p_admin_assets() {
			# TODO: add css and js
			wp_enqueue_style( 'learn-press-h5p', plugins_url( '/assets/css/admin-h5p.css', LP_ADDON_H5P_FILE ) );
            if ( LP_Request::get( 'post_type' ) == 'lp_h5p' ) {
                wp_enqueue_style( 'learn-press-h5p-edit', plugins_url( '/assets/css/admin-edit-h5p.css', LP_ADDON_H5P_FILE ) );
            }
		}

		/**
		 * Update single course h5p item template files.
		 *
		 * @param $located
		 * @param $template_name
		 *
		 * @return mixed|string
		 */
		public function update_h5p_template( $located, $template_name ) {
			if ( $template_name == 'single-course/section/item-h5p.php' ) {
				$located = learn_press_h5p_locate_template( 'single-course/section/item-h5p.php' );
			} elseif ( $template_name == 'single-course/content-item-lp_h5p.php' ) {
				$located = learn_press_h5p_locate_template( 'single-course/content-item-lp_h5p.php' );
			}

			return $located;
		}

		public function learnpress_h5p_can_view_item( $return, $item_id, $userid, $course_id ) {
			if ( get_post_type( $item_id ) == 'lp_h5p' ) {

				$return = learn_press_can_view_h5p( $item_id, $course_id, $userid );
			}

			return $return;
		}

		/**
		 * @param $course_result
		 * @param $user_course
		 *
		 * @return array|bool|int|mixed
		 */
		public function learnpress_h5p_evaluate( $results, $course_result, $user_course ) {
			if ( ! $user_course->is_enrolled() ) {
				return false;
			}
			switch ( $course_result ) {
				case 'evaluate_h5p_items':
					$results = _evaluate_course_by_h5p_items( $user_course );
					break;
				case 'evaluate_h5p_passed_items':
					$results = _evaluate_course_by_passed_h5p_items( $user_course );
					break;
				case 'evaluate_h5p_quizz_passed_items':
					$results = _evaluate_course_by_passed_h5p_quizzes_items( $user_course );
					break;
				default:
					$results = 0;
					break;
			}

			return $results;
		}

		/**
		 * @param $item
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return bool|LP_H5p
		 */
		public function learnpress_h5p_get_item( $item, $item_type, $item_id ) {
			if ( LP_H5P_CPT === $item_type ) {
				$item = LP_H5p::get_h5p( $item_id );
			}

			return $item;
		}

		public function learnpress_h5p_default_user_item_status( $status, $item_id ) {
			if ( get_post_type( $item_id ) === LP_H5P_CPT ) {
				$status = 'viewed';
			}

			return $status;
		}

	}

}