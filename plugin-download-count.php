<?php
/**
* Plugin Name: Plugin Download Count
* Plugin URI: http://www.wpcube.co.uk/plugins/plugin-download-count
* Version: 1.0
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Gets the total download count for one or more defined WordPress Themes and/or Plugins
* License: GPL2
*/

/*  Copyright 2013 WP Cube (email : support@wpcube.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* DownloadCount Class
* 
* @package WP Cube
* @subpackage Download Count
* @author Tim Carr
* @version 1.0
* @copyright WP Cube
*/
class PluginDownloadCount {
    /**
    * Constructor.
    */
    function PluginDownloadCount() {
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name = 'plugin-download-count'; // Plugin Folder
        $this->plugin->displayName = 'Download Count'; // Plugin Name
        $this->plugin->version = 1.0;
        $this->plugin->folder = WP_PLUGIN_DIR.'/'.$this->plugin->name; // Full Path to Plugin Folder
        $this->plugin->url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
        
        // Dashboard Submodule
        if (!class_exists('WPCubeDashboardWidget')) {
			require_once($this->plugin->folder.'/_modules/dashboard/dashboard.php');
		}
		$dashboard = new WPCubeDashboardWidget($this->plugin); 
		
		// Hooks
        add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndCSS'));
        add_action('admin_menu', array(&$this, 'adminPanelsAndMetaBoxes'));
        add_action('init', array(&$this, 'setupTinyMCEPlugins'));
        
        // Frontend
        add_action('wp_enqueue_scripts', array(&$this, 'frontendScriptsAndCSS'));
        
        // AJAX
        add_action('wp_ajax_count', array(&$this, 'updateDownloadCount'));
        add_action('wp_ajax_nopriv_count', array(&$this, 'updateDownloadCount'));
        
        // Shortcode
        add_shortcode('PDC', array(&$this, 'displayDownloadCount'));
    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function adminScriptsAndCSS() {
    	// CSS
        wp_enqueue_style($this->plugin->name.'-admin', $this->plugin->url.'css/admin.css', array(), $this->plugin->version); 
    }
    
    /**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
        add_menu_page($this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'), $this->plugin->url.'images/icons/small.png');
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
        // Save Settings
        if (isset($_POST['submit'])) {
        	if (isset($_POST[$this->plugin->name])) {
        		update_option($this->plugin->name, $_POST[$this->plugin->name]);
				$this->message = __('Settings Updated.', $this->plugin->name);
			}
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
        
		// Load Settings Form
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/views/settings.php');  
    }
    
    /**
    * Setup calls to add a button and plugin to the TinyMCE Rich Text Editors, except on the plugin's
    * own screens.
    */
    function setupTinyMCEPlugins() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array(&$this, 'addTinyMCEPlugin'));
        	add_filter('mce_buttons', array(&$this, 'addTinyMCEButton'));
    	}
    }
    
    /**
    * Adds a button to the TinyMCE Editor for shortcode inserts
    */
	function addTinyMCEButton($buttons) {
	    array_push($buttons, "|", 'pdc');
	    return $buttons;
	}
	
	/**
    * Adds a plugin to the TinyMCE Editor for shortcode inserts
    */
	function addTinyMCEPlugin($plugin_array) {
	    $plugin_array['pdc'] = $this->plugin->url.'/js/editor_plugin.js';
	    return $plugin_array;
	}
	
	/**
    * Register and enqueue any JS and CSS for the frontend web site
    */
	function frontendScriptsAndCSS() {
		// JS
		wp_enqueue_script('jquery');
		wp_enqueue_script($this->plugin->name.'-frontend', $this->plugin->url.'js/ui.js', array('jquery'), $this->plugin->version, true); 
		
		// Localize
		$this->settings = get_option($this->plugin->name);
		wp_localize_script($this->plugin->name.'-frontend', 'wp', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'interval' => ((isset($this->settings['interval']) AND is_numeric($this->settings['interval'])) ? ($this->settings['interval'] * 1000) : 15000),
        ));
	}
	
	/**
    * Called by AJAX requests in js/ui.js
    *
    * Used to update the download count
    */
    function updateDownloadCount() {
    	$count = $this->getDownloadCount(true);
		$countArr = str_split(number_format($count));
		$html = '';
		foreach ($countArr as $char) {
			if (is_numeric($char)) {
				$html .= '<span class="number">'.$char.'</span>';
			} else {
				$html .= '<span class="comma">'.$char.'</span>';
			}
		}
		
		echo $html;
    	die();	
    }
    
    /**
    * Get the total number of downloads recorded across all chosen plugins and themes
    *
    * @param bool $force Force update (false = load from transient)
    */
    function getDownloadCount($force = false) {
    	// If not forcing an update, check if there is a cached result + return it
    	$cachedDownloadCount = get_transient($this->plugin->name);
    	if (!$force AND $cachedDownloadCount !== false) {
    		return $cachedDownloadCount;
    	}
    
    	// Setup vars
    	$this->settings = get_option($this->plugin->name);
		$downloadCount = 0;
		
		// If no plugins/themes defined, return 0
		if (!isset($this->settings) OR !(isset($this->settings['plugins']))) return 0;
		$plugins = explode("\n", rtrim($this->settings['plugins'], "\n"));
		if (!is_array($plugins) OR count($plugins) == 0) return 0;
		
		foreach ($plugins as $plugin) {
			$response = wp_remote_post('http://api.wordpress.org/plugins/info/1.0/', array(
				'body' => array(
					'action' => 'plugin_information',
					'timeout' => 15,
					'request' => serialize((object) array( 'slug' => $plugin )),
				) ,
			));
			
			$pluginInfo = unserialize($response['body']);
			
			$downloadCount += $pluginInfo->downloaded;
		}
		
		// Store in transient + return
		set_transient($this->plugin->name, $downloadCount, HOUR_IN_SECONDS);
		return $downloadCount;
	}
	
	/**
	* Displays the download count
	*
	* Called programmatically or using a shortcode
	*/
	function displayDownloadCount($atts = '') {
		$count = $this->getDownloadCount();
		$countArr = str_split(number_format($count));
		
		$html = '<div class="'.$this->plugin->name.'">';
		foreach ($countArr as $char) {
			if (is_numeric($char)) {
				$html .= '<span class="number">'.$char.'</span>';
			} else {
				$html .= '<span class="comma">'.$char.'</span>';
			}
		}
		$html .= '</div>';
		
		return $html;	
	}
}
$pdc = new PluginDownloadCount();
?>
