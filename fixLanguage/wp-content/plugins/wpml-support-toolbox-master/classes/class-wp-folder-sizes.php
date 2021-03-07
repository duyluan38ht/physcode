<?php 

//Move Posts between Languages
class WPMLD_Wp_Folder_Sizes {

  function display_folder_sizes()   {
      $upload_dir     = wp_upload_dir(); 
      $upload_space   = $this->check_foldersize( $upload_dir['basedir'] );
      $content_space  = $this->check_foldersize( WP_CONTENT_DIR );
      $wp_space       = $this->check_foldersize( ABSPATH );
      $themes_space   = $this->check_foldersize( get_theme_root() );
      $plugins_space  = $this->check_foldersize( WP_PLUGIN_DIR  );

      //Check database size
      global $wpdb;
      $dbname = $wpdb->dbname;
      $result = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );
      $rows = count( $result );
      $dbsize = 0;

      if( $wpdb->num_rows > 0 ) {
        foreach( $result as $row ) {
          $dbsize += $row[ "Data_length" ] + $row[ "Index_length" ];
        }
      }
      
      echo '<strong>Entire site </strong>: ' . $this->format_size( $wp_space ). ' &nbsp;|&nbsp; '; 

      echo '<strong>Database </strong>: ' . $this->format_size( $dbsize ). ' &nbsp;|&nbsp; '; 

      echo '<strong>Wp-Content folder</strong>: ' . $this->format_size( $content_space ) . ' &nbsp;|&nbsp; ';    

      echo '<strong>Uploads folder</strong>: ' . $this->format_size( $upload_space ) . ' &nbsp;|&nbsp; ';

      echo '<strong>Themes folder</strong>: ' . $this->format_size( $themes_space) . ' &nbsp;|&nbsp; ';

      echo '<strong>Plugins folder</strong>: ' . $this->format_size( $plugins_space );
      
  }


  function check_foldersize( $path )   {
     if( false === ( $total_size = get_transient( $path ) ) ) {

      $total_size = 0;
      foreach( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path, \FilesystemIterator::CURRENT_AS_FILEINFO ) ) as $file ) {
        $total_size += $file->getSize();
      }

      // Set transient, expires in 1 hour
      set_transient( $path, $total_size, 1 * HOUR_IN_SECONDS );

      return $total_size;

    } else {

      return $total_size;
    }
  }

  function format_size($size)   {
      $units = explode( ' ', 'B KB MB GB TB PB' );

      $mod = 1024;

      for ( $i = 0; $size > $mod; $i++ )
          $size /= $mod;

      $endIndex = strpos( $size, "." ) + 3;

      return substr( $size, 0, $endIndex ) . ' ' . $units[$i];
  }

}


?>