<?php 


//Connect using Quick Edit
class WPMLD_Connect_Quick_Edit {

  //Add a extra column to the post list
  public static function wpmld_add_language_column($columns) {

      $columns['wpmld_post_language'] = __( 'Language', 'textdomain' );

      return $columns;
  }



  //Add the checkbox on the bulk edit form
  public static function wpmld_change_language_form( $column, $post_type ) {
    if ( 'wpmld_post_language' !== $column ) {
        return;
    }

    if ( 'post' !== $post_type || 'page' !== $post_type) {
        //return;
    }

    //Get active languages
    $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );     

    //Display in the form
    echo '<div style="clear: both"></div>';
    echo '<div style="padding: 10px; border: 1px solid #ccc; margin-top: 20px; text-align: center"><span class="title">'.__( '<strong>(WPML) Set this post as a translation of: </strong> ', 'textdomain' ).'</span>';

    $posts_list = get_posts( array( 'post_type' => $post_type, 'suppress_filters' => true, 'numberposts' => -1 ) );

    echo '<select name="wpmld_post_language">';      
    echo '<option value="none">None</option>';
      if ( !empty( $posts_list ) ) {
          foreach( $posts_list as $post ) {
              $post_language = apply_filters( 'wpml_post_language_details', NULL, $post->ID ) ;
              if ( defined( 'ICL_LANGUAGE_CODE' ) && $post_language['language_code'] != ICL_LANGUAGE_CODE ) {
                  echo '<option value="'.$post->ID.'">'.'('.$post_language['language_code'].') '.$post->post_title.'</option>';
              }
          }
      }
    echo '</select></div>';  
  }


  //Save the data and edit the post
  public function save_quick_edit_data( $post_id ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
          return;
      }
   
      if ( ! current_user_can( 'edit_post', $post_id )  ) {
          return;
      }

      if ( isset( $_POST['wpmld_post_language']) && $_POST['wpmld_post_language'] != 'none' ) {
   
        $data = $_POST['wpmld_post_language'];

        $post_type = apply_filters( 'wpml_element_type', get_post_type( $post_id ) );

        $trid = apply_filters( 'wpml_element_trid', NULL, $data, $post_type );

        $element_id = $post_id;    

        $post_language = apply_filters( 'wpml_post_language_details', NULL, $post_id  ) ;
        $language_code = $post_language['language_code'] ;

        $original_language_filter = apply_filters( 'wpml_post_language_details', NULL, $data ) ;
        $original_language = $original_language_filter['language_code'] ;

        $my_args = array(
            'element_id'    => $element_id,
            'element_type'  => $post_type,
            'trid'   => $trid,
            'language_code'   => $language_code,
            'source_language_code' => $original_language
        );

        do_action( 'wpml_set_element_language_details', $my_args );
    }
  }


  //Filters and Actions
  public function wpmld_connect_quick_edit_hooks() {
    add_filter( 'manage_posts_columns', array( __CLASS__, 'wpmld_add_language_column') , 10, 2 );
    add_filter( 'manage_pages_columns', array( __CLASS__, 'wpmld_add_language_column') , 10, 2 );

    add_action( 'quick_edit_custom_box',  array( __CLASS__, 'wpmld_change_language_form') , 10, 2 );

    add_action( 'save_post',  array( __CLASS__, 'save_quick_edit_data'), 10, 2  );
  }
  
}

?>