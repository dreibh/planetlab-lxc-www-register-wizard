<script language="javascript">

function updateNodeCombo()
{
  node_ids= document.fm.node_ids;
  nodegroup_ids= document.fm.nodegroup_ids;
  scope= document.fm.scope;

  for ( var i=0; i < scope.length ; i++ )
  {
  	if ( scope[i].checked )
	{
	  switch( scope[i].value )
	  {
	  case "global":
		node_ids.disabled= 1;
		nodegroup_ids.disabled= 1;
		break;

	  case "group":
		node_ids.disabled= 1;
		nodegroup_ids.disabled= 0;
		break;

	  case "node":
		node_ids.disabled= 0;
		nodegroup_ids.disabled= 1;
		break;
	  }
	}
  }
}

</script>


<a href="index.php">Return to listing...</a>
<form name='fm' action="conffile/add" method="post">
<input type="hidden" name="submitted" value="1">

<p><table width=100% cellpadding=5 border=0>

<tr>
<td></td>
<td>
	<input type=checkbox <?= $this->validation->set_checkbox('enabled', $this->validation->enabled ) ?> 
			name="enabled">Enabled</input>
	<?= $this->validation->enabled_error ?>
</td>
</tr>
<tr>
<td>File Scope:</td>
<td>
<input type='radio' name='scope' value='global' onClick="updateNodeCombo();" <?= ( $scope == "global" ?  "checked" : "" ) ?>> Global<br>
<input type='radio' name='scope' value='node' onClick="updateNodeCombo();" <?= ( $scope == "node" ?  "checked" : "" ) ?>> Node : 
	<select name="node_ids">
	<?php foreach( $nodes as $node ): ?>
	  	<option $selected value="<?= $node['node_id'] ?>"><?= $node['hostname'] ?></option>
	<?php endforeach; ?>
	</select>
<br>

<input type='radio' name='scope' value='group' onChange="updateNodeCombo();" <?= ( $scope == "group" ?  "checked" : "" ) ?>> Group : 
	<select name="nodegroup_ids">
	<?php foreach( $nodegroups as $nodegroup ): ?>
		  <option value="<?= $nodegroup['nodegroup_id'] ?>"><?= $nodegroup['name'] ?></option>
	<?php endforeach; ?>
	</select></td>

</td>
</tr>

<tr>
<td>Source:</td>
<td><i>http://<?= PLC_BOOT_HOST ?>/</i> 
<input name="source" value="<?= htmlspecialchars($this->validation->source) ?>" size=40 maxlength=255>
<?= $this->validation->source_error ?>
</td>
</tr>

<tr>
<td>Destination:</td>
<td>
<input name="dest" value="<?= htmlspecialchars($this->validation->dest) ?>" size=40 maxlength=255>
<?= $this->validation->dest_error ?>
</td>
</tr>

<tr>
<td>Permissions:</td>
<td>
<input name="file_permissions" value="<?= htmlspecialchars($this->validation->file_permissions) ?>" size=5 maxlength=20>
<?= $this->validation->file_permissions_error ?>
</td>
</tr>

<tr>
<td>Owner:</td>
<td>
<input name="file_owner" value="<?= htmlspecialchars($this->validation->file_owner) ?>" size=15 maxlength=50>
<?= $this->validation->file_owner_error ?>
</td>
</tr>

<tr>
<td>Group:</td>
<td>
<input name="file_group" value="<?= htmlspecialchars($this->validation->file_group) ?>" size=15 maxlength=50>
<?= $this->validation->file_group_error ?>
</td>
</tr>

<tr>
<td>Pre-Install Command:</td>
<td>
<input name="preinstall_cmd" value='<?= htmlspecialchars($this->validation->preinstall_cmd) ?>' size=70 maxlength=1024>
<?= $this->validation->preinstall_cmd_error ?>
</td>
</tr>

<tr>
<td>Post-Install Command:</td>
<td>
<input name="postinstall_cmd" value='<?= htmlspecialchars($this->validation->postinstall_cmd) ?>' size=70 maxlength=1024>
<?= $this->validation->postinstall_cmd_error ?>
</td>
</tr>

<tr>
<td>Error Command:</td>
<td>
<input name="error_cmd" value='<?= htmlspecialchars($this->validation->error_cmd) ?>' size=70 maxlength=1024>
<?= $this->validation->postinstall_cmd_error ?>
<br>
(run if an error occured, regardless if errors are being ignored)</td>
</tr>

<tr>
<td></td>
<td>
	<input type=checkbox <?= $this->validation->set_checkbox('ignore_cmd_errors', $this->validation->ignore_cmd_errors) ?> 
			name="ignore_cmd_errors">Ignore pre/post install command errors</input>
	<?= $this->validation->ignore_cmd_errors_error?>
</td>
</tr>


<tr>
<td></td>
<td>
	<input type=checkbox <?= $this->validation->set_checkbox('always_update', $this->validation->always_update) ?> 
			name="always_update">Always update this file, even if same as original</input>
	<?= $this->validation->always_update_error?>
</td>
</tr>

<tr>
<td><input type="submit" value="<?= htmlspecialchars($submit_caption) ?>"></td>
</tr>


</table>
</form>
<script language="javascript">
updateNodeCombo();
</script>
