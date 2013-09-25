<a href="<?=$create_parcel_url?>" class="action button"><span class="icon-plus"></span> New Parcel</a>

<h2><a href="#help" class="help">Parcels <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="width:30%">Title</th>
			<th style="width:10%">Enabled</th>
			<th style="width:15%">Channel</th>
			<th style="width:15%">Triggers</th>
			<th style="width:20%">Service</th>
			<th style="width:5%;max-width:112px;min-width:112px">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($parcels) == 0): ?>
		<tr>
			<td colspan="8">
				<p class="empty">You have not created any e-mail parcels yet.</p>
			</td>
		</tr>
		<?php endif; ?>

		<?php foreach($parcels as $parcel): ?>
		<tr>
			<td class="title">
				<?php echo $parcel->title;?>
			</td>
			<td class="enabled">
				<?php echo $parcel->enabled != 0 ? 'Enabled' : 'Disabled'; ?>
			</td>
			<td class="channel-name">
				<?php echo $parcel->channel_title;?>
			</td>
			<td class="trigger">
				<?php if(count($parcel->trigger) > 0): ?>
					<ul>
					<?php foreach($parcel->trigger as $trigger): ?>
						<li>
							<?php echo $trigger?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>No member groups have been defined.</p>
				<?php endif; ?>
			</td>
			<td class="service">
				<?php echo $parcel->service;?>
			</td>
			<td class="actions">
				<ul>
					<li><a href="<?php echo $parcel->edit_url?>" class="button"><span class="icon-edit"></span></a></li>
					<li><a href="<?php echo $parcel->duplicate_url?>" class="button"><span class="icon-copy"></span></a></li>
					<li><a href="<?php echo $parcel->delete_url?>" class="delete button"><span class="icon-trash"></span></a></li>
				</ul>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<a href="<?php echo $add_hook_url?>" class="action button"><span class="icon-plus"></span> New Hook</a>

<h2><a href="#hooks" class="help">Hooks <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="width:30%">Title</th>
			<th style="width:10%">Enabled</th>
			<th style="width:25%">Hook</th>
			<th style="width:15%">Service</th>
			<th style="width:5%;max-width:112px;min-width:112px">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($hooks) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not setup any hooks yet.</p>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php foreach($hooks as $hook): ?>
		<tr>
			<td class="title"><?php echo $hook->title?></td>
			<td class="enabled"><?php echo $hook->enabled != 0 ? 'Enabled' : 'Disabled'; ?></td>
			<td><?php echo !empty($hook->installed_hook) ? $hook->installed_hook : $hook->user_defined_hook ?></td>
			<td><?php echo !empty($hook->service) ? $hook->service : $hook->service ?></td>			
			<td class="actions">
				<a href="<?php echo $hook->edit_url?>" title="Edit" class="button tooltip"><span class="icon-edit"></span></a>
				<a href="<?php echo $hook->duplicate_url?>" title="Duplicate" class="button tooltip"><span class="icon-copy"></span></a>
				<a href="<?php echo $hook->delete_url?>" title="Delete" class="button delete tooltip"><span class="icon-trash"></span></a>			
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<a href="<?php echo $add_notification_url?>" class="action button"><span class="icon-plus"></span> New Notification</a>

<h2><a href="#notications" class="help">Notifications <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="width:30%">Title</th>
			<th style="width:10%">Enabled</th>
			<th style="width:25%">Notification</th>
			<th style="width:15%">Ping</th>
			<th style="width:5%;max-width:112px;min-width:112px">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($notifications) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not setup any notifications yet.</p>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php foreach($notifications as $notication): ?>
		<tr>
			<td class="title"><?php echo $notication->title?></td>
			<td class="enabled"><?php echo $notication->enabled != 0 ? 'Enabled' : 'Disabled';?></td>
			<td class="notification"><?php echo $notication->notification?></td>
			<td><a href="<?php echo $notication->ping_url?>"><?php echo $notication->ping_url?></a></td>			
			<td class="actions">
				<a href="<?php echo $notication->edit_url?>" title="Edit" class="button tooltip"><span class="icon-edit"></span></a>
				<a href="<?php echo $notication->duplicate_url?>" title="Duplicate" class="button tooltip"><span class="icon-copy"></span></a>
				<a href="<?php echo $notication->delete_url?>" title="Delete" class="button delete tooltip"><span class="icon-trash"></span></a>			
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<a href="<?php echo $add_task_url?>" class="action button"><span class="icon-plus"></span> New Task</a>

<h2><a href="#tasks" class="help">Tasks <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="width:30%">Title</th>
			<th style="width:10%">Enabled</th>
			<th style="width:25%">Task</th>
			<th style="width:25%">Ping</th>
			<th style="width:5%;max-width:112px;min-width:112px">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($tasks) == 0): ?>
		<tr>
			<td colspan="5">
				<p class="empty">You have not setup any tasks yet.</p>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php foreach($tasks as $task): ?>
		<tr>
			<td class="title"><?php echo $task->title?></td>
			<td class="title"><?php echo $task->enabled != 0 ? 'Enabled' : 'Disabled'; ?></td>
			<td><?php echo $task->task ?></td>	
			<td><?php echo $task->ping_url ?></td>	
			<td class="actions">
				<a href="<?php echo $task->edit_url?>" title="Edit" class="button tooltip"><span class="icon-edit"></span></a>
				<a href="<?php echo $task->duplicate_url?>" title="Duplicate" class="button tooltip"><span class="icon-copy"></span></a>
				<a href="<?php echo $task->delete_url?>" title="Delete" class="button delete tooltip"><span class="icon-trash"></span></a>			
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h2><a href="#delegates" class="help">Utilities <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="width:40%">Name</th>
			<th>Description</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($delegates) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not installed any delegates yet.</p>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php foreach($delegates as $delegate): ?>
		<tr>
			<td><?php echo $delegate->name?></td>
			<td><?php echo $delegate->description?></td>
			<td style="text-align:center"><a href="<?php echo $doctag_url . '&id='.$delegate->doctag?>"><?php echo $lang['documentation']?></a></td>
		</tr>
		<?php endforeach; ?>
		
	</tbody>
</table>
					
<h2><a href="#ping" class="help">Ping URL <span>(?)</span></a></h2>

<div class="box">
	<a href="<?php echo $ping_url?>"><?php echo $ping_url ?></a>
</div>

<div id="delegates" class="help-text">
	<h2>What is a Utility?</h2>
	<p>Utilites are resources that use the API to provide even more functionality. Utilities can extend far beyond just sending emails by creating new templates tags.</p>
</div>

<div id="notications" class="help-text">
	<h2>What is a Notifcation?</h2>
	<p>Notifications are special templates that have very arbitrary and specific rules. Each notification is assigned a driver, and each driver can do something radically different. The Basic Notification driver for instance just send basic emails which others perform much more complicated tasks. Notifications can be triggered within a EE template or with a CRON job.</p>
</div>

<div id="ping" class="help-text">
	<h2>Ping URL</h2>
	<p>This URL can be used to setup cron jobs to automatically send your email. Anytime a HTTP request is sent to this URL, Postmaster will run through your queue and send any emails past their specified send date.</p>
</div>

<div id="help" class="help-text">
	<h2>What is a parcel?</h2>

	<p>Parcels are just like standard ExpressionEngine templates, except that they are parsed each time a channel entry is submitted. A parcel can be restricted to a specific status, member group(s), categorie(s), and/or a channel. After parcel has been successfully parsed it an email is sent according to your defined parameters. Parcels are very dynamic and 100% configurable. Any add-on or ExperssionEngine tag you would use in a template can also work in a parcel.</p>
</div>

<div id="hooks" class="help-text">
	<h2>What is a Hook?</h2>

	<p>Hooks allow developers to extend add-ons in ways that would otherwise require a hack. Hooks can override default functionality with something more custom and desirable. Postmaster allows developer to take advantage of developer hooks to send email within virtually any application.</p>
</div>

<div class="delete dialog">

	<p>Are you use you want to delete this record?</p>

</div>


<div id="tasks" class="help-text">
	<h2>What is a Tasks?</h2>

	<p>Tasks allow developers to trigger specific actions at specific times. Tasks can be triggered using hooks or with a simple HTTP request. The major difference with Tasks is they aren't related to sending emails. For example, registering an email to a MailChimp list when they register would be a perfect example of a Task.</p>
</div>

<div class="delete dialog">

	<p>Are you use you want to delete this record?</p>

</div>
