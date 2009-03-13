<div class="plroundedupdate">
<div class="plroundedwhite">
<h3>Confirm Reboot Node With PCU</h3>
<dl>
	<?php if ( $node['boot_state'] == "disable" ): ?>
	<dt>Step 1</dt>
		<dd>
			<span class='error'>
			Use the 'Test Reboot' button.  This will set the boot state to 'rins' and 
			attempt to reboot your machine using the registered PCU.
			</span>
		</dd>
	<?php elseif ( $node['boot_state'] == "rins" || $node['boot_state'] == "reinstall" ): ?>
	<dt>If Reboot succeeds,</dt><dd>
			your machine will reboot, install the bootstrap image, and 
			contacted PLC.  Finally, the boot state will be updated to 'boot'.
			<br>
			Continue using the 'Reload' button to check whether the node has
			completed its installation.
	</dd>
	<dt>If Reboot fails,</dt><dd>
			there will be an error displayed that will assist you in
			diagnosing the error.  Otherwise, please double check the
			registered information for the PCU, and that you can operate the
			PCU manually (using it's web or ssh interface).
			<br>
			To try the test again, use 'Test Reboot'.<br>
	</dd>
	<?php else: ?>
	<dt><b>Success!!</b></dt><dd>
			The installation is complete; your machine has reached the final
			'boot' state.  You can continue to the final stage.</dd>
	<?php endif; ?>
</dl>

<table><tbody>
	<tr><th>Site: </th><td> <a href='/db/sites/index.php?id=<?= $site_id ?>'><?= $site['name'] ?></a></td></tr>
	<tr><th>Hostname: </th><td nowrap><?= $node['hostname'] ?></td></tr>
	<tr><th>Boot State: </th><td><?= $node['boot_state'] ?> </td>
		<td>
		<?php if ( $node['boot_state'] == "disable" ): ?>
			<span class='error'> Start with the 'Test Reboot' button.</span>
		<?php elseif ( $node['boot_state'] == "rins" ): ?>
			<br>'Test Reboot' to start again.<br>
			'Reload' to Reload this page.
		<?php else: ?>
			<b>Success!!</b> Your machine has booted and contacted PLC.
			'Continue' to the final stage.
		<?php endif; ?>
		</td>
	</tr>
	<tr><th>Model: </th><td><?= $node['model'] ?></td></tr>
	<tr><th>Version: </th><td nowrap='1'><?= $node['version'] ?></td></tr>
	<tr><th>Last Contact: </th><td><?= $last_contact_str ?></td></tr>
<?php if ( isset($error) && ! empty($error) ): ?>
	<tr>
		<td colspan=2>
			<span class='error'> <?= $error ?> </span>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<td>
		</td>
		<td colspan='2'>
			<?= form_open("confirm/reboot/$site_id/$pcu_id/$node_id", 
						   array('style' => 'display: inline; margin: 0px')) ?>
				<input type='submit' name='reboot_node' value='Test Reboot'>
			</form>
			<?= form_open("confirm/stage8_rebootpcu/$pcu_id/$site_id/$node_id", array('style' => 'display: inline; margin: 0px')) ?>
				<input type='submit' name='reload_contact' value='Reload'>
			</form>
			<?= form_open("confirm/complete/$site_id/$pcu_id/$node_id", 
						   array('style' => 'display: inline; margin: 0px')) ?>
		<?php if ( $node['boot_state'] == "boot" ): ?>
				<input type='submit' value='Continue'>
		<?php else: ?>
				<input type='submit' disabled value='Continue'>
		<?php endif; ?>
				<input type='submit' value='Debug Continue'>
		</form>
</tbody></table><br />
</div>
</div>

<h3>Tips:</h3>
<ul>
    <li>
			If you do not see an error, this does not mean that none have
			occurred.  We need your help in verifying that the PCU function is
			configured correctly.
	</li>
    <li>
			Please be patient.  Installations may take between 10 to 30
			minutes depending on the speed of your hardware and network.  Checking the
			console is the fastest way to verify that the installation is proceeding.
	</li>
</ul>
