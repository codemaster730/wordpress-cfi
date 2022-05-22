/* global jQuery */
(function ($) {
	'use strict';

	var EPKBAdminDialogApp = {
		cacheElements: function cacheElements() {
			this.cache = {
				$deactivateLink: $('#the-list').find('[data-slug="echo-knowledge-base"] span.deactivate a'),
				$dialogHeader: $('#epkb-deactivate-feedback-dialog-header'),
				$dialogForm: $('#epkb-deactivate-feedback-dialog-form')
			};
		},
		bindEvents: function bindEvents() {
			var self = this;
			self.cache.$deactivateLink.on('click', function (event) {
				event.preventDefault();
				self.getModal().show();
			});
		},
		deactivate: function deactivate() {
			location.href = this.cache.$deactivateLink.attr('href');
		},
		initModal: function initModal() {
			var self = this,
				modal;

			self.getModal = function () {
				if (!modal) {
					var dialogsManager = new EPKBDialogsManager.Instance();
					modal = dialogsManager.createWidget('lightbox', {
						id: 'epkb-deactivate-feedback-modal',
						headerMessage: self.cache.$dialogHeader,
						classes: {
							globalPrefix: 'epkb-dialog',
						},
						message: self.cache.$dialogForm,
						hide: {
							onButtonClick: false
						},
						position: {
							my: 'center',
							at: 'center'
						},
						onReady: function onReady() {

							EPKBDialogsManager.getWidgetType('lightbox').prototype.onReady.apply(this, arguments);


							this.addButton({
								name: 'submit',
								text: 'Submit & Deactivate',
								callback: function callback() {

									if ( $('.epkb-deactivate-feedback-dialog-input-wrapper input[name="reason_key"]:checked').length ) {
										jQuery('#epkb-deactivate-feedback-dialog-form-error').hide();
										self.sendFeedback();
									}
									else {
										jQuery('#epkb-deactivate-feedback-dialog-form-error').show();
									}
								}
							});

							this.addButton({
								name: 'skip',
								text: 'Skip & Deactivate',
								callback: function callback() {
									self.deactivate();
								}
							});

						},
						onShow: function onShow() {
							var $dialogModal = $('#epkb-deactivate-feedback-modal'),
								radioSelector = '.epkb-deactivate-feedback-dialog-input';
							$dialogModal.find(radioSelector).on('change', function () {
								$dialogModal.attr('data-feedback-selected', $(this).val());
							});
							$dialogModal.find(radioSelector + ':checked').trigger('change');
						}
					});
				}

				return modal;
			};
		},
		sendFeedback: function sendFeedback() {
			var self = this,
				formData = self.cache.$dialogForm.serialize();
			self.getModal().getElements('submit').text('').addClass('epkb-loading');
			$.post(ajaxurl, formData, this.deactivate.bind(this));
		},
		init: function init() {
			this.initModal();
			this.cacheElements();
			this.bindEvents();
		}
	};
	$(function () {
		EPKBAdminDialogApp.init();
	});
})(jQuery);