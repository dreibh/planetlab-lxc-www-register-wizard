<div class="plroundedupdate">
<div class="plroundedwhite">
<h3>Confirm Node Boot and PLC Contact</h3>
<table><tbody>
	<tr><th>Site: </th><td> <a href='/db/sites/index.php?id=$site_id'><?= $site['name'] ?></a></td></tr>
	<tr><th>Hostname: </th><td nowrap='1'><?= $node['hostname'] ?></td></tr>
	<tr><th>Boot State: </th><td><?= $node['boot_state'] ?> </td></tr>
	<tr><th>Model: </th><td><?= $node['model'] ?></td></tr>
	<tr><th>Version: </th><td nowrap='1'><?= $node['version'] ?></td></tr>
	<tr><th>Last Contact: </th><td><?= $last_contact_str ?></td>
		<td>
		<?php if ( $last_contact_str == "Never" ): ?>
			<span class='error'> Once your machine has booted and contacted PLC,
				this field will update and you will be able to 'Continue' to the next
				stage.
			</span>
			<br>
			'Reload' to refresh this page.
		<?php else: ?>
			<b>Success!!</b> Your machine has booted and contacted PLC.<br>
			'Continue' and test the remote reset function.
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
		</td>
		<td colspan='2'>
			<?= form_open("confirm/stage7_firstcontact/$pcu_id/$site_id/$node_id", array('style' => 'display: inline; margin: 0px')) ?>
				<input type='submit' name='reload_contact' value='Reload'>
			</form>
			<?= form_open("confirm/stage8_rebootpcu/$pcu_id/$site_id/$node_id", array('style' => 'display: inline; margin: 0px')) ?>
		<?php if ( $last_contact_str != "Never" ): ?>
				<input type='submit' value='Continue'>
		<?php else: ?>
				<input type='submit' disabled value='Continue'>
		<?php endif; ?>
				<!--input type='submit' value='Debug Continue'-->
			</form>
		</td>
	</tr>
</tbody></table><br/>
</div>
</div>
<p>
<h3>Trouble Shooting:</h3>
<ul>
<li>If you have booted the machine, but the <em>Last Contact</em> field has
not updated, then there is very likely a local, network configuration problem. <br> 
We recommend verifying that the machine's network settings are correct and operational.  You can achieve this using the <code>site_admin</code> account on the BootCD to manually diagnose the problem.</li>
</ul>
</p>
