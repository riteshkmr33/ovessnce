/* Pagination functions starts here */
paginateTransactions("div#referredfrom-pagination ul", bookingsCallback, $('input#bookings').val());
paginateTransactions("div#referredto-pagination ul", subscriptionsCallback, $('input#subscriptions').val());

function paginateTransactions(element, callbackFunction, totalNewsletters)
{
	$(element).pagination(totalNewsletters, {
		callback: callbackFunction,
		items_per_page: 5,
		num_display_entries: 5,
		num_edge_entries: 2,
		current_page : $('input#page').val(),
		prev_text: '&lt;',
		next_text: '&gt;'
	});
}

function bookingsCallback(page_index, jq)
{
	$("div.pagination-list ul li:empty, div.pagination-list ul span").remove(); // Removing blank elements
	$('input#page').val(page_index);  // storing current page number
	
	var records = Array();
	var items_per_page = 5;
	var page = parseInt(page_index)+1;
	
	$.ajax({
		url : OVEconfig.BASEURL+'/practitioner/transactions/',
		type : 'POST',
		async : false,
		data : {action: 'bookings', page : page, items : items_per_page},
		dataType : 'json',
		success : function(data){records = data;},
		error : function(xhr, errorType, errorMsg) {console.log(errorMsg)}
	});
	
	var max_elem = Math.min((page_index+1) * items_per_page, records.length);
	var newcontent = Array();
	
	if (records.length > 0) {
		// Iterate through a selection of the content and build an HTML string
		for(var i=0;i<max_elem;i++)   //page_index*items_per_page
		{
			switch (records[i].payment_history_status_id) {
				case '7' :
					var status = 'Paid';
					break;
					
				case '8' :
					var status = 'Unpaid';
					break;
					
				case '11' :
					var status = 'Refunded';
					break;
					
				default :
					var status = 'Unpaid';
					break;
			}
			
                        var currency = (records[i].payment_history_currency != '' && records[i].payment_history_currency != 'None') ?records[i].payment_history_currency:'';
                        
			newcontent.push("<tr>");
			newcontent.push("<td>"+records[i].consumer_first_name+' '+records[i].consumer_last_name+"</td>");
			newcontent.push("<td>"+records[i].category_name+" "+records[i].duration+" Mins</td>");
			newcontent.push("<td>"+currency+" $"+records[i].price+"</td>");
			newcontent.push("<td>"+formatDate(records[i].created_date, 'Day d/m/Y h:i A')+"</td>");
			newcontent.push("<td>"+status+"</td>");
			newcontent.push("<td>");
			/*newcontent.push("<div class='select-form'><form>");
			newcontent.push("<label for='select-all'>");
			newcontent.push("<input type='checkbox' class='checkReferredFrom' value='"+records[i].id+"'><span></span>");
			newcontent.push("</label>");
			newcontent.push("</form></div>");*/
			newcontent.push("<span class='btn-invoice' id='generateInvoice' data-val='"+records[i].id+"' onclick='window.location = \""+OVEconfig.BASEURL+"/booking/invoice/"+records[i].id+"\"'>D</span>");
			newcontent.push("</td></tr>");
			
		}
	} else {
		newcontent.push('<tr><td colspan="6"> No Records Found</td></tr>');
	}
	
	// Replace old content with new content
	$('div.services-data > table#referredFromTable >tbody').html(newcontent.join(''));
	
	// Prevent click eventpropagation
	return false;
}

function subscriptionsCallback(page_index, jq)
{
	$("div.pagination-list ul li:empty").remove(); // Removing blank elements
	$('input#page').val(page_index);  // storing current page number
	
	var records = Array();
	var items_per_page = 5;
	var page = parseInt(page_index)+1;
	
	$.ajax({
		url : OVEconfig.BASEURL+'/practitioner/transactions/',
		type : 'POST',
		async : false,
		data : {action: 'subscriptions', page : page, items : items_per_page},
		dataType : 'json',
		success : function(data){records = data;},
		error : function(xhr, errorType, errorMsg) {console.log(errorMsg)}
	});
	
	var max_elem = Math.min((page_index+1) * items_per_page, records.length);
	var newcontent = Array();
	
	if (records.length > 0) {
		// Iterate through a selection of the content and build an HTML string
		for(var i=0;i<max_elem;i++)   //page_index*items_per_page
		{
			switch (records[i].payment_history_status_id) {
				case '7' :
					var status = 'Paid';
					break;
					
				case '8' :
					var status = 'Unpaid';
					break;
					
				case '11' :
					var status = 'Refunded';
					break; 
					
				default :
					var status = 'Unpaid';
					break;
			}
			
			newcontent.push("<tr>");
			newcontent.push("<td>"+records[i].invoice_details_sale_item_details.replace('Subscription Plan - ', '')+"</td>");
			newcontent.push("<td>"+records[i].payment_history_currency+" $"+records[i].payment_history_amount_paid+"</td>");
			newcontent.push("<td>"+formatDate(records[i].payment_history_payment_date, 'Day d/m/Y')+"</td>");
			newcontent.push("<td>"+status+"</td>");
			newcontent.push("<td>");
			/*newcontent.push("<div class='select-form'><form>");
			newcontent.push("<label for='select-all'>");
			newcontent.push("<input type='checkbox' class='checkReferredTo' value='"+records[i].id+"'><span></span>");
			newcontent.push("</label>");
			newcontent.push("</form></div>");*/
			newcontent.push("<span class='btn-invoice' id='generateInvoice' data-val='"+records[i].id+"' onclick='window.location = \""+OVEconfig.BASEURL+"/membership/invoice/"+records[i].id+"\"'>D</span>");
			newcontent.push("</td></tr>");
			
		}
	} else {
		newcontent.push('<tr><td colspan="5"> No Records Found</td></tr>');
	}
	
	// Replace old content with new content
	$('div.services-data > table#referredToTable >tbody').html(newcontent.join(''));
	
	// Prevent click eventpropagation
	return false;
}
/* Pagination functions ends here */
