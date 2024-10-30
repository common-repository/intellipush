(function($) {
	'use strict';
	var $doc = $(document);
	var $parent = $('#acf-group_intellipush_tools_woocommerceOrderExport');
	var $action = $parent.find('.acf-field-intellipush-tools-woocommerceOrderExport-action');
	if(!acf) {return}
	$(window).off('beforeunload.intellipushToolsWoocommerceOrderExportInProgress');
	var addToContactlist = function(args) {
		args.paged++;
		$.ajax({
			type: 'POST',
			url: acf.get('ajaxurl'),
			data: args,
			dataType: 'json',
			success: function(response) {
				var $status = null;

				if (response.error) {
					$status = $action.find('.status-error');
					$status.html(response.error).removeClass('ip--display-none').siblings('.status').addClass('ip--display-none');
					return false;
				}

				if (!response.total && response.paged === 1) {
					$status = $action.find('.status-no-order');
					$status.removeClass('ip--display-none').siblings('.status').addClass('ip--display-none');
				} else {
					if(response.paged !== response.max_num_pages) {
						$status = $action.find('.status-in-progress');
						$status.removeClass('ip--display-none').siblings('.status').addClass('ip--display-none');
						$status.find('.status-in-progress-exported').html(response.orders.length * response.paged);
						$status.find('.status-in-progress-total').html(response.total);
						args['contactlist'] = response.contactlist;
						addToContactlist(args);
						$(window).on('beforeunload.intellipushToolsWoocommerceOrderExportInProgress', false);
					} else {
						$status = $action.find('.status-completed');
						$status.removeClass('ip--display-none').siblings('.status').addClass('ip--display-none');
						$status.find('.status-completed-exported').html(response.total);
						$status.find('.status-completed-total').html(response.total);
						$(window).off('beforeunload.intellipushToolsWoocommerceOrderExportInProgress');
						$action.find('.acf-button').removeClass('button-disabled');
					}
				}
			}
		});
	}
	
	$action.on('click', '.acf-button:not(.button-disabled)', function(){
		var args = {
			action: 'intellipush_tools_woocommerceOrderExport_addToContactlist',
			limit: 100,
			paged: 0,
			orderStatus: $('#acf-field_intellipush_tools_woocommerceorderexport_orderstatus').val(),
			country: $('#acf-field_intellipush_tools_woocommerceorderexport_country').val(),
			dateBefore: $('#acf-field_intellipush_tools_woocommerceorderexport_datebefore').val(),
			dateAfter: $('#acf-field_intellipush_tools_woocommerceorderexport_dateafter').val(),
			contactlist: $('#acf-field_intellipush_tools_woocommerceorderexport_contactlist').val()
		};

		if (args.contactlist) {
			addToContactlist(args);
			$action.find('.acf-button').addClass('button-disabled');
		} else {
			$action.find('.status-no-contactlist').removeClass('ip--display-none').siblings('.status').addClass('ip--display-none');
		}
		return false;
	});



})(jQuery);
