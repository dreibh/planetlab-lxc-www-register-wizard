
<h2>All current node configuration files</h2>

<p>

<table width=100%>
<tr>
<th width=50%>Destination File <font size=-1>[click to view/edit details]</font></th>
<th width=40%>Scope</th>
<th width=10%>Enabled</th>
</tr>


<?php 
	foreach( $all_conf_files as $conf_file )
    {
		$conf_file_id  = $conf_file['conf_file_id'];
		$enabled       = $conf_file['enabled'];
		$dest          = $conf_file['dest'];
		$node_id       = $conf_file['node_ids'];
		$nodegroup_id  = $conf_file['nodegroup_ids'];

		if( $enabled == True )
			$enabled= "Yes";
		else
			$enabled= "No";

		// find out what the scope is for this conf file
		if( count($node_id) == 0 && count($nodegroup_id) == 0)
		{
			// if there is no associate entry, its a global scope
			$scope= "global";
		} else {
			if( count($nodegroup_id) == 0 && is_numeric($node_id[0]) )
			{
				$nodes= $api->GetNodes( $node_id, array("hostname") );
				  
				$hostname= $nodes[0]["hostname"];
				$scope= "node: $hostname";
			} elseif(count($node_id) == 0 && is_numeric($nodegroup_id[0]) )
			{
				$nodegroups= $api->GetNodeGroups( $nodegroup_id );
				$group_name= $nodegroups[0]["name"];
				$scope= "group: $group_name";
			}
		}
      
		print( "<tr>\n<td>" );
		print( "<a href=\"conffile/update/$conf_file_id\">$dest</a></td>\n" );
		print( "<td>$scope</td>\n" );
		print( "<td>$enabled</td>\n</tr>\n" );
	}
?>


</tr>
</table>

<p><a href="edit.php?action=create">Create new...</a>
<br><a href="copy.php">Copy...</a>
<br><a href="delete.php">Delete...</a>

<p><br>
<h2>File scope priority</h2>

The following priority is applied to files that have the same destination:
<ol>
<li>Files that have a one-node scope
<li>Files that have a group scope
<li>Files that have a global scope
</ol>
