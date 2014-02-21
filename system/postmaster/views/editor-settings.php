<form action="<?php echo $action?>" method="post" class="group">
	<h3>Text Editor Default Settings</h3>
	
	<p>Postmaster uses a robust text editor to create and maintain the e-mail parcels (templates). You can adjust any of the following settings to fit your individual needs and preference. For more information regarding the text editor, visit <a href="http://codemirror.net/doc/manual.html">CodeMirror.net</a></p>

	<table class="mainTable" cellpadding="0" cellspacing="0">
		
		<input type="hidden" name="XID" value="<?php echo $xid?>">
	
		<thead>
			<tr>
				<th>Preference</th>
				<th>Setting</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="45%">
					<label for="interval">Preview Interval</label>
					<p>With this setting you can change the rate at which the preview is refreshed. This number represents the number of milliseconds the refresh will occur after the user is finished typing. Obviously a lower interval will result in a higher number of HTTP requests to the server.</p>
				</td>
				<td><?php echo $settings['interval_input']?></td>
			</tr>
			<tr>
				<td width="45%">
					<label for="theme">Theme</label>
					<p>You can easily edit the default theme for the text editor by adjusting this setting.</p>
				</td>
				<td><?php echo $settings['theme_dropdown']?></td>
			</tr>
			<tr>
				<td>
					<label for="mode">Mode</label>
					<p>Override the default highlighting mode if you desire a different syntax mode.</p>
				</td>
				<td><?php echo $settings['mode_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="value">Value</label>
					<p>This value will be used as the default value for each new template parcel.</p>
				</td>
				<td><?php echo $settings['value_text']?></td>
			</tr>
			<tr>
				<td>
					<label for="value">Height</label>
					<p>This changed the height of the text editor.</p>
				</td>
				<td><?php echo $settings['height_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="indentUnit">Indent Unit</label>
					<p>How many spaces a block (whatever that means in the edited language) should be indented. The default is 2.</p>
				</td>
				<td><?php echo $settings['indentUnit_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="smartUnit">Smart Indent</label>
					<p>Whether to use the context-sensitive indentation that the mode provides (or just indent the same as the line before). Defaults to true.</p>
				</td>
				<td><?php echo $settings['smartUnit_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="tabSize">Tab Size</label>
					<p>The width of a tab character. Defaults to 4.</p>
				</td>
				<td><?php echo $settings['tabSize_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="indentWithTabs">Indent with Tabs</label>
					<p>Whether, when indenting, the first N*tabSize spaces should be replaced by N tabs. Default is false.</p>
				</td>
				<td><?php echo $settings['indentWithTabs_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="electricChars">Electric Chars</label>
					<p>Configures whether the editor should re-indent the current line when a character is typed that might change its proper indentation (only works if the mode supports indentation). Default is true.</p>
				</td>
				<td><?php echo $settings['electricChars_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="autoClearEmptyLines">autoClearEmptyLines</label>
					<p>When turned on (default is off), this will clear automatically clear lines consisting only of whitespace when the cursor leaves them. This is mostly useful to prevent auto indentation from introducing trailing whitespace in a file.</p>
				</td>
				<td><?php echo $settings['autoClearEmptyLines_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="keyMap">Key Map</label>
					<p>Configures the keymap to use. The default is "default", which is the only keymap defined in codemirror.js itself.</p>
				</td>
				<td><?php echo $settings['keyMap_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="lineWrapping">Line Wrapping</label>
					<p>Whether the text editor should scroll or wrap for long lines. Defaults to false (scroll).</p>
				</td>
				<td><?php echo $settings['lineWrapping_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="lineNumbers">Line Numbers</label>
					<p>Whether to show line numbers to the left of the editor.</p>
				</td>
				<td><?php echo $settings['lineNumbers_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="firstLineNumber">First Line Number</label>
					<p>At which number to start counting lines. Default is 1.</p>
				</td>
				<td><?php echo $settings['firstLineNumber_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="gutter">Gutter</label>
					<p>   Can be used to force a 'gutter' (empty space on the left of the editor) to be shown even when no line numbers are active.</p>
				</td>
				<td><?php echo $settings['gutter_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="fixedGutter">Fixed Gutter</label>
					<p>When enabled (off by default), this will make the gutter stay visible when the document is scrolled horizontally.</p>
				</td>
				<td><?php echo $settings['fixedGutter_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="matchBrackets">Match Brackets</label>
					<p>Determines whether brackets are matched whenever the cursor is moved next to a bracket.</p>
				</td>
				<td><?php echo $settings['matchBrackets_bool']?></td>
			</tr>
			<tr>
				<td>
					<label for="pollInterval">Poll Interval</label>
					<p>Indicates how quickly the text editor should poll its input textarea for changes. Most input is captured by events, but some things, like IME input on some browsers, doesn't generate events that allow the text editor to properly detect it. Thus, it polls. Default is 100 milliseconds.</p>
				</td>
				<td><?php echo $settings['pollInterval_input']?></td>
			</tr>
			<tr>
				<td>
					<label for="undoDepth">Undo Depth</label>
					<p>The maximum number of undo levels that the editor stores. Defaults to 40.</p>
				</td>
				<td><?php echo $settings['undoDepth_input']?></td>
			</tr>
		</tbody>
	</table>
	
	<input type="hidden" name="return" value="<?php echo $return?>" />
	<button type="submit" class="submit">Update Settings</button>
</form>
