InterfaceBuilder = function(elem) {
	
	var $document = $(document);
	var $wrapper  = $(elem);
	
	/*-------------------------------------------    
		Properties
	-------------------------------------------*/
	
	/*-------------------------------------------    
		Methods
	-------------------------------------------*/
	
	/*-------------------------------------------    
		Events
	-------------------------------------------*/
	
	/*-------------------------------------------    
		Constructor
	-------------------------------------------*/
			
	$wrapper.find(".add-row").click(function() {
		
		var $body = $(this).prev().find('tbody');
		var row = $("<tr />");
		var index = $body.find("tr").length;

		row.append("<td><div class=\"ib-drag-handle\"></div></td>");

		for(var x = 1; x < $head.find("th").length; x++) {
			row.append("<td><input type=\"text\" name=\"'.$this->name.'["+index+"]["+columns[x-1].name+"]\" value=\"\" /></td>");
		}

		$body.append(row);

		return false;
	});
}