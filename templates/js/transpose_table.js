$.each({
	transposeTable: function (tableId) {        
	    var tbl = $('#' + tableId);
	    var tbody = tbl.find('tbody');
	    var oldWidth = tbody.find('tr:first td').length;
	    var oldHeight = tbody.find('tr').length;
	    var newWidth = oldHeight;
	    var newHeight = oldWidth;
	
	    var jqOldCells = tbody.find('td');        
	
	    var newTbody = $("<tbody></tbody>");
	    for(var y=0; y<newHeight; y++)
	    {
	        var newRow = $("<tr></tr>");
	        for(var x=0; x<newWidth; x++)
	        {
	            newRow.append(jqOldCells.eq((oldWidth*x)+y));
	        }
	        newTbody.append(newRow);
	    }
	
	    tbody.replaceWith(newTbody);        
    }
},$.univ._import);


