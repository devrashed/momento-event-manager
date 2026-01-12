/**
 * Frontend JavaScript
 *
 * @package Ultimate_Events_Manager
 */

(function ($) {
	'use strict';

	$(document).ready(function () {

		// WooCommerce cart update
		if (typeof uemData !== 'undefined' && uemData.isWooCommerce) {
			handleWooCommerceCartUpdate();
		}

		// Simple registration form
		if (typeof uemData !== 'undefined' && !uemData.isWooCommerce) {
			handleSimpleRegistration();
		}

	});

	/**
	 * Handle WooCommerce cart updates
	 */
	function handleWooCommerceCartUpdate() {
		// Update ticket totals immediately on change
		jQuery(document).on('input change', '.uem-ticket-quantity', function () {
			var $input = $(this);
			var quantity = parseInt($input.val()) || 0;
			var ticketPrice = parseFloat($input.data('ticket-price')) || 0;
			var ticketIndex = $input.data('ticket-index');
			var $totalCell = $('.uem-ticket-total[data-ticket-index="' + ticketIndex + '"]');

			// Update individual ticket total
			var ticketTotal = ticketPrice * quantity;
			if (typeof wc_price !== 'undefined') {
				$totalCell.html(wc_price(ticketTotal));
			} else {
				$totalCell.html('$' + ticketTotal.toFixed(2));
			}

			// Update grand total
			updateGrandTotal();
		});

		// Update cart via AJAX when quantity changes
		jQuery(document).on('change', '.uem-ticket-quantity', function () {

			var $input = $(this);
			var cartItemKey = $input.data('cart-item-key');
			var quantity = parseInt($input.val()) || 0;
			var ticketIndex = $input.data('ticket-index');
			var eventId = $input.closest('.uem-woocommerce-registration').data('event-id') || '';

			// Show loading state
			$input.prop('disabled', true);
			jQuery('.uem-checkout-form').addClass('uem-loading');

			// Update cart via AJAX
			jQuery.ajax({
				url: uemData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'webcu_uem_update_cart',
					nonce: uemData.nonce,
					cart_item_key: cartItemKey || '',
					quantity: quantity,
					ticket_index: ticketIndex,
					event_id: eventId
				},
				success: function (response) {
					if (response.success) {
						// Update cart item key if it was returned
						if (response.data.cart_item_key && !cartItemKey) {
							$input.data('cart-item-key', response.data.cart_item_key);
						}

						// Update fragments
						if (response.data.fragments) {
							$.each(response.data.fragments, function (selector, html) {
								var $target = $(selector);
								if ($target.length) {
									if (selector === '#uem-attendee-details-section' && !html) {
										// Remove section if empty
										$target.remove();
									} else {
										// Use html() to replace content, preserving parent structure
										$target.html(html);
										//alert($target.html(html))
									}
								} else if (selector === '#uem-attendee-details-section' && html) {
									// Insert attendee section if it doesn't exist
									var $orderReview = $('.woocommerce-checkout-review-order');
									if ($orderReview.length) {
										// Find the order review table and insert after it
										var $orderTable = $orderReview.find('table.shop_table');
										if ($orderTable.length) {
											$(html).insertAfter($orderTable);
										} else {
											$orderReview.prepend(html);
										}
									}
								}
							});

							// Trigger WooCommerce checkout update to reinitialize scripts
							$('body').trigger('update_checkout');
						}

						// Update ticket totals from response
						if (response.data.ticket_totals) {
							$.each(response.data.ticket_totals, function (index, total) {
								var $totalCell = $('.uem-ticket-total[data-ticket-index="' + index + '"]');
								if (typeof wc_price !== 'undefined') {
									$totalCell.html(wc_price(total));
								} else {
									$totalCell.html('$' + total.toFixed(2));
								}
							});
						}

						// Update grand total
						if (response.data.grand_total !== undefined) {
							var $grandTotal = $('.uem-grand-total');
							if (typeof wc_price !== 'undefined') {
								$grandTotal.html('<strong>' + wc_price(response.data.grand_total) + '</strong>');
							} else {
								$grandTotal.html('<strong>$' + response.data.grand_total.toFixed(2) + '</strong>');
							}
						}

						// Update cart item keys for all inputs
						if (response.data.cart_item_keys) {
							$.each(response.data.cart_item_keys, function (index, key) {
								$('.uem-ticket-quantity[data-ticket-index="' + index + '"]').data('cart-item-key', key);
							});
						}

						// Trigger update checkout event
						$('body').trigger('update_checkout');
					}
				},
				error: function (xhr, status, error) {
					var errorMessage = 'Failed to update cart. Please refresh the page.';
					if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
						errorMessage = xhr.responseJSON.data.message;
					}
					alert(errorMessage);
					console.error('AJAX Error:', status, error, xhr.responseText);
				},
				complete: function () {
					$input.prop('disabled', false);
					$('.uem-checkout-form').removeClass('uem-loading');
				}
			});
		});
	}

	/**
	 * Update grand total
	 */
	function updateGrandTotal() {
		var grandTotal = 0;
		$('.uem-ticket-quantity').each(function () {
			var quantity = parseInt($(this).val()) || 0;
			var ticketPrice = parseFloat($(this).data('ticket-price')) || 0;
			grandTotal += ticketPrice * quantity;
		});

		var $grandTotal = $('.uem-grand-total');
		if (typeof wc_price !== 'undefined') {
			$grandTotal.html('<strong>' + wc_price(grandTotal) + '</strong>');
		} else {
			$grandTotal.html('<strong>$' + grandTotal.toFixed(2) + '</strong>');
		}
	}



	/**
	 * Handle simple registration form
	 */
	function handleSimpleRegistration() {
		// Update totals when quantity changes
		$(document).on('change', '.uem-ticket-quantity', function () {
			updateSimpleRegistrationTotals();
			updateAttendeeFields();
		});

		// Form submission
		$('#uem-registration-form').on('submit', function (e) {
			var form = $(this);

			// Validate
			if (!validateSimpleRegistrationForm()) {
				e.preventDefault();
				return false;
			}

			// Submit via AJAX
			e.preventDefault();

			var formData = form.serialize();
			formData += '&action=uem_submit_registration&nonce=' + uemData.nonce;

			$.ajax({
				url: uemData.ajaxUrl,
				type: 'POST',
				data: formData,
				beforeSend: function () {
					form.addClass('uem-loading');
					$('.uem-submit-registration').prop('disabled', true).text('Submitting...');
				},
				success: function (response) {
					if (response.success) {
						window.location.href = response.data.redirect_url;
					} else {
						alert(response.data.message || 'Registration failed. Please try again.');
						form.removeClass('uem-loading');
						$('.uem-submit-registration').prop('disabled', false).text('Submit Registration');
					}
				},
				error: function () {
					alert('An error occurred. Please try again.');
					form.removeClass('uem-loading');
					$('.uem-submit-registration').prop('disabled', false).text('Submit Registration');
				}
			});
		});
	}

	/**
	 * Update simple registration totals
	 */
	function updateSimpleRegistrationTotals() {
		var grandTotal = 0;

		$('.uem-ticket-quantity').each(function () {
			var $input = $(this);
			var quantity = parseInt($input.val()) || 0;
			var price = parseFloat($input.data('ticket-price')) || 0;
			var ticketIndex = $input.data('ticket-index');
			var total = quantity * price;

			// Update ticket total
			$('.uem-ticket-total[data-ticket-index="' + ticketIndex + '"]').text(formatPrice(total));

			grandTotal += total;
		});

		// Update grand total
		$('.uem-grand-total').html('<strong>' + formatPrice(grandTotal) + '</strong>');
	}

	/**
	 * Update attendee fields based on total quantity
	 */

	function updateAttendeeFields_Main() {
		var totalQuantity = 0;

		$('.uem-ticket-quantity').each(function () {

			var quantity = parseInt($(this).val()) || 0;
			totalQuantity += quantity;
		});

		var $attendeeDetails = $('#uem-attendee-details');
		var $attendeeFields = $('#uem-attendee-fields');

		if (totalQuantity > 0) {
			$attendeeDetails.show();

			// Generate attendee fields
			var html = '';
			for (var i = 1; i <= totalQuantity; i++) {
				html += '<div class="uem-attendee-field-group">';
				html += '<h4>Attendee ' + i + '</h4>';
				html += '<p>';
				html += '<label for="uem_attendee_name_' + i + '">Name <span class="required">*</span></label>';
				html += '<input type="text" name="uem_attendee_name_' + i + '" id="uem_attendee_name_' + i + '" required>';
				html += '</p>';
				html += '<p>';
				html += '<label for="uem_attendee_phone_' + i + '">Phone <span class="required">*</span></label>';
				html += '<input type="tel" name="uem_attendee_phone_' + i + '" id="uem_attendee_phone_' + i + '" required>';
				html += '</p>';
				html += '<p>';
				html += '<label for="uem_attendee_email_' + i + '">Email</label>';
				html += '<input type="email" name="uem_attendee_email_' + i + '" id="uem_attendee_email_' + i + '">';
				html += '</p>';
				html += '</div>';
			}

			$attendeeFields.html(html);
		} else {
			$attendeeDetails.hide();
			$attendeeFields.html('');
		}
	}


	/* ===== New Script =====*/

	(function ($) {
		'use strict';

		// Configuration constants
		const CONFIG = {
			TICKET_QUANTITY_SELECTOR: '.uem-ticket-quantity',
			ATTENDEE_SECTION_SELECTOR: '#uem-attendee-details-section',
			ATTENDEE_WRAPPER_SELECTOR: '.uem-attendee-fields-wrapper',
			ATTENDEE_GROUP_SELECTOR: '.uem-attendee-group',
			ATTENDEE_FORM_TEMPLATE: `
				<div class="uem-attendee-group" data-attendee-index="[index]" data-attendee-id="attendee-[index]">
					<div class="uem-attendee-header">
						<h4 class="attendee-title">
							<span class="attendee-number">[index]</span>
							<span class="attendee-label">[label]</span>
						</h4>
					</div>
					<div class="uem-attendee-form-fields">
						<div class="uem-attendee-field-group">
							<p class="uem-attendee-field-row">
								<label for="uem_attendee_name_[index]">Name <span class="required">*</span></label>
								<input type="text" name="uem_attendee_name_[index]" id="uem_attendee_name_[index]" class="uem-attendee-input" value="" required>
							</p>
							<p class="uem-attendee-field-row">
								<label for="uem_attendee_phone_[index]">Phone <span class="required">*</span></label>
								<input type="tel" name="uem_attendee_phone_[index]" id="uem_attendee_phone_[index]" class="uem-attendee-input" value="" required>
							</p>
							<p class="uem-attendee-field-row">
								<label for="uem_attendee_email_[index]">Email</label>
								<input type="email" name="uem_attendee_email_[index]" id="uem_attendee_email_[index]" class="uem-attendee-input" value="">
							</p>
						</div>
					</div>
				</div>`
		};

		// State management
		let currentAttendeeCount = 0;
		let isUpdating = false;

		// Initialize
		$(document).ready(function () {
			initializeAttendeeManager();
		});

		/**
		 * Initialize the attendee form manager
		 */
		function initializeAttendeeManager() {
			console.log('UEM Attendee Manager Initializing...');

			// Bind events
			bindEvents();

			// Initial update
			updateAttendeeFields();

			// Handle AJAX cart updates
			handleAjaxCartUpdates();

			console.log('UEM Attendee Manager Initialized');
		}

		/**
		 * Bind all necessary events
		 */
		function bindEvents() {
			// Quantity change with debounce
			$(document).on('change', CONFIG.TICKET_QUANTITY_SELECTOR, debounce(handleQuantityChange, 300));

			// Input events for immediate feedback
			$(document).on('input', CONFIG.TICKET_QUANTITY_SELECTOR, debounce(handleQuantityInput, 150));

			// Handle keyboard navigation
			$(document).on('keydown', CONFIG.TICKET_QUANTITY_SELECTOR, handleQuantityKeydown);

			// Focus/blur for better UX
			$(document).on('focus', CONFIG.TICKET_QUANTITY_SELECTOR, handleQuantityFocus);
			$(document).on('blur', CONFIG.TICKET_QUANTITY_SELECTOR, handleQuantityBlur);
		}

		/**
		 * Handle AJAX cart updates
		 */
		function handleAjaxCartUpdates() {
			$(document).on('updated_cart_totals updated_checkout', function () {
				console.log('Cart updated, refreshing attendee fields...');
				setTimeout(updateAttendeeFields, 100);
			});
		}

		/**
		 * Handle quantity input change
		 */
		function handleQuantityChange(event) {
			event.preventDefault();
			console.log('Quantity changed:', $(this).val());
			updateAttendeeFields();

			// Dispatch custom event
			$(document).trigger('uem:attendeeFieldsChanged', {
				quantity: $(this).val(),
				element: this
			});
		}

		/**
		 * Handle quantity input (for real-time updates)
		 */
		function handleQuantityInput(event) {
			event.preventDefault();
			// Real-time updates can be handled here
			// We'll just update for immediate visual feedback on large changes
			const newValue = parseInt($(this).val()) || 0;
			if (Math.abs(newValue - currentAttendeeCount) > 3) {
				updateAttendeeFields();
			}
		}

		/**
		 * Handle keyboard navigation
		 */
		function handleQuantityKeydown(event) {
			// Allow only numbers and control keys
			const allowedKeys = [8, 9, 13, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 46];
			const isNumber = (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105);

			if (!isNumber && allowedKeys.indexOf(event.keyCode) === -1) {
				event.preventDefault();
			}
		}

		/**
		 * Handle quantity focus
		 */
		function handleQuantityFocus(event) {
			$(this).addClass('is-focused');
		}

		/**
		 * Handle quantity blur
		 */
		function handleQuantityBlur(event) {
			$(this).removeClass('is-focused');
			// Validate and ensure minimum value
			const value = parseInt($(this).val()) || 0;
			const min = parseInt($(this).attr('min')) || 0;
			if (value < min) {
				$(this).val(min).trigger('change');
			}
		}

		/**
		 * Calculate total quantity from all ticket inputs
		 */
		function calculateTotalQuantity() {
			let totalQuantity = 0;

			$(CONFIG.TICKET_QUANTITY_SELECTOR).each(function () {
				const $input = $(this);
				const quantity = parseInt($input.val()) || 0;

				// Ensure we don't count negative numbers
				if (quantity > 0) {
					totalQuantity += quantity;
				}
			});

			return totalQuantity;
		}

		/**
		 * Update attendee fields based on total quantity
		 */
		function updateAttendeeFields() {
			if (isUpdating) return;

			isUpdating = true;

			const totalQuantity = calculateTotalQuantity();
			const $attendeeSection = $(CONFIG.ATTENDEE_SECTION_SELECTOR);
			const $attendeeWrapper = $(CONFIG.ATTENDEE_WRAPPER_SELECTOR);

			// Store existing data before clearing
			const existingData = updateAttendeeFields();

			if (totalQuantity > 0) {
				showAttendeeSection($attendeeSection);
				generateAttendeeForms(totalQuantity, $attendeeWrapper, existingData);
			} else {
				hideAttendeeSection($attendeeSection);
				clearAttendeeForms($attendeeWrapper);
			}

			currentAttendeeCount = totalQuantity;
			isUpdating = false;

			// Dispatch update complete event
			$(document).trigger('uem:attendeeFieldsUpdated', {
				attendeeCount: totalQuantity
			});
		}

		/**
		 * Show attendee section with animation
		 */
		function showAttendeeSection($section) {
			if ($section.is(':visible')) return;

			$section.stop(true, true)
				.css({
					'opacity': 0,
					'display': 'block'
				})
				.animate({
					'opacity': 1
				}, 300);

			// Accessibility improvements
			$section.attr('aria-hidden', 'false');
			announceToScreenReader('Attendee details section is now visible');
		}

		/**
		 * Hide attendee section with animation
		 */
		function hideAttendeeSection($section) {
			if (!$section.is(':visible')) return;

			$section.stop(true, true)
				.animate({
					'opacity': 0
				}, 300, function () {
					$(this).css('display', 'none');
				});

			// Accessibility improvements
			$section.attr('aria-hidden', 'true');
		}

		/**
		 * Generate attendee forms based on quantity
		 */
		function generateAttendeeForms(totalQuantity, $wrapper, existingData) {
			let html = '';

			// Generate HTML for each attendee
			for (let i = 1; i <= totalQuantity; i++) {
				const attendeeLabel = getAttendeeLabel(i);
				const attendeeHtml = CONFIG.ATTENDEE_FORM_TEMPLATE
					.replace(/\[index\]/g, i)
					.replace(/\[label\]/g, attendeeLabel);

				html += attendeeHtml;
			}

			// Update with animation
			$wrapper.stop(true, true)
				.fadeOut(200, function () {
					$(this).html(html).fadeIn(300, function () {
						// Restore existing data if available
						restoreFormData(existingData, totalQuantity);

						// Initialize new forms
						initializeAttendeeForms();

						// Add sequential animation for each attendee
						animateAttendeeForms($wrapper);

						// Focus first input for better UX
						focusFirstAttendeeInput($wrapper);
					});
				});
		}

		/**
		 * Get appropriate label for attendee
		 */
		function getAttendeeLabel(index) {
			const labels = [
				'Primary Attendee',
				'Additional Attendee',
				'Additional Attendee',
				'Additional Attendee'
			];

			if (index <= labels.length) {
				return labels[index - 1];
			}
			return `Attendee ${index}`;
		}

		/**
		 * Collect existing form data before regenerating
		 */
		function updateAttendeeFields() {
			const data = {};
			const $attendeeGroups = $(CONFIG.ATTENDEE_GROUP_SELECTOR);

			$attendeeGroups.each(function (index) {
				const $group = $(this);
				const attendeeIndex = $group.data('attendee-index') || (index + 1);
				data[attendeeIndex] = {};

				$group.find('.uem-attendee-input').each(function () {
					const $input = $(this);
					const name = $input.attr('name');
					const value = $input.val();

					if (name && value) {
						data[attendeeIndex][name] = value;
					}
				});
			});

			return data;
		}

		/**
		 * Restore form data after regeneration
		 */
		function restoreFormData(existingData, totalQuantity) {
			$.each(existingData, function (oldIndex, attendeeData) {
				// Only restore if this attendee still exists
				if (oldIndex <= totalQuantity) {
					$.each(attendeeData, function (fieldName, fieldValue) {
						// Update field name to match new indexing if needed
						const $field = $(`[name="${fieldName}"]`);
						if ($field.length) {
							$field.val(fieldValue).trigger('change');
						}
					});
				}
			});
		}

		/**
		 * Initialize attendee forms
		 */
		function initializeAttendeeForms() {
			$(CONFIG.ATTENDEE_GROUP_SELECTOR).each(function () {
				const $group = $(this);
				const index = $group.data('attendee-index');

				// Set ARIA attributes for accessibility
				$group.attr({
					'role': 'group',
					'aria-labelledby': `attendee-title-${index}`
				});

				// Add unique IDs to titles
				$group.find('.attendee-title').attr('id', `attendee-title-${index}`);

				// Initialize input events
				initializeAttendeeInputEvents($group);
			});
		}

		/**
		 * Initialize attendee input events
		 */
		function initializeAttendeeInputEvents($group) {
			$group.find('.uem-attendee-input').on('focus', function () {
				$(this).addClass('is-focused');
			}).on('blur', function () {
				$(this).removeClass('is-focused');
			}).on('change', function () {
				$(this).addClass('is-dirty');
			});
		}

		/**
		 * Animate attendee forms sequentially
		 */
		function animateAttendeeForms($wrapper) {
			$wrapper.find(CONFIG.ATTENDEE_GROUP_SELECTOR).each(function (index) {
				const $group = $(this);
				$group.css({
					'opacity': 0,
					'transform': 'translateY(10px)'
				}).delay(index * 100).animate({
					'opacity': 1,
					'transform': 'translateY(0)'
				}, 300);
			});
		}

		/**
		 * Focus first attendee input
		 */
		function focusFirstAttendeeInput($wrapper) {
			const $firstInput = $wrapper.find('.uem-attendee-input').first();
			if ($firstInput.length) {
				setTimeout(function () {
					$firstInput.focus();
				}, 500);
			}
		}

		/**
		 * Clear attendee forms
		 */
		function clearAttendeeForms($wrapper) {
			$wrapper.stop(true, true)
				.fadeOut(200, function () {
					$(this).empty();
				});
		}

		/**
		 * Announce to screen readers
		 */
		function announceToScreenReader(message) {
			// Create and immediately remove aria-live element
			const $announcement = $('<div>', {
				'class': 'sr-only',
				'aria-live': 'polite',
				'aria-atomic': 'true'
			}).text(message);

			$('body').append($announcement);

			setTimeout(function () {
				$announcement.remove();
			}, 1000);
		}

		/**
		 * Debounce function for performance
		 */
		function debounce(func, wait) {
			let timeout;
			return function () {
				const context = this;
				const args = arguments;
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					func.apply(context, args);
				}, wait);
			};
		}

		/**
		 * Public API for external access
		 */
		window.UEMAttendeeForms = {
			refresh: function () {
				updateAttendeeFields();
			},
			getAttendeeCount: function () {
				return currentAttendeeCount;
			},
			destroy: function () {
				$(document).off('change', CONFIG.TICKET_QUANTITY_SELECTOR);
				$(document).off('input', CONFIG.TICKET_QUANTITY_SELECTOR);
				$(document).off('keydown', CONFIG.TICKET_QUANTITY_SELECTOR);
				$(document).off('focus blur', CONFIG.TICKET_QUANTITY_SELECTOR);
				$(document).off('updated_cart_totals updated_checkout');
				console.log('UEM Attendee Forms destroyed');
			}
		};

	})(jQuery);


	/* =============================== 
		 Validate generated script 
	   ============================= */

	jQuery(document).ready(function ($) {
		// Clone the first attendee form for duplication
		var originalAttendeeForm = $('.webcu-attendee-form-section:first').clone();

		// Function to update attendee forms based on quantity
		function updateAttendeeForms(quantity) {
			var formsContainer = $('.webcu-attendee-forms-wrapper');
			var currentForms = formsContainer.children('.webcu-attendee-form-section').length;

			// Remove extra forms if quantity decreased
			if (currentForms > quantity) {
				formsContainer.children('.webcu-attendee-form-section:gt(' + (quantity - 1) + ')').remove();
			}
			// Add new forms if quantity increased
			else if (currentForms < quantity) {
				for (var i = currentForms + 1; i <= quantity; i++) {
					var newForm = originalAttendeeForm.clone();

					// Update labels, IDs, and names
					newForm.find('h4').text('Attendee ' + i);
					newForm.attr('data-attendee-index', i);

					newForm.find('input, select, textarea').each(function () {
						var $this = $(this);
						var oldName = $this.attr('name');
						var oldId = $this.attr('id');

						if (oldName) {
							var newName = oldName.replace(/_\d+$/, '_' + i);
							$this.attr('name', newName);
						}

						if (oldId) {
							var newId = oldId.replace(/_\d+$/, '_' + i);
							$this.attr('id', newId);
						}

						// Clear values for new forms
						if (i > 1) {
							if ($this.is(':checkbox') || $this.is(':radio')) {
								$this.prop('checked', false);
							} else {
								$this.val('');
							}
						}
					});

					// Update labels' for attributes
					newForm.find('label').each(function () {
						var $this = $(this);
						var oldFor = $this.attr('for');
						if (oldFor) {
							var newFor = oldFor.replace(/_\d+$/, '_' + i);
							$this.attr('for', newFor);
						}
					});

					formsContainer.append(newForm);
				}
			}

			// Update the note about number of attendees
			$('.webcu-attendee-note').text('Please fill information for all ' + quantity + ' attendee(s).');
		}

		// Monitor quantity changes
		$('input.qty').on('change', function () {
			var quantity = parseInt($(this).val()) || 1;
			updateAttendeeForms(quantity);
		});

		// Initial update based on current quantity
		var initialQuantity = parseInt($('input.qty').val()) || 1;
		updateAttendeeForms(initialQuantity);
	});










	/* === End the generated script ====*/




	/**
	 * Validate simple registration form
	 */
	function validateSimpleRegistrationForm() {
		var isValid = true;

		// Check if at least one ticket is selected
		var hasTickets = false;
		$('.uem-ticket-quantity').each(function () {
			if (parseInt($(this).val()) > 0) {
				hasTickets = true;
				return false;
			}
		});

		if (!hasTickets) {
			alert('Please select at least one ticket.');
			isValid = false;
		}

		// Validate required fields
		if (!$('#uem_registration_name').val().trim()) {
			alert('Please enter your name.');
			$('#uem_registration_name').focus();
			isValid = false;
		}

		if (!$('#uem_registration_phone').val().trim()) {
			alert('Please enter your phone number.');
			$('#uem_registration_phone').focus();
			isValid = false;
		}

		return isValid;
	}

	/**
	 * Format price
	 */
	function formatPrice(amount) {
		// This is a simple formatter - WooCommerce has its own
		if (typeof wc_price !== 'undefined') {
			return wc_price(amount);
		}
		return '$' + amount.toFixed(2);
	}

})(jQuery);
