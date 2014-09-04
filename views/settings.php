<div class="wrap">
    <div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
    		
    			<!-- Form Start -->
		        <form id="post" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
		                <div class="postbox">
		                    <h3 class="hndle"><?php _e('Title', $this->plugin->name); ?></h3>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Plugins', $this->plugin->name); ?></strong>
		                    		<textarea name="<?php echo $this->plugin->name; ?>[plugins]"><?php echo (isset($this->settings['plugins']) ? $this->settings['plugins'] : ''); ?></textarea>
		                    	</p>
		                    	<p class="description">
		                    		<?php _e('Enter the programmatic name of each plugin, one per line. For example, this plugin\'s programmatic name is plugin-download-count. Each Plugin listed here will form the download count total.', $this->plugin->name); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Themes', $this->plugin->name); ?></strong>
		                    		<textarea name="<?php echo $this->plugin->name; ?>[themes]"><?php echo (isset($this->settings['themes']) ? $this->settings['themes'] : ''); ?></textarea>
		                    	</p>
		                    	<p class="description">
		                    		<?php _e('Enter the programmatic name of each theme, one per line. For example, spun. Each Theme listed here will form the download count total.', $this->plugin->name); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Update Interval', $this->plugin->name); ?></strong>
		                    		<input type="number" name="<?php echo $this->plugin->name; ?>[interval]" value="<?php echo (isset($this->settings['interval']) ? $this->settings['interval'] : '30'); ?>" min="15" max="9999" step="1" />
		                    	</p>
		                    	<p class="description">
		                    		<?php _e('When a visitor views the download count, it\'s updated via AJAX periodically by querying the wordpress.org API. Define the number of seconds to check for a new download count.', $this->plugin->name); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Enable CSS', $this->plugin->name); ?></strong>
		                    		<select name="<?php echo $this->plugin->name; ?>[enableCSS]" size="1">
		                    			<option value="1"<?php echo ((isset($this->settings['enableCSS']) AND $this->settings['enableCSS'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
		                    			<option value=""<?php echo ((!isset($this->settings['enableCSS']) OR $this->settings['enableCSS'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
		                    		</select>
		                    	</p>
		                    	<p class="description">
		                    		<?php _e('Select Yes if you want to use this plugin\'s CSS styles for the download count. Select No if you will provide CSS in your Theme.', $this->plugin->name); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                       		<input type="submit" name="submit" value="<?php _e('Save', $this->plugin->name); ?>" class="button button-primary" /> 
		                 		</p>
		                    </div>
		                </div>
		                <!-- /postbox -->
		                
		                <div class="postbox">
		                    <h3 class="hndle"><?php _e('Output', $this->plugin->name); ?></h3>
		                    
		                    <div class="option">
		                    	<p>
		                    		<?php _e('Use the shortcode [PDC] to display the download count on your Page, Post or Custom Post Type', $this->plugin->name); ?>
		                    	</p>
		                    </div>
		                </div>
					</div>
					<!-- /normal-sortables -->
			    </form>
			    <!-- /form end -->
    			
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/sidebar-donate.php'); ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div> 
	
	<!-- If this plugin has a pro/premium version, include this + change sidebar-donate = sidebar-upgrade -->
	<div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-1">
    		<div id="post-body-content">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/footer-upgrade.php'); ?>
    		</div>
    	</div>
    </div>        
</div>