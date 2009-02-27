<?php

require_once 'plc_login.php'; // Require login
require_once 'plc_session.php'; // Get session and API handles
global $plc, $api, $adm;

// Print header
require_once 'plc_drupal.php';

include 'plc_header.php';

// Common functions
require_once 'plc_functions.php';
require_once 'plc_sorts.php';
include 'plc_objects.php';


class Conffile extends Controller {
	
	function index()
	{
		global $api, $plc;
		$all_conf_files= $api->GetConfFiles();
		print $api->error();
		$data = array();
		$data['all_conf_files'] = $all_conf_files;
		$data['api'] = $api;
		$data['stage'] = 1;

		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('ListConfFile', $data);
		$this->load->view('footer', $data);

	}
	function get_stage_data(&$data)
	{
		global $api, $plc;
		$data['nodegroups'] = $api->GetNodeGroups();
		$data['nodes'] = $api->GetNodes(NULL, array('hostname', 'node_id'));
		return $data;
	}
	function convert_checked($val)
	{
		if ( $val == "on" || $val == True )
		{
			print "CALLED CHECKED '$val' .<BR>";
			return True;
		} else {
			return False;
		}
	}
	function update($conf_file_id=-1)
	{
		global $api, $plc;
		$conf_file_id = intval($conf_file_id);

		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$data = array();

		$data['submit_caption'] = "Update";
		$data['title'] = "Update existing node configuration file";
		$data['conf_file_id'] = $conf_file_id;

		$fields['enabled']  = "Enabled";
		$fields['source'] 		= "Source";
		$fields['dest']  = "Destination";
		$fields['file_permissions']  = "File Permissions";
		$fields['file_owner']  = "File Owner";
		$fields['file_group']  = "File Group";
		$fields['preinstall_cmd']  = "Preinstall Command";
		$fields['postinstall_cmd']  = "Postinstall Command";
		$fields['error_cmd']  = "Error Command";
		$fields['ignore_cmd_errors']  = "Ignore Command Errors";
		$fields['always_update']  = "Always Update";
		$fields['scope']  = "ConfFile Scope";
		$fields['node_ids']  = "";
		$fields['nodegroup_ids']  = "";
		$this->validation->set_fields($fields);

		if( isset($_REQUEST['submitted']) )
		{
			print "SUBMITTED REQUEST";
			$rules['enabled']  = "trim";
			$rules['source'] 		= "trim|required";
			$rules['dest']  = "trim|required";
			$rules['file_permissions']  = "trim";
			$rules['file_owner']  = "trim";
			$rules['file_group']  = "trim";
			$rules['preinstall_cmd']  = "trim";
			$rules['postinstall_cmd']  = "trim";
			$rules['error_cmd']  = "trim";
			$rules['ignore_cmd_errors']  = "trim";
			$rules['always_update']  = "trim";
			$rules['scope']  = "trim";
			$rules['node_ids']  = "trim";
			$rules['nodegroup_ids']  = "trim";
			$this->validation->set_rules($rules);

			$error_occurred= 0;
			if ($this->validation->run() == TRUE)
			{
				$data['enabled']			=	$this->validation->enabled;
				$data['source']				=	$this->validation->source;
				$data['dest']				=	$this->validation->dest;
				$data['file_permissions']	=	$this->validation->file_permissions;
				$data['file_owner']			=	$this->validation->file_owner;
				$data['file_group']			=	$this->validation->file_group;
				$data['preinstall_cmd']		=	$this->validation->preinstall_cmd;
				$data['postinstall_cmd']	=	$this->validation->postinstall_cmd;
				$data['error_cmd']			=	$this->validation->error_cmd;
				$data['ignore_cmd_errors']	=	$this->validation->ignore_cmd_errors;
				$data['always_update']		=	$this->validation->always_update;
				$data['scope']				=	$this->validation->scope;
				$data['node_ids']			=	array($this->validation->node_ids);
				$data['nodegroup_ids']		=	array($this->validation->nodegroup_ids);

				print "<br>";
				print "ena: " . $data['enabled'] . "<br>";
				print "ice: " . $data['ignore_cmd_errors'] . "<br>";
				print "alu: " . $data['always_update'] . "<br>";
				$conf_fields = array();

				$data['enabled'] = $this->convert_checked($data['enabled']);
				$data['ignore_cmd_errors'] = $this->convert_checked($data['ignore_cmd_errors']);
				$data['always_update'] = $this->convert_checked($data['always_update']);

				# TODO: UpdateConfFile does not honor the values of 'node_ids'
				# and 'nodegroup_ids'.  These are read-only values.  Instead
				# there needs to be a check to GetConfFile() followed by:
				#   AddConfFileToNode(or Group) or,
				#   DeleteConfFileFromNode(or Group) as appropriate.
				# This would be easier to update in the API, but it's not
				# clear if there are other semantics that are being honored by
				# doing it the way that it currently is.

				$return =	$api->UpdateConfFile($conf_file_id, $data);
				if ($return != 1 ) {
					print $api->error();
			    	$error_occurred= 1;
				}
				$return =	$api->GetConfFiles(array($conf_file_id));
				print "<pre>";
				print_r($return );
				print "</pre>";
				 
			} else {
				print "VALIDATION FAILED<br>";
				print $this->validation->error_string ;
				print "VALIDATION FAILED<br>";
			    $error_occurred= 1;
			}

			if( !$error_occurred )
			{
			    $edit_finalized= 1;
			    $finalized_message= "Successfully updated.";
			} 
		} else {

			$conf_file = $api->GetConfFiles(array(intval($conf_file_id)));
			if (empty($conf_file)) {
				print $api->error();
			}
			$conf_file = $conf_file[0];

			$data['enabled']			=	$conf_file['enabled'];
			$data['source']				=	$conf_file['source'];
			$data['dest']				=	$conf_file['dest'];
			$data['file_permissions']	=	$conf_file['file_permissions'];
			$data['file_owner']			=	$conf_file['file_owner'];
			$data['file_group']			=	$conf_file['file_group'];
			$data['preinstall_cmd']		=	$conf_file['preinstall_cmd'];
			$data['postinstall_cmd']	=	$conf_file['postinstall_cmd'];
			$data['error_cmd']			=	$conf_file['error_cmd'];
			$data['ignore_cmd_errors']	=	$conf_file['ignore_cmd_errors'];
			$data['always_update']		=	$conf_file['always_update'];
			$data['node_ids']			=	$conf_file['node_ids'];
			$data['nodegroup_ids']		=	$conf_file['nodegroup_ids'];

			if( count($data['nodegroup_ids']) == 0 && count($data['node_ids']) == 0 )
			{
				 $data['scope'] = "global";
			} else {		 
				if( count($data['nodegroup_ids']) == 0 && is_numeric($data['node_ids'][0]) )
				{
					$data['scope'] = "node";
				} elseif( count($data['node_ids']) == 0 && is_numeric($data['nodegroup_ids'][0]) )
				{
					$data['scope'] = "group";
				}
			}
		}

		$data = $this->get_stage_data($data);

		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('UpdateConfFile', $data);
		$this->load->view('footer', $data);
	}

	function add()
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$data = array();

		$fields['enabled']  = "Enabled";
		$fields['source'] 		= "Source";
		$fields['dest']  = "Destination";
		$fields['file_permissions']  = "File Permissions";
		$fields['file_owner']  = "File Owner";
		$fields['file_group']  = "File Group";
		$fields['preinstall_cmd']  = "Preinstall Command";
		$fields['postinstall_cmd']  = "Postinstall Command";
		$fields['error_cmd']  = "Error Command";
		$fields['ignore_cmd_errors']  = "Ignore Command Errors";
		$fields['always_update']  = "Always Update";
		$fields['scope']  = "ConfFile Scope";
		$fields['node_ids']  = "";
		$fields['nodegroup_ids']  = "";
		$this->validation->set_fields($fields);

		if ( isset($_REQUEST['submitted']) )
		{
			$action= $_REQUEST["action"];
			$submitted= $_REQUEST["submitted"];
		} else {
			$action= 'default';
			$submitted= False;
		}

		$data['submit_caption']= "Create";
		$data['title'] = "Create new node configuration file";

		if(	$submitted	)
		{
			$rules['enabled']  = "trim|required|integer|intval";
			$rules['source'] 		= "trim|required";
			$rules['dest']  = "trim|required";
			$rules['file_permissions']  = "trim";
			$rules['file_owner']  = "trim";
			$rules['file_group']  = "trim";
			$rules['preinstall_cmd']  = "trim";
			$rules['postinstall_cmd']  = "trim";
			$rules['error_cmd']  = "trim";
			$rules['ignore_cmd_errors']  = "trim|integer|intval";
			$rules['always_update']  = "trim|integer|intval";
			$rules['scope']  = "trim";
			$rules['node_ids']  = "trim";
			$rules['nodegroup_ids']  = "trim";
			$this->validation->set_rules($rules);

			$data['enabled']			=	$this->validation->enabled;
			$data['source']				=	$this->validation->source;
			$data['dest']				=	$this->validation->dest;
			$data['file_permissions']	=	$this->validation->file_permissions;
			$data['file_owner']			=	$this->validation->file_owner;
			$data['file_group']			=	$this->validation->file_group;
			$data['preinstall_cmd']		=	$this->validation->preinstall_cmd;
			$data['postinstall_cmd']	=	$this->validation->postinstall_cmd;
			$data['error_cmd']			=	$this->validation->error_cmd;
			$data['ignore_cmd_errors']	=	$this->validation->ignore_cmd_errors;
			$data['always_update']		=	$this->validation->always_update;
			$data['scope']				=	$this->validation->scope;
			$data['node_ids']			=	array($this->validation->node_ids);
			$data['nodegroup_ids']		=	array($this->validation->nodegroup_ids);

			if ($this->validation->run() == TRUE)
			{
				$conf_file_id =	$api->AddConfFile($data);
				if ($conf_file_id == -1 ) {
					print $api->error();
				}
			}

			if(	!$error_occurred	)
			{
				$edit_finalized=	1;
				$finalized_message=	"Successfully	created.";
			}
		} else {
			//	set	up	default	values	for	a	new	one
			$data['enabled']			=	0;
			$data['source']				=	"";
			$data['dest']				=	"";
			$data['file_permissions']	=	"644";
			$data['file_owner']			=	"root";
			$data['file_group']			=	"root";
			$data['preinstall_cmd']		=	"";
			$data['postinstall_cmd']	=	"";
			$data['error_cmd']			=	"";
			$data['ignore_cmd_errors']	=	0;
			$data['always_update']		=	0;
			$data['scope']				=	"global";
		}

		$data = $this->get_stage_data($data);

		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('AddConfFile', $data);
		$this->load->view('footer', $data);

	}
}
