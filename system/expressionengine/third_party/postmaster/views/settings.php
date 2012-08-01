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
		<? if(count($parcels) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not created any e-mail parcels yet.</p>
			</td>
		</tr>
		<? endif; ?>

		<? foreach($parcels as $parcel): ?>
		<tr>
			<td class="channel-name">
				<? echo $parcel->channel_name;?>
			</td>
			<td class="categories">
				<? if(count($parcel->categories) > 0): ?>
					<ul>
					<? foreach($parcel->categories as $category): ?>
						<li>
							<? echo $category->cat_name?>
						</li>
					<? endforeach; ?>
					</ul>
				<? else: ?>
					<p>No categories have been defined.</p>
				<? endif; ?>
			</td>
			<td class="member-groups">
				<? if(count($parcel->member_groups) > 0): ?>
					<ul>
					<? foreach($parcel->member_groups as $group): ?>
						<li>
							<? echo $group->group_title?>
						</li>
					<? endforeach; ?>
					</ul>
				<? else: ?>
					<p>No member groups have been defined.</p>
				<? endif; ?>
			</td>
			<td class="statuses">
				<? if(count($parcel->statuses) > 0): ?>
					<ul>
					<? foreach($parcel->statuses as $status): ?>
						<li>
							<? echo $status?>
						</li>
					<? endforeach; ?>
					</ul>
				<? else: ?>
					<p>No statuses have been defined.</p>
				<? endif; ?>
			</td>
			<td class="trigger">
				<? if(count($parcel->trigger) > 0): ?>
					<ul>
					<? foreach($parcel->trigger as $trigger): ?>
						<li>
							<? echo $trigger?>
						</li>
					<? endforeach; ?>
					</ul>
				<? else: ?>
					<p>No member groups have been defined.</p>
				<? endif; ?>
			</td>
			<td class="service">
				<? echo $parcel->service;?>
			</td>
			<td class="actions">
				<ul>
					<li><a href="<? echo $parcel->edit_url?>"><img src="<? echo $theme_url?>postmaster/css/icons/edit_page.png" alt="Edit" title="Edit" class="tooltip" /></a></li>
					<li><a href="<? echo $parcel->duplicate_url?>"><img src="<? echo $theme_url?>postmaster/css/icons/copy_paste.png" alt="Duplicate" title="Duplicate" class="tooltip" /></a></li>
					<li><a href="<? echo $parcel->delete_url?>" title="Delete" class="delete tooltip"><img src="<? echo $theme_url?>postmaster/css/icons/delete_page.png" alt="Delete" /></a></li>
				</ul>
			</td>
		</tr>
		<? endforeach; ?>
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
		<? if(count($delegates) == 0): ?>
		<tr>
			<td colspan="7">
				<p class="empty">You have not installed any delegates yet.</p>
			</td>
		</tr>
		<? endif; ?>
		
		<? foreach($delegates as $delegate): ?>
		<tr>
			<td><? echo $delegate->name?></td>
			<td><? echo $delegate->description?></td>
			<td style="text-align:center"><a href="<? echo $doctag_url . '&id='.$delegate->doctag?>"><?=$lang['documentation']?></a></td>
		</tr>
		<? endforeach; ?>
		
	</tbody>
</table>
					
<h2><a href="#ping" class="help">Ping URL <span>(?)</span></a></h2>

<div class="box">
	<a href="<? echo $ping_url?>"><? echo $ping_url ?></a>
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
