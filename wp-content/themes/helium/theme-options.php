<?php
/**
 * Initialize the custom theme options.
 */
add_action( 'admin_init', 'custom_theme_options' );

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {
  
  /* OptionTree is not loaded yet */
  if ( ! function_exists( 'ot_settings_id' ) )
    return false;
    
  /**
   * Get a copy of the saved settings array. 
   */
  $saved_settings = get_option( ot_settings_id(), array() );
  
  /**
   * Custom settings array that will eventually be 
   * passes to the OptionTree Settings API Class.
   */
  $custom_settings = array( 
    'contextual_help' => array( 
      'sidebar'       => ''
    ),
    'sections'        => array( 
      array(
        'id'          => 'addthis',
        'title'       => __( 'AddThis', 'helium' )
      ),
      array(
        'id'          => 'ajax_navigation',
        'title'       => __( 'AJAX Navigation', 'helium' )
      ),
      array(
        'id'          => 'api_keys',
        'title'       => __( 'API Keys', 'helium' )
      ),
      array(
        'id'          => 'envato_credentials',
        'title'       => __( 'Envato Credentials', 'helium' )
      ),
      array(
        'id'          => 'miscellaneous',
        'title'       => __( 'Miscellaneous', 'helium' )
      )
    ),
    'settings'        => array( 
      array(
        'id'          => 'addthis_sharing_buttons',
        'label'       => __( 'Sharing Buttons', 'helium' ),
        'desc'        => __( 'Enter a comma separated list of AddThis social media sharing buttons to show at the end of each item page.
See this for available buttons: <a href="http://www.addthis.com/services/list">www.addthis.com/services/list</a>', 'helium' ),
        'std'         => 'facebook, twitter, email, compact',
        'type'        => 'text',
        'section'     => 'addthis',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'addthis_profile_id',
        'label'       => __( 'Profile ID', 'helium' ),
        'desc'        => __( 'Specify here your AddThis profile ID if you want to track your AddThis sharing data.', 'helium' ),
        'std'         => '',
        'type'        => 'text',
        'section'     => 'addthis',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'ajax_navigation',
        'label'       => __( 'Enabled', 'helium' ),
        'desc'        => __( 'Specify whether to enable sitewide AJAX navigation to enhance the user experience. Turn this off if you\'re experiencing problems while viewing your site.', 'helium' ),
        'std'         => 'on',
        'type'        => 'on-off',
        'section'     => 'ajax_navigation',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'ajax_navigation_scroll_top',
        'label'       => __( 'Scroll to Top Before Navigation', 'helium' ),
        'desc'        => __( 'Specify whether to scroll to top of the page before navigating away from a page.', 'helium' ),
        'std'         => 'on',
        'type'        => 'on-off',
        'section'     => 'ajax_navigation',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => 'ajax_navigation:is(on)',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'ajax_navigation_loading_text',
        'label'       => __( 'Loading Text', 'helium' ),
        'desc'        => __( 'Specify the text to show while a page is being loaded.', 'helium' ),
        'std'         => 'Loading',
        'type'        => 'text',
        'section'     => 'ajax_navigation',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => 'ajax_navigation:is(on)',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'twitter_api_keys',
        'label'       => __( 'Twitter API Keys', 'helium' ),
        'desc'        => __( 'Before using Twitter widgets throughout the site, you need to register your API keys by following the instructions below.
<ol>
  <li>Go to <a>http://dev.twitter.com/apps</a> and sign in with your Twitter account.</li>
  <li>Create a new application by clicking the button on the right hand side.</li>
  <li>Once you\'ve created the app, scroll down the application\'s details page to find the OAuth section.</li>
  <li>Copy the consumer secret and consumer key into the fields below.</li>
  <li>Then click the Create Access Token button at the bottom of the page.</li>
  <li>Copy the Access token and Access token secret and paste it into the fields below.</li>
</ol>', 'helium' ),
        'std'         => '',
        'type'        => 'textblock-titled',
        'section'     => 'api_keys',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'twitter_consumer_key',
        'label'       => __( 'Twitter API Consumer Key', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'api_keys',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'twitter_consumer_secret',
        'label'       => __( 'Twitter API Consumer Secret', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'api_keys',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'twitter_access_token',
        'label'       => __( 'Twitter API Access Token', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'api_keys',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'twitter_access_token_secret',
        'label'       => __( 'Twitter API Access Token Secret', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'api_keys',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'envato_credentials',
        'label'       => __( 'Envato Credentials', 'helium' ),
        'desc'        => __( 'Enter your Envato Market credentials below to access the demo content importer and get automatic theme update notifications directly from WordPress admin.', 'helium' ),
        'std'         => '',
        'type'        => 'textblock-titled',
        'section'     => 'envato_credentials',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'envato_username',
        'label'       => __( 'Envato Username', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'envato_credentials',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'envato_api_key',
        'label'       => __( 'Envato API Key', 'helium' ),
        'desc'        => '',
        'std'         => '',
        'type'        => 'text',
        'section'     => 'envato_credentials',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'custom_css',
        'label'       => __( 'Custom CSS', 'helium' ),
        'desc'        => __( 'Enter here your custom CSS code to be applied to the whole site.', 'helium' ),
        'std'         => '',
        'type'        => 'css',
        'section'     => 'miscellaneous',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'favicon',
        'label'       => __( 'Favicon', 'helium' ),
        'desc'        => __( 'Upload here your site favicon', 'helium' ),
        'std'         => '',
        'type'        => 'upload',
        'section'     => 'miscellaneous',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      )
    )
  );
  
  /* allow settings to be filtered before saving */
  $custom_settings = apply_filters( ot_settings_id() . '_args', $custom_settings );
  
  /* settings are not the same update the DB */
  if ( $saved_settings !== $custom_settings ) {
    update_option( ot_settings_id(), $custom_settings ); 
  }
  
  /* Lets OptionTree know the UI Builder is being overridden */
  global $ot_has_custom_theme_options;
  $ot_has_custom_theme_options = true;
  
}