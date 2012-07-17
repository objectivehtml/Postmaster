<script type="text/javascript" src="<?=$ib_path?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
	
		var IB = new InterfaceBuilder();
	
	});
</script>

<style type="text/css">
	.CodeMirror-scroll {
		min-height: <? echo $template->height?>;
	}
</style>

<form action="<? echo $template->action?>" method="post" class="group postmaster">
	
	<fieldset class="column group sidebar">
	
		<div class="container">
			<h3><a href="#send-receive" class="help">Send / Receive <span>(?)</span></a></h3>
					
			<div id="send-receive" class="help-text">
				<h2>Send/Receive</h2>

				<p>These fields dictate who sends and receives the email. These values can be static and/or dynamic. Any of the following text fields you may also include: variables, EE tags, and conditionals. Anything you would expect in normal template can be used here too.</p>

				<p><i>Note,</i> not all of these fields are supported for all email services. While every attempt was made to make all the services as constent as possible, each third-party API is unique and uses the fields differently. Be sure to read up on the documentation for the service(s) you choose.</p>
			</div>
		</div>

		<ul>
			<li class="to_name">
				<label for="to_name">To (Name)</label>
				<input type="text" name="to_name" id="to_name" value="<? echo form_prep($template->to_name) ?>" />
			</li>
			<li class="to_email">
				<label for="to_email">To (Email)</label>
				<input type="text" name="to_email" id="to_email" value="<? echo form_prep($template->to_email) ?>" />
			</li>
			<li class="from">
				<label for="from">From (Name)</label>
				<input type="text" name="from_name" id="from_name" value="<? echo form_prep($template->from_name) ?>" />
			</li>
			<li class="from">
				<label for="from">From (E-mail)</label>
				<input type="text" name="from_email" id="from_email" value="<? echo form_prep($template->from_email) ?>" />
			</li>
			<li class="cc">
				<label for="cc">CC</label>
				<input type="text" name="cc" id="cc" value="<? echo form_prep($template->cc) ?>" />
			</li>
			<li class="bcc">
				<label for="bcc">BCC</label>
				<input type="text" name="bcc" id="bcc" value="<? echo form_prep($template->bcc) ?>" />
			</li>
			<li class="channel">
				<h3><a href="#channel-helper" class="help">Channel <span>(?)</span></a></h3>
				<select name="channel_id" id="channel_id">
				<? foreach($template->channels() as $channel): ?>
					<option value="<? echo $channel->channel_id?>" <? echo (int)$template->channel_id == (int)$channel->channel_id ? 'selected="selected"' : ''?>><? echo $channel->channel_title?></option>
				<? endforeach; ?>
				</select>
			</li>
			<li class="entry_trigger container">
				<h3><a href="#entry_trigger" class="help">Entry Trigger <span>(?)</span></a></h3>
				
				<div>
					<label><input type="checkbox" name="trigger[]" value="new" <? echo in_array('new', $template->trigger) ? 'checked="checked"' : NULL ?> /> New</label>
					<label><input type="checkbox" name="trigger[]" value="edit" <? echo in_array('edit', $template->trigger) ? 'checked="checked"' : NULL ?> /> Edit</label>
				</div>

			</li>
			<li class="categories container">
				
				<h3><a href="#categories" class="help">Categories <span>(?)</span></a></h3>
				
				<? if(count($template->category_tree()) == 0): ?>
					
					<p>This channel has no categories.</p>
				
				<? else: ?>

					<? foreach($template->category_tree() as $cat_id => $category): ?>
						<label><input type="checkbox" name="category[]" value="<? echo $category[0]?>" <? echo in_array($category[0], $template->categories) ? 'checked="checked"' : NULL ?> /><? echo $category[1]?></label>
					<? endforeach; ?>	
				<? endif; ?>

			</li>
			<li class="statuses container">

				<h3><a href="#status" class="help">Statuses <span>(?)</span></a></h3>

				<div>
				<? 

				if(count($template->statuses()) == 0): ?>
					
					<p>This channel has no statuses.</p>
				
				<? else: ?>

					<? foreach($template->statuses() as $status): ?>
						<label><input type="checkbox" name="statuses[]" value="<? echo $status['status']?>" <? echo in_array($status['status'], $template->statuses) ? 'checked="checked"' : NULL ?> /><? echo $status['status']?></label>
					<? endforeach; ?>

				<? endif; ?>
				</div>

			</li>
			<li class="member_groups container">
				<h3><a href="#member-groups" class="help">Member Group <span>(?)</span></a></h3>
				
				<div>
				<? foreach($template->member_groups() as $group): ?>
					<label><input type="checkbox" name="member_group[]" value="<? echo $group->group_id?>" <? echo in_array($group->group_id, $template->member_groups) ? 'checked="checked"' : NULL ?> /><? echo $group->group_title?></label>
				<? endforeach; ?>
				</div>

			</li>
			<li class="post-date container">
				<h3><a href="#post-date" class="help">Post Date <span>(?)</span></a></h3>
				
				<div class="help-text" id="post-date">
					<h2>Post Date</h2>
					<p><h3>Post Date Specific</h3> <i>Post Date Specific</i> allows you to define a specific date to send the email. If the date has already passed, the email will be sent. If it's in the future, the email will be sent to the queue where it will sit until the appropriated time.</p>
					<p><h3>Post Date Relative</h3> <i>Post Date Relative</i> allows you to define relativity to the <i>Post Date Specific</i> field. (If no <i>Post Date Specific</i> is set, then the current time is used.) For instance, if the <i>Post Date Specific</i> field was set to the date the entry was submitted by the user, <i>Post Date Relative</i> could say "+5 days" which would send the email 5 days after the entry was submitted.</p>
					<p><h3>Send Every</h3> <i>Send Every</i> allows you to automatically send reocurring emails. The same values are accepted as <i>Post Date Relative</i>. For instance, to send every 7 days use "+7 days". To send next Thursday use "next Thursday".</p>
				</div>

				<label for="post_date_specific">Post Date Specific</label>
				<input type="text" name="post_date_specific" id="post_date_specific" value="<? echo form_prep($template->post_date_specific) ?>" />
			</li>
			<li class="post-date">
				<label for="post_date_relative">Post Date Relative</label>
				<input type="text" name="post_date_relative" id="post_date_relative" value="<? echo form_prep($template->post_date_relative) ?>" />
			</li>
			<li class="send-every">
				<label for="send_every">Send Every</label>
				<input type="text" name="send_every" id="send_every" value="<? echo form_prep($template->send_every) ?>" />
			</li>
			<li class="send-every">
				<h3><a href="#extra-conditionals" class="help"><label for="extra_conditionals">Extra Conditionals <span>(?)</span></label></a></h3>
				
				<div class="help-text" id="extra-conditionals">
					<h2>Extra Conditionals</h2>
					<p>This field allows you define a set of proprietary conditionals use EE tags and fieldtypes. Three values are expected in this field, all others will be ignored. If you want to email to send return TRUE. If not, return FALSE. If no value is return the field is ignored.</p>
					<p>For instance, if you wanted to prevent the email from being sent if your member doesn't include their first or last name. In this case, the fields are "member_first_name" and "member_last_name". Use either one of the following examples:</p>
					
					<h3>Example 1</h3>
					
					<p>	{if parcel:member_first_name != "" && parcel:member_last_name != ""}
							TRUE
						{/if}
					</p>
					
					<h3>Example 2</h3>
					
					<p>
						{if parcel:member_first_name == "" || parcel:member_last_name == ""}
							FALSE
						{/if}
					</p>
				</div>

				<textarea name="extra_conditionals" id="extra_conditionals"><? echo form_prep($template->extra_conditionals) ?></textarea>
		</ul>

		<div class="help-text" id="channel-helper">
			<h2>Channel</h2>
			<p>This field is required. Anytime a user submits a new entry in this channel, an email will be sent provided the other triggers are met.</p>
		</div>
		
		<div class="help-text" id="entry_trigger">
			<h2>Entry Trigger</h2>
			<p>This trigger allows you to define if you want the email to be sent for new and/or editted entries. If no trigger is specified, then the email will not be sent.</p>
		</div>

		<div class="help-text" id="categories">
			<h2>Categories</h2>
			<p>Each entry must belong to one the following categories before the email will be sent. The categories do not match have to match explicitly.</p>
		</div>

		<div class="help-text" id="status">
			<h2>Statuses</h2>
			<p>Only entries submitted with one of the following statuses will trigger the email to send.</p>
		</div>

		<div class="help-text" id="member-groups">
			<h2>Member Groups</h2>
			<p>Each entry author must have belong to one of the checked member groups before the email will be sent.</p>
		</div>

	</fieldset>
	
	<fieldset class="column group editor">
	
		<div class="message">

			<ul>
				<li class="preview">

					<div class="window">

						<div class="panel container">

							<h3><a href="#preview" class="help">Preview<span> (?)</span></a></h3>
							
							<label for="test_entry"><a href="#parse-entry" class="help">Parse Entry <span>(?)</span></a></label>
							
							<div class="help-text" id="preview">

								<h2>Preview</h2>

								<p>This is a live preview that parses EE tags and renders your markup in real time. Everything is including within an iFrame, so anything you would expect out of a normal webpage applies to the preview. You can include your own external stylesheets too.</p>
							
							</div>

							<div class="help-text" id="parse-entry">

								<h2>Parse Entry</h2>

								<p>This field allows you to select an entry to parse against your template. This is strictly for development purposes. This field has no effect on the sending the email.</p>

							</div>
							
							<select name="test_entry" id="test_entry">
							<? foreach($template->entries->result() as $entry): ?>
								<option value="<? echo $entry->entry_id?>"><? echo $entry->title?></option>
							<? endforeach; ?>
							</select>

						</div>

						<iframe src="" class="pain"></iframe> 

					</div>

				</li>
				<li class="text-editor">

					<a href="#" class="refresh">Refresh Preview</a>		

					<div class="themes">

						<label for="theme">Theme</label>
						
						<select name="theme" class="theme">
						<? foreach($template->themes() as $theme): ?>
							<option value="<? echo $theme->value?>" <? echo ($theme->value == $template->default_theme) ? 'selected="selected"' : ''?>><? echo $theme->name?></option>
						<? endforeach; ?>
						</select>
					
					</div>		

					<h3><label for="message"><a href="#email-message" class="help">Message <span>(?)</span></a></label></h3>
					
					<div class="help-text" id="email-message">
						<h2>Message</h2>

						<p>This value will appear in the body or message of the email. It can be static and/or dynamic and can include: variables, EE tags, and conditionals. Anything you would expect in normal template can be used here too. Just be sure you use the 'parcel:' prefix. Ex: {parcel:title}</p>
					</div>

					<div style="position:relative">		
						<textarea name="message" id="message"><? echo $template->message?></textarea>

						<div class="flyout">
							<a href="#" class="tab">+</a>
							
							<a href="#" class="close">&times; close</a>

							<ul class="toggle">
								<li><a href="#with" class="first">{var}</a></li>
								<li><a href="#without" class="last">var</a></li>
							</ul>

							<ul class="data">
							<? foreach($template->fields() as $field): ?>
								<li><a href="#<? echo $field->field_name?>"><? echo $field->field_label?></a></li>
							<? endforeach; ?>
							</ul>
						</div>
					</div>

				</li>
				<li class="margin-top subject">				
					<h3><label for="subject"><a href="#subject" class="help">Subject <span>(?)</span></a></label></h3>
					
					<div class="help-text" id="subject">
						<h2>Subject</h2>

						<p>This value will appear in the subject of the email. It can be static and/or dynamic and can include: variables, EE tags, and conditionals. Anything you would expect in normal template can be used here too. Just be sure you use the 'parcel:' prefix. Ex: {parcel:title}</p>
					</div>

					<input type="text" name="subject" id="subject" value="<? echo form_prep($template->subject) ?>" />
				</li>
			</ul>

		</div>
		
		<div class="margin-top service">
		
			<h2><a href="#email_service" class="help">Email Service <span>(?)</span></a></h2>

			<div class="help-text" id="email_service">
				<h2>Email Service</h2>
				<p>This is the method used to send an email. By default, ExpressionEngine is used to send email. This can be simple and effective for some. But to reliably send emails to thousands of people, you will need to use a third-party service.
			</div>

			<select name="service" id="service">			
			<? foreach($template->services() as $service): ?>
				<option value="<? echo $service->name ?>" <? if($template->service == $service->name): ?>selected="selected"<? endif; ?>><? echo $service->name ?></option>
			<? endforeach; ?>
			</select>


			<div class="service-panels">
			
				<? foreach($template->services() as $service): ?>
				<div class="service-panel" id="<? echo $service->name ?>_panel">
	
					<h3><? echo $service->name ?> Settings</h3>
	
					<? echo $service->description ?>
	
					<? echo $service->display_settings($template->settings, $template) ?>
	
				</div>
				<? endforeach; ?>
				
			</div>

		</div>
		
		<input type="hidden" name="id" value="<? echo $template->id?>" />
		<input type="hidden" name="return" value="<? echo $template->return?>" />

		<button type="submit" class="submit float-right"><? echo $template->button?></button>
		<button type="button" class="refresh submit float-right margin-right">Refresh Preview</button>
	
	</fieldset>
	
</form>

<script type="text/javascript">

	Postmaster.editorSettings = <? echo $template->editor_settings?>;
	Postmaster.settings       = <? echo json_encode($template->settings)?>;
	Postmaster.parser		  = '<? echo $template->parser_url?>';

</script>
