/**
 * Admin JavaScript
 *
 * @package Ultimate_Events_Manager
 */

(function ($) {
	'use strict';

	$(document).ready(function () {

		// Add ticket
		$(document).on('click', '.uem-add-ticket', function (e) {
			e.preventDefault();

			var $container = $('#uem-tickets-container');
			var index = $container.find('.uem-ticket-item').length;

			var html = '<div class="uem-ticket-item" data-index="' + index + '">';
			html += '<p>';
			html += '<label>Ticket Name:</label>';
			html += '<input type="text" name="uem_tickets[' + index + '][name]" value="" class="widefat" />';
			html += '</p>';
			html += '<p>';
			html += '<label>Price:</label>';
			html += '<input type="number" name="uem_tickets[' + index + '][price]" value="0" step="0.01" min="0" class="widefat" />';
			html += '</p>';
			html += '<p>';
			html += '<label>Quantity Available:</label>';
			html += '<input type="number" name="uem_tickets[' + index + '][quantity]" value="0" min="0" class="widefat" />';
			html += '</p>';
			html += '<p>';
			html += '<label>Description:</label>';
			html += '<textarea name="uem_tickets[' + index + '][description]" class="widefat" rows="3"></textarea>';
			html += '</p>';
			html += '<button type="button" class="button uem-remove-ticket">Remove Ticket</button>';
			html += '<hr>';
			html += '</div>';

			$container.append(html);
		});

		// Remove ticket
		$(document).on('click', '.uem-remove-ticket', function (e) {
			e.preventDefault();

			if (confirm('Are you sure you want to remove this ticket?')) {
				$(this).closest('.uem-ticket-item').remove();
			}
		});

	});

})(jQuery);


/* ========== Metabox =============
   
   ============================== */



// ==== left column 

jQuery(document).ready(function ($) {
	jQuery('.event-sidebar li').on('click', function () {
		var tabID = $(this).data('tab');

		jQuery('.event-sidebar li').removeClass('active');
		jQuery(this).addClass('active');

		jQuery('.event-tab').removeClass('active');
		jQuery('#' + tabID).addClass('active');
	});
});


// ==== Venue / location ====

jQuery(document).ready(function ($) {

	jQuery('#toggleStatus').on('change', function () {
		if (jQuery(this).is(':checked')) {
			jQuery('#offlineDiv').slideUp(300);
			jQuery('#onlineDiv').slideDown(300);
		} else {
			jQuery('#onlineDiv').slideUp(300);
			jQuery('#offlineDiv').slideDown(300);
		}
	});

	/* vanue / location show hide */
	jQuery('#offline_meeting').show();
	jQuery('#vistual_on_meeting').hide();
	jQuery('#toggleStatus').on('change', function () {

		if (jQuery(this).is(':checked')) {
			jQuery('#offline_meeting').hide();
			jQuery('#vistual_on_meeting').show();
		} else {
			jQuery('#vistual_on_meeting').hide();
			jQuery('#offline_meeting').show();
		}
	});

});

//select 2 and search 
jQuery('.js-searchBox').searchBox({ elementWidth: '250' });


/* === Google Map API key toggle ====*/

/* document.addEventListener('DOMContentLoaded', function () {
	const checkbox = document.getElementById('webcu_ve_googleMap');
	const textField = document.getElementById('mapTextField');

	// Show/hide on page load based on checkbox state
	if (checkbox.checked) {
		textField.style.display = 'block';
	}

	// Show/hide on click
	checkbox.addEventListener('change', function () {
		if (this.checked) {
			textField.style.display = 'block';
		} else {
			textField.style.display = 'none';
		}
	});
}); */


// ==== ticket price ===

jQuery(function ($) {
	var $tbody = $('#webcu_tk_ticketTable tbody');

	jQuery('#webcu_tk_registrationToggle').on('click', function () {
		jQuery(this).toggleClass('webcu_tk_on');
		jQuery('#webcu_tk_registrationSection').toggle(jQuery(this).hasClass('webcu_tk_on'));
		jQuery('#webcu_tk_registration_enabled').val(jQuery(this).hasClass('webcu_tk_on') ? '1' : '0');
	});
	jQuery('#webcu_tk_advancedToggle').on('click', function () {
		jQuery(this).toggleClass('webcu_tk_on');
		var isOn = jQuery(this).hasClass('webcu_tk_on');
		jQuery('#webcu_tk_advanced_toggle').val(isOn ? '1' : '0');
		if (isOn) {
			jQuery('.webcu_tk_advanced').show();
		} else {
			jQuery('.webcu_tk_advanced').hide();
		}
	});
	function updateDraggables() {
		jQuery('#webcu_tk_ticketTable tbody tr').removeClass('webcu_tk_draggable-top');
		var first = jQuery('#webcu_tk_ticketTable tbody tr.webcu_tk_ticket-row');
		if (first.length) { first.addClass('webcu_tk_draggable-top'); }
	}

	jQuery('#webcu_tk_addTicket').on('click', function (e) {
		e.preventDefault();
		var cols = jQuery('#webcu_tk_ticketTable thead th').length;
		var timestamp = Date.now();
		var row = '<tr class=\"webcu_tk_ticket-row\">' +
			'<td><input type=\"text\" name=\"webcu_tk_tickets[' + timestamp + '][name]\" placeholder=\"Ticket\"></td>' +
			'<td><input type=\"text\" name=\"webcu_tk_tickets[' + timestamp + '][desc]\" placeholder=\"Short description\"></td>' +
			'<td><input type=\"number\" name=\"webcu_tk_tickets[' + timestamp + '][price]\" value=\"0\"></td>' +
			'<td><input type=\"number\" name=\"webcu_tk_tickets[' + timestamp + '][capacity]\" value=\"0\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"number\" name=\"webcu_tk_tickets[' + timestamp + '][default_qty]\" placeholder=\"Ex:1\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"number\" name=\"webcu_tk_tickets[' + timestamp + '][reserve_qty]\" placeholder=\"Ex:1\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"date\" name=\"webcu_tk_tickets[' + timestamp + '][sale_start_date]\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"time\" name=\"webcu_tk_tickets[' + timestamp + '][sale_start_time]\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"date\" name=\"webcu_tk_tickets[' + timestamp + '][sale_end_date]\"></td>' +
			'<td class=\"webcu_tk_advanced\"><input type=\"time\" name=\"webcu_tk_tickets[' + timestamp + '][sale_end_time]\"></td>' +
			'<td><select name=\"webcu_tk_tickets[' + timestamp + '][qty_box]\"><option>Input Box</option><option>Dropdown</option><option>Hidden</option></select></td>' +
			'<td class=\"webcu_tk_action-icons\">' +
			'<button type=\"button\" class=\"webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_tk_remove-row\">✖</button>' +
			'<button type=\"button\" class=\"webcu_tk_btn webcu_tk_btn-outline webcu_tk_btn-small webcu_tk_move-row\">☰</button>' +
			'</td>' +
			'</tr>';
		jQuery('#webcu_tk_ticketTable tbody').append(row);
		updateDraggables();
		if (jQuery('#webcu_tk_advancedToggle').hasClass('webcu_tk_on')) {
			jQuery('.webcu_tk_advanced').show();
		}
	});

	jQuery('#webcu_tk_addExtra').on('click', function (e) {
		e.preventDefault();

		jQuery('#webcu_tk_extraTable .webcu_tk_empty-row').remove();


		var timestamp = Date.now();
		var row = '<tr>' +
			'<td><input type=\"text\" name=\"webcu_tk_extras[' + timestamp + '][name]\" placeholder=\"Name\" /></td>' +
			'<td><input type=\"number\" name=\"webcu_tk_extras[' + timestamp + '][price]\" placeholder=\"Price\" /></td>' +
			'<td><input type=\"number\" name=\"webcu_tk_extras[' + timestamp + '][available_qty]\" placeholder=\"Available Qty\" /></td>' +
			'<td><select name=\"webcu_tk_extras[' + timestamp + '][qty_box]\"><option>Input Box</option><option>Dropdown</option></select></td>' +
			'<td><button type=\"button\" class=\"webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_tk_remove_extra_row\">✖</button></td>' +
			'</tr>';
		jQuery('#webcu_tk_extraTable tbody').append(row);
	});

	jQuery(document).on('click', '.webcu_tk_remove_extra_row', function () {

		if (confirm('Are you sure you want to remove this Field?')) {
			jQuery(this).closest('tr').remove();
		}

		if (jQuery('#webcu_tk_extraTable tbody tr').length === 0) {
			jQuery('#webcu_tk_extraTable tbody').append('<tr class=\"webcu_tk_empty-row\"><td colspan=\"5\">No extra service added yet.</td></tr>');
		}
	});
	jQuery(document).on('click', '.webcu_tk_remove-row', function () {

		if (confirm('Are you sure you want to remove this Ticket & Pricing?')) {
			jQuery(this).closest('tr').remove();
			updateDraggables();
		}

	});
	updateDraggables();
	var dragging = false, $dragged = null, $placeholder = null;
	var colCount = jQuery('#webcu_tk_ticketTable thead th').length;
	function createPlaceholder() {
		return jQuery('<tr class=\"webcu_tk_placeholder-row\"><td colspan=\"' + colCount + '\"></td></tr>');
	}
	jQuery(document).on('mousedown', '.webcu_tk_move-row', function (e) {
		var $tr = jQuery(this).closest('tr');
		if (!$tr.hasClass('webcu_tk_draggable-top')) {
			alert('Only the top row is movable in this demo.');
			return;
		}
		e.preventDefault();
		e.stopPropagation();
		dragging = true;
		$dragged = $tr.addClass('webcu_tk_dragging');
		jQuery('body').addClass('webcu_tk_no-select');
		$placeholder = createPlaceholder();
		$dragged.after($placeholder);
		jQuery(document).on('mousemove.ticketdrag', function (ev) {
			ev.preventDefault();
			var el = document.elementFromPoint(ev.clientX, ev.clientY);
			if (!el) return;
			var $targetRow = jQuery(el).closest('tr');
			if (!$targetRow.length || $targetRow.is($placeholder) || !$targetRow.closest('tbody').is($tbody)) return;
			if ($targetRow.is($dragged)) return;
			var phIndex = $placeholder.index();
			var targetIndex = $targetRow.index();
			if (targetIndex > phIndex) $targetRow.after($placeholder);
			else $targetRow.before($placeholder);
		});
		jQuery(document).on('mouseup.ticketdrag', function () {
			jQuery(document).off('.ticketdrag');
			jQuery('body').removeClass('webcu_tk_no-select');
			if ($placeholder && $dragged) { $placeholder.replaceWith($dragged); }
			$dragged.removeClass('webcu_tk_dragging');
			$placeholder = null; $dragged = null; dragging = false;
			updateDraggables();
		});
	});
	jQuery(document).on('selectstart', function (e) { if (dragging) e.preventDefault(); });
	jQuery('.webcu_tk_advanced').hide();
});



// ======= Date / Time ========

jQuery(document).ready(function () {
	// Add new row
	jQuery('#webcu_date_addDate').click(function () {
		let newRow = `
            <tr class="webcu_date_date-row">
                <td><input type="date" name="webcu_start_date[]" class="start-date"></td>
                <td><input type="time" name="webcu_start_time[]" class="start-time"></td>
                <td><input type="date" name="webcu_end_date[]" class="end-date"></td>
                <td><input type="time" name="webcu_end_time[]" class="end-time"></td>
                <td><button type="button" class="webcu_date_remove-row"><span class="dashicons dashicons-no"></span></button></td>
            </tr>`;
		jQuery('#webcu_date_dateTable tbody').append(newRow);
	});

	// Remove row
	jQuery(document).on('click', '.webcu_date_remove-row', function () {
		if (confirm('Are you sure you want to remove this Date & Time?')) {
			jQuery(this).closest('tr').remove();
		}
	});

	// Toggle format section
	jQuery('#webcu_date_toggleSwitch').change(function () {

		if (jQuery(this).is(':checked')) {
			jQuery('#webcu_date_formatSection').slideDown();
		} else {
			jQuery('#webcu_date_formatSection').slideUp();
		}
	});
});


//====== settings form =======


jQuery(document).ready(function ($) {

	jQuery('#webcu_setting_roles').hide();
	jQuery('#webcu_setting_toggleStatus').on('change', function () {
		if (jQuery(this).is(':checked')) {
			jQuery('#webcu_setting_roles').show();

		} else {
			jQuery('#webcu_setting_roles').hide();
		}
	});

});

//====== Rich Text=======

jQuery(document).ready(function () {
	jQuery('#rich_text_status').on('change', function () {

		if (jQuery(this).val() === 'enable') {
			jQuery('#webcu_form_section').slideDown(600);
		} else {
			jQuery('#webcu_form_section').slideUp(600);
		}
	});
});


///======== Emails =========      

jQuery(function ($) {
	let counter = ajax_ob.counter || 1;
	// Expand/Collapse button
	jQuery(document).on('click', '.webcu_re_expand-btn', function () {
		const box = jQuery(this).closest('.webcu_re_email-block'); // fixed selector
		box.toggleClass('webcu_re_collapsed');

		// Toggle button text
		if (box.hasClass('webcu_re_collapsed')) {
			jQuery(this).text('Expand');
		} else {
			$(this).text('Collapse');
		}
	});

	//Remove button
	jQuery(document).on('click', '.webcu_re_remove-btn', function () {

		if (confirm('Are you sure you want to remove this Emails?')) {
			jQuery(this).closest('.webcu_re_email-block').remove(); // fixed selector
		}

	});

	// Add new email block
	jQuery('#addNewEmail').on('click', function () {
		counter++;
		const newBox = `
        <div class="webcu_re_box webcu_re_email-block" data-index="${counter}">
            <div class="webcu_re_expand-remove">
                <button type="button" class="webcu_re_expand-btn">Expand</button>
                <button type="button" class="webcu_re_remove-btn">Remove</button>
            </div>

            <div class="webcu_re_header-row">
                <div class="webcu_re_title">Event email reminder ${counter}</div>
                <div class="webcu_re_top-actions">
                    <button class="webcu_re_send-now">Send Now</button>
                </div>
            </div>

            <div class="webcu_re_form-row">
                <div class="webcu_re_label">Email Timing:</div>
                <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input class="webcu_re_timing" name="timing_${counter}" type="text" value="72" /> 
                        <span class="webcu_re_hours-label">Hours</span>
                    </div>
                    <div class="webcu_re_small-help">Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.</div>
                </div>
            </div>

            <div class="webcu_re_form-row">
                <div class="webcu_re_label">Time count:</div>
                <div>
                    <div class="webcu_re_radios">
                        <label class="webcu_re_radio-item"><input type="radio" name="timecount_${counter}" value="before" checked> Before Event Start</label>
                        <label class="webcu_re_radio-item"><input type="radio" name="timecount_${counter}" value="after"> After Event End</label>
                    </div>
                    <div class="webcu_re_small-help">Schedule email send before event start or after event end?</div>
                </div>
            </div>

            <div class="webcu_re_form-row">
                <div class="label">Email Receiver:</div>
                <div>
                    <div class="webcu_re_radios">
                        <select name="email_reciever_${counter}">
                            <option value="organizer">organizer</option>
                            <option value="sponsor">Sponsor</option>
                            <option value="volunteer">Volunteer</option>
                            <option value="attendee">Attendee</option>
                        </select>
                    </div>
                    <div class="webcu_re_small-help">Who Recieve the email</div>
                </div>
            </div> 

            <div class="webcu_re_form-row">
                <div class="webcu_re_label">Email Subject line:</div>
                <div>
                    <input class="webcu_re_subject" name="subject_${counter}" placeholder="First Reminder email subject line" />
                </div>
            </div>

            <div class="webcu_re_form-row">
                <div class="webcu_re_label">Email Content:</div>
                <div id="webcu_re_editor_container_${counter}"></div>
            </div>
        </div>`;

		jQuery('#emailContainer').append(newBox);

		const newId = 'content_' + counter;
		jQuery('#webcu_re_editor_container_' + counter).html('<textarea id="' + newId + '" name="' + newId + '"></textarea>');
		if (typeof wp !== 'undefined' && wp.editor) {
			wp.editor.initialize(newId, { tinymce: true, quicktags: true });
		}
	});


	/* ==== email send script ===== */

	// Add this to your existing JavaScript
	jQuery(document).on('click', '.webcu_re_send-now_old', function (e) {
		e.preventDefault();

		var $button = jQuery(this);
		var $block = $button.closest('.webcu_re_email-block');
		var index = $block.data('index');

		// Get form data
		var post_id = $button.data('post-id');
		var timing = $block.find('input[name="timing_' + index + '"]').val();
		var timecount = $block.find('input[name="timecount_' + index + '"]:checked').val();
		var receiver = $block.find('select[name="email_reciever_' + index + '"]').val();
		var subject = $block.find('input[name="subject_' + index + '"]').val();
		var content = '';

		// Get content from TinyMCE editor
		if (typeof tinyMCE !== 'undefined') {
			var editor_id = 'content_' + index;
			if (tinyMCE.get(editor_id)) {
				content = tinyMCE.get(editor_id).getContent();
			} else {
				content = jQuery('textarea[name="content_' + index + '"]').val();
			}
		} else {
			content = jQuery('textarea[name="content_' + index + '"]').val();
		}

		// Validate data
		if (!timing || !subject || !content) {
			alert('Please fill in all required fields: timing, subject, and content.');
			return;
		}

		// Disable button and show loading
		$button.prop('disabled', true).text('Sending...');

		// Send AJAX request
		jQuery.ajax({
			url: ajax_ob.ajax_url,
			type: 'POST',
			data: {
				action: 'webcu_send_email_now_old',
				nonce: ajax_ob.nonce,
				post_id: post_id,
				index: index,
				timing: timing,
				timecount: timecount,
				receiver: receiver,
				subject: subject,
				content: content,
				event_start: $button.data('event-start'),
				event_end: $button.data('event-end')
			},
			success: function (response) {
				if (response.success) {
					alert(response.data.message);
					console.log('Scheduled time:', response.data.scheduled_time);
				} else {
					alert('Error: ' + response.data);
				}
				$button.prop('disabled', false).text('Send Now');
			},
			error: function () {
				alert('An error occurred while sending the email.');
				$button.prop('disabled', false).text('Send Now');
			}
		});
	});

	// Also update your "Add New Email" jQuery to include data attributes
	jQuery('#addNewEmail').on('click', function () {
		counter++;
		const newBox = `
        <div class="webcu_re_box webcu_re_email-block" data-index="${counter}">
            <div class="webcu_re_expand-remove">
                <button type="button" class="webcu_re_expand-btn">Expand</button>
                <button type="button" class="webcu_re_remove-btn">Remove</button>
            </div>

            <div class="webcu_re_header-row">
                <div class="webcu_re_title">Event email reminder ${counter}</div>
                <div class="webcu_re_top-actions">
                    <button type="button" class="webcu_re_send-now" 
                        data-post-id="<?php echo esc_attr(get_the_ID()); ?>"
                        data-index="${counter}"
                        data-timing="72"
                        data-timecount="before"
                        data-receiver="organizer"
                        data-event-start="<?php echo esc_attr(get_post_meta(get_the_ID(), '_event_start_date', true)); ?>"
                        data-event-end="<?php echo esc_attr(get_post_meta(get_the_ID(), '_event_end_date', true)); ?>">
                        Send Now
                    </button>
                </div>
            </div>
            
            <!-- Rest of your form fields -->
            <!-- ... -->
            
        </div>`;

		jQuery('#emailContainer').append(newBox);

		// Initialize editor
		const newId = 'content_' + counter;
		jQuery('#webcu_re_editor_container_' + counter).html('<textarea id="' + newId + '" name="' + newId + '"></textarea>');
		if (typeof wp !== 'undefined' && wp.editor) {
			wp.editor.initialize(newId, { tinymce: true, quicktags: true });
		}
	});






});


//====== Attendee Form =======



jQuery(document).ready(function ($) {

	jQuery('#add-field-row').on('click', function () {
		let row = `
            <tr>
                <td><input type="text" name="ue_field_label[]" placeholder="Field Label"></td>
                <td><input type="text" name="ue_field_id[]" placeholder="Unique ID"></td>
                <td>
                    <select name="ue_field_type[]" class="ue-field-type">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="number">Number</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="ue_field_options[]" class="ue-field-options" placeholder="Option1, Option2" style="display:none;">
                </td>
                <td>
                    <select name="ue_field_required[]">
                        <option value="no">Not Required</option>
                        <option value="yes">Required</option>
                    </select>
                </td>
                <td> 
                    
                <button type="button" id="custom_form_remove-row" class="remove-row button button-danger"><span class="dashicons dashicons-no"></span></button>    
                </td>
            </tr>`;
		jQuery('#ue-fields-table tbody').append(row);
	});

	jQuery(document).on('click', '.custom_form_remove-row', function () {
		console.log('aaaa');

		if (confirm('Are you sure you want to remove this Field?')) {
			jQuery(this).closest('tr').remove();
		}
	});


	jQuery(document).on('change', '.ue-field-type', function () {
		let fieldType = jQuery(this).val();
		let optionsInput = jQuery(this).closest('tr').find('.ue-field-options');

		if (fieldType === 'select' || fieldType === 'checkbox' || fieldType === 'radio') {
			optionsInput.show();
		} else {
			optionsInput.hide().val('');
		}
	});

});


/* ==== Registration form =====*/

jQuery(document).ready(function ($) {

	jQuery('#add-field-regi-row').on('click', function () {
		let row = `
            <tr>
                <td><input type="text" name="ue_regi_field_label[]" placeholder="Field Label"></td>
                <td><input type="text" name="ue_regi_field_id[]" placeholder="Unique ID"></td>
                <td>
                    <select name="ue_regi_field_type[]" class="ue-field-type">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="number">Number</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="ue_regi_field_options[]" class="ue-field-options" placeholder="Option1, Option2" style="display:none;">
                </td>
                <td>
                    <select name="ue_regi_field_required[]">
                        <option value="no">Not Required</option>
                        <option value="yes">Required</option>
                    </select>
                </td>
                <td> 
                    
                <button type="button" id="custom_form_remove-row" class="remove-row button button-danger"><span class="dashicons dashicons-no"></span></button>    
                </td>
            </tr>`;
		jQuery('#ue_regi-fields-table tbody').append(row);
	});

	jQuery(document).on('click', '#custom_form_remove-row', function () {
		if (confirm('Are you sure you want to remove this Field?')) {
			jQuery(this).closest('tr').remove();
		}
	});


	jQuery(document).on('change', '.ue_regi-field-type', function () {

		let fieldType = jQuery(this).val();
		let optionsInput = jQuery(this).closest('tr').find('.ue_regi-field-options');

		if (fieldType === 'select' || fieldType === 'checkbox' || fieldType === 'radio') {
			optionsInput.show();
		} else {
			optionsInput.hide().val('');
		}
	});

});


//====== Registration Form =======


jQuery(function ($) {
	jQuery('#regiForm_type').on('change', function () {

		if (jQuery(this).val() === 'custom_form') {
			jQuery('#custom_form_wrapper').slideDown();
			jQuery('#global_form').hide();
		} else if (jQuery(this).val() === 'global_form') {
			jQuery('#custom_form_wrapper').slideUp();
			jQuery('#global_form').show();
		} else {
			jQuery('#custom_form_wrapper').hide();
			jQuery('#global_form').hide();
		}
	});

	jQuery('#regiForm_type').trigger('change');
});


//====== F.A.Q =======

jQuery(document).ready(function ($) {

	if (typeof $.fn.sortable === 'undefined') {
		console.error('jQuery UI Sortable is NOT loaded!');
		return;
	}

	var list = jQuery("#faq_timelineList");

	// Sortable
	list.sortable({
		handle: ".faq_btn-drag",
		items: "> li.faq_timeline-item",
		placeholder: "faq_timeline-placeholder",
		forcePlaceholderSize: true,
		tolerance: "pointer",
		cursor: "move",
		opacity: 0.8,
		distance: 5,
		cancel: "input,textarea,select,button:not(.faq_btn-drag),a,iframe",
		update: function (event, ui) {
			updateIndexes();
		}
	});

	// Expand/Collapse
	jQuery(document).on('click', '.faq_btn-expand', function (e) {
		e.preventDefault();
		var body = jQuery(this).closest('.faq_timeline-item').find('.faq_timeline-body');
		body.slideToggle(160);

		var icon = jQuery(this).find('.dashicons');
		icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
	});

	// Remove
	jQuery(document).on('click', '.faq_btn-remove', function (e) {
		e.preventDefault();
		if (confirm('Are you sure you want to remove this FAQ item?')) {
			jQuery(this).closest('.faq_timeline-item').slideUp(200, function () {
				jQuery(this).remove();
				updateIndexes();
			});
		}
	});

	jQuery("#faq_addTimeline").on('click', function (e) {
		e.preventDefault();
		var index = jQuery("#faq_timelineList .faq_timeline-item").length;
		var editorId = 'faq_faq_content_' + index;

		var newItem = `
                <li class="faq_timeline-item">
                    <div class="faq_timeline-top">
                        <button type="button" class="faq_btn faq_btn-expand">
                            <span class="dashicons dashicons-arrow-down-alt2"></span> Expand
                        </button>
                        <button type="button" class="faq_btn faq_btn-remove">
                            <span class="dashicons dashicons-no"></span>
                        </button>
                        <button type="button" class="faq_btn faq_btn-drag">
                            <span class="dashicons dashicons-fullscreen-alt"></span>
                        </button>
                    </div>
                    <div class="faq_timeline-body" style="display:block;">
                        <label>Title</label>
                        <input type="text" name="faq_faq[${index}][title]" class="faq_title" placeholder="Enter title">

                        <label>Content</label>
                        <textarea id="${editorId}" name="faq_faq[${index}][content]" rows="5"></textarea>
                    </div>
                </li>`;

		// Append to list
		list.append(newItem);
		list.sortable('refresh');
		updateIndexes();

		if (typeof wp !== 'undefined' && wp.editor && wp.editor.initialize) {
			setTimeout(function () {
				wp.editor.initialize(editorId, {
					tinymce: { wpautop: true, plugins: 'lists,paste,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wpdialogs,wptextpattern,wplink,wpview' },
					quicktags: true,
					mediaButtons: true
				});
			}, 300);
		} else {
			console.warn('wp.editor API not available');
		}
	});

	// Update indexes
	function updateIndexes() {
		jQuery("#faq_timelineList .faq_timeline-item").each(function (i) {
			jQuery(this).find('input.faq_title').attr('name', 'faq_faq[' + i + '][title]');
			jQuery(this).find('textarea[name*="[content]"]').attr({
				name: 'faq_faq[' + i + '][content]',
				id: 'faq_faq_content_' + i
			});
		});
	}

});


(function ($) {
	function clearTempFaqInputs() {
		jQuery('#webcu_temp_faq_inputs').remove();
	}
	function buildTempFaqInputs() {
		clearTempFaqInputs();
		var container = jQuery('<div id="webcu_temp_faq_inputs" style="display:none"></div>');
		jQuery('#post').append(container);
		jQuery('#faq_timelineList .faq_timeline-item').each(function (i) {
			var $li = jQuery(this);
			var title = $li.find('input.faq_title').val() || '';
			var contentVal = '';
			var $ta = $li.find('textarea[name*="[content]"]');
			if ($ta.length) {
				contentVal = $ta.val();
			} else {

				var $anyTa = $li.find('textarea').first();
				if ($anyTa.length) contentVal = $anyTa.val();
			}

			container.append(jQuery('<input>').attr({
				type: 'hidden',
				name: 'faq_faq[' + i + '][title]',
				value: title
			}));
			container.append(jQuery('<input>').attr({
				type: 'hidden',
				name: 'faq_faq[' + i + '][content]',
				value: contentVal
			}));
		});
	}
	jQuery(document).on('submit', '#post', function (e) {
		try {
			if (typeof tinymce !== 'undefined') {
				tinymce.triggerSave();
			}
		} catch (err) {
			// ignore if tinymce not present
			console.warn('tinymce.triggerSave() failed or not available', err);
		}
		if (typeof updateIndexes === 'function') {
			updateIndexes();
		}
		buildTempFaqInputs();
		return true;
	});
	jQuery(window).on('beforeunload', function () {
		clearTempFaqInputs();
	});

})(jQuery);


/* ===== Additional Content ====== */

jQuery(function ($) {
	var list = jQuery("#webcu_timelineList");

	list.sortable({
		handle: ".webcu_btn-drag",
		items: "> li.webcu_timeline-item",
		placeholder: "webcu_timeline-placeholder",
		forcePlaceholderSize: true,
		tolerance: "pointer",
		cancel: ""
	});

	// Fix drag conflict
	jQuery(document).on('mousedown', '.webcu_btn-drag', function (e) {
		e.preventDefault();
	});

	// Expand/Collapse
	jQuery(document).on('click', '.webcu_btn-expand', function (e) {
		e.preventDefault();
		var body = jQuery(this).closest('.webcu_timeline-item').find('.webcu_timeline-body');
		body.slideToggle(160);

		var icon = jQuery(this).find('.dashicons');
		icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
	});

	// Remove
	jQuery(document).on('click', '.webcu_btn-remove', function () {
		if (confirm('Are you sure you want to remove this additional content?')) {
			jQuery(this).closest('.webcu_timeline-item').remove();
			list.sortable('refresh');
			updateIndexes();
		}
	});

	// Add new timeline
	jQuery("#webcu_addTimeline").on('click', function () {
		var index = jQuery("#webcu_timelineList .webcu_timeline-item").length;
		var editorId = 'webcu_content_' + index;

		var newItem = `
            <li class="webcu_timeline-item">
                <div class="webcu_timeline-top">
                    <button type="button" class="webcu_btn webcu_btn-expand"><span class="dashicons dashicons-arrow-down-alt2"></span> Expand</button>
                    <button type="button" class="webcu_btn webcu_btn-remove"><span class="dashicons dashicons-no"></span></button>
                    <button type="button" class="webcu_btn webcu_btn-drag"><span class="dashicons dashicons-fullscreen-alt"></span></button>
                </div>
                <div class="webcu_timeline-body" style="display:block;">
                    <label>Title</label>
                    <input type="text" name="webcu_timeline[` + index + `][title]" class="webcu_title" placeholder="Enter title">
                    <label>Content</label>
                    <textarea id="`+ editorId + `" name="webcu_timeline[` + index + `][content]" rows="5" placeholder="Enter content"></textarea>
                </div>
            </li>`;

		jQuery("#webcu_timelineList").append(newItem);

		//Initialize WordPress TinyMCE editor dynamically
		if (typeof wp !== 'undefined' && wp.editor && wp.editor.initialize) {
			setTimeout(function () {
				wp.editor.initialize(editorId, {
					tinymce: { wpautop: true, plugins: 'lists,paste,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wpdialogs,wptextpattern,wplink,wpview' },
					quicktags: true,
					mediaButtons: true
				});
			}, 300);
		} else {
			console.warn('wp.editor API not available');
		}

		// Initialize Quicktags (HTML editor)
		if (typeof quicktags !== "undefined") {
			quicktags({ id: editorId });
			QTags._buttonsInit();
		}
	});

	// Update name indexes after drag/remove
	function updateIndexes() {
		jQuery("#webcu_timelineList .webcu_timeline-item").each(function (i) {
			jQuery(this).find('input.webcu_title').attr('name', 'webcu_timeline[' + i + '][title]');
			jQuery(this).find('textarea').attr('name', 'webcu_timeline[' + i + '][content]');
		});
	}

	list.on("sortupdate", updateIndexes);
});




/* ===== Terms & Conditon ====== */

jQuery(function ($) {

	// Sortable (Drag & Drop)
	jQuery("#webcu_tc_list").sortable({
		handle: ".webcu_tc-drag",
		items: ".webcu_tc-item",
		placeholder: "webcu_sortable-placeholder",
		tolerance: "pointer",
		axis: "y"
	}).disableSelection();

	// Expand / Collapse Toggle

	jQuery(document).on('click', '.webcu_tc-expand', function () {
		var body = jQuery(this).closest('.webcu_tc-item').find('.webcu_tc-body');
		body.slideToggle(160);
		var icon = jQuery(this).find('.dashicons');
		icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
	});


	// Delete Term & Condition Block
	jQuery(document).on('click', '.webcu_tc-delete', function () {

		if (confirm('Are you sure you want to remove this Term and Condition?')) {
			jQuery(this).closest('.webcu_tc-item').remove();
			updateIndexes(); // Re-index after removal
		}

	});

	// Add New Term & Condition
	jQuery(document).on('click', '#webcu_add_new_tc', function () {
		let lastItem = jQuery('.webcu_tc-item:last');
		let newIndex = lastItem.length ? parseInt(lastItem.data('index')) + 1 : 0;

		let clone = lastItem.clone();

		clone.attr('data-index', newIndex);

		// Update field names to be unique
		clone.find('select[name*="tc_required"]').attr('name', 'tc_required[' + newIndex + ']').val('');
		clone.find('input[name*="tc_title"]').attr('name', 'tc_title[' + newIndex + ']').val('');
		clone.find('input[name*="tc_url"]').attr('name', 'tc_url[' + newIndex + ']').val('');

		// Hide content initially
		clone.find('.webcu_tc-body').hide();

		// Append to list
		jQuery('#webcu_tc_list').append(clone);
	});

	// Function to Keep Indexes Correct After Delete/Reorder
	function updateIndexes() {
		jQuery('#webcu_tc_list .webcu_tc-item').each(function (index) {
			jQuery(this).attr('data-index', index);
			jQuery(this).find('select[name*="tc_required"]').attr('name', 'tc_required[' + index + ']');
			jQuery(this).find('input[name*="tc_title"]').attr('name', 'tc_title[' + index + ']');
			jQuery(this).find('input[name*="tc_url"]').attr('name', 'tc_url[' + index + ']');
		});
	}

});


//==== Event phooto Gallery =====     

jQuery(document).ready(function ($) {
	var mediaUploader;

	jQuery('#webcu_add_gallery_images').on('click', function (e) {
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media({
			title: 'Select Gallery Images',
			button: {
				text: 'Add to Gallery'
			},
			multiple: true
		});

		mediaUploader.on('select', function () {
			var attachments = mediaUploader.state().get('selection').toJSON();
			var ids = jQuery('#webcu_gallery_ids').val();
			var idsArray = ids ? ids.split(',') : [];

			attachments.forEach(function (attachment) {
				if (idsArray.indexOf(attachment.id.toString()) === -1) {
					idsArray.push(attachment.id);

					var imgHtml = '<div class="webcu-gallery-item" data-id="' + attachment.id + '">';
					imgHtml += '<img src="' + attachment.sizes.thumbnail.url + '" />';
					imgHtml += '<span class="webcu-remove-image" title="Remove">&times;</span>';
					imgHtml += '</div>';

					jQuery('#webcu-gallery-images').append(imgHtml);
				}
			});

			jQuery('#webcu_gallery_ids').val(idsArray.join(','));
		});

		mediaUploader.open();
	});

	jQuery('#webcu-gallery-images').on('click', '.webcu-remove-image', function () {
		var item = jQuery(this).parent();
		var id = item.data('id').toString();
		var ids = jQuery('#webcu_gallery_ids').val().split(',');

		ids = ids.filter(function (val) {
			return val !== id;
		});

		jQuery('#webcu_gallery_ids').val(ids.join(','));
		item.remove();
	});
});


/* ===== Global registration ===== */

(function ($) {
	// synchronize checkboxes and hidden enabled input, and handle UI
	jQuery('.webcu_regi_field-row').each(function () {
		var row = jQuery(this);
		var cb = row.find('.webcu_regi_checkbox');
		var enabledInp = row.find('.webcu_regi_enabled_input');

		// initialization: if checked set enabled input
		cb.on('change', function () {
			enabledInp.val(cb.is(':checked') ? '1' : '0');
			row.toggleClass('active', cb.is(':checked'));
		});
	});
	// Manage custom fields list and hidden JSON input
	var customFields = [];
	// initialize from hidden input if present
	try {
		var existing = jQuery('#webcu_regi_custom_fields_input').val();
		if (existing) customFields = JSON.parse(existing);
	} catch (e) { customFields = []; }


	function refreshCustomFieldsUI() {
		var container = jQuery('#webcu_regi_custom_fields');
		var type = jQuery('#webcu_regi_field_type').val();
		container.empty();

		customFields.forEach(function (cf, index) {

			var row = jQuery('<div class="webcu_regi_custom-field-row" data-index="' + index + '">');
			// Label
			row.append(
				'<input type="text" class="cf-input" data-key="label" value="' + cf.label + '">'
			);
			// ID
			row.append(
				'<input type="text" class="cf-input" data-key="id" value="' + cf.id + '">'
			);
			// Options (comma separated)
			if (cf.type === 'Select' || cf.type === 'Checkbox' || cf.type === 'Radio') {
				row.append(
					'<input type="text" class="cf-input" data-key="options" value="' + cf.options.join(",") + '">'
				);
			}
			row.append('<select disabled><option>' + cf.type + '</option></select>');
			row.append('<select disabled><option>' + cf.required + '</option></select>');
			row.append('<span class="webcu_regi_delete-btn">🗑️</span>');

			container.append(row);
		});

		updateHiddenInput();
	}

	jQuery(document).on('input', '.cf-input', function () {
		var rowIndex = jQuery(this).closest('.webcu_regi_custom-field-row').data('index');
		var key = jQuery(this).data('key');
		var value = jQuery(this).val();

		if (key === 'options') {
			value = value.split(',').map(function (o) {
				return o.trim();
			});
		}
		customFields[rowIndex][key] = value;
		updateHiddenInput();
	});

	function updateHiddenInput() {
		jQuery('#webcu_regi_custom_fields_input')
			.val(JSON.stringify(customFields));
	}


	jQuery('#webcu_regi_add_field').on('click', function (e) {
		e.preventDefault();

		var label = jQuery('#webcu_regi_field_label').val().trim();
		var id = jQuery('#webcu_regi_unique_id').val().trim();
		var type = jQuery('#webcu_regi_field_type').val();
		var required = jQuery('#webcu_regi_field_required').val();
		var optionsRaw = jQuery('#webcu_regi_options').val().trim();

		if (!label || !id) {
			alert('Field Label and Unique ID must not be empty.');
			return;
		}
		let options = [];
		if (type === 'Select' || type === 'Checkbox' || type === 'Radio') {
			if (optionsRaw) {
				options = optionsRaw.split(',').map(function (o) {
					return o.trim();
				});
			}
		}
		// ensure unique id
		var exists = customFields.some(function (x) {
			return x.id === id;
		});

		if (exists) {
			alert('Unique ID already exists. Pick another one.');
			return;
		}

		//options now correctly stored
		customFields.push({
			label: label,
			id: id,
			type: type,
			required: required,
			options: options
		});

		// reset inputs
		jQuery('#webcu_regi_field_label').val('');
		jQuery('#webcu_regi_unique_id').val('');
		jQuery('#webcu_regi_options').val('');

		refreshCustomFieldsUI();
	});


	// Delete custom field (delegate)
	jQuery(document).on('click', '.webcu_regi_delete-btn', function () {
		var uid = jQuery(this).closest('.webcu_regi_custom-field-row').data('uid');
		customFields = customFields.filter(function (x) { return x.id !== uid; });
		refreshCustomFieldsUI();
	});

	refreshCustomFieldsUI();
	// Before form submit (save post) ensure hidden enabled inputs reflect checkbox states:

	jQuery('#post').on('submit', function () {
		jQuery('.webcu_regi_field-row').each(function () {
			var cb = jQuery(this).find('.webcu_regi_checkbox');
			var hidden = jQuery(this).find('.webcu_regi_enabled_input');
			hidden.val(cb.is(':checked') ? '1' : '0');
		});
		// hidden custom fields JSON already kept in sync on changes
	});
})(jQuery);


/*  === Text Field show/hide===  */

document.addEventListener("DOMContentLoaded", function () {
	const fieldType = document.getElementById("webcu_regi_field_type");
	const optionBox = document.getElementById("webcu_regi_option_box");

	function toggleOptions() {
		const value = fieldType.value;

		// Show text field only for Select, Checkbox, Radio
		if (value === "Select" || value === "Checkbox" || value === "Radio") {
			optionBox.style.display = "block";
		} else {
			optionBox.style.display = "none";
		}
	}

	// Run on change
	fieldType.addEventListener("change", toggleOptions);

	// Run on page load
	toggleOptions();
});



/* ===== Organizer  ====*/

jQuery(document).ready(function ($) {
	jQuery('#webcu_orga_addExtra').on('click', function (e) {
		e.preventDefault();

		var timestamp = Date.now(); // Unique key for each row

		var row = '<tr>' +
			'<td>' +
			'<select name="webcu_orga_extras[' + timestamp + '][org_social_media]">' +
			'<option value="facebook">Facebook</option>' +
			'<option value="linkedin">Linkedin</option>' +
			'<option value="X">X (former Twitter)</option>' +
			'<option value="instagram">Instagram</option>' +
			'<option value="pinterest">Pinterest</option>' +
			'<option value="tiktok">Tiktok</option>' +
			'</select>' +
			'</td>' +
			'<td><input type="url" name="webcu_orga_extras[' + timestamp + '][url]" placeholder="URL"></td>' +
			'<td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">✖</button>' +
			'<span class="webcu_orga-drag"> <span class="dashicons dashicons-fullscreen-alt"></span>'
		'</td>'
		'</tr>';

		jQuery('#webcu_orga_extraTable tbody').append(row);
	});

	// Remove row
	jQuery(document).on('click', '.webcu_remove_extra_row', function () {
		jQuery(this).closest('tr').remove();
	});


	/* === organization drag & drop ====*/

	// Enable sortable drag-and-drop for table rows
	jQuery("#webcu_orga_extraTable tbody").sortable({
		handle: ".webcu_orga-drag",
		placeholder: "ui-state-highlight",
		update: function () {
			webcu_reindex_extra_rows();
		}
	});

	// Reindex rows after drag & drop
	function webcu_reindex_extra_rows() {
		jQuery("#webcu_orga_extraTable tbody tr").each(function (i) {
			jQuery(this).find('select').attr('name', 'webcu_orga_extras[' + i + '][org_social_media]');
			jQuery(this).find('input').attr('name', 'webcu_orga_extras[' + i + '][url]');
		});
	}


	/* ===== Hero banner upload ====*/

	var mediaUploader;
	jQuery('#rk-upload-btn').on('click', function (e) {
		e.preventDefault();

		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media({
			title: 'Select Image',
			button: { text: 'Use this image' },
			multiple: false
		});

		mediaUploader.on('select', function () {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery("#rk-image-preview").attr("src", attachment.url).show();
			jQuery("#event_sponser_image_id").val(attachment.id);
			jQuery("#rk-remove-btn").show();
			jQuery("#rk-upload-btn").text("Change Image");
		});

		mediaUploader.open();
	});

	jQuery('#rk-remove-btn').on('click', function (e) {
		e.preventDefault();
		jQuery("#rk-image-preview").hide();
		jQuery("#event_sponser_image_id").val('');
		jQuery(this).hide();
		jQuery("#rk-upload-btn").text("Upload Image");
	});


});


//===== organizer photogallery ======

jQuery(document).ready(function ($) {
	var mediaUploader;

	jQuery('#webcu_organizer_gallery_images').on('click', function (e) {
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media({
			title: 'Select Gallery Images',
			button: {
				text: 'Add to Gallery'
			},
			multiple: true
		});

		mediaUploader.on('select', function () {
			var attachments = mediaUploader.state().get('selection').toJSON();
			var ids = jQuery('#webcu_organizer_gallery').val();
			var idsArray = ids ? ids.split(',') : [];

			attachments.forEach(function (attachment) {
				if (idsArray.indexOf(attachment.id.toString()) === -1) {
					idsArray.push(attachment.id);

					var imgHtml = '<div class="webcu-gallery-item" data-id="' + attachment.id + '">';
					imgHtml += '<img src="' + attachment.sizes.thumbnail.url + '" />';
					imgHtml += '<span class="webcu-remove-image" title="Remove">&times;</span>';
					imgHtml += '</div>';

					jQuery('#webcu-gallery-images').append(imgHtml);
				}
			});

			jQuery('#webcu_organizer_gallery').val(idsArray.join(','));
		});

		mediaUploader.open();
	});

	jQuery('#webcu-gallery-images').on('click', '.webcu-remove-image', function () {
		var item = jQuery(this).parent();
		var id = item.data('id').toString();
		var ids = jQuery('#webcu_organizer_gallery').val().split(',');

		ids = ids.filter(function (val) {
			return val !== id;
		});

		jQuery('#webcu_organizer_gallery').val(ids.join(','));
		item.remove();
	});
});


/* === organizer video type === */

jQuery(document).ready(function ($) {
	function toggleFields() {
		const type = jQuery('#webcu-video-type').val();

		jQuery('#webcu-youtube-field').hide();
		jQuery('#webcu-vimeo-field').hide();
		jQuery('#webcu-ownvideo-field').hide();

		if (type === 'youtube') {
			jQuery('#webcu-youtube-field').show();
		}
		if (type === 'vimeo') {
			jQuery('#webcu-vimeo-field').show();
		}
		if (type === 'ownvideo') {
			jQuery('#webcu-ownvideo-field').show();
		}
	}
	toggleFields();
	jQuery('#webcu-video-type').on('change', toggleFields);
	// Media Uploader
	var videoUploader;
	jQuery('#webcu-upload-video-btn').click(function (e) {
		e.preventDefault();

		if (videoUploader) {
			videoUploader.open();
			return;
		}
		videoUploader = wp.media({
			title: 'Select Video',
			button: { text: 'Use this video' },
			library: { type: 'video' },
			multiple: false
		});

		videoUploader.on('select', function () {
			var file = videoUploader.state().get('selection').first().toJSON();

			jQuery('#webcu-video-preview').attr('src', file.url).show();
			jQuery('#webcu-video-id').val(file.id);
			jQuery('#webcu-remove-video-btn').show();
			jQuery('#webcu-upload-video-btn').text('Change Video');
		});

		videoUploader.open();
	});

	jQuery('#webcu-remove-video-btn').click(function () {
		jQuery('#webcu-video-preview').hide();
		jQuery('#webcu-video-id').val('');
		jQuery(this).hide();
		jQuery('#webcu-upload-video-btn').text('Upload Video');
	});
});

/* ======== Sponsers  =========*/

jQuery(document).ready(function ($) {
	jQuery('#webcu_spon_addExtra').on('click', function (e) {
		e.preventDefault();

		var timestamp = Date.now(); // Unique key for each row

		var row = '<tr>' +
			'<td>' +
			'<select name="webcu_spon_extras[' + timestamp + '][org_social_media]">' +
			'<option value="facebook">Facebook</option>' +
			'<option value="linkedin">Linkedin</option>' +
			'<option value="X"></option>' +
			'<option value="instagram">Instagram</option>' +
			'<option value="pinterest">Pinterest</option>' +
			'<option value="tiktok">Tiktok</option>' +
			'</select>' +
			'</td>' +
			'<td><input type="url" name="webcu_spon_extras[' + timestamp + '][url]" placeholder="URL"></td>' +
			'<td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">✖</button>' +
			'<span class="webcu_sponser-drag"> <span class="dashicons dashicons-fullscreen-alt"></span>'
		'</td>' +

			'</tr>';

		jQuery('#webcu_spon_extraTable tbody').append(row);
	});

	// Remove row
	jQuery(document).on('click', '.webcu_remove_extra_row', function () {
		jQuery(this).closest('tr').remove();
	});


	/* === Sponsers drag & drop ====*/

	jQuery("#webcu_spon_extraTable tbody").sortable({
		handle: ".webcu_sponser-drag",
		placeholder: "ui-state-highlight",
		update: function () {
			webcu_reindex_extra_rows();
		}
	});

	// Reindex rows after drag & drop
	function webcu_reindex_extra_rows() {
		jQuery("#webcu_spon_extraTable tbody tr").each(function (i) {
			jQuery(this).find('select').attr('name', 'webcu_spon_extras[' + i + '][spon_social_media]');
			jQuery(this).find('input').attr('name', 'webcu_spon_extras[' + i + '][url]');
		});
	}


	/* === Hero Banner ====*/

	jQuery(document).ready(function ($) {

		var mediaUploader;

		jQuery('#rk-upload-btn').on('click', function (e) {
			e.preventDefault();
			if (mediaUploader) {
				mediaUploader.open();
				return;
			}
			mediaUploader = wp.media({
				title: 'Select Image',
				button: { text: 'Use this image' },
				multiple: false
			});

			mediaUploader.on('select', function () {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				jQuery("#rk-image-preview").attr("src", attachment.url).show();
				jQuery("#event_sponser_image_id").val(attachment.id);
				jQuery("#rk-remove-btn").show();
				jQuery("#rk-upload-btn").text("Change Image");
			});

			mediaUploader.open();
		});

		jQuery('#rk-remove-btn').on('click', function (e) {
			e.preventDefault();
			jQuery("#rk-image-preview").hide();
			jQuery("#event_sponser_image_id").val('');
			jQuery(this).hide();
			jQuery("#rk-upload-btn").text("Upload Image");
		});
	});

});

/* === Sponser video ====*/

jQuery(document).ready(function ($) {
	function toggleFields() {
		const type = jQuery('#webcu_spon-video-type').val();

		jQuery('#webcu_spon-youtube-field').hide();
		jQuery('#webcu_spon-vimeo-field').hide();
		jQuery('#webcu_spon-ownvideo-field').hide();

		if (type === 'youtube') {
			jQuery('#webcu_spon-youtube-field').show();
		}
		if (type === 'vimeo') {
			jQuery('#webcu_spon-vimeo-field').show();
		}
		if (type === 'ownvideo') {
			jQuery('#webcu_spon-ownvideo-field').show();
		}
	}
	toggleFields();
	jQuery('#webcu_spon-video-type').on('change', toggleFields);
	// Media Uploader
	var videoUploader;
	jQuery('#webcu_spon-upload-video-btn').click(function (e) {
		e.preventDefault();

		if (videoUploader) {
			videoUploader.open();
			return;
		}
		videoUploader = wp.media({
			title: 'Select Video',
			button: { text: 'Use this video' },
			library: { type: 'video' },
			multiple: false
		});

		videoUploader.on('select', function () {
			var file = videoUploader.state().get('selection').first().toJSON();

			jQuery('#webcu_spon-video-preview').attr('src', file.url).show();
			jQuery('#webcu_spon-video-id').val(file.id);
			jQuery('#webcu_spon-remove-video-btn').show();
			jQuery('#webcu_spon-upload-video-btn').text('Change Video');
		});

		videoUploader.open();
	});

	jQuery('#webcu_spon-remove-video-btn').click(function () {
		jQuery('#webcu_spon-video-preview').hide();
		jQuery('#webcu_spon-video-id').val('');
		jQuery(this).hide();
		jQuery('#webcu_spon-upload-video-btn').text('Upload Video');
	});
});




/* ===== volenteer ======*/

jQuery(document).ready(function ($) {
	jQuery('#webcu_volun_addExtra').on('click', function (e) {
		e.preventDefault();

		var timestamp = Date.now(); // Unique key for each row

		var row = '<tr>' +
			'<td>' +
			'<select name="webcu_volun_extras[' + timestamp + '][org_social_media]">' +
			'<option value="facebook">Facebook</option>' +
			'<option value="linkedin">Linkedin</option>' +
			'<option value="X">X</option>' +
			'<option value="instagram">Instagram</option>' +
			'<option value="pinterest">Pinterest</option>' +
			'<option value="tiktok">Tiktok</option>' +
			'</select>' +
			'</td>' +
			'<td><input type="url" name="webcu_volun_extras[' + timestamp + '][url]" placeholder="URL"></td>' +
			'<td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">✖</button>' +
			'<span class="webcu_orga-drag"> <span class="dashicons dashicons-fullscreen-alt"></span>' + '</td>' +

			'</tr>';

		jQuery('#webcu_volun_extraTable tbody').append(row);
	});

	// Remove row
	jQuery(document).on('click', '.webcu_remove_extra_row', function () {
		jQuery(this).closest('tr').remove();
	});


	/* === Volenteer drag & drop ====*/

	jQuery("#webcu_volun_extraTable tbody").sortable({
		handle: ".webcu_volunteer-drag",
		placeholder: "ui-state-highlight",
		update: function () {
			webcu_reindex_extra_rows();
		}
	});

	// Reindex rows after drag & drop
	function webcu_reindex_extra_rows() {
		jQuery("#webcu_volun_extraTable tbody tr").each(function (i) {
			jQuery(this).find('select').attr('name', 'webcu_volun_extras[' + i + '][volun_social_media]');
			jQuery(this).find('input').attr('name', 'webcu_volun_extras[' + i + '][url]');
		});
	}


	/* === Volenteer hero banner ====*/

	var mediaUploader;

	jQuery('#rk-upload-btn').on('click', function (e) {
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media({
			title: 'Select Image',
			button: { text: 'Use this image' },
			multiple: false
		});

		mediaUploader.on('select', function () {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery("#rk-image-preview").attr("src", attachment.url).show();
			jQuery("#event_volunteer_image_id").val(attachment.id);
			jQuery("#rk-remove-btn").show();
			jQuery("#rk-upload-btn").text("Change Image");
		});
		mediaUploader.open();
	});

	jQuery('#rk-remove-btn').on('click', function (e) {
		e.preventDefault();
		jQuery("#rk-image-preview").hide();
		jQuery("#event_volunteer_image_id").val('');
		jQuery(this).hide();
		jQuery("#rk-upload-btn").text("Upload Image");
	});

});

/* === volunteer video === */

jQuery(document).ready(function ($) {
	function toggleFields() {
		const type = jQuery('#webcu_volun-video-type').val();

		jQuery('#webcu_volun-youtube-field').hide();
		jQuery('#webcu_volun-vimeo-field').hide();
		jQuery('#webcu_volun-ownvideo-field').hide();

		if (type === 'youtube') {
			jQuery('#webcu_volun-youtube-field').show();
		}
		if (type === 'vimeo') {
			jQuery('#webcu_volun-vimeo-field').show();
		}
		if (type === 'ownvideo') {
			jQuery('#webcu_volun-ownvideo-field').show();
		}
	}
	toggleFields();
	jQuery('#webcu_volun-video-type').on('change', toggleFields);
	// Media Uploader
	var videoUploader;
	jQuery('#webcu_volun-upload-video-btn').click(function (e) {
		e.preventDefault();

		if (videoUploader) {
			videoUploader.open();
			return;
		}
		videoUploader = wp.media({
			title: 'Select Video',
			button: { text: 'Use this video' },
			library: { type: 'video' },
			multiple: false
		});

		videoUploader.on('select', function () {
			var file = videoUploader.state().get('selection').first().toJSON();

			jQuery('#webcu_volun-video-preview').attr('src', file.url).show();
			jQuery('#webcu_volun-video-id').val(file.id);
			jQuery('#webcu_volun-remove-video-btn').show();
			jQuery('#webcu_volun-upload-video-btn').text('Change Video');
		});

		videoUploader.open();
	});

	jQuery('#webcu_volun-remove-video-btn').click(function () {
		jQuery('#webcu_volun-video-preview').hide();
		jQuery('#webcu_volun-video-id').val('');
		jQuery(this).hide();
		jQuery('#webcu_volun-upload-video-btn').text('Upload Video');
	});
});


/* === Gallery video === */


jQuery(document).ready(function ($) {
	var volenteerUploader;

	jQuery('#volenteer_add_gallery_images').on('click', function (e) {
		e.preventDefault();

		if (volenteerUploader) {
			volenteerUploader.open();
			return;
		}

		volenteerUploader = wp.media({
			title: 'Select Volenteer Gallery Images',
			button: { text: 'Add to Gallery' },
			multiple: true
		});

		volenteerUploader.on('select', function () {
			var attachments = volenteerUploader.state().get('selection').toJSON();

			var ids = jQuery('#volenteer_gallery_ids').val();
			var idsArray = ids ? ids.split(',') : [];

			attachments.forEach(function (attachment) {
				if (idsArray.indexOf(attachment.id.toString()) === -1) {

					idsArray.push(attachment.id);

					var imgHtml =
						'<div class="volenteer-gallery-item" data-id="' + attachment.id + '">' +
						'<img src="' + attachment.sizes.thumbnail.url + '" />' +
						'<span class="volenteer-remove-image" title="Remove">&times;</span>' +
						'</div>';

					jQuery('#volenteer-gallery-images').append(imgHtml);
				}
			});

			jQuery('#volenteer_gallery_ids').val(idsArray.join(','));
		});

		volenteerUploader.open();
	});

	jQuery('#volenteer-gallery-images').on('click', '.volenteer-remove-image', function () {
		var item = jQuery(this).parent();
		var id = item.data('id').toString();
		var ids = jQuery('#volenteer_gallery_ids').val().split(',');

		ids = ids.filter(function (val) { return val !== id; });
		jQuery('#volenteer_gallery_ids').val(ids.join(','));

		item.remove();
	});

});



//===== Event photogallery ======

jQuery(document).ready(function ($) {
	var mediaUploader;

	jQuery('#webcu_event_gallery_images').on('click', function (e) {
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media({
			title: 'Select Gallery Images',
			button: {
				text: 'Add to Gallery'
			},
			multiple: true
		});

		mediaUploader.on('select', function () {
			var attachments = mediaUploader.state().get('selection').toJSON();
			var ids = jQuery('#webcu_event_gallery').val();
			var idsArray = ids ? ids.split(',').filter(function (v) { return v !== ''; }) : [];

			attachments.forEach(function (attachment) {
				if (idsArray.indexOf(attachment.id.toString()) === -1) {
					idsArray.push(attachment.id);

					// Get thumbnail URL with fallback to full size or URL.
					var imgUrl = '';
					if (attachment.sizes && attachment.sizes.thumbnail) {
						imgUrl = attachment.sizes.thumbnail.url;
					} else if (attachment.sizes && attachment.sizes.full) {
						imgUrl = attachment.sizes.full.url;
					} else {
						imgUrl = attachment.url;
					}

					var imgHtml = '<div class="webcu-event-gallery-item" data-id="' + attachment.id + '">';
					imgHtml += '<img src="' + imgUrl + '" />';
					imgHtml += '<span class="webcu-event-remove-image" title="Remove">&times;</span>';
					imgHtml += '</div>';

					jQuery('#webcu-event-gallery-images').append(imgHtml);
				}
			});

			jQuery('#webcu_event_gallery').val(idsArray.join(','));
		});

		mediaUploader.open();
	});

	// Use same class name as PHP template for consistency.
	jQuery('#webcu-event-gallery-images').on('click', '.webcu-event-remove-image', function () {
		var item = jQuery(this).parent();
		var id = item.data('id').toString();
		var ids = jQuery('#webcu_event_gallery').val().split(',');

		ids = ids.filter(function (val) {
			return val !== id;
		});

		jQuery('#webcu_event_gallery').val(ids.join(','));
		item.remove();
	});
});


/* ==== Event Hero banner ==== */


var heroMediaUploader;
jQuery('#rk-event-upload-btn').on('click', function (e) {
	e.preventDefault();

	if (heroMediaUploader) {
		heroMediaUploader.open();
		return;
	}

	heroMediaUploader = wp.media({
		title: 'Select Image',
		button: { text: 'Use this image' },
		multiple: false
	});

	heroMediaUploader.on('select', function () {
		var attachment = heroMediaUploader.state().get('selection').first().toJSON();
		jQuery("#rk-event-image-preview").attr("src", attachment.url).show();
		jQuery("#event_image_id").val(attachment.id); // Fixed: was using wrong field ID.
		jQuery("#rk-event-remove-btn").show();
		jQuery("#rk-event-upload-btn").text("Change Image");
	});

	heroMediaUploader.open();
});

jQuery('#rk-event-remove-btn').on('click', function (e) {
	e.preventDefault();
	jQuery("#rk-event-image-preview").hide();
	jQuery("#event_image_id").val(''); // Fixed: was using wrong field ID.
	jQuery(this).hide();
	jQuery("#rk-event-upload-btn").text("Upload Image"); // Fixed: was using wrong button ID.
});


/* jQuery(document).ready(function ($) {

	jQuery('#webcu_woo_inte_save').on('click', function (e) {
		location.reload();
	});
}); */


/* ========== End Metabox ============= */


/* ==== Email section send ===== */


/* jQuery(document).ready(function ($) {
	// Handle Send Now button click
	$(document).on('click', '.webcu_re_send-now', function (e) {
		e.preventDefault();

		console.log('dddddddd')

		const button = $(this);
		const originalText = button.text();
		const postId = button.data('post-id');
		const index = button.data('index');
		const receiver = button.data('receiver');
		const subject = button.data('subject') || $('input[name="subject_' + index + '"]').val();
		const content = button.data('content') || $('#content_' + index + '-editor-container').find('iframe').contents().find('body').html();

		// Get content from editor if not in data attribute
		if (!content || content.trim() === '') {
			if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content_' + index)) {
				var editorContent = tinyMCE.get('content_' + index).getContent();
			} else {
				var editorContent = $('textarea[name="content_' + index + '"]').val();
			}
		} else {
			var editorContent = content;
		}

		// Validate
		if (!subject || subject.trim() === '') {
			alert('Please enter email subject');
			return;
		}

		if (!editorContent || editorContent.trim() === '') {
			alert('Please enter email content');
			return;
		}

		// Show loading state
		button.text('Sending...').prop('disabled', true);

		// AJAX request
		jQuery.ajax({
			url: ajax_ob.ajax_url,
			type: 'POST',
			data: {
				action: 'webcu_send_email_now',
				nonce: ajax_ob.nonce,
				post_id: postId,
				index: index,
				receiver: receiver,
				subject: subject,
				content: editorContent
			},
			success: function (response) {
				if (response.success) {
					alert(response.data.message);

					// Show success message temporarily
					var originalHtml = button.html();
					alert('Email send')
					button.html('<span style="color:green;">✓ Sent</span>');

					setTimeout(function () {
						button.html(originalText);
					}, 3000);

				} else {
					alert('Error: ' + response.data.message);
					button.text(originalText).prop('disabled', false);
				}
			},
			error: function () {
				alert('An error occurred while sending email');
				button.text(originalText).prop('disabled', false);
			}
		});
	});

	// Update button data when form fields change
	$(document).on('change keyup', '.webcu_re_subject, .webcu_re_timing, select[name^="email_reciever_"]', function () {
		var input = $(this);
		var name = input.attr('name');
		var index = name.split('_')[1];
		var button = $('.webcu_re_email-block[data-index="' + index + '"]').find('.webcu_re_send-now');

		// Update subject
		if (name.startsWith('subject_')) {
			button.data('subject', input.val());
		}

		// Update receiver
		if (name.startsWith('email_reciever_')) {
			button.data('receiver', input.val());
		}

		// Update timing
		if (name.startsWith('timing_')) {
			button.data('timing', input.val());
		}
	});

	// Handle timecount radio change
	$(document).on('change', 'input[name^="timecount_"]', function () {
		var input = $(this);
		var name = input.attr('name');
		var index = name.split('_')[1];
		var button = $('.webcu_re_email-block[data-index="' + index + '"]').find('.webcu_re_send-now');

		button.data('timecount', input.val());
	});
}); */


jQuery(document).ready(function ($) {
	// Handle Send Now button click
	$(document).on('click', '.webcu_re_send-now', function (e) {
		e.preventDefault();

		const button = $(this);
		const originalText = button.text();
		const postId = button.data('post-id');
		const index = button.data('index');
		const receiver = button.data('receiver');

		// Get subject from input field
		let subject = $('input[name="subject_' + index + '"]').val();
		if (!subject && button.data('subject')) {
			subject = button.data('subject');
		}

		// Get content from editor
		let content = '';
		if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content_' + index)) {
			content = tinyMCE.get('content_' + index).getContent();
		} else {
			content = $('textarea[name="content_' + index + '"]').val();
		}

		// If no content from editor, try data attribute
		if (!content || content.trim() === '') {
			content = button.data('content') || '';
		}

		// Debug logging
		console.log('Post ID:', postId);
		console.log('Index:', index);
		console.log('Receiver:', receiver);
		console.log('Subject:', subject);
		console.log('Content length:', content ? content.length : 0);

		// Validate
		if (!subject || subject.trim() === '') {
			alert('Please enter email subject');
			return;
		}

		if (!content || content.trim() === '') {
			alert('Please enter email content');
			return;
		}

		// Show loading state
		button.text('Sending...').prop('disabled', true);

		// AJAX request
		$.ajax({
			url: ajax_ob.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'webcu_send_email_now',
				nonce: ajax_ob.nonce,
				post_id: postId,
				index: index,
				receiver: receiver,
				subject: subject,
				content: content
			},
			success: function (response) {
				console.log('AJAX Success:', response);

				if (response.success) {
					// Show success message
					alert('✓ ' + response.data.message);

					// Update button temporarily
					button.html('<span style="color:green;">✓ Sent</span>');

					setTimeout(function () {
						button.text(originalText).prop('disabled', false);
					}, 3000);

				} else {
					alert('✗ Error: ' + (response.data.message || 'Unknown error'));
					button.text(originalText).prop('disabled', false);
				}
			},
			error: function (xhr, status, error) {
				console.log('AJAX Error:', status, error);
				console.log('XHR Response:', xhr.responseText);

				let errorMsg = 'An error occurred while sending email.';

				// Try to parse error from response
				try {
					const errorResponse = JSON.parse(xhr.responseText);
					if (errorResponse && errorResponse.data && errorResponse.data.message) {
						errorMsg = errorResponse.data.message;
					}
				} catch (e) {
					// If can't parse JSON, use default message
					if (xhr.responseText && xhr.responseText.includes('error')) {
						errorMsg = xhr.responseText;
					}
				}

				alert('✗ ' + errorMsg);
				button.text(originalText).prop('disabled', false);
			},
			complete: function () {
				console.log('AJAX request completed');
			}
		});
	});

	// Update button data when form fields change
	$(document).on('change keyup', '.webcu_re_subject, .webcu_re_timing, select[name^="email_reciever_"]', function () {
		var input = $(this);
		var name = input.attr('name');
		var matches = name.match(/(\d+)$/);

		if (matches && matches[1]) {
			var index = matches[1];
			var block = input.closest('.webcu_re_email-block');
			var button = block.find('.webcu_re_send-now');

			if (button.length) {
				// Update subject
				if (name.startsWith('subject_')) {
					button.data('subject', input.val());
				}

				// Update receiver
				if (name.startsWith('email_reciever_')) {
					button.data('receiver', input.val());
				}

				// Update timing
				if (name.startsWith('timing_')) {
					button.data('timing', input.val());
				}
			}
		}
	});

	// Handle timecount radio change
	$(document).on('change', 'input[name^="timecount_"]', function () {
		var input = $(this);
		var name = input.attr('name');
		var matches = name.match(/(\d+)$/);

		if (matches && matches[1]) {
			var index = matches[1];
			var block = input.closest('.webcu_re_email-block');
			var button = block.find('.webcu_re_send-now');

			if (button.length) {
				button.data('timecount', input.val());
			}
		}
	});
});