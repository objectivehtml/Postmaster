<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>

<script type="text/javascript">
	
	$(document).ready(function() {
			
		$('.reveal').click(function(e) {
			var id = $(this).attr('href');
			
			$(id).reveal();
			
			e.preventDefault();
		});
		
	});
	
</script>


<style type="text/css">
	
	.ui-doctag-column {
		float: left;
	}
	
	.ui-doctag-sidebar {
		width: 20%;
		
	}
			
	.ui-doctag-sidebar ul {
		margin: 10px 30px 0 0;
	}
	
	.ui-doctag-sidebar a {
		padding: 10px 10px 10px;
		position: relative;
		font-size: 15px;
		border-right: 1px solid rgb(222, 222, 222);
		display: block;
		text-decoration: none !important;
		color: rgb(82, 83, 84) !important;
		text-shadow: 0 1px 0 white;
	}
	
	.ui-doctag-sidebar .ui-doctag-active,
	.ui-doctag-sidebar a.ui-doctag-active:hover {
		background: white;
		border-right-color: transparent;
	}
	
	.ui-doctag-sidebar .ui-doctag-active:after {
		left: 100%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
	}
	
	.ui-doctag-sidebar .ui-doctag-active:after {
		border-left-color: #fff;
		border-width: 19px;
		top: 50%;
		margin-top: -19px;
	}
	
	.ui-doctag-sidebar a:hover {
		background: rgb(201, 205, 207);
		border-right-color: transparent;
	}

	.ui-doctag-page {
		width: 80%;
	}
	
	.ui-doctag-contents {
		padding: 10px 20px;
		font-size: 15px;
	}
	
	.ui-doctag-contents img[alt="fullscreen"] {
		width: 100%;	
	}
	
	.ui-doctag-contents img[alt="float-right"] {
		float: right;
		clear: right;
		margin-left: 1em;	
		margin-bottom: 1em;
	}
	
	.ui-doctag-contents p {
		line-height: 1.45em;
	}
	
	.ui-doctag-contents ol,
	.ui-doctag-contents ul {
		margin: 10px 0 5px 22px;
	}
	
	.ui-doctag-contents ul li {
		list-style: disc;	
	}
	
	.ui-doctag-contents li {
		padding-bottom: .5em;
	}
	.ui-doctag-contents h1 {
		margin-bottom: 20px;
	}
	
	.ui-doctag-contents h3 {
		clear: both;
	}
	
	.ui-doctag-contents h4 {
	}
	
	.ui-doctag-contents dl {
		margin-bottom: 1em;
	}
	
	.ui-doctag-contents dt {
		display: block;
		font-family: monospace;
		margin: 1em .25em 0;
		color: rgb(55, 68, 77);
	}
	
	.ui-doctag-contents dd {
		padding: 1em .25em;
		margin: 0;
		border-bottom: 1px solid rgb(204, 208, 210);
		font-size: .9em;
	}
	
	.ui-doctag-contents dd:last-child {
		border-bottom: 0;
	}
	
	.ui-doctag-tag {
		font-size: 1.6em !important;
		margin-bottom: 1em !important;
	}
	
	.ui-doctag-tag a {
		text-decoration: none !important;
	}
	
	.ui-doctag-overview table { margin-top: 1.5em; }
	
	.ui-doctag-overview h3 { margin-top: 1em; }
	
	.reveal-modal-bg { position: fixed; height: 100%; width: 100%; background: #000; background: rgba(0, 0, 0, 0.45); z-index: 40; display: none; top: 0; left: 0; }

	.reveal-modal { background: white; visibility: hidden; top: 100px; left: 50%; margin-left: -260px; width: 520px; position: absolute; z-index: 41; padding: 30px; -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.4); -moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.4); box-shadow: 0 0 10px rgba(0, 0, 0, 0.4); }
	
	.reveal-modal h3 { font-size: 1.5em !important; }
	.reveal-modal textarea { height: 300px; }
	.reveal-modal *:first-child { margin-top: 0; }
	.reveal-modal *:last-child { margin-bottom: 0; }
	.reveal-modal .close-reveal-modal { font-size: 22px; font-size: 1.7rem; line-height: .5; position: absolute; top: 8px; right: 11px; color: #aaa; text-shadow: 0 -1px 1px rbga(0, 0, 0, 0.6); font-weight: bold; cursor: pointer; text-decoration: none !important; }
	.reveal-modal.small { width: 30%; margin-left: -10%; }
	.reveal-modal.medium { width: 40%; margin-left: -20%; }
	.reveal-modal.large { width: 60%; margin-left: -30%; }
	.reveal-modal.expand { width: 90%; margin-left: -45%; }
	.reveal-modal .row { min-width: 0; }

</style>

<div class="ui-doctag-sidebar ui-doctag-column">
	
	<h2>Contents</h2>
	
	<ul>
		<li><a href="<?php echo $index_url?>" <?php echo !$tag || $tag == 'Overview' ? 'class="ui-doctag-active"' : NULL?>>Overview</a></li>
	<?php foreach($page->methods as $method_name => $method): ?>
		<li><a href="<?php echo $method->url?>" <?php echo $method->selected ? 'class="ui-doctag-active"' : NULL?>><?php echo $method->title?></a></li>
	<?php endforeach; ?>
	</ul>
	
</div>

<div class="ui-doctag-page ui-doctag-column">

	<div class="ui-doctag-contents">
		
		<?php echo $page_overview?>
		
		<?php foreach($page->methods as $method_name => $method): ?>
			<?php if($method->selected): ?>
			<div class="ui-doctag-method" id="<?php echo $method_name?>">
				
				<h3 class="ui-doctag-tag"><a href="<?php echo $method->url?>"><?php echo $method->tag?></a></h3>
			
				<div class="ui-doctag-overview">
					
					<?php echo $method->documentation?>
								
					<?php echo $method->snippet_table?>
					
				</div>
				
			</div>
			<?php endif; ?>
		<?php endforeach; ?>
		
	</div>

</div>

<?php foreach($page->methods as $method_name => $method): ?>
	<?php foreach($method->snippets as $file_name => $snippet): ?>		
	<div class="reveal-modal" id="<?php echo preg_replace("/\\.[\\w]*/u", "", $file_name)?>">
		<a class="close-reveal-modal">&#215;</a>
		<h3><?php echo LD.preg_replace("/\\.[\\w]*/u", "", $file_name).RD;?></h3>
		<textarea><?php echo $snippet?></textarea>
	</div>
	<?php endforeach; ?>
<?php endforeach; ?>

<script type="text/javascript">
	
/*
 * jQuery Reveal Plugin 1.0
 * www.ZURB.com
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/


(function($) {

/*---------------------------
 Defaults for Reveal
----------------------------*/
	 
/*---------------------------
 Listener for data-reveal-id attributes
----------------------------*/

	$('a[data-reveal-id]').live('click', function(e) {
		e.preventDefault();
		var modalLocation = $(this).attr('data-reveal-id');
		$('#'+modalLocation).reveal($(this).data());
	});

/*---------------------------
 Extend and Execute
----------------------------*/

    $.fn.reveal = function(options) {
        
        
        var defaults = {  
	    	animation: 'fadeAndPop', //fade, fadeAndPop, none
		    animationspeed: 300, //how fast animtions are
		    closeonbackgroundclick: true, //if you click background will modal close?
		    dismissmodalclass: 'close-reveal-modal' //the class of a button or element that will close an open modal
    	}; 
    	
        //Extend dem' options
        var options = $.extend({}, defaults, options); 
	
        return this.each(function() {
        
/*---------------------------
 Global Variables
----------------------------*/
        	var modal = $(this),
        		topMeasure  = parseInt(modal.css('top')),
				topOffset = modal.height() + topMeasure,
          		locked = false,
				modalBG = $('.reveal-modal-bg');

/*---------------------------
 Create Modal BG
----------------------------*/
			if(modalBG.length == 0) {
				modalBG = $('<div class="reveal-modal-bg" />').insertAfter(modal);
			}		    
     
/*---------------------------
 Open & Close Animations
----------------------------*/
			//Entrance Animations
			modal.bind('reveal:open', function () {
			  modalBG.unbind('click.modalEvent');
				$('.' + options.dismissmodalclass).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({'top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"top": $(document).scrollTop()+topMeasure + 'px',
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					}
					if(options.animation == "fade") {
						modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+topMeasure});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					} 
					if(options.animation == "none") {
						modal.css({'visibility' : 'visible', 'top':$(document).scrollTop()+topMeasure});
						modalBG.css({"display":"block"});	
						unlockModal()				
					}
				}
				modal.unbind('reveal:open');
			}); 	

			//Closing Animation
			modal.bind('reveal:close', function () {
			  if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"top":  $(document).scrollTop()-topOffset + 'px',
							"opacity" : 0
						}, options.animationspeed/2, function() {
							modal.css({'top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
							unlockModal();
						});					
					}  	
					if(options.animation == "fade") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"opacity" : 0
						}, options.animationspeed, function() {
							modal.css({'opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
							unlockModal();
						});					
					}  	
					if(options.animation == "none") {
						modal.css({'visibility' : 'hidden', 'top' : topMeasure});
						modalBG.css({'display' : 'none'});	
					}		
				}
				modal.unbind('reveal:close');
			});     
   	
/*---------------------------
 Open and add Closing Listeners
----------------------------*/
        	//Open Modal Immediately
    	modal.trigger('reveal:open')
			
			//Close Modal Listeners
			var closeButton = $('.' + options.dismissmodalclass).bind('click.modalEvent', function () {
			  modal.trigger('reveal:close')
			});
			
			if(options.closeonbackgroundclick) {
				modalBG.css({"cursor":"pointer"})
				modalBG.bind('click.modalEvent', function () {
				  modal.trigger('reveal:close')
				});
			}
			$('body').keyup(function(e) {
        		if(e.which===27){ modal.trigger('reveal:close'); } // 27 is the keycode for the Escape key
			});
			
			
/*---------------------------
 Animations Locks
----------------------------*/
			function unlockModal() { 
				locked = false;
			}
			function lockModal() {
				locked = true;
			}	
			
        });//each call
    }//orbit plugin call
})(jQuery);
</script>