<?php
/*
Plugin Name: Extended Table of Contents (with nextpage support)
Plugin URI: http://http://www.web-cloud-apps.com/produkte/wordpress-extended-toc/
Description: This plugin automatically generates and inserts a table of contents (ToC) to your pages and posts, based on tags h1-h6. Whenever the plugin discovers more than a certain amount of headings (default: 3) the ToC is inserted at the top of the page. This plugin also can handle posts that are divided into pages by the nextpage-wordpress-tag. Any feedback or suggestions are welcome.  
Version: 0.9.5
Author: Daniel Boldura, Web.Cloud.Apps. UG
Author URI: http://www.web-cloud-apps.com


/*  Copyright 2013 Web.Cloud.Apps. UG // Daniel Boldura (email: info at web-cloud-apps.com or daniel at boldura.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'EXTENDED_TOC_VERSION',  '0.9.5' );
define( 'EXTENDED_TOC_ID',       'extended_toc' );
define( 'EXTENDED_TOC_NAME',     'Extended-ToC' );
define( 'TOC_MIN_START',         2 );
define( 'TOC_MAX_START',         10 );

if( !class_exists('ExToC') ) {
	class ExToC 
  {
    private $path;
    private $content = "";
    private $fullcontent = "";
    private $pages = array(); 
    private $ID = 0;
    private $counter = array();
    private $totalHeadings = 0;
    private $minLevel = null;
    
    public function __construct() {
      $this->path = plugins_url( '', __FILE__ );
      $this->exclude_post_types = array( 'attachment', 'revision', 'nav_menu_item', 'safecss' );
      
			// get options
			$defaults = array(		// default options
				'heading_text' => 'Contents',
				'start' => 3,
				'show_heading_text' => true,
        'auto_insert_post_types' => array('page', 'post'),
        'heading_levels' => array('1', '2' ,'3', '4', '5', '6'),
        'show_hierarchy' => true,
        'number_list_items' => true,
			);
			$options = get_option( EXTENDED_TOC_ID, $defaults );
			$this->options = wp_parse_args( $options, $defaults );
      
      add_action( 'plugins_loaded', array(&$this, 'plugins_loaded') );
      
      if( is_admin() ) {
    		//Additional links on the plugin page
    		add_filter('plugin_row_meta', array(&$this, 'register_plugin_links'), 10, 2);
        
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
      }
      else {
        /** Add the content filter and enqueue css **/
        add_filter( 'the_content', array(&$this, 'the_content'), 100 );
        add_action( 'wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts') );
        
			add_shortcode( 'extoc', array(&$this, 'shortcode_extoc') );
			add_shortcode( 'noextoc', array(&$this, 'shortcode_noextoc') );
      }
    }
    
		public function __destruct() {
		}

    public function register_plugin_links($links, $file) {
      if( $file == plugin_basename(__FILE__) ) { 
  		  $links[] = '<a href="http://http://www.web-cloud-apps.com/produkte/wordpress-extended-toc">' . __('Donate', EXTENDED_TOC_ID) . '</a>';
      }
      
  		return $links;
	 }


		public function admin_init() {
			wp_register_style( EXTENDED_TOC_ID, $this->path . '/admin-style.css', array(), EXTENDED_TOC_VERSION );
      wp_enqueue_style(EXTENDED_TOC_ID);
		}
    
    public function admin_menu() {
      // Create menu tab
			$page = add_submenu_page( 'plugins.php', EXTENDED_TOC_NAME, EXTENDED_TOC_NAME, 'manage_options', EXTENDED_TOC_ID, array(&$this, 'admin_options') );
    }
    
		private function save_admin_options()
		{
			global $post_id;

			// security check
			if ( !wp_verify_nonce( @$_POST[EXTENDED_TOC_ID], plugin_basename(__FILE__) ) )
				return false;
        
			// require an administrator level to save
			if ( !current_user_can( 'manage_options', $post_id ) )
				return false;
        
			$this->options = array_merge(
				$this->options,
				array(
					'heading_text' => stripslashes( trim($_POST['heading_text']) ),
          'auto_insert_post_types' => @(array)$_POST['auto_insert_post_types'],
          'start' => intval($_POST['start']),
          'show_heading_text' => (isset($_POST['show_heading_text']) && $_POST['show_heading_text']) ? true : false,
          'show_hierarchy' => (isset($_POST['show_hierarchy']) && $_POST['show_hierarchy']) ? true : false,
          'number_list_items' => (isset($_POST['number_list_items']) && $_POST['number_list_items']) ? true : false,
          'heading_levels' => @(array)$_POST['heading_levels'],
				)
			);
			
			// update_option will return false if no changes were made
			update_option( EXTENDED_TOC_ID, $this->options );
			
			return true;
    }
    
    public function admin_options() {
      if( isset($_GET['update']) ) {       
				if( $this->save_admin_options() )
					$msg = '<div id="message" class="updated fade"><p>' . __('Options saved.', EXTENDED_TOC_ID) . '</p></div>';
				else
					$msg = '<div id="message" class="error fade"><p>' . __('Save failed.', EXTENDED_TOC_ID) . '</p></div>';
			}
?>
  <div class="wrap">
    <div id="icon-plugins" class="icon32">
      <br />
    </div>
      
    <h2><?php echo __("Extended Table of Contents", EXTENDED_TOC_ID)?></h2>
    
    <?php echo $msg; ?>

    <h2 class="nav-tab-wrapper">
      <a class="nav-tab<?php echo !$_GET['tab']||$_GET['tab']==''?' nav-tab-active':''; ?>" href="?page=<?php echo $_GET['page']; ?>"><?php _e('Main Options', 'EXTENDED_TOC_ID'); ?></a>
      <a class="nav-tab<?php echo $_GET['tab']=='help'?' nav-tab-active':''; ?>" href="?page=<?php echo $_GET['page']; ?>&amp;tab=help"><?php _e('Help', 'EXTENDED_TOC_ID'); ?></a>
    </h2>
    
    <?php
      if( !$_GET['tab'] || $_GET['tab'] == '' ) {
        $this->displayMainContent();
      }
      else {
        $this->displayHelpContent();  
      }
    ?>
  </div>                        
<?php
    }
    
    private function displayMainContent() {
?>
    <form method="post" action="<?php echo htmlentities('?page=' . $_GET['page'] . '&update'); ?>">
      <?php wp_nonce_field( plugin_basename(__FILE__), EXTENDED_TOC_ID ); ?>

      <div class="form_container">
        <table class="form-table">
          <tbody>
            <tr>
              <th><label for="show_heading_text"><?php echo __('Show heading text', EXTENDED_TOC_ID); ?></label></th>
              <td>
                <input id="show_heading_text" type="checkbox" name="show_heading_text" <?php if ( $this->options['show_heading_text'] ) echo ' checked="checked"'; ?> />
              </td>
            </tr>
            
            <tr>
              <th><label for="heading_text"><?php echo __('Heading text', EXTENDED_TOC_ID); ?></label></th>
              <td><input id="heading_text" type="text" class="regular-text" name="heading_text" value="<?php echo $this->options['heading_text']; ?>" /></td>
            </tr>
            
            <tr>
            	<th><?php echo __('Add table of contents to following content types', EXTENDED_TOC_ID); ?></th>
            	<td>
                <?php foreach( get_post_types() as $post_type ): ?>
                  <?php if( !in_array($post_type, $this->exclude_post_types) ): ?>
                    <input type="checkbox" value="<?php echo $post_type?>" id="auto_insert_post_types_<?php echo $post_type?>" name="auto_insert_post_types[]"<?php echo in_array($post_type, $this->options['auto_insert_post_types'])?' checked="checked"':''?> />
                    <label for="auto_insert_post_types_<?php echo $post_type?>"><?php echo $post_type?></label><br />
                  <?php endif; ?>
                <?php endforeach; ?>
              </td>
            </tr>
            
            <tr>
              <th><label for="start"><?php echo __('Show when', EXTENDED_TOC_ID); ?></label></th>
              <td>
            		<select name="start" id="start">
                <?php 
                for ($i = TOC_MIN_START; $i <= TOC_MAX_START; $i++) {
          				echo '<option value="' . $i . '"';
          				if ( $i == $this->options['start'] ) echo ' selected="selected"';
          				echo '>' . $i . '</option>' . "\n";
          			}
                ?>
            		</select>
                <span>><?php echo __('or more headings are present', EXTENDED_TOC_ID); ?></span>
              </td>
            </tr>
            
            <tr>
              <th><label for="show_hierarchy"><?php echo __('Show hierarchy', EXTENDED_TOC_ID); ?></label></th>
              <td>
                <input id="show_hierarchy" type="checkbox" name="show_hierarchy" <?php if ( $this->options['show_hierarchy'] ) echo ' checked="checked"'; ?> />
              </td>
            </tr>
            
            <tr>
              <th><label for="number_list_items"><?php echo __('Number list items', EXTENDED_TOC_ID); ?></label></th>
              <td>
                <input id="number_list_items" type="checkbox" name="number_list_items" <?php if ( $this->options['number_list_items'] ) echo ' checked="checked"'; ?> />
              </td>
            </tr>
            
            <tr>
              <th>
                <label for="number_list_items"><?php echo __('Heading levels', EXTENDED_TOC_ID); ?></label>
              </th>
              <td>
                <?php
                  // show heading 1 to 6 options
                  for ($i = 1; $i <= 6; $i++) {
                  	echo '<input type="checkbox" value="' . $i . '" id="heading_levels' . $i .'" name="heading_levels[]"';
                  	if ( in_array($i, $this->options['heading_levels']) ) echo ' checked="checked"';
                  	echo ' /><label for="heading_levels' . $i .'"> ' . __('heading ') . $i . ' - h' . $i . '</label><br />';
                  }
                ?>
              </td>
            </tr>
          </tbody>
        </table>     
      </div>
      
      <p class="submit"><input class="button-primary" type="submit" value="<?php echo __("Save Options", EXTENDED_TOC_ID)?>" name="submit" /></p>
    </form>
<?php
    }

    // ToDo: Also allow to markup settings Hierarchie and Kapitelnummer
    private function displayHelpContent() {
?>
    <div class="form_container">
      <h3>Position the ToC</h3>
      <div>
        The table of contents is generated automatically and is inserted at the very top of your post and, if its paginated, at the top of every subpage. To change the position of the ToC 
        you can insert the markup [extoc] at the position you want it to be displayed. You have to position the ToC on every subpage, otherwise it will be shown on the subpages again at the top of the page.
      </div>
      
      <h3>Blacklist posts/pages</h3>     
      <div>
        If you need a table of content for the main part of you posts and pages, but you want to exclude the ToC from sepcial posts, you can use a blacklist. Per default the ToC is
        shown in posts and pages. You can insert the markup [noextoc] to prohibit the insertion of the ToC in this page/post/subpage. This markup also has to be inserted in every subpage, if you use
        the nextpage-tag, otherwise it will be inserted within the subpages.
      </div>
      
      <h3>Whitelist posts/pages</h3>

      <div>
        If you have only a few posts where you want the ToC to be inserted, you can switch off the ToC from general settings and insert it within your posts/pages by the markup [extoc].
      </div>
      
      <h3>Individual setting withthin the [extoc] markup</h3>      
      <div>
        The [extoc] markup can also be used to change the main settings for the ToC only for some posts/pages.<br /><br /> 
        
        Example: [extoc start=10 headers=1,2,3 title="My individual ToC title"]<br /><br /> 
        
        This will insert a ToC that only will be displayed if 10 oder more headings are contained. "headers=1,2,3" means that only the header h1, h2 and h3 are considered for the ToC. The "title" attribute
        lets you set an individual title for the ToC. If one of these attributes is missing, the default value will be taken.<br /><br /> 
        
        You can also remove the title by adding "notitle" e.g. [extoc notitle]. Leaving the title attribute empty will also take the header defined within the general plugin settings.
      </div>
    </div>
<?php
    }
        
    public function wp_enqueue_scripts() {
      wp_register_style(EXTENDED_TOC_ID, $this->path . '/style.css', array(), POWER_TOC_VERSION);
      wp_enqueue_style(EXTENDED_TOC_ID);
    }
    
    public function plugins_loaded() {
			load_plugin_textdomain( EXTENDED_TOC_ID, false, dirname(plugin_basename(__FILE__)) . '/locale/' );
		} 
    
    public function shortcode_extoc($atts) {
    	extract( shortcode_atts( array(
    		'start' => $this->options["start"],
    		'headers' => $this->options["heading_levels"],
        'title' => $this->options["heading_text"],
    	), $atts ) );
      
      if( !is_array($headers) )
        $headers = preg_split('/[\s*,]+/i', $headers);

      if($start)    $this->options['start'] = $start;
      if($headers)  $this->options['heading_levels'] = $headers;
      if($title)    $this->options['heading_text'] = $title;
      
      if( isset($atts[0]['notitle']) )  
        $this->options['show_heading_text'] = false;
      
      if( !is_search() && !is_archive() && !is_feed() && !is_front_page() )
        return '[extoc]';
      else
    	 return;
		} 
    
    public function shortcode_noextoc($atts) {
    	return;
		} 

    public function the_content($content) {
      global $post;
      
      // Reset the counter
      $this->counter = array();

      if( is_search() || is_archive() || is_front_page() || is_feed() )
        return $content;
    
      /** Extract the content, and extract the part content if <!--nextpage--> was used **/
      $this->content = $content;  // The original content (subpage) that is displayed 
      $this->extract_full_post_content();
      
      $toc_content = "<div id=\"toc-np-container\">";
      
      if( $this->options['show_heading_text'] == true ) 
        $toc_content .= "<p id=\"toc-np-title\">" . $this->options["heading_text"] . "</p>";
      
      $toc_content .= "<ul class=\"no-bullets\">";
      $toc_content .= $this->extract_toc();
      $toc_content .= "</ul></div>";
            
      if( $this->totalHeadings >= $this->options['start'] )
        return $this->insert_toc_at_markup_position($toc_content); // $toc_content . $this->content;
      else {
        $content = preg_replace("/\[extoc\]|\[noextoc\]/", "", $this->content);
        
        return $content;
      } 
    }

    /** returns the content for display added by the ToC */
    private function insert_toc_at_markup_position($toc_content) {
      // clean content without markups for returning
      $content = $this->content;
      $content = preg_replace("/\[extoc\]|\[noextoc\]/", "", $content);

      // [noextoc] has priority. If this is found, return the original
      if( strpos($this->content, '[noextoc]') !== false )
        return $content;
    
      // try to find the markup for the ToC
      $pos = strpos($this->content, '[extoc]');
      
      if( $pos === false ) {
        // There was no markup, so insert at top or return original if this type does not need a ToC
        if( !in_array(get_post_type($post), $this->options['auto_insert_post_types']) )
          return $content;
        else
          return $toc_content . $content;
      }
      
      if( is_numeric($pos) && $pos >= 0 ) {
        return substr($content, 0, $pos) . $toc_content . substr($content, $pos);
      }
      
      // Absolute backup, return the content. This point should actually never be reached
      return $content;
    }

    /** Extract the full unshortened content from the post **/
    private function extract_full_post_content() {
      global $post;
      $this->fullcontent = $post->post_content;  
      $this->ID = $post->ID; 
    }
    
    private function extract_toc() {
      /** check within the full content how many pages exists */
      $this->extract_pages();
    
      $headers = "";
      
      // Reset all settings
      $this->minLevel = null;
      
//     private $path;
//     private $content = "";
//     private $fullcontent = "";
//     private $pages = array(); 
//     private $ID = 0;
//     private $counter = array();
//     private $totalHeadings = 0;
//     private $minLevel = null;
    
      /** Extract headings from every pages */
      for( $pagenum = 1; $pagenum <= count($this->pages); $pagenum++ ) {
        $headers .= $this->exctract_headings($pagenum);   
      }
      
      return $headers;
    }
    
    private function extract_pages() {
      /** Split the content by "nextpage"-tags if some exists */
      $this->pages = preg_split("/<!--nextpage-->/msuU", $this->fullcontent); 
    }
    
    private function exctract_headings($pagenum) {
      /** find all header tags within the page **/
      preg_match_all('/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU', $this->pages[$pagenum-1], $matches, PREG_SET_ORDER);

			/** Check the headings that are desired */
			if( count($this->options['heading_levels']) != 6 ) {
				$new_matches = array();
				for ($i = 0; $i < count($matches); $i++) {
					if( in_array($matches[$i][2], $this->options['heading_levels']) )
						$new_matches[] = $matches[$i];
				}
				$matches = $new_matches;
			}

      $items = "";
      
      /** Take first h-level as baseline */
      if( $this->minLevel == null )
        $this->minLevel = $matches[0][2];          // lowest level e.g. h3
        
      $currentLevel   =   $this->minLevel; // $minLevel;

    	for( $i = 0; $i < count($matches); $i++ ) {
    		/** get anchor and add to find and replace arrays **/
    		$anchor = $this->url_encode_anchor($matches[$i][0]);
    		$find = $matches[$i][0];
    		$replace = str_replace(
    			array(
    				$matches[$i][1],				// start of heading
    				'</h' . $matches[$i][2] . '>'	// end of heading
    			),
    			array(
    				$matches[$i][1] . '<span id="' . $anchor . '">',
    				'</span></h' . $matches[$i][2] . '>'
    			),
    			$matches[$i][0]
    		);    
        
        $this->content = str_replace($find, $replace, $this->content);                

        /** Check if header lower current header, then add level and update current header */
        if( $matches[$i][2] > $currentLevel && $this->options['show_hierarchy'] == true) {
          $currentLevel = $matches[$i][2];
          $this->counter[$currentLevel] = 1;
        }
        else if( $matches[$i][2] < $currentLevel && $matches[$i][2] >= $this->minLevel && $this->options['show_hierarchy'] == true) {
          $currentLevel = $matches[$i][2];
          $this->counter[$currentLevel] += 1;
        }
        else {
          $this->counter[$currentLevel] += 1;
        }
        
    		/** build html */
        $items .= '<li class="header-level-' . ($currentLevel - $this->minLevel + 1) . '">';

        global $page;  
        if( $pagenum == $page && is_single() )
          $items .= '<a href="#' . $anchor . '">';
        else {
          if( $pagenum == 1 )
            $items .= '<a href="' . get_permalink($this->ID) . '#' . $anchor . '">';
          else
    		    $items .= '<a href="?p='.$this->ID.($pagenum>1?'&page='.$pagenum:'').'#' . $anchor . '">';
        }

        // Show numbers only if user wants it
        if( $this->options['number_list_items'] ) {
          $items .= "<span class=\"toc-np-number\">";

          if( $this->options['show_hierarchy'] == true ) {
            for( $j = $this->minLevel; $j < $currentLevel; $j++ ) {
                $items = $items . $this->counter[$j] . ".";
            }
          }
          $items = $items . $this->counter[$currentLevel];

          $items .= "</span>";
        }
        
    		$items .= strip_tags($matches[$i][0]) . '</a>';
        $items .= '</li>';
        
        $this->totalHeadings++;
    	}  
      
      return $items;
    }
    
    private function url_encode_anchor($anchor)
    {
    	$return = false;
    	
    	if(!empty($anchor) ) {
        /** Remove tags */
        $return = trim( strip_tags($anchor) );
        
        /** remove &amp; */
    		$return = str_replace( '&amp;', '', $return );
        
        /** remove all unknown chars **/
        $return = preg_replace("/[^0-9a-zA-Z \-_]+/", "", $return);
        
        /** Remove backspace etc */
        $return = preg_replace("/[\s]+/", "-", $return);

        /** If we now start or end with a - or _ remove it */
        $return = preg_replace("/^[-_]/", "", $return);
        $return = preg_replace("/[-_]$/", "", $return);
    	}
    	
    	return $return;
    }
  }
}  

/** Initialise the class */
$tocPlugin = new ExToC();

?>