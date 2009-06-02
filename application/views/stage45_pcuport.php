<?php
if( isset($errors) && count($errors) > 0 )
{
  print( "<p><strong>The following errors occured:</strong>" );
  print( "<font color='red' size='-1'><ul>\n" );
  foreach( $errors as $err )
    {
      print( "<li>$err\n" );
    }
  print( "</ul></font>\n" );
}
?>
<div class="plroundedconfirm">
<h3>Confirm PCU Port to Node Mapping</h3>
Please confirm that the node is associated with the correct port on the PCU.
Most newer models have this function built-in, where as older models allow for
multiple 'ports'.  Port 1 is appropriate for built-in models.
<?php if ( $pcu_assigned ): ?>
<?= form_open("download/stage5_bootimage/$pcu_id/$site_id/$node_id", array('method'=>'post')) ?>
<center>
		<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
		<input type=hidden name='node_id' value='<?= $node_id ?>'>
		<input type=hidden name='site_id' value='<?= $site_id ?>'>
		<input type=submit name='node_confirm' value='Confirm & Proceed'>
</center>
</form>
<?php endif; ?>
</div>
<br>
<div class="plroundedupdate">
<?= form_open("register/stage45_mappcu/$pcu_id/$site_id/$node_id", array('name'=>'fm', 'method'=>'post')) ?>
		<table border=0 cellpadding=3>
			<tbody>
		<tr><td colspan='2'>
		</td></tr>
				</tr>
					<th width=200>Hostname : PCUPort</td>
					<td><?= $node->hostname ?> : <?= $pcu_port ?></td>
				<tr>
					<th>PCU Port:</th>
					<td>
						<select name='port_number'>
							<option value='noaction'>--</option>
							<?php 
									$ports= array( 1, 2, 3, 4, 5, 6, 7, 8);
									foreach ($ports as $port )
									{?>
										<option value='<?= $port ?>'>
											<?= $node->hostname ?> port <?= $port ?>
										</option>
							<?php   } ?>
						</select>

					</td>
				</tr>
				<tr><td></td>
					<td>
						<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
						<input type=hidden name='node_id' value='<?= $node_id ?>'>
						<input type=hidden name='site_id' value='<?= $site_id ?>'>
						<input type=submit name='pcumap_update' value='Update & Return'> 
					</td>
				</tr>
			</tbody>
			</table>
</div>
        <br /><p>
			<a href='/db/sites/index.php?id=<?= $site_id ?>'>Back to Home Site</a>
	
	</form>
