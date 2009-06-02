<?php

require_once 'plc_login.php'; // Require login
require_once 'plc_session.php'; // Get session and API handles
global $plc, $api, $adm;

// Print header
require_once 'plc_drupal.php';

include 'plc_header.php';

// Common functions
require_once 'plc_functions.php';
include 'plc_objects.php';


class Confirm extends Controller {
	
	var $pcu_id = 0;
	function stage7_firstcontact($pcu_id, $site_id, $node_id)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['node_id'] = intval($node_id);
		$data['site_id'] = intval($site_id);
		$data['stage'] = 7;
		$data = $this->get_stage7_data($person, $data);
		/*print "RESULT: ".$result . "<br>";*/
		/*print $this->validation->error_string . "<br>";*/
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage7_firstcontact', $data);
		$this->load->view('footer', $data);
	}

	function getnode($node_id)
	{
		global $api, $plc;
		$plc_node_list = $api->GetNodes(array('node_id' => intval($node_id) ));
		return $plc_node_list[0];
	}
	function getsite($site_id)
	{
		global $api, $plc;
		$site_info = $api->GetSites($site_id, array( "name", "site_id", "login_base" ) );
		return $site_info[0];
	}

	function get_stage7_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['node']  = $this->getnode($data['node_id']);
		$data['site']  = $this->getsite($data['node']['site_id']);
		if( $data['node']['last_contact'] != NULL && $data['node']['last_contact'] != '' ) {
			$last_contact = $data['node']['last_contact'];
		} else {
			$last_contact = NULL;
		}
		if( $last_contact != NULL ) {
			$last_contact_str = timeDiff($last_contact);
		} else {
			$last_contact_str = "Never";
		}
		$data['last_contact_str'] = $last_contact_str;
		return $data;
	}

	function stage8_rebootpcu($pcu_id, $site_id, $node_id)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['node_id'] = intval($node_id);
		$data['site_id'] = intval($site_id);
		$data['stage'] = 8;
		$data = $this->get_stage8_data($person, $data);
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage8_rebootpcu', $data);
		$this->load->view('footer', $data);
	}

	function reboot($site_id, $pcu_id, $node_id)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');
		$this->load->library('validation');
		$person = new Person($plc->person);

		$data = array();
		$data['site_id'] = intval($site_id);
		$data['node_id'] = intval($node_id);
		$data['pcu_id'] = intval($pcu_id);
		$data['stage'] = 8;
		$this->reboot_node($data);
		$data = $this->get_stage8_data($person, $data);

		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage8_rebootpcu', $data);
		$this->load->view('footer', $data);
	}

	function complete($site_id, $pcu_id, $node_id)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');
		$this->load->library('validation');

		$data = array();
		$data['site_id'] = $site_id;
		$data['stage'] = 9;

		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage9_complete', $data);
		$this->load->view('footer', $data);
	}

	function get_stage8_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['node']  = $this->getnode($data['node_id']);
		$data['site']  = $this->getsite($data['node']['site_id']);
		if( $data['node']['last_contact'] != NULL && $data['node']['last_contact'] != '' ) {
			$last_contact = $data['node']['last_contact'];
		} else {
			$last_contact = NULL;
		}
		if( $last_contact != NULL ) {
			$last_contact_str = timeDiff($last_contact);
		} else {
			$last_contact_str = "Never";
		}
		$data['last_contact_str'] = $last_contact_str;
		return $data;
	}

	function reboot_node(&$data)
	{
		global $api, $plc;
		$hostname = $data['node_id'];
		$api->UpdateNode( $hostname, array( "boot_state" => 'reinstall') );
		$ret = $api->RebootNodeWithPCU( $hostname );
		if ( "$ret" != "0" ) {
			$data['error'] = $api->error();
		} else {
			$data['error'] = $ret;
		}
	}
}
?>
