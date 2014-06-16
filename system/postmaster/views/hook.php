<script type="text/javascript" src="<?php echo $ib_path?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
	
		var IB = new InterfaceBuilder();
	
	});
</script>

<style type="text/css">
	.CodeMirror-scroll {
		min-height: <?php echo $template->height?>;
	}
</style>

<form action="<?php echo $template->action?>" method="post" class="group postmaster">
	
	<input type="hidden" name="XID" value="<?php echo $xid?>">
	
	<fieldset class="column group sidebar">
				
		<div class="container">
			<h3><a href="#help-title" class="help"><label for="title">Hook Title</label> <span>(?)</span></a></h3>
			
			<input type="text" name="title" id="title" value="<?php echo form_prep($template->title) ?>" />
			
			<div id="help-title" class="help-text">
				
				<h2>Hook Title</h2>

				<p>The Hook Title is a value you can use to give each hook you install some meaning or context. This field has no programatic purpose, and is strictly for you to use to know what is what.</p>
				
			</div>
		</div>
		
		<div class="container margin-top">
			<h3><a href="#help-enabled" class="help"><label for="enabled">Enabled?</label> <span>(?)</span></a></h3>
			
			<select name="enabled" id="enabled">
				<option value="1" <?php echo $template->is_enabled() ? 'selected="selected"' : ''; ?>>Enabled</option>
				<option value="0" <?php echo !$template->is_enabled() ? 'selected="selected"' : ''; ?>>Disabled</option>
			</select>
			
			<div id="help-enabled" class="help-text">
				
				<h2>Is the Hook Enabled?</h2>

				<p>The Enabled property is a value you can use to prevent the hook from sending without having to change the data or delete it.</p>
				
			</div>
		</div>
		
		<div class="container margin-top">
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
				<input type="text" name="to_name" id="to_name" value="<?php echo form_prep($template->to_name) ?>" />
			</li>
			<li class="to_email">
				<label for="to_email">To (Email)</label>
				<input type="text" name="to_email" id="to_email" value="<?php echo form_prep($template->to_email) ?>" />
			</li>
			<li class="from">
				<label for="from">From (Name)</label>
				<input type="text" name="from_name" id="from_name" value="<?php echo form_prep($template->from_name) ?>" />
			</li>
			<li class="from">
				<label for="from">From (E-mail)</label>
				<input type="text" name="from_email" id="from_email" value="<?php echo form_prep($template->from_email) ?>" />
			</li>
			<li class="reply-to">
				<label for="from">Reply To (E-mail)</label>
				<input type="text" name="reply_to" id="reply_tp" value="<?php echo form_prep($template->reply_to) ?>" />
			</li>
			<li class="cc">
				<label for="cc">CC</label>
				<input type="text" name="cc" id="cc" value="<?php echo form_prep($template->cc) ?>" />
			</li>
			<li class="bcc">
				<label for="bcc">BCC</label>
				<input type="text" name="bcc" id="bcc" value="<?php echo form_prep($template->bcc) ?>" />
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
				<input type="text" name="post_date_specific" id="post_date_specific" value="<?php echo form_prep($template->post_date_specific) ?>" />
			</li>
			<li class="post-date">
				<label for="post_date_relative">Post Date Relative</label>
				<input type="text" name="post_date_relative" id="post_date_relative" value="<?php echo form_prep($template->post_date_relative) ?>" />
			</li>
			<li class="send-every">
				<label for="send_every">Send Every</label>
				<input type="text" name="send_every" id="send_every" value="<?php echo form_prep($template->send_every) ?>" />
			</li>
		</ul>

		<div class="container margin-top">
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

			<textarea name="extra_conditionals" id="extra_conditionals"><?php echo form_prep($template->extra_conditionals) ?></textarea>
		</div>

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

						<iframe src="" class="pain"></iframe> 

					</div>

				</li>
				<li class="text-editor">

					<a href="#" class="refresh">Refresh Preview</a>		

					<div class="themes">

						<label for="theme">Theme</label>
						
						<select name="theme" class="theme">
						<?php foreach($template->themes() as $theme): ?>
							<option value="<?php echo $theme->value?>" <?php echo ($theme->value == $template->default_theme) ? 'selected="selected"' : ''?>><?php echo $theme->name?></option>
						<?php endforeach; ?>
						</select>
					
					</div>		

					<h3><label for="message"><a href="#email-message" class="help">Message <span>(?)</span></a></label></h3>
					
					<div class="help-text" id="email-message">
						<h2>Message</h2>

						<p>This value will appear in the body or message of the email. It can be static and/or dynamic and can include: variables, EE tags, and conditionals. Anything you would expect in normal template can be used here too. Just be sure you use the 'parcel:' prefix. Ex: {parcel:title}</p>
					</div>

					<div style="position:relative">		
						<textarea name="message" id="message"><?php echo $template->message?></textarea>

						<div class="flyout">
							<a href="#" class="tab">+</a>
							
							<a href="#" class="close">&times; close</a>

							<ul class="toggle">
								<li><a href="#with" class="first">{var}</a></li>
								<li><a href="#without" class="last">var</a></li>
							</ul>

							<ul class="data">
							<?php foreach($template->fields() as $field): ?>
								<li><a href="#<?php echo $field->field_name?>"><?php echo $field->field_label?></a></li>
							<?php endforeach; ?>
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

					<input type="text" name="subject" id="subject" value="<?php echo form_prep($template->subject) ?>" />
				</li>
			</ul>

		</div>
		
		<div class="margin-top hooks group">
			
			<h2>Email Trigger</h2>
				
				
			<div class="columns">
				
				<ul class="column third">
					<li>
						<h3><a href="#installed" class="help">Installed Hooks <span>(?)</span></a></h3>			
						<select name="installed_hook" class="onchange" data-group=".hook-panel" data-default="postmaster_base_hook">
							<option value="">--</option>
						<?php foreach($template->hooks() as $hook): ?>
							<option value="<?php echo $hook->get_name()?>" <?php if($hook->get_name() == $template->installed_hook): ?>selected="selected"<?php endif; ?>><?php echo $hook->get_title()?></option>
						<?php endforeach; ?>
						</select>
					</li>
				</ul>
				
				<ul class="column third">				
					<li>
						<h3><a href="#user_defined" class="help">User Defined Hook <span>(?)</span></a></h3>
						<input type="text" name="user_defined_hook" value="<?php echo $template->user_defined_hook?>" />
					</li>
				</ul>
				
				<div class="column third">
					<h3><a href="#priority" class="help">Priority <span>(?)</span></a></h3>
						
						<select name="priority">
						<?php foreach($template->priorities() as $priority): ?>
							<option value="<?php echo $priority?>" <?php if($priority== $template->priority): ?>selected="selected"<?php endif; ?>><?php echo $priority?></option>
						<?php endforeach; ?>
						</select>
				</div>
			
			</div>
			
			<div class="hook-settings clear">
				<?php foreach($template->hooks(TRUE) as $hook): ?>
				
					<?php if($hook->display_settings($template->settings)): ?>
						<div class="margin-top hook-panel" id="<?php echo $hook->get_name()?>_panel">
							<h3><?php echo $hook->get_title()?> Settings</h3>
							<?php echo $hook->display_settings($template->settings); ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
				
			</div>
			
			<div id="installed" class="help-text">
				
				<h2>Installed Hooks</h2>
				
				<p>Installed hooks have improved functionality, and can pass variables to the email template. EE tags, snippets, and global variables are also accepted. If you don't see the hook you need, you can use the API to build your own hook. Contact <a href="mailto:support@objectivehtml.com">support@objectivehtml.com</a> if you need custom or advanced integration.</p>
				
			</div>
			
			<div id="user_defined" class="help-text">
				
				<h2>User Defined Hooks</h2>
				
				<p>User defined hooks only have only the default functionality. No template variables are passed within user defined hooks, so only EE tags, snippets, and global variables are accepted. Any hook from first or third-parties may be used to send emails. Installed hooks will take precedence if one is defined.</p>
				
			</div>
			
			<div id="priority" class="help-text">
				
				<h2>Priority</h2>
				
				<p>Priority used to determine when the same two hooks are fired. Priority goes in order of importance from least to greatest. So 1 is the most important, and 10 being the EE default. When in doubt, select '1' to ensure Postmaster hooks get executed before everything else.</p>
			</div>
			
		</div>
		
		<div class="margin-top service clear">
		
			<h2><a href="#email_service" class="help">Email Service <span>(?)</span></a></h2>

			<div class="help-text" id="email_service">
				<h2>Email Service</h2>
				<p>This is the method used to send an email. By default, ExpressionEngine is used to send email. This can be simple and effective for some. But to reliably send emails to thousands of people, you will need to use a third-party service.
			</div>
			
			<select name="service" id="service" class="onchange" data-group=".service-panel">		
			<?php foreach($template->services() as $service): ?>
				<option value="<?php echo $service->get_name() ?>" <?php if($template->service == $service->get_name()): ?>selected="selected"<?php endif; ?>><?php echo $service->get_title() ?></option>
			<?php endforeach; ?>
			</select>


			<div class="service-panels">
			
				<?php foreach($template->services() as $service): ?>
				<div class="service-panel" id="<?php echo $service->get_name() ?>_panel">
	
					<h3><?php echo $service->get_title() ?> Settings</h3>
	
					<?php echo $service->get_description() ?>
	
					<?php echo $service->display_settings($template->settings, $template) ?>
	
				</div>
				<?php endforeach; ?>
				
			</div>

		</div>
		
		<input type="hidden" name="id" value="<?php echo $template->id?>" />
		<input type="hidden" name="site_id" value="<?php echo $template->site_id?>" />
		<input type="hidden" name="return" value="<?php echo $template->return?>" />

		<button type="submit" class="submit float-right"><?php echo $template->button?></button>
		<button type="button" class="refresh submit float-right margin-right">Refresh Preview</button>
	
	</fieldset>
	
</form>

<?php if(version_compare(APP_VER, '2.8.0', '<')): ?>
<script type="text/javascript">
	Postmaster.editorSettings = <?php echo $template->editor_settings?>;
	Postmaster.settings       = <?php echo json_encode($template->settings)?>;
	Postmaster.parser		  = '<?php echo $template->parser_url?>';
</script>
<?php endif; ?>