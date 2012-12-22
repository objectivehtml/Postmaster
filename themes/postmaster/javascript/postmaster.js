/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.1
 * @build		20120901
 */

$(document).ready(function() {

	 $.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = $.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ? function(s) { return s; } : decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };

	var toRefresh = true;
	var timer = false;

	if(typeof CodeMirror != "undefined") {
		Postmaster.editor = CodeMirror.fromTextArea($('#message').get(0), Postmaster.editorSettings);

		if(Postmaster.editor.getValue() != "") {
			$('.refresh').click();
		}

		if(Postmaster.editorSettings.interval > 0) {
			Postmaster.editor.setOption('onChange', function() {
				if(timer) clearTimeout(timer);

				toRefresh = false;

				timer = setTimeout(function() {
					Postmaster.refresh();
					toRefresh = true;
				}, Postmaster.editorSettings.interval);
			});
		}
		
		$.extend(Postmaster, {
			
			refresh: function() {
				var entryId = $('#test_entry').val();
				var message = Postmaster.editor.getValue();
				var iframe  = $('.pain').get(0);
				var url 	= Postmaster.parser+'&entry_id='+entryId;
				
				$.post(Postmaster.parser, {
					message: message
				}, function(data) {
					iframe.src = url;
				});
			},

			replaceSelection: function(text) {
				Postmaster.editor.replaceSelection(text);
				Postmaster.editor.focus();
			},

			setTheme: function(theme) {

				if(!theme) {
					var theme = $('.theme').val();
				}

				Postmaster.editor.setOption('theme', theme);
			},

			toggleFlyout: function(isOpen, callback) {
				var $flyout = $('.flyout');
				var $tab 	= $('.tab');

				if(isOpen) {
					$flyout.animate({
						right: -$flyout.outerWidth()
					}, function() {
						$flyout.removeClass('open');
						$tab.animate({
							left: -37
						}, function() {
							if(typeof callback == "function")
								callback();
						});
					});
				}
				else {
					$tab.animate({
						left: 0
					}, function() {
						$flyout.addClass('open');
						$flyout.animate({
							right: 0
						}, function() {
							if(typeof callback == "function")
								callback();
						});
					});
				}
			}

		});

	}

	$('.flyout .toggle a').click(function() {
		var $t = $(this);

		$('.flyout .toggle a').removeClass('active');
		
		$t.addClass('active');

		return false;
	});
	
	$('.flyout .tab').click(function() {

		Postmaster.toggleFlyout(false);

		return false;
	});

	$('.flyout .close').click(function() {

		Postmaster.toggleFlyout(true);

		return false;
	});

	$('.flyout').css('right', -$('.flyout').outerWidth());

	$('.flyout .toggle li:first-child a').click();

	$('.flyout .data a').live('click', function() {
		var $t = $(this);
		var field = $t.attr('href').replace('#', '');

		var option = $('.flyout .toggle .active').attr('href').replace('#', '');

		if(option == 'with') {
			field = '{parcel:'+field+'}';
		}

		Postmaster.replaceSelection(field);

		return false;
	});

	$('.advanced-toggle').click(function() {
		var $t = $(this);
		var css = $t.data('class');
		var val = $t.html();
		
		if(val.match(/^(s|S)how/)) {
			$t.html(val.replace(/^(s|S)how/, 'Hide'));
		}
		else {
			$t.html(val.replace(/^(h|H)ide/, 'Show'));
		}
		
		$('.'+css).toggle();
	});
	
	$('select[name="channel"]').change(function() {
		
		var $t = $(this);
		var id = $t.val();
		
		Postmaster.getCategories(id);
		
	});

	$('select[name="theme"]').change(function() {
		Postmaster.setTheme();
	});
	
	$('select[name="theme"]').change();

	/*
	$('select[name="service"]').change(function() {
		var $t = $(this);
		var val = $t.val();

		$('.service-panel').hide();
		$('#'+val+'_panel').show();
	});
	
	$('select[name="service"]').change();
	
	$('select[name="installed_hook"]').change(function() {
		var $t = $(this);
		var val = $t.val();
		
		if(val == "") {
			val = 'postmaster_base_hook';
		}

		$('.hook-panel').hide();
		$('#'+val+'_panel').show();
	});

	$('select[name="installed_hook"]').change();
	*/
	
	$('select.onchange').change(function() {
		var $t       = $(this);
		var val      = $t.val();
		var _default = $t.data('default');
		var hide 	 = $t.data('hide');
		var group 	 = $t.data('group');
		
		if(val == "" && _default) {
			val = _default;
		}
		
		if(hide) {
			$(hide).hide();
		}
		
		if(group) {
			$(group).hide();
		}
		
		if(val != "") {
			$('#'+val+'_panel').show();
		}
	});
	
	$('select.onchange').change();
	
	$('#test_entry').change(function() {
		Postmaster.refresh();
	});

	$('.text-input').keypress(function(e) {
		if(e.keyCode == 13) {
			$(this).parents('form').submit();
			return false;
		}
	});

	$('select[name="channel_id"]').change(function() {
		var $t  = $(this);
		var val = $t.val();

		var $categories    = $('.categories div');
		var $statuses      = $('.statuses div');
		var $member_groups = $('.member_groups div');
		var $fields		   = $('.flyout .data');
		var $entries	   = $('#test_entry');

		$categories.html('');
		
		var catCount = 0;
		
		if(Postmaster.categories[val]) {
			$.each(Postmaster.categories[val], function(i, category) {
				$categories.append('<label><input type="checkbox" name="category[]" value="'+category[0]+'" />'+category[1]+'</label>');
				catCount++;
			});
		}
		
		if(catCount == 0) {
			$categories.html('<p>This channel has no categories.</p>');
		}

		$statuses.html('');

		if(Postmaster.statuses[val].length > 0) {
			$.each(Postmaster.statuses[val], function(i, status) {
				$statuses.append('<label><input type="checkbox" name="statuses[]" value="'+status.status+'" />'+status.status+'</label>');
			});
		}
		else {
			$statuses.html('<p>This channel has no statuses.</p>');
		}

		$member_groups.html('');

		$.each(Postmaster.groups[val], function(i, group) {
			$member_groups.append('<label><input type="checkbox" name="member_group[]" value="'+group.group_id+'" />'+group.group_title+'</label>');
		});

		$fields.html('');

		$.each(Postmaster.fields[val], function(i, field) {
			$fields.append('<li><a href="#'+field.field_name+'">'+field.field_label+'</a></li>');
		});

		$entries.html('');

		$.each(Postmaster.entries[val], function(i, entry) {
			$entries.append('<option value="'+entry.entry_id+'">'+entry.title+'</option>');
		});
	});

	$('.refresh').click(function() {

		Postmaster.refresh();

		return false;

	});

	$('a.help').qtip({
		content: {
			text: function(api) {
				return $($(this).attr('href')).html();
			}
		}
	});

	$('.tooltip').each(function() {
		$(this).qtip({
			position: {
				my: 'bottom center',
				at: 'top center',
				target: $(this)
			},
			style: {
				classes: 'ui-tooltip-youtube ui-tooltip-shadow tooltip'
			}
		});
	});

	$('a.delete').click(function() {
		var url = $(this).attr('href');

		$('.delete.dialog').dialog({
			title: 'Confirmation',
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
				},
				'Yes, Delete': function() {
					window.location = url;
				}
			}
		});

		return false;
	});

	if(Postmaster.refresh) {
		Postmaster.refresh();
	}

});