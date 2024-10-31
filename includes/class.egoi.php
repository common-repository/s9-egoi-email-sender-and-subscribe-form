<?
/*
Class Name: Egoi API
Description: PHP Class developed to work with Egoi's API via url.
Version: 1.0.1
Author: Sector 9
Author URI: http://www.sector9.pt
*/
require_once('class.xmlrpc.php');
class Egoi {
	
	var $api_key;
	var $plugin_key;
	var $xmlrpc_url;
	var $result;
	var $error;
	
	function Egoi($api_key = '', $plugin_key = '', $soap_url = '') {
		$this->plugin_key = $plugin_key;
		$this->api_key = $api_key;
		
		if (!empty($soap_url)) { $this->soap_url = $soap_url; } else { $this->xmlrpc_url = '/v2/xmlrpc.php'; }
	}
	
	function _request($method = '', $params = array()) {
		$response = FALSE;
		
		if (!empty($method) && !empty($this->api_key) && !empty($this->xmlrpc_url)) {
			$params['apikey'] = trim($this->api_key);
			
			if (!empty($this->plugin_key)) { $params['plugin_key'] = $this->plugin_key; }
			$params  = php_xmlrpc_encode((object) $params);
			$xmlrpc  = new xmlrpc_client($this->xmlrpc_url, 'api.e-goi.com', 80);
			$message = new xmlrpcmsg($method, array($params));
			$response = $xmlrpc->send($message);
			if($response->faultCode() == 0){
				$response = php_xmlrpc_decode($response->value());
			} else {
				$this->error = $response->faultString();
				$this->result = FALSE;	
			}
		}
		$this->_response($response);
	}
	
	function _response($response) {
		if (isset($response['ERROR'])) {
			$this->error  = $response['ERROR'];
			$this->result = FALSE;	
		} else {
			$this->error = FALSE;
			if (is_array($response))
				$this->_transform_keys($response);
			$this->result = $response;
		}
	}
	
	function _transform_keys(&$array) {
		foreach (array_keys($array) as $key) {
    		$value = &$array[$key];
    		unset($array[$key]);
   			$transformedKey = strtolower($key);
   			if (is_array($value)) $this->_transform_keys($value);
    		$array[$transformedKey] = utf8_encode($value);      
    		unset($value);
		}
	}
	
	function get_client_data() {
		$this->_request('getClientData');
	}
	
	function get_subscriber($list_id, $subscriber = '', $segment = '') {
		$data = array('listID' => 0, 'subscriber' => 'all_subscribers');
		$data['listID']     = $list_id;
		if (!empty($segment)) {
			$data['segment'] = $segment;	
		}
		if (!empty($subscriber)) {
			$data['subscriber'] = $subscriber;
		} else {
			$data['subscriber'] = 'all_subscribers';
		}
		$this->_request('subscriberData', $data);
	}
	
	function add_subscriber($list_id, $subscriber) {
		$data = array('listID' => 0, 'subscriber' => 0, 'from' => '', 'status' => '1', 'lang' => 'pt', 'email' => '', 'validate_email' => 0, 'cellphone' => '', 'telephone' => '', 'fax' => '', 'first_name' => '', 'last_name' => '', 'tax_id' => '', 'address' => '', 'zip_code' => '', 'city' => '', 'district' => '', 'state' => '', 'country' => '', 'age' => '', 'gender' => '', 'id_card' => '', 'company' => '', 'birth_date' => '');
		$data['listID'] = $list_id;
		
		if (isset($subscriber['subscriber'])) {
			if (!empty($subscriber['subscriber'])) {
				$data['subscriber'] = $subscriber['subscriber'];	
			}
		}
		
		if (isset($subscriber['status'])) {
			if ($subscriber['status'] >= 0 && $subscriber['status'] <= 4 ) {
				$data['status'] = $subscriber['status'];	
			}	
		}
		
		if (isset($subscriber['from'])) {
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $subscriber['from'])) {
				$data['from'] = $subscriber['from'];
			}
		}
		
		if (isset($subscriber['lang'])) {
			if (in_array($subscriber['lang'], array('pt','br','en','es'))) {
				$data['lang'] = $subscriber['lang'];	
			}
		}
		
		if (isset($subscriber['email'])) {
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $subscriber['email'])) {
				$data['email'] = $subscriber['email'];
			}
		}
		
		if (isset($subscriber['validate_email'])) {
			if ($subscriber['validate_email'] >= 0 && $subscriber['validate_email'] <=	2) {
				$data['validate_email'] = $subscriber['validate_email'];	
			}
		}
		
		if (isset($subscriber['cellphone'])) {
			$data['cellphone'] = $subscriber['cellphone'];	
		}
		
		if (isset($subscriber['telephone'])) {
			$data['telephone'] = $subscriber['telephone'];	
		}
		
		if (isset($subscriber['fax'])) {
			$data['fax'] = $subscriber['fax'];	
		}
		
		if (isset($subscriber['first_name'])) {
			$data['first_name'] = $subscriber['first_name'];	
		}
		
		if (isset($subscriber['last_name'])) {
			$data['last_name'] = $subscriber['last_name'];	
		}
		
		if (isset($subscriber['tax_id'])) {
			$data['tax_id'] = $subscriber['tax_id'];	
		}
		
		if (isset($subscriber['address'])) {
			$data['address'] = $subscriber['address'];	
		}
		
		if (isset($subscriber['zip_code'])) {
			$data['zip_code'] = $subscriber['zip_code'];	
		}
		
		if (isset($subscriber['city'])) {
			$data['city'] = $subscriber['city'];	
		}
		
		if (isset($subscriber['district'])) {
			$data['district'] = $subscriber['district'];	
		}
		
		if (isset($subscriber['state'])) {
			$data['state'] = $subscriber['state'];	
		}
		
		if (isset($subscriber['country'])) {
			$data['country'] = $subscriber['country'];	
		}
		
		if (isset($subscriber['age'])) {
			$data['age'] = $subscriber['age'];	
		}
		
		if (isset($subscriber['gender'])) {
			$data['gender'] = $subscriber['gender'];	
		}
		
		if (isset($subscriber['id_card'])) {
			$data['id_card'] = $subscriber['id_card'];	
		}
		
		if (isset($subscriber['company'])) {
			$data['company'] = $subscriber['company'];	
		}
		
		if (isset($subscriber['birth_date'])) {
			$data['birth_date'] = $subscriber['birth_date'];	
		}
		
		if (isset($subscriber['extra'])) {
			if (is_array($subscriber['extra'])) {
				foreach ($subscriber['extra'] as $k=>$v) {
					$data['extra_'.$k] = $v;	
				}
			}
		}
		$this->_request('addSubscriber', $data);
	}
	
	function edit_subscriber($list_id, $subscriber_id, $subscriber) {
		$data['listID'] = $list_id;
		$data['subscriber'] = $subscriber_id;
	
		if (isset($subscriber['status'])) {
			if ($subscriber['status'] >= 0 && $subscriber['status'] <= 4 ) {
				$data['status'] = $subscriber['status'];	
			}	
		}
		
		if (isset($subscriber['from'])) {
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $subscriber['from'])) {
				$data['from'] = $subscriber['from'];
			}
		}
		
		if (isset($subscriber['lang'])) {
			if (in_array($subscriber['lang'], array('pt','br','en','es'))) {
				$data['lang'] = $subscriber['lang'];	
			}
		}
		
		if (isset($subscriber['email'])) {
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $subscriber['email'])) {
				$data['email'] = $subscriber['email'];
			}
		}
		
		if (isset($subscriber['validate_email'])) {
			if ($subscriber['validate_email'] >= 0 && $subscriber['validate_email'] <=	2) {
				$data['validate_email'] = $subscriber['validate_email'];	
			}
		}
		
		if (isset($subscriber['cellphone'])) {
			$data['cellphone'] = $subscriber['cellphone'];	
		}
		
		if (isset($subscriber['telephone'])) {
			$data['telephone'] = $subscriber['telephone'];	
		}
		
		if (isset($subscriber['fax'])) {
			$data['fax'] = $subscriber['fax'];	
		}
		
		if (isset($subscriber['first_name'])) {
			$data['first_name'] = $subscriber['first_name'];	
		}
		
		if (isset($subscriber['last_name'])) {
			$data['last_name'] = $subscriber['last_name'];	
		}
		
		if (isset($subscriber['tax_id'])) {
			$data['tax_id'] = $subscriber['tax_id'];	
		}
		
		if (isset($subscriber['address'])) {
			$data['address'] = $subscriber['address'];	
		}
		
		if (isset($subscriber['zip_code'])) {
			$data['zip_code'] = $subscriber['zip_code'];	
		}
		
		if (isset($subscriber['city'])) {
			$data['city'] = $subscriber['city'];	
		}
		
		if (isset($subscriber['district'])) {
			$data['district'] = $subscriber['district'];	
		}
		
		if (isset($subscriber['state'])) {
			$data['state'] = $subscriber['state'];	
		}
		
		if (isset($subscriber['country'])) {
			$data['country'] = $subscriber['country'];	
		}
		
		if (isset($subscriber['age'])) {
			$data['age'] = $subscriber['age'];	
		}
		
		if (isset($subscriber['gender'])) {
			$data['gender'] = $subscriber['gender'];	
		}
		
		if (isset($subscriber['id_card'])) {
			$data['id_card'] = $subscriber['id_card'];	
		}
		
		if (isset($subscriber['company'])) {
			$data['company'] = $subscriber['company'];	
		}
		
		if (isset($subscriber['birth_date'])) {
			$data['birth_date'] = $subscriber['birth_date'];	
		}
		
		if (isset($subscriber['extra'])) {
			if (is_array($subscriber['extra'])) {
				foreach ($subscriber['extra'] as $k=>$v) {
					$data['extra_'.$k] = $v;	
				}
			}
		}
		if (count($data) > 2) 
			$this->_request('editSubscriber', $data);
		else {
			$this->error = 'NOTHING_TO_DO';
			$this->result = FALSE;
		}
	}
	
	function remove_subscriber($list_id, $subscriber_id, $remove_data = array()) {
		$data = array('listID' => 0, 'subscriber' => '', 'removeMethod' => 'unknown', 'removeReason' => '5', 'removeObs' => '');
		$data['listID'] = $list_id;
		$data['subscriber'] = $subscriber_id;
		
		if (isset($remove_data['removeMethod'])) {
			$data['removeMethod'] = $remove_data['removeMethod'];	
		}
		if (isset($remove_data['removeReason'])) {
			if ($remove_data['removeReaons'] >= 1 && $remove_data['removeReason'] <= 5) {
				$data['removeReason'] =	$remove_data['removeReason'];
			}
			if (isset($remove_data['removeObs'])) {
				if ($data['removeReason'] == '5') {
					$data['removeObs'] = $remove_data['removeObs'];	
				}
			}
		}
		$this->_request('removeSubscriber', $data);
	}
	
	function get_segments() {
		// TO DO	
	}
	
	function add_segment() {
		// TO DO
	}

	function remove_segment() {
		// TO DO
	}
	
	function add_email ($list_id, $url='', $subject = '', $from = array(), $reply = array(), $link = array()) {
		$data = array('listID' => 0, 'url' => '', 'from_name' => '', 'from_email' => '', 'reply_name' => '', 'reply_email' => '', 'link_referer_top' => 0, 'link_referer_bottom' => 0, 'link_view_top' => 0, 'link_view_bottom' => 0, 'link_remove_top' => 0, 'link_remove_bottom' => 0, 'link_edit_top' => 0, 'link_edit_bottom' => 0, 'link_print_top' => 0, 'link_print_bottom' => 0, 'link_social_networks_top' => 0, 'link_social_networks_bottom' => 0);
		$data['listID'] = $list_id;
		if (!empty($url) && !empty($subject) && isset($from['from_name']) && isset($from['from_email'])) {
			$data['url']        = $url;
			$data['subject']    = $subject;
			$data['from_name']  = $from['from_name'];
			$data['from_email'] = $from['from_email'];
			if (isset($reply['reply_name'])) {
				$data['reply_name'] = $reply['reply_name'];
			}
			if (isset($reply['reply_email'])) {
				$data['reply_email'] = $reply['reply_email'];
			}
			if (isset($link['link_referer_top'])) {
				$data['link_referer_top'] = $link['link_referer_top'];	
			}
			if (isset($link['link_referer_bottom'])) {
				$data['link_referer_bottom'] = $link['link_referer_bottom'];	
			}
			if (isset($link['link_view_top'])) {
				$data['link_view_top'] = $link['link_view_top'];	
			}
			if (isset($link['link_view_bottom'])) {
				$data['link_view_bottom'] = $link['link_view_bottom'];	
			}
			if (isset($link['link_remove_top'])) {
				$data['link_remove_top'] = $link['link_remove_top'];	
			}
			if (isset($link['link_remove_bottom'])) {
				$data['link_remove_bottom'] = $link['link_remove_bottom'];	
			}
			if (isset($link['link_edit_top'])) {
				$data['link_edit_top'] = $link['link_edit_top'];	
			}
			if (isset($link['link_edit_bottom'])) {
				$data['link_edit_bottom'] = $link['link_edit_bottom'];	
			
			}if (isset($link['link_print_top'])) {
				$data['link_print_top'] = $link['link_print_top'];	
			}
			if (isset($link['link_print_bottom'])) {
				$data['link_print_bottom'] = $link['link_print_bottom'];	
			}
			if (isset($link['link_social_networks_top'])) {
				$data['link_social_networks_top'] = $link['link_social_networks_top'];	
			}
			if (isset($link['link_social_networks_bottom'])) {
				$data['link_social_networks_bottom'] = $link['link_social_networks_bottom'];	
			}
			
			$this->_request('createCampaignEmail', $data);
			
		} else {
			$this->error = 'NOTHING_TO_DO';
			$this->result = FALSE;
		}
	}
	
	function edit_email($campaign_id, $list_id = '', $url ='', $subject = '', $from = array(), $reply = array(), $link = array()) {
		$data = array('listID' => 0, 'url' => '', 'from_name' => '', 'from_email' => '', 'reply_name' => '', 'reply_email' => '', 'link_referer_top' => 0, 'link_referer_bottom' => 0, 'link_view_top' => 0, 'link_view_bottom' => 0, 'link_remove_top' => 0, 'link_remove_bottom' => 0, 'link_edit_top' => 0, 'link_edit_bottom' => 0, 'link_print_top' => 0, 'link_print_bottom' => 0, 'link_social_networks_top' => 0, 'link_social_networks_bottom' => 0);
		$data['campaign'] = $campaign_id;
		$data['listID']   = $list_id;
		if (!empty($url) && !empty($subject) && isset($from['from_name']) && isset($from['from_email'])) {
			$data['url']        = $url;
			$data['subject']    = $subject;
			$data['from_name']  = $from['from_name'];
			$data['from_email'] = $from['from_email'];
			
			if (isset($reply['reply_name'])) {
				$data['reply_name'] = $reply['reply_name'];
			}
			if (isset($reply['reply_email'])) {
				$data['reply_email'] = $reply['reply_email'];
			}
			if (isset($link['link_referer_top'])) {
				$data['link_referer_top'] = $link['link_referer_top'];	
			}
			if (isset($link['link_referer_bottom'])) {
				$data['link_referer_bottom'] = $link['link_referer_bottom'];	
			}
			if (isset($link['link_view_top'])) {
				$data['link_view_top'] = $link['link_view_top'];	
			}
			if (isset($link['link_view_bottom'])) {
				$data['link_view_bottom'] = $link['link_view_bottom'];	
			}
			if (isset($link['link_remove_top'])) {
				$data['link_remove_top'] = $link['link_remove_top'];	
			}
			if (isset($link['link_remove_bottom'])) {
				$data['link_remove_bottom'] = $link['link_remove_bottom'];	
			}
			if (isset($link['link_edit_top'])) {
				$data['link_edit_top'] = $link['link_edit_top'];	
			}
			if (isset($link['link_edit_bottom'])) {
				$data['link_edit_bottom'] = $link['link_edit_bottom'];	
			
			}if (isset($link['link_print_top'])) {
				$data['link_print_top'] = $link['link_print_top'];	
			}
			if (isset($link['link_print_bottom'])) {
				$data['link_print_bottom'] = $link['link_print_bottom'];	
			}
			if (isset($link['link_social_networks_top'])) {
				$data['link_social_networks_top'] = $link['link_social_networks_top'];	
			}
			if (isset($link['link_social_networks_bottom'])) {
				$data['link_social_networks_bottom'] = $link['link_social_networks_bottom'];	
			}
			
			$this->_request('editCampaignEmail', $data);
			
		} else {
			$this->error = 'NOTHING_TO_DO';
			$this->result = FALSE;
		}
	}
	
	function send_email($campaign, $list_id, $subscriber = '', $segment = '') {
		$data = array ('listID' => 0, 'uid' => '', 'email' => '', 'segment' => 'ALL', 'campaign' => '');
		$data['campaign'] = $campaign;
		$data['listID'] = $list_id;
		if (!empty($segment)) {
			$data['segment'] = $segment;	
		}
		if (!empty($subscriber)) {
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $subscriber)) {
				$data['email'] = $subscriber;
			} else {
				$data['uid'] = $subscriber;
			}
			unset ($data['segment']);
		}
		$this->_request('sendEmail', $data);
	}
	
	function add_sms() {
		// TO DO
	}
	
	function send_sms() {
		// TO DO
	}
	
	function add_voice() {
		// TO DO
	}
	
	function send_voice() {
		// TO DO
	}
	
	function add_fax() {
		// TO DO
	}
	
	function send_fax() {
		// TO DO
	}
	
	function remove_campaign($campaign) {
		$data = array();
		if (!empty($campaign)) {
			$data['campaign'] = $campaign;
			$this->_request('deleteCampaign', $data);
		} else {
			$this->error = "NOTHING_TO_DO";
			$this->result = FALSE;
		}
	}
}
?>