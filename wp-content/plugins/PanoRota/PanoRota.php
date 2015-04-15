<?php
/*
Plugin Name: PanoRota
Plugin URI: http://www.bigpepperdesign.com
Description: Information about plugin here..
Author: Darren Powell
Version: 1
Author URI: http://www.bigpepperdesign.com
*/

define( 'PANO_PATH', plugin_dir_path(__FILE__) );

require PANO_PATH . 'inc/panjs.php';

// LOAD SCRIPTS
	// load google maps api
	wp_register_script('pano_gmap', 'https://maps.googleapis.com/maps/api/js?sensor=false', 'jquery', false);


// REGISTER PANOROTA POST TYPE

function regPanoRotaPostType() {
  $labels = array(
    'name' => 'PanoRota',
    'singular_name' => 'PanoRota',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New PanoRota',
    'edit_item' => 'Edit PanoRota',
    'new_item' => 'New PanoRota',
    'all_items' => 'All PanoRotas',
    'view_item' => 'View PanoRota',
    'search_items' => 'Search PanoRota',
    'not_found' =>  'No PanoRotas found',
    'not_found_in_trash' => 'No PanoRotas found in Trash', 
    'parent_item_colon' => '',
    'menu_name' => 'PanoRota'
  );

  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => false,
    'capability_type' => 'post',
    'has_archive' => false, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title'),
	'menu_icon' => plugins_url( 'inc/panorotaicon.png' , __FILE__ )
  ); 

  register_post_type( 'panoRota', $args );
  
}

add_action( 'init', 'regPanoRotaPostType' );

add_action('admin_head', 'panoAdminStyle');



// ADD PANOROTA SETTINGS/META BOXES

// SETUP META BOXES
add_action('add_meta_boxes', 'init_meta_boxes');
function init_meta_boxes () {
	add_meta_box( 'panoRotaSettings', 'PanoRota Settings', 'panoRotaSettingsMeta', 'panoRota', 'normal', 'high');
}

function panoRotaSettingsMeta () {
	global $post;
	$thisMeta = get_post_meta($post->ID, '_PanoRotaMeta', true);
?>	
	<h2>This PanoRota Shortcode:</h2>
    <p style="font-size:1.8em">[PanoRota id="<?php echo $post->ID;?>"]</p>
    
    <p>Preview:</p>
    <script type="text/javascript">
			var idpano = "<?php echo ($thisMeta && isset($thisMeta['panoidid']) && $thisMeta['panoidid'] != '') ? $thisMeta['panoidid'] : 'C-7SY-PmQE6qNp0AOCUuzQ';?>", currentYaw<?php echo $post->ID;?> = <?php echo ($thisMeta && isset($thisMeta['panoyaw']) && $thisMeta['panoyaw'] != '') ? $thisMeta['panoyaw'] : '310';?>, currentPitch<?php echo $post->ID;?> = -<?php echo ($thisMeta && isset($thisMeta['panopitch']) && $thisMeta['panopitch'] != '') ? $thisMeta['panopitch'] : '0.7';?>, currentZoom<?php echo $post->ID;?> = <?php echo ($thisMeta && isset($thisMeta['panozoom']) && $thisMeta['panozoom'] != '') ? $thisMeta['panozoom'] : '1.25';?>, panoOptions = {
				addressControl:!1, imageDateControl:true, zoomControl:<?php echo (!isset($thisMeta['panozoomcontrol']) || $thisMeta['panozoomcontrol'] == 'yes') ? 'true' : 'false';?>, linksControl:<?php echo (!isset($thisMeta['panoarrows']) || $thisMeta['panoarrows'] == 'yes') ? 'true' : 'false';?>, panControl:<?php echo (!isset($thisMeta['panocompass']) || $thisMeta['panocompass'] == 'yes') ? 'true' : 'false';?>, enableCloseButton:false, visible:true, pov: {
				  heading: 310,
				  pitch: -7,
				  zoom: 1.25
				}}, speed = <?php echo ($thisMeta && isset($thisMeta['panospeed']) && $thisMeta['panospeed'] != '') ? $thisMeta['panospeed'] : '30';?>, delta = <?php echo ($thisMeta && $thisMeta['panodelta'] && $thisMeta['panodelta'] != '') ? $thisMeta['panodelta'] : '0.1';?>, timer<?php echo $post->ID;?>, panorama<?php echo $post->ID;?>;
			
			function initialize<?php echo $post->ID;?>() {
			  panorama<?php echo $post->ID;?> = new google.maps.StreetViewPanorama(document.getElementById("pano<?php echo $post->ID;?>"), panoOptions);
			  panorama<?php echo $post->ID;?>.setPano(idpano);
			
			  timer<?php echo $post->ID;?> = window.setInterval(spiral<?php echo $post->ID;?>, speed);
			  google.maps.event.addListener(panorama<?php echo $post->ID;?>, "pov_changed", function() {
				currentYaw<?php echo $post->ID;?> = panorama<?php echo $post->ID;?>.getPov().heading;
				currentPitch<?php echo $post->ID;?> = panorama<?php echo $post->ID;?>.getPov().pitch;
				currentZoom<?php echo $post->ID;?> = panorama<?php echo $post->ID;?>.getPov().zoom
			  });

			}
			
			function spiral<?php echo $post->ID;?>() {
			  currentYaw<?php echo $post->ID;?> += delta;
			  panorama<?php echo $post->ID;?>.setPov({heading:currentYaw<?php echo $post->ID;?>, pitch:currentPitch<?php echo $post->ID;?>, zoom:currentZoom<?php echo $post->ID;?>})
			}
			function stopTour() {
			  clearInterval(timer<?php echo $post->ID;?>)
			}
			function startTour() {
			  clearInterval(timer<?php echo $post->ID;?>);
			  timer<?php echo $post->ID;?> = window.setInterval(spiral<?php echo $post->ID;?>, speed)
			}
			function speedUp() {
			  clearInterval(timer<?php echo $post->ID;?>);
			  0 < speed && (speed -= 60);
			  timer<?php echo $post->ID;?> = window.setInterval(spiral<?php echo $post->ID;?>, speed)
			}
			function slowDown() {
			  clearInterval(timer<?php echo $post->ID;?>);
			  180 > speed && (speed += 60);
			  timer<?php echo $post->ID;?> = window.setInterval(spiral<?php echo $post->ID;?>, speed)
			}

			
			jQuery(document).ready(function() { 
			   initialize<?php echo $post->ID;?>();
				
				jQuery("#pano<?php echo $post->ID;?>").hover(function() {
				  clearInterval(timer<?php echo $post->ID;?>);
				}, function() {
				  timer<?php echo $post->ID;?> = window.setInterval(spiral<?php echo $post->ID;?>, speed)
				})   
			});
			</script>
        
        <div class="panoRota" id="pano<?php echo $post->ID;?>" style="width: <?php echo ($thisMeta && $thisMeta['panowidth'] && $thisMeta['panowidth'] != '') ? $thisMeta['panowidth'] : '75%';?>; height: <?php echo ($thisMeta && $thisMeta['panoheight'] && $thisMeta['panoheight'] != '') ? $thisMeta['panoheight'] : '400px';?>;float:center"></div>
        
        
	<h2>PanoRota Settings</h2>
    <p>Change this PanoRota's settings below, click "Publish" to see changes.</p>
    <label>Panoid ID:</label><br/>
    <input name="panorota[panoidid]" value="<?php echo ($thisMeta && isset($thisMeta['panoidid']) && $thisMeta['panoidid'] != '') ? $thisMeta['panoidid'] : 'C-7SY-PmQE6qNp0AOCUuzQ';?>"/>
    <em>E.G: C-7SY-PmQE6qNp0AOCUuzQ</em>
    <br/>
    <label>PanoRota Width (px/%):</label><br/>
    <input name="panorota[panowidth]" value="<?php echo ($thisMeta && isset($thisMeta['panowidth']) && $thisMeta['panowidth'] != '') ? $thisMeta['panowidth'] : '75%';?>"/>
    <em>E.G: 400px or 75%</em>
    <br/>
    <label>PanoRota Height (px/%):</label><br/>
    <input name="panorota[panoheight]" value="<?php echo ($thisMeta && isset($thisMeta['panoheight']) && $thisMeta['panoheight'] != '') ? $thisMeta['panoheight'] : '400px';?>"/>
    <em>E.G: 400px or 75%</em>
    <br/>
    <label>Refresh Time in mS:</label><br/>
    <input name="panorota[panospeed]" value="<?php echo ($thisMeta && isset($thisMeta['panospeed']) && $thisMeta['panospeed'] != '') ? $thisMeta['panospeed'] : '30';?>"/>
    <em>Enter time between image refresh e.g. 30</em>
    <br/>
    <label>Heading Delta:</label><br/>
    <input name="panorota[panodelta]" value="<?php echo ($thisMeta && isset($thisMeta['panodelta']) && $thisMeta['panodelta'] != '') ? $thisMeta['panodelta'] : '0.1';?>"/>
    <em>Enter the change in heading (in degrees) for every refresh e.g. 0.1</em>
    <br/>
    <label>Initial Heading, in degrees:</label><br/>
    <input name="panorota[panoyaw]" value="<?php echo ($thisMeta && isset($thisMeta['panoyaw']) && $thisMeta['panoyaw'] != '') ? $thisMeta['panoyaw'] : '310';?>"/>
    <em>e.g. 105</em>
    <br/>
    <label>Pitch:</label><br/>
    <input name="panorota[panopitch]" value="<?php echo ($thisMeta && isset($thisMeta['panopitch']) && $thisMeta['panopitch'] != '') ? $thisMeta['panopitch'] : '0.7';?>"/>
    <em>Enter the vertical tilt in degrees e.g. 7. Forward is 0, Down is 90, Straight up is 270.</em>
    <br/>
    <label>Zoom:</label><br/>
    <input name="panorota[panozoom]" value="<?php echo ($thisMeta && isset($thisMeta['panozoom']) && $thisMeta['panozoom'] != '') ? $thisMeta['panozoom'] : '1.25';?>"/>
    <em>E.G: 1.25</em>
    <br/>
    <label>Show Link Arrows:</label><br/>
    <label>Yes:</label>
    <input type="radio" name="panorota[panoarrows]" value="yes" <?php checked(!isset($thisMeta['panoarrows']) || $thisMeta['panoarrows'] == 'yes');?>/><label> No:</label>
    <input type="radio" name="panorota[panoarrows]" value="no" <?php checked($thisMeta['panoarrows'] == 'no');?>/>
    <br/>
    <label>Show Pan Compass:</label><br/>
    <label>Yes:</label>
    <input type="radio" name="panorota[panocompass]" value="yes" <?php checked(!isset($thisMeta['panocompass']) || $thisMeta['panocompass'] == 'yes');?>/><label> No:</label>
    <input type="radio" name="panorota[panocompass]" value="no" <?php checked($thisMeta['panocompass'] == 'no');?>/>
    <br/>
    <label>Show Zoom Control:</label><br/>
    <label>Yes:</label>
    <input type="radio" name="panorota[panozoomcontrol]" value="yes" <?php checked(!isset($thisMeta['panozoomcontrol']) || $thisMeta['panozoomcontrol'] == 'yes');?>/><label> No:</label>
    <input type="radio" name="panorota[panozoomcontrol]" value="no" <?php checked($thisMeta['panozoomcontrol'] == 'no');?>/>
<?php
}

add_action('save_post', 'panosaveMetaBoxes');

function panosaveMetaBoxes ($post_id) {
	
	if (isset($_POST['panorota'])) {
		$postdata = $_POST['panorota'];
		update_post_meta($post_id, '_PanoRotaMeta', $postdata);
	}
}
	



// ADD SHORTCODES
$getPanoPosts = get_posts(array('posts_per_page'=>-1,'post_status'=>'publish', 'post_type'=>'panoRota'));

if (!empty($getPanoPosts)) {
	foreach ($getPanoPosts as $PanoPost) {
		$pid = $PanoPost->ID;
		
		new PanoRotaInit($pid);
	}
}

// ENABLE SHORTCODE USE IN WIDGETS
add_filter('widget_text', 'do_shortcode');


class PanoRotaInit {

	public $_pid;
	
	function PanoRotaInit ($pid) {
		$this->_pid = $pid;
		add_shortcode('PanoRota', array(&$this,'output'));
	}
	
	function output ($atts) {	
		$thisMeta = get_post_meta($atts['id'], '_PanoRotaMeta', true);
		
		// check is actual post
		if ($atts['id'] != '' && get_post($atts['id']) && get_post($atts['id'])->post_type == 'panorota') {
		ob_start();?>

        <script type="text/javascript">
			var idpano<?php echo $atts['id'];?> = "<?php echo ($thisMeta && isset($thisMeta['panoidid']) && $thisMeta['panoidid'] != '') ? $thisMeta['panoidid'] : 'C-7SY-PmQE6qNp0AOCUuzQ';?>", currentYaw<?php echo $atts['id'];?> = <?php echo ($thisMeta && isset($thisMeta['panoyaw']) && $thisMeta['panoyaw'] != '') ? $thisMeta['panoyaw'] : '310';?>, currentPitch<?php echo $atts['id'];?> = -<?php echo ($thisMeta && isset($thisMeta['panopitch']) && $thisMeta['panopitch'] != '') ? $thisMeta['panopitch'] : '0.7';?>, currentZoom<?php echo $atts['id'];?> = <?php echo ($thisMeta && isset($thisMeta['panozoom']) && $thisMeta['panozoom'] != '') ? $thisMeta['panozoom'] : '1.25';?>, panoOptions<?php echo $atts['id'];?> = {
				addressControl:!1, imageDateControl:true, zoomControl:<?php echo (!isset($thisMeta['panozoomcontrol']) || $thisMeta['panozoomcontrol'] == 'yes') ? 'true' : 'false';?>, linksControl:<?php echo (!isset($thisMeta['panoarrows']) || $thisMeta['panoarrows'] == 'yes') ? 'true' : 'false';?>, panControl:<?php echo (!isset($thisMeta['panocompass']) || $thisMeta['panocompass'] == 'yes') ? 'true' : 'false';?>, enableCloseButton:false, visible:true, pov: {
				  heading: 310,
				  pitch: -7,
				  zoom: 1.25
				}}, speed<?php echo $atts['id'];?> = <?php echo ($thisMeta && isset($thisMeta['panospeed']) && $thisMeta['panospeed'] != '') ? $thisMeta['panospeed'] : '30';?>, delta<?php echo $atts['id'];?> = <?php echo ($thisMeta && $thisMeta['panodelta'] && $thisMeta['panodelta'] != '') ? $thisMeta['panodelta'] : '0.1';?>, timer<?php echo $atts['id'];?>, panorama<?php echo $atts['id'];?>;
			function initialize<?php echo $atts['id'];?>() {
			  panorama<?php echo $atts['id'];?> = new google.maps.StreetViewPanorama(document.getElementById("pano<?php echo $atts['id'];?>"), panoOptions<?php echo $atts['id'];?>);
			  panorama<?php echo $atts['id'];?>.setPano(idpano<?php echo $atts['id'];?>);
			  timer<?php echo $atts['id'];?> = window.setInterval(spiral<?php echo $atts['id'];?>, speed<?php echo $atts['id'];?>);
			  google.maps.event.addListener(panorama<?php echo $atts['id'];?>, "pov_changed", function() {
				currentYaw<?php echo $atts['id'];?> = panorama<?php echo $atts['id'];?>.getPov().heading;
				currentPitch<?php echo $atts['id'];?> = panorama<?php echo $atts['id'];?>.getPov().pitch;
				currentZoom<?php echo $atts['id'];?> = panorama<?php echo $atts['id'];?>.getPov().zoom
			  });
			}
 			function spiral<?php echo $atts['id'];?>() {
			  currentYaw<?php echo $atts['id'];?> += delta<?php echo $atts['id'];?>;
			  panorama<?php echo $atts['id'];?>.setPov({heading:currentYaw<?php echo $atts['id'];?>, pitch:currentPitch<?php echo $atts['id'];?>, zoom:currentZoom<?php echo $atts['id'];?>})
			}
			function stopTour() {
			  clearInterval(timer<?php echo $atts['id'];?>)
			}
			function startTour() {
			  clearInterval(timer<?php echo $atts['id'];?>);
			  timer<?php echo $atts['id'];?> = window.setInterval(spiral<?php echo $atts['id'];?>, speed<?php echo $atts['id'];?>)
			}
			function speedUp() {
			  clearInterval(timer<?php echo $atts['id'];?>);
			  0 < speed<?php echo $atts['id'];?> && (speed<?php echo $atts['id'];?> -= 60);
			  timer<?php echo $atts['id'];?> = window.setInterval(spiral<?php echo $atts['id'];?>, speed<?php echo $atts['id'];?>)
			}
			function slowDown() {
			  clearInterval(timer<?php echo $atts['id'];?>);
			  180 > speed<?php echo $atts['id'];?> && (speed<?php echo $atts['id'];?> += 60);
			  timer<?php echo $atts['id'];?> = window.setInterval(spiral<?php echo $atts['id'];?>, speed<?php echo $atts['id'];?>)
			}
			jQuery(document).ready(function() { 
			   initialize<?php echo $atts['id'];?>();
				jQuery("#pano<?php echo $atts['id'];?>").hover(function() {
				  clearInterval(timer<?php echo $atts['id'];?>);
				}, function() {
				  timer<?php echo $atts['id'];?> = window.setInterval(spiral<?php echo $atts['id'];?>, speed<?php echo $atts['id'];?>)
				})   
			});
			</script>
        <div class="panoRota" id="pano<?php echo $atts['id'];?>" style="width: <?php echo ($thisMeta && $thisMeta['panowidth'] && $thisMeta['panowidth'] != '') ? $thisMeta['panowidth'] : '75%';?>; height: <?php echo ($thisMeta && $thisMeta['panoheight'] && $thisMeta['panoheight'] != '') ? $thisMeta['panoheight'] : '400px';?>;float:center"></div>
        
		<?php return ob_get_clean();
		}
	}
	
}
