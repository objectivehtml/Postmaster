var InterfaceBuilder = function() {
	
	var IB = this;
	
	/*------------------------------------------
	 *	Methods
	/* -------------------------------------- */
	
	IB.matrix = function(obj) {
	
		$(obj).each(function() {
			
			var $wrapper = $(this);
		
			var $body = $wrapper.find("tbody");
			var $head = $wrapper.find("thead tr");
			var name = $wrapper.data('name');
			var columns = [];
			
			$head.find('th').each(function(i) {
				if(i > 0) {
					columns.push($(this).data('column-name'));
				}
			});
			
			$wrapper.find(".ib-add-row").click(function() {		
				var row = $("<tr />");
				var index = $body.find("tr").length;
				
				row.append("<td><div class=\"ib-drag-handle\"></div></td>");
		
				for(var x = 1; x < $head.find("th").length - 1; x++) {
					row.append("<td><input type=\"text\" name=\""+name+"["+index+"]["+columns[x-1]+"]\" value=\"\" class=\"ib-cell\" /></td>");
				}
		
				row.append("<td><a href=\"#"+index+"\" class=\"ib-delete-row\">Delete</a></td>");
		
				$body.append(row);
		
				return false;
			});
			
			$wrapper.find(".ib-delete-row").live('click', function() {		
				
				$(this).parent().parent().remove();
				
				$body.find('tr').each(function(index) {
					
					var td = $(this).find('td');
					var length = td.length;
					
					td.each(function(i) {
						if(i > 0 && (i + 1) < length) {
							var name = $(this).find('.ib-cell').attr('name').replace(/(\[\d\]\[)/g, "\["+index+"\][");
									
							$(this).find('.ib-cell').attr('name', name);
						}	
					});
					
				});
				
				return false;
			});
		});
	}
	
	/*------------------------------------------
	 *	Construct
	/* -------------------------------------- */
	
	IB.matrix('.ib-matrix');	
}