<?php 

//Move Posts between Languages
class WPMLD_Move_posts {

  //function to move Posts Between Languages
  public function wpmld_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {

      $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' ); 
    
      if ( !empty( $languages ) ) {

          foreach( $languages as $l ) {
             $bulk_action_name = 'wpml_move_to_'.$l['language_code'];

              if ( $doaction == $bulk_action_name ) {

                  //Run action on each post
                  foreach ( $post_ids as $post_id ) {
                      $element_type = apply_filters( 'wpml_element_type', get_post_type($post_id) );
                      $trid = apply_filters( 'wpml_element_trid', NULL, $post_id, $element_type);

                      $my_args = array(
                          'element_id'    => $post_id,
                          'element_type'  => $element_type,
                          'trid'   => $trid,
                          'language_code'   => $l['language_code'],
                          'source_language_code' => NULL
                      );

                      do_action( 'wpml_set_element_language_details', $my_args );                     
                  }

                  $redirect_to = add_query_arg( 'wpml_move_language', count( $post_ids ), $redirect_to );
                  return $redirect_to;
              }  
          }

      }    
  }

  //Add option to Bulk Actions
  public static function wpmld_register_my_bulk_actions($bulk_actions) {
 
    $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' ); 

    if ( !empty( $languages ) ) {
          foreach( $languages as $l ) {
             $bulk_action_name = 'wpml_move_to_'.$l['language_code'];
             $bulk_actions[$bulk_action_name] = 'WPML: Move to the "'.$l['language_code'].'" language ';
          }
    }    
    return $bulk_actions;
  }

  //Filters and Actions
  public function wpmld_move_posts_hooks() {
    //New bulk action on posts and pages
    add_filter( 'bulk_actions-edit-post', array( __CLASS__, 'wpmld_register_my_bulk_actions') ); 
    add_filter( 'bulk_actions-edit-page', array( __CLASS__, 'wpmld_register_my_bulk_actions') );


    //Handling the bulk action on posts and pages
    add_filter( 'handle_bulk_actions-edit-post', array( __CLASS__, 'wpmld_bulk_action_handler') , 10, 3 );
    add_filter( 'handle_bulk_actions-edit-page', array( __CLASS__, 'wpmld_bulk_action_handler'), 10, 3 );
  }

}


?>