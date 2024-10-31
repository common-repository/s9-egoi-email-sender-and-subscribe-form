<?php
/*
Plugin Name: Sector 9 - Egoi Plugin
Plugin URI: http://egoi.sector9.pt
Description: Sector 9 - Egoi Plugin. Version 1.0.1
Version: 1.0.2
Author: Sector 9 - Tecnologias de Informação, Lda.
Author URI: http://www.sector9.pt
*/


include_once('includes/class.egoi.php');
include_once('s9-egoi.widget.php');

global $S9Egoi;
$file = __FILE__;
$options = array(
	'plugin_key' => '37a1fb384ce4a2643bc5fc93561438a8',
	'api_key'    => '',
	'sender_email' => '',
	'sender_name' =>'',
	'reply_email' => '',
	'reply_name' => '',
	'auto_campaign' => 0,
	'auto_campaign_min_posts' => 5,
	'auto_campaign_subject' => '!blog_name Newsletter - !current_date',
	'auto_subscription' => 0,
	'auto_subscription_list' => 0,
	'image_header' => '',
	'link_referer_top' => 0,
	'link_referer_bottom' => 0,
	'link_view_top' => 0,
	'link_view_bottom' => 0,
	'link_remove_top' => 0,
	'link_remove_bottom' => 0,
	'link_edit_top' => 0,
	'link_edit_bottom' => 0,
	'link_print_top' => 0,
	'link_print_bottom' => 0,
	'link_social_networks_top' => 0,
	'link_social_networks_bottom' => 0,
	'form_list_id' => 0,
	'form_email' => 1,
	'form_first_name' => 0,
	'form_last_name' => 0,
	'form_cellphone' => 0,
	'form_telephone' => 0,
	'form_fax' => 0,
	'form_address' => 0,
	'form_zip_code' => 0,
	'form_city' => 0,
	'form_district' => 0,
	'form_state' => 0,
	'form_age' => 0,
	'form_birth_date' => 0,
	'form_gender' => 0,
	'form_company' => 0,
);

if (!class_exists("S9Egoi")) {
	class S9Egoi {
		
		var $egoi;
		var $base_url;
		var $default_options;
		var $options;
		var $message;
		var $status;
		
		protected $_slug = 's9-egoi';
		
		public function S9Egoi($options) {
			
			load_plugin_textdomain( $this->_slug, false, basename(dirname(__FILE__)) . '/languages');
			add_action('init', array(&$this, 'init'));
			
			if (is_admin()) {
				register_activation_hook   (__FILE__, array(&$this, 'plugin_install') );
				register_deactivation_hook (__FILE__, array(&$this, 'plugin_uninstall') );
				
				add_action('admin_menu', array(&$this, 'admin_menu'));
				add_action('admin_notices', array(&$this, 'admin_notices'));
				add_action('admin_init', array(&$this, 'admin_init'));
			}
			$path = str_replace('\\','/',dirname(__FILE__));
			$path = substr($path, strpos($path, 'plugins') + 8, strlen($path));
			
			$this->base_url['plugin']  = get_bloginfo('url') . '/wp-content/plugins/'.$path;
			$this->base_url['posts']   = get_bloginfo('url') . '/wp-admin/edit.php?page=';
			$this->base_url['options'] = get_bloginfo('url') . '/wp-admin/options-general.php?page=';
			
			$form_options = array (
			'form_email_label'      => __('Email', $this->_slug),      'form_email_required'      => 1,
			'form_first_name_label' => __('First Name', $this->_slug), 'form_first_name_required' => 0,
			'form_last_name_label'  => __('Last Name', $this->_slug),  'form_last_name_required'  => 0,
			'form_cellphone_label'  => __('Cellphone', $this->_slug),  'form_cellphone_required'  => 0,
			'form_telephone_label'  => __('Telephone', $this->_slug),  'form_telephone_required'  => 0,
			'form_fax_label'        => __('Fax', $this->_slug),        'form_fax_required'        => 0,
			'form_address_label'    => __('Address', $this->_slug),    'form_address_required'    => 0,
			'form_zip_code_label'   => __('Zip Code', $this->_slug),   'form_zip_code_required'   => 0,
			'form_city_label'       => __('City', $this->_slug),       'form_city_required'       => 0,
			'form_district_label'   => __('District', $this->_slug),   'form_district_required'   => 0,
			'form_state_label'      => __('State', $this->_slug),      'form_state_required'      => 0,
			'form_age_label'        => __('Age', $this->_slug),        'form_age_required'        => 0,
			'form_birth_date_label' => __('Birth Date', $this->_slug), 'form_birth_date_required' => 0,
			'form_gender_label'     => __('Gender', $this->_slug),     'form_gender_required'     => 0,
			'form_company_label'    => __('Company', $this->_slug),    'form_company_required'    => 0,
			);
			$options = array_merge($options, $form_options);
			$this->default_options = $options;
			$options_from_table = get_option( $this->_slug );
			foreach( (array) $options as $default_options_name => $default_options_value ) {
				if ( !is_null($options_from_table[$default_options_name]) ) {
					if ( is_int($default_options_value) ) {
						$options[$default_options_name] = (int) $options_from_table[$default_options_name];
					} else {
						$options[$default_options_name] = $options_from_table[$default_options_name];
					}
				}
			}
			$this->options = $options;
			unset($options); unset($options_from_table); unset($default_options_value);
			if (!empty($this->options['api_key'])) {
				$this->egoi = new Egoi($this->options['api_key'], $this->options['plugin_key']);
			}
			if ($this->options['auto_subscription'] == '1') {
				add_action('user_register',array(&$this, 'auto_subscription'),99);
			}
			add_action('s9egoicron', array(&$this,'auto_campaign'));
		}
		
		public function plugin_install() {
			$options_from_table = get_option( $this->_slug );
			if ( !$options_from_table ) {
				update_option($this->_slug, $this->default_options);
				$this->options = $this->default_options;
			}
			wp_clear_scheduled_hook('s9egoicron');
			wp_schedule_event( time(), 'hourly', 's9egoicron' );
		}
		
		public function plugin_uninstall() {
			delete_option($this->_slug, $this->default_options);
			wp_clear_scheduled_hook('s9egoicron');
		}
		
		function set_option($optname, $optval) {
			$this->options[$optname] = $optval;
		}

		function save_options() {
			update_option($this->_slug, $this->options);
		}
		
		function init() {
			$labels = array(
						'name' => _x('Emails', 'post type general name'),
						'singular_name' => _x('Email', 'post type singular name'),
						'add_new' => _x('New Email', $this->_slug),
						'add_new_item' => __('Create Email', $this->_slug),
						'edit_item' => __('Edit Email', $this->_slug),
						'new_item' => __('New Email', $this->_slug),
						'view_item' => __('View Email', $this->_slug),
						'search_items' => __('Search Emails', $this->_slug),
						'not_found' =>  __('No Results', $this->_slug),
						'not_found_in_trash' => __('No Results found in Trash', $this->_slug), 
						'parent_item_colon' => ''
			);	
			$args = array(
						'labels' => $labels,
						'public' => true,
						'show_ui' => true,
						'capability_type' => 'post',
						'hierarchical' => false,
						'rewrite' => true,
						'query_var' => true,
						'show_in_nav_menus'=> false,
						'menu_position' => 5,
						'supports' => array('title','editor','custom-fields')
			);
			register_post_type('s9egoiemail', $args );
			wp_enqueue_script  ('jquery');
			wp_register_script ($this->_slug.'functions', $this->base_url['plugin'].'/includes/functions.js');
			wp_enqueue_script  ($this->_slug.'functions');
			wp_register_style  ($this->_slug.'styles', $this->base_url['plugin'].'/includes/styles.css');
			wp_enqueue_style   ($this->_slug.'styles');
			add_action('wp_ajax_s9egoi_message', array($this, 'message') );
			add_action('wp_ajax_nopriv_s9egoi_message', array($this, 'message'));
		}
		
		function admin_init() {
			add_meta_box($this->_slug . '-box-post-info', __('Egoi Info', $this->_slug), array($this, 'box_post_info'), "s9egoiemail", "side", "low");	
			add_action('save_post', array($this, 'save_post_info'));
			add_action("manage_posts_custom_column",  array($this, 's9egoiemail_custom_columns'));
			add_filter("manage_edit-s9egoiemail_columns", array($this, 's9egoiemail_edit_columns'));
		}
		
		function admin_menu() {
			add_options_page( 'Egoi: Options', 'Egoi', 10, $this->_slug.'page-options', array(&$this, 'page_options'));
		}
		
		function admin_notices() {
			if ( $this->message != '') { $message = $this->message; $status = $this->status; $this->message = $this->status = ''; }
			if ( $message ) { 
				echo '<div id="message" class="'.(($status != '') ? $status : 'updated').'">'."\n";
				echo '<p><strong>'.$message.'</strong></p>'."\n";
				echo '</div>'."\n";
			}
		}

		function page_options() {
			if ( isset($_POST['update_api_key']) ) {
				$this->set_option( 'api_key', $_POST['api_key']);
				$this->save_options();	
			}
			if ( isset($_POST['reset_api_key']) ) {
				$this->set_option( 'api_key', '');
				$this->save_options();	
			}
			if (empty($this->options['api_key'])) {
				$this->message = __('Insert your Egoi Api Key to activate this plugin. If you don\'t have a Egoi Account',$this->_slug).', <a href="http://e-goi.com/s/e-goi/40301803c0" target="_blank">'.__('create one here',$this->_slug).'</a>.';
				$this->status  = 'error';	
				$this->admin_notices();
			} else {
				$this->message = '';
				$this->egoi = new Egoi($this->options['api_key'], $this->options['plugin_key']);	
			}
			
			$currentTab = isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general';
			
			add_meta_box( $this->_slug . '-box-info', __('Egoi Info', $this->_slug), array($this, 'box_info'), $this->_slug . 'page-options'); 
            add_meta_box( $this->_slug . '-box-support', __('Support', $this->_slug), array($this, 'box_support'), $this->_slug . 'page-options'); 
			
			$tabs = array( 'general' => __('General', $this->_slug), 'forms' => __('Forms', $this->_slug), 'layout' => __('Layout', $this->_slug), 'advanced' => __('Advanced', $this->_slug) );
    		$links = array();
    		foreach( $tabs as $tab => $name ) {
        		if ( $tab == $currentTab ) {
            		$links[] = '<a class="nav-tab nav-tab-active" href="?page='.$this->_slug . 'page-options'.'&tab='.$tab.'">'.$name.'</a>';
				} else {
            		$links[] = '<a class="nav-tab" href="?page='.$this->_slug . 'page-options'.'&tab='.$tab.'">'.$name.'</a>';
				}
			}
			
			if ( isset($_POST['update_options']) ) {
				foreach((array) $this->options as $key => $value) {
					if (isset($_POST[$key])) {
						$newval = stripslashes($_POST[$key]);
						if ( $newval != $value ) {
							$this->set_option( $key, $newval );
						}
					} 
				}
				$this->save_options();
				$this->message = __('Options updated.',$this->_slug);
				$this->status = 'updated';
				$this->admin_notices();
			} 
			
			if ( isset($_POST['erase_options']) ) {
				delete_option($this->_slug, $this->default_options);
				$this->message = __('All options were erased.',$this->_slug);
				$this->status = 'updated';
				$this->admin_notices();
			} 
			if ( isset($_POST['erase_campaigns']) ) {
				$this->delete_campaigns();
				$this->message = __('All saved campaigns were erased.',$this->_slug);
				$this->status = 'updated';
				$this->admin_notices();
			} 
			
			if ( isset($_POST['auto_campaign']) == 1) {
				$this->auto_campaign();	
			}
			
			?>
            <div class="wrap">
            	<h2>S9 Egoi: Options</h2>
                <form action="<?php echo '?page='.$this->_slug . 'page-options'.'&tab='.$currentTab; ?>" method="post">
            	<div class="metabox-holder">
					<div class="postbox-container" style="width:74%; margin-right:1%">
                    	<h2 class="tab-nav"><?php foreach ( $links as $link ) echo $link;?></h2>
                        <div class="tab-container">
                            <?php
                            switch ( $currentTab ) :
                                case 'general' :
                                $this->tab_options_general();
                                break;
                                case 'forms' :
                                $this->tab_options_forms();
                                break;
                                case 'layout' :
                                $this->tab_options_layout();
                                break;
                                case 'advanced' :
                                $this->tab_options_advanced();
                                break;
                            endswitch;
                            ?>
                            <p class="submit">
                                <input type="submit" name="update_options" value="<?php esc_attr_e('Update Options &raquo;', $this->_slug); ?>" />
                            </p>
                        </div>
                    </div>
                	<div class="postbox-container" style="width:24%;">
                    	<?php do_meta_boxes($this->_slug . 'page-options', 'advanced', ''); ?>
                	</div>
                </div>
                 </form>
            </div>
            <?php
		}
		
		function box_post_info() {
			global $post;
  			$custom = get_post_custom($post->ID);
			$list_id = $custom["_list_id"][0];
			$segment = $custom["_segment"][0];
			$send_date  = $custom["_send_date"][0];
			$auto_post  = (isset($custom["_auto_post"][0])) ? $custom["_auto_post"][0] : 0;
			
		    ?>
            <?php 
				if (!empty($custom["_s9egoi_result"][0])){
					echo '<p>'.	$custom["_s9egoi_result"][0] . '</p>';
					update_post_meta($post->ID, "_s9egoi_result", '');
				}
			?>
		    <table class="optiontable form-table">
            	<tr valign="top">
					<td><label for="list_id"><?php _e('List ID', $this->_slug)?>:</label></td>
					<td><input id="list_id" name="list_id" value="<?php echo $list_id?>" size="15" type="text"></td>
				</tr>
                <tr valign="top">
					<td><label for="segment"><?php _e('Segment', $this->_slug)?>:</label></td>
					<td><input id="segment" name="segment" value="<?php echo $segment?>" size="15" type="text"></td>
				</tr>
                <tr valign="top">
					<td><label for="send_date"><?php _e('Send Date', $this->_slug)?>:</label></td>
					<td><input id="send_date" name="send_date" value="<?php echo ( $send_date > 0 ) ? date("Y-m-d H:i", $send_date) : __('Never', $this->_slug) ?>" size="15" type="text" disabled="disabled"></td>
				</tr>
                <tr>
                	<td colspan="2" align="center">
                    	<select name="campaign_action" id="campaign_action">
                        	<option value="0"><?php esc_attr_e('Save Campaign Draft', $this->_slug); ?></option>
                            <option value="1"><?php esc_attr_e('Send Campaign for Publishing', $this->_slug); ?></option>
                        </select>
                        <input type="hidden" name="auto_post" id="auto_post" value="<?php $auto_post?>" />
                    </td>
                </tr>
            </table>
		    <?php
		}
		
		function save_post_info() {
			global $post;
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return $post->ID;
			} 
  			update_post_meta($post->ID, "_list_id", $_POST["list_id"]);
  			update_post_meta($post->ID, "_segment", $_POST["segment"]);
			
			if ($_POST['campaign_action'] == 1) {
				
				$list_id = $_POST['list_id'];
				
				$segment = $_POST['segment'];
				
				$url     = $this->base_url['plugin'] . '/s9-egoi.email.php?post_ID='.$post->ID;
				
				$subject = $post->post_title;
				
				$from    = array ('from_email' => $this->options['sender_email'], 
							  	  'from_name' => $this->options['sender_name']);
								  
				$reply   = array ('reply_email' => $this->options['reply_email'], 
								  'reply_name' => $this->options['reply_name']);
								  
				$link    = array ('link_referer_top' => $this->options['link_referer_top'], 
								  'link_referer_bottom' => $this->options['link_referer_bottom'],
								  'link_view_top' => $this->options['link_view_top'],
								  'link_view_bottom' => $this->options['link_view_bottom'],
								  'link_remove_top' => $this->options['link_remove_top'],
								  'link_remove_bottom' => $this->options['link_remove_bottom'],
								  'link_edit_top' => $this->options['link_edit_top'],
								  'link_edit_bottom' => $this->options['link_edit_bottom'],
								  'link_print_top' => $this->options['link_print_top'],
								  'link_print_bottom' => $this->options['link_print_bottom'],
								  'link_social_networks_top' => $this->options['link_social_networks_top'],
								  'link_social_networks_bottom' => $this->options['link_social_networks_bottom']);				  
				$this->egoi->add_email($list_id, $url, $subject, $from, $reply, $link);
				if (!$this->egoi->error) {
					$campaign = $this->egoi->result['id'];
					$this->egoi->send_email($campaign,$list_id,'',$segment);
					if (!$this->egoi->error) {
						update_post_meta($post->ID, "_send_date", time());	
						update_post_meta($post->ID, "_s9egoi_result", '');
					} else {
						update_post_meta($post->ID, "_s9egoi_result", $this->message($this->egoi->error, TRUE));	
					}
				} else {
					update_post_meta($post->ID, "_s9egoi_result", $this->message($this->egoi->error, TRUE));		
				}
			}
		}
		
		function s9egoiemail_custom_columns($column) {
			global $post;
			$custom = get_post_custom($post->ID);
			switch ($column) {
				case "list_id":
				  echo $custom["_list_id"][0];
				  break;
				case "segment":
				  echo $custom["_segment"][0];
				  break;
				case "send_date":
				  echo ( $custom["_send_date"][0] > 0 ) ? date("Y-m-d H:i", $custom["_send_date"][0]) : __('Never', $this->_slug);
				  break;
			  }
		}
		
		function s9egoiemail_edit_columns($columns) {
			$newcolumns  = array(
    			"cb" => "<input type=\"checkbox\" />",
    			"title" => __('Subject', $this->_slug),
    			"list_id" => __('List ID', $this->_slug),
				"segment" =>  __('Segment', $this->_slug),
    			"send_date" =>  __('Send Date', $this->_slug),
  			);
  			$columns= array_merge($newcolumns, $columns);
			return $columns;
		}
		
		function box_info() {
			if (empty($this->options['api_key'])) {
			?>
            <table class="optiontable form-table">
            	<tr valign="top">
					<th scope="row"><label for="api_key">Api Key:</label></th>
					<td><input id="api_key" name="api_key" value="" size="25" type="text"></td>
				</tr>
                <tr>
                	<td colspan="2" align="center"><input type="submit" name="update_api_key" value="<?php esc_attr_e('Activate Plugin', $this->_slug); ?>" /></td>
                </tr>
            </table>
            <?php
			} else {
				$client_data = array();
				$this->egoi->get_client_data();
				if (!$this->egoi->error) {
					$client_data = $this->egoi->result;	
					?>
                    <table class="optiontable form-table">
            		<tr valign="top">
						<td style="width:75px;"><strong><?php _e('Account', $this->_slug)?>:</strong></td>
						<td><?php echo $client_data['company_name']?></td>
					</tr>
                    <tr valign="top">
						<td><strong><?php _e('Created in', $this->_slug)?>:</strong></td>
						<td><?php echo $client_data['signup_date']?></td>
					</tr>
                    <tr valign="top">
						<td><strong><?php _e('Credits', $this->_slug)?>:</strong></td>
						<td><?php echo $client_data['credits']?></td>
					</tr>
                    </table>
                    <?php
				} 
			?>
            <table class="optiontable form-table">
                <tr>
                	<td align="center"><input type="submit" name="reset_api_key" value="<?php esc_attr_e('Reset Api Key', $this->_slug); ?>" /></td>
                </tr>
            </table>
            <?
			}
		}
		
		function box_support() {
			?>
            <table class="optiontable form-table">
            <tr valign="top">
                <td style="width:75px;"><strong>DONATE:</strong></td>
                <td>Liked the plugin? Support our work with a small donation.</td>
            </tr>
            <tr valign="top">
                <td colspan="2" style="text-align:center;">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="25YEABYFVGEAC">
                <input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
                </td>
            </tr>
            <tr valign="top">
                <td style="width:75px;"><strong>EGOI:</strong></td>
                <td><a href="http://e-goi.com/s/e-goi/40301803c0" target="_blank">http://www.e-goi.com</a></td>
            </tr>
            <tr valign="top">
                <td><strong>SECTOR 9:</strong></td>
                <td><a href="http://www.sector9.pt" target="_blank">http://www.sector9.pt</a></td>
            </tr>
            <tr valign="top">
                <td colspan="2"><div style="text-align:center;">For questions, bugs and feature requests, send us an email to <strong>wordpress[at]sector9[dot]pt</strong></div></td>
                
            </tr>
            </table>
            <?
		}
		
		function tab_options_general() {
			?>
            <h3><?php _e('Email Options', $this->_slug)?>:</h3>
            <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="sender_email"><?php _e('Sender Email', $this->_slug)?>:</label></th>
                      <td><input id="sender_email" name="sender_email" value="<?php echo $this->options['sender_email'];?>" size="25" type="text"></td>
                      <th scope="row"><label for="sender_name"><?php _e('Sender Name', $this->_slug)?>:</label></th>
                      <td><input id="sender_name" name="sender_name" value="<?php echo $this->options['sender_name'];?>" size="25" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Sender Email Address. Needs to be certified in your Egoi Account.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Sender Name. Needs to be certified in your Egoi Account.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="reply_email"><?php _e('Reply Email', $this->_slug)?>:</label></th>
                      <td><input id="reply_email" name="reply_email" value="<?php echo $this->options['reply_email'];?>" size="25" type="text"></td>
                      <th scope="row"><label for="reply_name"><?php _e('Reply Name', $this->_slug)?>:</label></th>
                      <td><input id="reply_name" name="reply_name" value="<?php echo $this->options['reply_name'];?>" size="25" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('If empty, sender email will be used. Needs to be certified in your Egoi Account.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('If empty, sender name will be used. Needs to be certified in your Egoi Account.', $this->_slug)?></div></td>
                  </tr>
             </table>
             <h3><?php _e('Automatic Campaigns', $this->_slug);?></h3>
             <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="auto_campaign"><?php _e('Create automatic newsletter', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="auto_campaign" name="auto_campaign" value="0" /><input id="auto_campaign" name="auto_campaign" value="1" <?php if ($this->options['auto_campaign'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="auto_campaign_min_posts"><?php _e('Minimum posts', $this->_slug)?>:</label></th>
                      <td><input id="auto_campaign_min_posts" name="auto_campaign_min_posts" value="<?php echo $this->options['auto_campaign_min_posts'];?>" size="10" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="4"><div class="s9_form_field_description"><?php _e('Create email campaign with a list of X new posts, where X is the minimun posts you select.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="auto_campaign_subject"><?php _e('Email Subject', $this->_slug)?>:</label></th>
                      <td colspan="3"><input id="auto_campaign_subject" name="auto_campaign_subject" value="<?php echo $this->options['auto_campaign_subject'];?>" size="65" type="text"></td>
                  </tr>
                  <tr valign="top">
				  	  <td colspan="4"><div class="s9_form_field_description"><?php _e('Email subject for automatic campaigns. You can use the following tags on the subject', $this->_slug)?>: !blog_name, !post_title, !current_date</div></td>
                  </tr>
             </table>
             <h3><?php _e('Automatic Subscription', $this->_slug);?></h3>
             <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="auto_subscription"><?php _e('Subscribe registered users', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="auto_subscription" name="auto_subscription" value="0" /><input id="auto_subscription" name="auto_subscription" value="1" <?php if ($this->options['auto_subscription'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="auto_subscription_list"><?php _e('List ID', $this->_slug)?>:</label></th>
                      <td><input id="auto_subscription_list" name="auto_subscription_list" value="<?php echo $this->options['auto_subscription_list'];?>" size="25" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="4"><div class="s9_form_field_description"><?php _e('Subscribe new wordpress registered users to the list you select.', $this->_slug)?></div></td>
                  </tr>
             </table>
            <?php
		}
		
		function tab_options_forms() {
			?>
            <h3><?php _e('Subscribe List', $this->_slug)?>:</h3>
            <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="form_list_id"><?php _e('List ID', $this->_slug)?>:</label></th>
                      <td><input id="form_list_id" name="form_list_id" value="<?php echo $this->options['form_list_id'];?>" size="25" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Subscribe users to the list you select.', $this->_slug)?></div></td>
                  </tr>
             </table>
            <h3><?php _e('Subscribe Fields', $this->_slug)?>:</h3>
            <table class="optiontable form-table s9_table">
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_email" name="form_email" value="1" /><input id="form_email" name="form_email" value="1" checked="checked" type="checkbox" disabled></td>
                      <td><label for="form_email_label"><?php _e('Email Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_email_label" name="form_email_label" value="<?php echo $this->options['form_email_label'];?>" size="30" type="text"></td>
                      <td><label for="form_email_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_email_required" name="form_email_required" value="1" /><input id="form_email_required" name="form_email_required" value="1" checked="checked" type="checkbox" disabled></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_first_name" name="form_first_name" value="0" /><input id="form_first_name" name="form_first_name" value="1" <?php if ($this->options['form_first_name'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_first_name_label"><?php _e('First Name Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_first_name_label" name="form_first_name_label" value="<?php echo $this->options['form_first_name_label'];?>" size="30" type="text"></td>
                      <td><label for="form_first_name_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_first_name_required" name="form_first_name_required" value="0" /><input id="form_first_name_required" name="form_first_name_required" value="1" <?php if ($this->options['form_first_name_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_last_name" name="form_last_name" value="0" /><input id="form_last_name" name="form_last_name" value="1" <?php if ($this->options['form_last_name'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_last_name_label"><?php _e('Last Name Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_last_name_label" name="form_last_name_label" value="<?php echo $this->options['form_last_name_label'];?>" size="30" type="text"></td>
                      <td><label for="form_last_name_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_last_name_required" name="form_last_name_required" value="0" /><input id="form_last_name_required" name="form_last_name_required" value="1" <?php if ($this->options['form_last_name_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_cellphone" name="form_cellphone" value="0" /><input id="form_cellphone" name="form_cellphone" value="1" <?php if ($this->options['form_cellphone'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_cellphone_label"><?php _e('Cellphone Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_cellphone_label" name="form_cellphone_label" value="<?php echo $this->options['form_cellphone_label'];?>" size="30" type="text"></td>
                      <td><label for="form_cellphone_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_cellphone_required" name="form_cellphone_required" value="0" /><input id="form_cellphone_required" name="form_cellphone_required" value="1" <?php if ($this->options['form_cellphone_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_telephone" name="form_telephone" value="0" /><input id="form_telephone" name="form_telephone" value="1" <?php if ($this->options['form_telephone'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_telephone_label"><?php _e('Telephone Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_telephone_label" name="form_telephone_label" value="<?php echo $this->options['form_telephone_label'];?>" size="30" type="text"></td>
                      <td><label for="form_telephone_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_telephone_required" name="form_telephone_required" value="0" /><input id="form_telephone_required" name="form_telephone_required" value="1" <?php if ($this->options['form_telephone_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_fax" name="form_fax" value="0" /><input id="form_fax" name="form_fax" value="1" <?php if ($this->options['form_fax'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_fax_label"><?php _e('Fax Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_fax_label" name="form_fax_label" value="<?php echo $this->options['form_fax_label'];?>" size="30" type="text"></td>
                      <td><label for="form_fax_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_fax_required" name="form_fax_required" value="0" /><input id="form_fax_required" name="form_fax_required" value="1" <?php if ($this->options['form_fax_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_address" name="form_address" value="0" /><input id="form_address" name="form_address" value="1" <?php if ($this->options['form_address'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_address_label"><?php _e('Address Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_address_label" name="form_address_label" value="<?php echo $this->options['form_address_label'];?>" size="30" type="text"></td>
                      <td><label for="form_address_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_address_required" name="form_address_required" value="0" /><input id="form_address_required" name="form_address_required" value="1" <?php if ($this->options['form_address_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_zip_code" name="form_zip_code" value="0" /><input id="form_zip_code" name="form_zip_code" value="1" <?php if ($this->options['form_zip_code'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_zip_code_label"><?php _e('Zip Code Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_zip_code_label" name="form_zip_code_label" value="<?php echo $this->options['form_zip_code_label'];?>" size="30" type="text"></td>
                      <td><label for="form_zip_code_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_zip_code_required" name="form_zip_code_required" value="0" /><input id="form_zip_code_required" name="form_zip_code_required" value="1" <?php if ($this->options['form_zip_code_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_city" name="form_city" value="0" /><input id="form_city" name="form_city" value="1" <?php if ($this->options['form_city'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_city_label"><?php _e('City Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_city_label" name="form_city_label" value="<?php echo $this->options['form_city_label'];?>" size="30" type="text"></td>
                      <td><label for="form_city_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_city_required" name="form_city_required" value="0" /><input id="form_city_required" name="form_city_required" value="1" <?php if ($this->options['form_city_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_district" name="form_district" value="0" /><input id="form_district" name="form_district" value="1" <?php if ($this->options['form_district'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_district_label"><?php _e('District Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_district_label" name="form_district_label" value="<?php echo $this->options['form_district_label'];?>" size="30" type="text"></td>
                      <td><label for="form_district_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_district_required" name="form_district_required" value="0" /><input id="form_district_required" name="form_district_required" value="1" <?php if ($this->options['form_district_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_state" name="form_state" value="0" /><input id="form_state" name="form_state" value="1" <?php if ($this->options['form_state'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_state_label"><?php _e('State Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_state_label" name="form_state_label" value="<?php echo $this->options['form_state_label'];?>" size="30" type="text"></td>
                      <td><label for="form_state_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_state_required" name="form_state_required" value="0" /><input id="form_state_required" name="form_state_required" value="1" <?php if ($this->options['form_state_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_age" name="form_age" value="0" /><input id="form_age" name="form_age" value="1" <?php if ($this->options['form_age'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_age_label"><?php _e('Age Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_age_label" name="form_age_label" value="<?php echo $this->options['form_age_label'];?>" size="30" type="text"></td>
                      <td><label for="form_age_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_age_required" name="form_age_required" value="0" /><input id="form_age_required" name="form_age_required" value="1" <?php if ($this->options['form_age_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_birth_date" name="form_birth_date" value="0" /><input id="form_birth_date" name="form_birth_date" value="1" <?php if ($this->options['form_birth_date'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_birth_date_label"><?php _e('Birth Date Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_birth_date_label" name="form_birth_date_label" value="<?php echo $this->options['form_birth_date_label'];?>" size="30" type="text"></td>
                      <td><label for="form_birth_date_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_birth_date_required" name="form_birth_date_required" value="0" /><input id="form_birth_date_required" name="form_birth_date_required" value="1" <?php if ($this->options['form_birth_date_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_gender" name="form_gender" value="0" /><input id="form_gender" name="form_gender" value="1" <?php if ($this->options['form_gender'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_gender_label"><?php _e('Gender Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_gender_label" name="form_gender_label" value="<?php echo $this->options['form_gender_label'];?>" size="30" type="text"></td>
                      <td><label for="form_gender_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_gender_required" name="form_gender_required" value="0" /><input id="form_gender_required" name="form_gender_required" value="1" <?php if ($this->options['form_gender_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  <tr valign="top">
                  	  <td><input type="hidden" id="form_company" name="form_company" value="0" /><input id="form_company" name="form_company" value="1" <?php if ($this->options['form_company'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <td><label for="form_company_label"><?php _e('Company Label', $this->_slug); ?>:</label></td>
                      <td><input id="form_company_label" name="form_company_label" value="<?php echo $this->options['form_company_label'];?>" size="30" type="text"></td>
                      <td><label for="form_company_required"><?php _e('Required', $this->_slug); ?>:</label></td>
                      <td><input type="hidden" id="form_company_required" name="form_company_required" value="0" /><input id="form_company_required" name="form_company_required" value="1" <?php if ($this->options['form_company_required'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="5"><div class="s9_form_field_description"><?php _e('Activate, insert the label you want for this field and select if field is required or not.', $this->_slug)?></div></td>
                  </tr>
                  
                  
                  
            </table>
            <?	
		}
		
		function tab_options_layout() {
			?>
            <h3><?php _e('Email Header Image', $this->_slug)?>:</h3>
            <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="image_header"><?php _e('Image URL', $this->_slug)?>:</label></th>
                      <td><input id="image_header" name="image_header" value="<?php echo $this->options['image_header'];?>" size="65" type="text"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Image url to use in the header of email campaings. Must be: 580px by 200px. If empty, default image will be used.', $this->_slug)?></div></td>
                  </tr>
            </table>
            <h3><?php _e('Header Links', $this->_slug)?>:</h3>
            <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="link_referer_top"><?php _e('Referer', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_referer_top" name="link_referer_top" value="0" /><input id="link_referer_top" name="link_referer_top" value="1" <?php if ($this->options['link_referer_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_view_top"><?php _e('View', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_view_top" name="link_view_top" value="0" /><input id="link_view_top" name="link_view_top" value="1" <?php if ($this->options['link_view_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Referer link on email header.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show View link on email header.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="link_remove_top"><?php _e('Remove', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_remove_top" name="link_remove_top" value="0" /><input id="link_remove_top" name="link_remove_top" value="1" <?php if ($this->options['link_remove_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_edit_top"><?php _e('Edit', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_edit_top" name="link_edit_top" value="0" /><input id="link_edit_top" name="link_edit_top" value="1" <?php if ($this->options['link_edit_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Remove link on email header.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show Edit link on email header.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="link_print_top"><?php _e('Print', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_print_top" name="link_print_top" value="0" /><input id="link_print_top" name="link_print_top" value="1" <?php if ($this->options['link_print_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_social_networks_top"><?php _e('Social Networks', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_social_networks_top" name="link_social_networks_top" value="0" /><input id="link_social_networks_top" name="link_social_networks_top" value="1" <?php if ($this->options['link_social_networks_top'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Print link on email header.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show Social Networks link on email header.', $this->_slug)?></div></td>
                  </tr> 
             </table>
             <h3><?php _e('Footer Links', $this->_slug)?>:</h3>
             <table class="optiontable form-table s9_table">
                  <tr valign="top">
                      <th scope="row"><label for="link_referer_bottom"><?php _e('Referer', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_referer_bottom" name="link_referer_bottom" value="0" /><input id="link_referer_bottom" name="link_referer_bottom" value="1" <?php if ($this->options['link_referer_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_view_bottom"><?php _e('View', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_view_bottom" name="link_view_bottom" value="0" /><input id="link_view_bottom" name="link_view_bottom" value="1" <?php if ($this->options['link_view_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Referer link on email footer.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show View link on email footer.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="link_remove_bottom"><?php _e('Remove', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_remove_bottom" name="link_remove_bottom" value="0" /><input id="link_remove_bottom" name="link_remove_bottom" value="1" <?php if ($this->options['link_remove_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_edit_bottom"><?php _e('Edit', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_edit_bottom" name="link_edit_bottom" value="0" /><input id="link_edit_bottom" name="link_edit_bottom" value="1" <?php if ($this->options['link_edit_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Remove link on email footer.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show Edit link on email footer.', $this->_slug)?></div></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><label for="link_print_bottom"><?php _e('Print', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_print_bottom" name="link_print_bottom" value="0" /><input id="link_print_bottom" name="link_print_bottom" value="1" <?php if ($this->options['link_print_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                      <th scope="row"><label for="link_social_networks_bottom"><?php _e('Social Networks', $this->_slug)?>:</label></th>
                      <td><input type="hidden" id="link_social_networks_bottom" name="link_social_networks_bottom" value="0" /><input id="link_social_networks_bottom" name="link_social_networks_bottom" value="1" <?php if ($this->options['link_social_networks_bottom'] == 1) echo 'checked="checked"'; ?> type="checkbox"></td>
                  </tr>
                  <tr valign="top">
                  	<td colspan="2"><div class="s9_form_field_description"><?php _e('Show Print link on email footer.', $this->_slug)?></div></td>
                    <td colspan="2"><div class="s9_form_field_description"><?php _e('Show Social Networks link on email footer.', $this->_slug)?></div></td>
                  </tr> 
             </table>
            <?php
		}
		
		function tab_options_advanced() {
			?>
            <table class="optiontable form-table s9_table">
            	<tr valign="top">
                    <td width="120"><input type="submit" name="erase_campaigns" value="<?php esc_attr_e('Erase Campaigns', $this->_slug); ?>" /></td>
                    <th scope="row"><?php _e('Delete all saved campaigns from the database.', $this->_slug)?></th>
                 </tr>
                 <tr valign="top">
                    <td width="120"><input type="submit" name="erase_options" value="<?php esc_attr_e('Erase Options', $this->_slug); ?>" /></td>
                    <th scope="row"><?php _e('Delete all saved options. This will reset all options to their default values.', $this->_slug)?></th>
                 </tr>
            </table>
            <?
		}
		
		function delete_campaigns() {
			global $wpdb;
			$posts = $wpdb->get_results ("SELECT ID FROM `$wpdb->posts` WHERE `post_type` = 's9egoiemail'", ARRAY_A);
			foreach ($posts as $p) {
				wp_delete_post($p['ID'], true);	
			}
		}
		
		function auto_subscription($user_id) {
			$user_info  = get_userdata($user_id);
			$list_id    = (string)$this->options['auto_subscription_list'];
			$subscriber = array();
			$subscriber['email']      = (string)$user_info->user_email;
			$subscriber['first_name'] = (string)$user_info->display_name;
			
			$this->egoi->add_subscriber($list_id, $subscriber);
		}
		
		function auto_campaign() {
			if ($this->options['auto_campaign'] == 1) { 
				global $wpdb;
				$min_posts = $this->options['auto_campaign_min_posts'];
				$subject   = $this->options['auto_campaign_subject'];
				$blog_name = get_bloginfo();
				$result    = $wpdb->get_results ("SELECT post_date FROM `$wpdb->posts` LEFT JOIN `$wpdb->postmeta` ON ID = post_id WHERE `post_status` = 'publish' AND `post_type` = 's9egoiemail' AND `meta_key` = '_auto_post' AND `meta_value` = '1' ORDER BY `post_date` DESC LIMIT 1", ARRAY_A);
				$last_date = (count($result) > 0) ? $result[0]['post_date'] : "0000-00-00 00:00:00";
				
				$posts = $wpdb->get_results ("SELECT * FROM `$wpdb->posts` WHERE `post_status` = 'publish' AND `post_type` = 'post' AND `post_date` > '".$last_date."' ORDER BY post_date ASC", ARRAY_A);
				if (count($posts) >= $min_posts) {
					$content = '';
					foreach ($posts as $k=>$p) {
						if ($k == 0) $post_title = get_the_title($p['ID']);
						$excerpt = trim ($p['post_excerpt']);
						if ($excerpt == '') {
							$e  = explode (" ", strip_tags( strip_shortcodes( $p['post_content'] ) ) );
							for($i=0;$i<50;$i++) { $excerpt .= $e[$i]." "; }
						}
						
						$content .= '<div class="entry">'."\n";
						$content .= '<h2><a href="'.get_permalink ($p['ID']).'" title="'.get_the_title($p['ID']).'">'.get_the_title($p['ID']).'</a></h2>'."\n";
						$content .= '<p>'.$excerpt.'</p>'."\n";
						$content .= '<p>&nbsp;</p>'."\n";
						$content .= '</div>'."\n";
					}
					
					$POST = array();
					$POST['post_title'] = str_replace (array('!blog_name','!post_title','!current_date'), array ($blog_name,$post_title, date("d-m-Y")), $subject);
					$POST['post_content'] = $content;
					$POST['post_type'] = 's9egoiemail';
					$POST['post_status'] = 'publish';
					$ID = wp_insert_post($POST);
					update_post_meta($ID, '_auto_post', '1');
				}
			}
		}
		
		function widget_action() {
			if (! wp_verify_nonce($_POST['security'], 's9egoi-widget') ) die('Security check');
			unset ($_POST['action']); unset ($_POST['security']); unset ($_POST['submit']);
			
			if (isset($_POST['s9egoi-form_list_id'])) {
				$list_id = $_POST['s9egoi-form_list_id'];
				unset ($_POST['s9egoi-form_list_id']);	
			}
			$subscriber = array();
			foreach ($_POST as $k=>$v) {
				if (!empty($v)) {
					$key   = str_replace ("s9egoi-form_", "", $k);
					$subscriber[$key] = trim((string)$v);
				}
			}
			$this->egoi->add_subscriber($list_id, $subscriber);
			if ($this->egoi->error) {
				echo $this->egoi->error;	
				exit;
			} else {
				echo "SUCCESS";	
				exit;
			}
		}
		
		function message($index = '', $return = FALSE) {
			$messages    = array();
			$messages[0] = 'Default Message';
			if (isset($_POST['message'])) {
				if (isset($messages[$_POST['message']])) {
					_e($messages[$_POST['message']], $this->_slug);	
				} else {
					_e($_POST['message'], $this->_slug);	
				}
			} else if (isset($messages[$index])) {
				if ($return == TRUE) {
					return __($messages[$index], $this->_slug);
				}
				else {
					_e($messages[$index], $this->_slug);		
				}
			} else {
				if ($return == TRUE) {
					return __($index, $this->_slug);
				} else {
					_e($index, $this->_slug);
				}
			}
			exit;
		}
	}
}
$S9Egoi = new S9Egoi($options);
