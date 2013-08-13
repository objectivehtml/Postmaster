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
	
	<fieldset class="column group sidebar">
				
		<div class="container">
			<h3><a href="#help-title" class="help"><label for="title">Task Title</label> <span>(?)</span></a></h3>
			
			<input type="text" name="title" id="title" value="<?php echo form_prep($template->title) ?>" />
			
			<div id="help-title" class="help-text">
				
				<h2>Task Title</h2>

				<p>The Task Title is a value you can use to give each task you install some meaning or context. This field has no programatic purpose, and is strictly for you to use to know what is what.</p>
				
			</div>
			
		</div>

		<div class="container margin-top">
		
			<h3><a href="#help-task" class="help"><label for="task">Task <span>(?)</span></label></a></h3>
							
			<select name="installed_hook" class="onchange" data-group=".hook-panel" data-default="postmaster_base_hook">
				<option value="">--</option>
			<?php foreach($template->tasks() as $task): ?>
				<option value="<?php echo $task->get_name()?>" <?php if($task->get_name() == $template->task): ?>selected="selected"<? endif; ?>><?php echo $task->get_title()?></option>
			<?php endforeach; ?>
			</select>
			
		</div>
		
	</fieldset>
	
	<fieldset class="column group editor">
			
		<h2>Task Settings</h2>		
			
		<div class="margin-top hooks group">
							
			<div class="columns">
				
				<ul class="column third">
					<li>
						<h3><a href="#installed" class="help">Installed Hooks <span>(?)</span></a></h3>	
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
							<option value="<?php echo $priority?>" <?php if($priority== $template->priority): ?>selected="selected"<? endif; ?>><?php echo $priority?></option>
						<?php endforeach; ?>
						</select>
				</div>
			
			</div>
			
			<div class="hook-settings clear">
				<?php foreach($template->tasks(TRUE) as $task): ?>
				
					<?php if($task->display_settings($template->settings)): ?>
						<div class="margin-top hook-panel" id="<?php echo $task->get_name()?>_panel">
							<h3><?php echo $task->get_title()?> Settings</h3>
							<?php echo $task->display_settings($template->settings); ?>
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
				<option value="<?php echo $service->name ?>" <?php if($template->service == $service->name): ?>selected="selected"<?php endif; ?>><?php echo $service->name ?></option>
			<?php endforeach; ?>
			</select>


			<div class="service-panels">
			
				<?php foreach($template->services() as $service): ?>
				<div class="service-panel" id="<?php echo $service->name ?>_panel">
	
					<h3><?php echo $service->name ?> Settings</h3>
	
					<?php echo $service->description ?>
	
					<?php echo $service->display_settings($template->settings, $template) ?>
	
				</div>
				<?php endforeach; ?>
				
			</div>

		</div>
		
		<input type="hidden" name="id" value="<?php echo $template->id?>" />
		<input type="hidden" name="site_id" value="<?php echo $template->site_id?>" />
		<input type="hidden" name="return" value="<?php echo $template->return?>" />

		<button type="submit" class="submit float-right"><?php echo $template->button?></button>
	
	</fieldset>
	
</form>

<script type="text/javascript">

	Postmaster.editorSettings = <?php echo $template->editor_settings?>;
	Postmaster.settings       = <?php echo json_encode($template->settings)?>;
	Postmaster.parser		  = '<?php echo $template->parser_url?>';

</script>
