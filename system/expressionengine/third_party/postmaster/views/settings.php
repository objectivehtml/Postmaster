<h2><a href="#help" class="help">Parcels <span>(?)</span></a></h2>

<table class="mainTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="20%">Channel</th>
			<th>Categories</th>
			<th>Member Groups</th>
			<th width="10%">Status</th>
			<th>Trigger</th>
			<th>Service</th>
			<th width="10%">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($parcels) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not created any e-mail parcels yet.</p>
			</td>
		</tr>
		<?php endif; ?>

		<?php foreach($parcels as $parcel): ?>
		<tr>
			<td class="channel-name">
				<?php echo $parcel->channel_name;?>
			</td>
			<td class="categories">
				<?php if(count($parcel->categories) > 0): ?>
					<ul>
					<?php foreach($parcel->categories as $category): ?>
						<li>
							<?php echo $category->cat_name?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>No categories have been defined.</p>
				<?php endif; ?>
			</td>
			<td class="member-groups">
				<?php if(count($parcel->member_groups) > 0): ?>
					<ul>
					<?php foreach($parcel->member_groups as $group): ?>
						<li>
							<?php echo $group->group_title?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>No member groups have been defined.</p>
				<?php endif; ?>
			</td>
			<td class="statuses">
				<?php if(count($parcel->statuses) > 0): ?>
					<ul>
					<?php foreach($parcel->statuses as $status): ?>
						<li>
							<?php echo $status?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>No statuses have been defined.</p>
				<?php endif; ?>
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
					<li><a href="<?php echo $parcel->edit_url?>"><img src="<?php echo $theme_url?>postmaster/css/icons/edit_page.png" alt="Edit" title="Edit" class="tooltip" /></a></li>
					<li><a href="<?php echo $parcel->duplicate_url?>"><img src="<?php echo $theme_url?>postmaster/css/icons/copy_paste.png" alt="Duplicate" title="Duplicate" class="tooltip" /></a></li>
					<li><a href="<?php echo $parcel->delete_url?>" title="Delete" class="delete tooltip"><img src="<?php echo $theme_url?>postmaster/css/icons/delete_page.png" alt="Delete" /></a></li>
				</ul>
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
	<h2>Utilities</h2>
	<p>Utilites are resources that use the API to provide even more functionality. Utilities can extend far beyond just sending emails by creating new templates tags.</p>
</div>

<div id="ping" class="help-text">
	<h2>Ping URL</h2>
	<p>This URL can be used to setup cron jobs to automatically send your email. Anytime a HTTP request is sent to this URL, Postmaster will run through your queue and send any emails past their specified send date.</p>
</div>

<div id="help" class="help-text">
	<h2>What is a parcel?</h2>

	<p>Parcels are just like standard ExpressionEngine templates, except that they are parsed each time a channel entry is submitted. A parcel can be restricted to a specific status, member group(s), categorie(s), and/or a channel. After parcel has been successfully parsed it an email is sent according to your defined parameters. Parcels are very dynamic and 100% configurable. Any add-on or ExperssionEngine tag you would use in a template can also work in a parcel.</p>
</div>

<div class="delete dialog">

	<p>Are you use you want to delete this parcel?</p>

</div>
