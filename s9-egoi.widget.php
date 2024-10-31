<?php

class S9EgoiWidget extends WP_Widget {
    
	protected $_slug = 's9-egoi';
	
	function S9EgoiWidget(){
		global $S9Egoi;
		$widget_ops = array();
		parent::WP_Widget(false,$name='Subscribe Form',$widget_ops);
		wp_enqueue_script('jquery');
		wp_register_script($this->_slug.'widget',    $S9Egoi->base_url['plugin'].'/includes/widget.js');
		wp_enqueue_script($this->_slug.'widget');
		load_plugin_textdomain( $this->_slug, false, basename(dirname(__FILE__)) . '/languages');
    }

    function widget($args, $instance){
		extract($args);
		global $S9Egoi;
		$title       = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$description = empty($instance['description']) ? '' : $instance['description'];
		$ip          = $_SERVER['REMOTE_ADDR'];
		$geoplugin   = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
		echo $before_widget;
		if ( !empty($title) ) echo $before_title . $title . $after_title;
		?>
        <p id="s9egoi-widget-message"><?php echo $description;?></p>
        <form name="s9egoi-widget-form" id="s9egoi-widget-form" method="post">
        	<?php if ($S9Egoi->options['form_email'] === 1) { ?>
            	<div class="s9egoi-form-row form_email">
                	<label for="s9egoi-form_email"><?php echo $S9Egoi->options['form_email_label']?> <?php echo ($S9Egoi->options['form_email_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_email" id="s9egoi-form_email" class="<?php echo ($S9Egoi->options['form_email_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_first_name'] === 1) { ?>
            	<div class="s9egoi-form-row form_first_name">
                	<label for="s9egoi-first_name"><?php echo $S9Egoi->options['form_first_name_label']?> <?php echo ($S9Egoi->options['form_first_name_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_first_name" id="s9egoi-form_first_name" class="<?php echo ($S9Egoi->options['form_first_name_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_last_name'] === 1) { ?>
            	<div class="s9egoi-form-row form_last_name">
                	<label for="s9egoi-form_last_name"><?php echo $S9Egoi->options['form_last_name_label']?> <?php echo ($S9Egoi->options['form_last_name_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_last_name" id="s9egoi-form_last_name" class="<?php echo ($S9Egoi->options['form_last_name_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_cellphone'] === 1) { ?>
            	<div class="s9egoi-form-row form_cellphone">
                	<label for="s9egoi-form_cellphone"><?php echo $S9Egoi->options['form_cellphone_label']?> <?php echo ($S9Egoi->options['form_cellphone_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_cellphone" id="s9egoi-form_cellphone" class="<?php echo ($S9Egoi->options['form_cellphone_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_telephone'] === 1) { ?>
            	<div class="s9egoi-form-row form_telephone">
                	<label for="s9egoi-form_telephone"><?php echo $S9Egoi->options['form_telephone_label']?> <?php echo ($S9Egoi->options['form_telephone_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_telephone" id="s9egoi-form_telephone" class="<?php echo ($S9Egoi->options['form_telephone_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_fax'] === 1) { ?>
            	<div class="s9egoi-form-row form_fax">
                	<label for="s9egoi-form_fax"><?php echo $S9Egoi->options['form_fax_label']?> <?php echo ($S9Egoi->options['form_fax_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_fax" id="s9egoi-form_fax" class="<?php echo ($S9Egoi->options['form_fax_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_address'] === 1) { ?>
            	<div class="s9egoi-form-row form_address">
                	<label for="s9egoi-form_address"><?php echo $S9Egoi->options['form_address_label']?> <?php echo ($S9Egoi->options['form_address_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_address" id="s9egoi-form_address" class="<?php echo ($S9Egoi->options['form_address_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_zip_code'] === 1) { ?>
            	<div class="s9egoi-form-row form_zip_code">
                	<label for="s9egoi-form_zip_code"><?php echo $S9Egoi->options['form_zip_code_label']?> <?php echo ($S9Egoi->options['form_zip_code_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_zip_code" id="s9egoi-form_zip_code" class="<?php echo ($S9Egoi->options['form_zip_code_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_city'] === 1) { ?>
            	<div class="s9egoi-form-row form_city">
                	<label for="s9egoi-form_city"><?php echo $S9Egoi->options['form_city_label']?> <?php echo ($S9Egoi->options['form_city_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_city" id="s9egoi-form_city" class="<?php echo ($S9Egoi->options['form_city_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_state'] === 1) { ?>
            	<div class="s9egoi-form-row form_state">
                	<label for="s9egoi-form_state"><?php echo $S9Egoi->options['form_state_label']?> <?php echo ($S9Egoi->options['form_state_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_state" id="s9egoi-form_state" class="<?php echo ($S9Egoi->options['form_state_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_district'] === 1) { ?>
            	<div class="s9egoi-form-row form_district">
                	<label for="s9egoi-form_district"><?php echo $S9Egoi->options['form_district_label']?> <?php echo ($S9Egoi->options['form_district_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_district" id="s9egoi-form_district" class="<?php echo ($S9Egoi->options['form_district_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_age'] === 1) { ?>
            	<div class="s9egoi-form-row form_age">
                	<label for="s9egoi-form_age"><?php echo $S9Egoi->options['form_age_label']?> <?php echo ($S9Egoi->options['form_age_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_age" id="s9egoi-form_age" class="<?php echo ($S9Egoi->options['form_age_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_birth_date'] === 1) { ?>
            	<div class="s9egoi-form-row form_birth_date">
                	<label for="s9egoi-form_birth_date"><?php echo $S9Egoi->options['form_birth_date_label']?> <?php echo ($S9Egoi->options['form_birth_date_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_birth_date" id="s9egoi-form_birth_date" class="<?php echo ($S9Egoi->options['form_birth_date_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_gender'] === 1) { ?>
            	<div class="s9egoi-form-row form_gender">
                	<label for="s9egoi-form_gender"><?php echo $S9Egoi->options['form_gender_label']?> <?php echo ($S9Egoi->options['form_gender_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <select name="s9egoi-form_gender" id="s9egoi-form_gender" class="<?php echo ($S9Egoi->options['form_gender_required'] == 1) ? "required" : ""?>">
                    	<option value=""><?php _e('Select', $this->_slug); ?></option>
                    	<option value="F"><?php _e('Female', $this->_slug); ?></option>
                    	<option value="M"><?php _e('Male', $this->_slug); ?></option>
                    </select>
                </div>
            <? } ?>
            <?php if ($S9Egoi->options['form_company'] === 1) { ?>
            	<div class="s9egoi-form-row form_company">
                	<label for="s9egoi-form_company"><?php echo $S9Egoi->options['form_company_label']?> <?php echo ($S9Egoi->options['form_company_required'] == 1) ? "<span class=\"required\">*</span>" : ""?></label>
                    <input type="text" name="s9egoi-form_company" id="s9egoi-form_company" class="<?php echo ($S9Egoi->options['form_company_required'] == 1) ? "required" : ""?>" value="" />
                </div>
            <? } ?>
            <div class="s9egoi-form-row form_submit">
            	<input type="hidden" name="s9egoi-form_list_id" id="s9egoi-form_list_id" value="<?php echo $S9Egoi->options['form_list_id']?>" />
        		<input type="hidden" name="s9egoi-form_country" id="s9egoi-form_country" value="<?php echo $geoplugin['geoplugin_countryCode']?>" />
                <input type="hidden" name="action" id="action" value="s9egoi_widget_action" />
            	<input type="hidden" name="security" value="<?php echo wp_create_nonce('s9egoi-widget'); ?>" />
        		<input type="submit" name="submit" id="submit" value="<?php _e('Subscribe', $this->_slug);?>" />
            </div>
        </form>
        <div class="widget-footer">
        	<p><span class="required">*</span> <?php _e('Required Fields', $this->_slug); ?></p>
        </div>
        <?
		echo $after_widget;
	}

    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title']       = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

    function form($instance){
		$instance    = wp_parse_args( (array) $instance, array('title' => '', 'description' => '') );
		$title       = strip_tags($instance['title']);
		$description = strip_tags($instance['description']);
		?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', $this->_slug);?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Widget Description', $this->_slug);?>: <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo attribute_escape($description); ?></textarea></label></p>
        <?php
	}

}
function S9EgoiWidgetInit() { 
	global $S9Egoi;
	add_action('wp_ajax_s9egoi_widget_action', array($S9Egoi, 'widget_action') );
	add_action('wp_ajax_nopriv_s9egoi_widget_action', array($S9Egoi, 'widget_action'));
	register_widget('S9EgoiWidget'); 
}
add_action('widgets_init', 'S9EgoiWidgetInit');