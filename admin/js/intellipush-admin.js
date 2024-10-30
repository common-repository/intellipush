(function($) {
	'use strict';
	var $doc = $(document);

	preventSubmitOnEnter();
	checkAndShortUrl();
	SMSCounter();
	Messages_sendNow();

	$(window).on('load',function(){
		$('.acf-accordion.-open .acf-accordion-title').trigger('click');
	});

	function SMSCounter() {
		var removeNonUnicodeCharacters=function(u){var e=u;return e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=(e=e.replace(/[\u00A0]/g," ")).replace(/[\u0009]/g," ")).replace(/[\u2028]/g,"\r\n")).replace(/[\u2013]/g,"-")).replace(/[ÀÁÂÃÄ]/g,"A")).replace(/[áâãä]/g,"a")).replace(/[ÈÉÊË]/g,"E")).replace(/[êë]/g,"e")).replace(/[Ç]/g,"C")).replace(/[ç]/g,"c")).replace(/[Ô]/g,"O")).replace(/[ô]/g,"o")).replace(/[Ù]/g,"U")).replace(/[ú]/g,"u")).replace(/[Î]/g,"I")).replace(/[î]/g,"i")).replace(/[^\u000A\u0020\u0020\u0040\u00A3\u005C\u0024\u00A5\u00E8\u00E9\u00F9\u00EC\u00F2\u00C7\u00D8\u00F8\u00C5\u00E5\u005F\u00C6\u00E6\u00DF\u00C9\u0021\u005C\u0022\u0023\u00A4\u0025\u0026\u0027\u0028\u0029\u002A\u002B\u002C\u002D\u002E\u002F\u0030\u0031\u0032\u0033\u0034\u0035\u0036\u0037\u0038\u0039\u003A\u003B\u003C\u003D\u003E\u003F\u00A1\u0041\u0042\u0043\u0044\u0045\u0046\u0047\u0048\u0049\u004A\u004B\u004C\u004D\u004E\u004F\u0050\u0051\u0052\u0053\u0054\u0055\u0056\u0057\u0058\u0059\u005A\u00C4\u00D6\u00D1\u00DC\u00A7\u00BF\u0061\u0062\u0063\u0064\u0065\u0066\u0067\u0068\u0069\u006A\u006B\u006C\u006D\u006E\u006F\u0070\u0071\u0072\u0073\u0074\u0075\u0076\u0077\u0078\u0079\u007A\u00E4\u00F6\u00F1\u00FC\u00E0\u005E\u007B\u007D\u005B\u007E\u005D\u007C\u20AC]/g,"")};
		var CheckForUnicodeCharacters=function(r){var t=!1,e=0;for(e=0;e<4;e++)0==t&&(t=/[\u0100-\u20AB]+/g.test(r)||/[\u20AD-\uFFFF]+/g.test(r));return t};
		var CountSpecialChars=function(C){var r=0;return r+=CountChar("^",C),r+=CountChar("{",C),r+=CountChar("}",C),r+=CountChar("\\",C),r+=CountChar("[",C),r+=CountChar("~",C),r+=CountChar("]",C),r+=CountChar("|",C),r+=CountChar(String.fromCharCode(8364),C)};
		var CountChar=function(n,t){return t.split(n).length-1};
		var setCharacterAndSMSInformation = function ($input) {
			var message = $input.val();
			var $target = $input.parents('.ip--sms-counter-field').find('.ip--sms-counter');
			var totalMessageLength = 1;
			var numberOfSms = 1;
			var SMSlengthLimit = 0;

			if (CheckForUnicodeCharacters(message)) {
				SMSlengthLimit = 69;
				totalMessageLength = message.length;
			} else {
				SMSlengthLimit = 159;
				totalMessageLength = message.length + CountSpecialChars(message);
			}
			if (totalMessageLength > SMSlengthLimit) {
				SMSlengthLimit = SMSlengthLimit - 7;
			}
			while ((SMSlengthLimit * numberOfSms) <= totalMessageLength - 1) {
				numberOfSms++;
			}

			var lettersBeforeNew = (SMSlengthLimit * numberOfSms) - totalMessageLength;

			$target.find('.ip--sms-counter-length').html(lettersBeforeNew);
			$target.find('.ip--sms-counter-number-of-sms').html(numberOfSms);
		};
		$doc.on('keyup', '.ip--sms-counter-field textarea', function(){
			var $this = $(this);
			$this.val(function(){
				return removeNonUnicodeCharacters(this.value);
			});
			setCharacterAndSMSInformation($this);
		});
		$('.ip--sms-counter-field textarea').each(function(){
			setCharacterAndSMSInformation($(this));
		})
	};

	function checkAndShortUrl() {
		var elements = [
			'.acf-field-intellipush-messages-sendNow textarea',
			'.acf-field-intellipush-messages-templates-message textarea'
		];
		$doc.on('input', elements.join(), function(){
			var $this = $(this);
			var message = $this.val();
			var regexp = /(https?:\/\/(?!.*1p.nu)[^\s]+)/g;
			if (regexp.exec(message)) {
				if (!$this.next('.ip-do-short-url').length) {
					$('<a class="button button-small ip-do-short-url ip--float-right ip--margin-top-5 ip--margin-bottom-10" data-target="">Short url?</a>').on('click',function(){
						var $button = $(this);
						var $textarea = $button.prev('textarea');
						if ($textarea.length) {
							var urls = $textarea.val().match(regexp);
							if (urls.length) {
								$.each(urls, function(i, url){
									$.ajax({
										type: 'POST',
										url: acf.get('ajaxurl'),
										data: {
											action: 'intellipush_helper_createShortUrl',
											url: url
										},
										dataType: 'json',
										success: function(response) {
											if(response && response.success) {
												$textarea.val(function(){
													return this.value.replace(url, response.response.data.short_url);
												});
												$button.remove();
											}
										}
									});
								});
								if (urls.length > 1) {
									alert('There should be only one shortened URL per message.');
								}
							}
						}
					}).insertAfter($this);
				}
			} else {
				$this.next('.ip-do-short-url').remove();
			}
		});
	}

	function Messages_sendNow() {
		var $parent = $('#acf-group_intellipush_messages_sendNow');
		var $action = $parent.find('.acf-field-intellipush-messages-sendNow-action');
		$action.on('click', '.acf-button:not(.button-disabled)', function(){
			var args = {
				message: $('#acf-field_intellipush_messages_sendnow').val(),
				contactlist: $('#acf-field_intellipush_messages_sendnow_contactlist').val(),
				telephone: $('#acf-field_intellipush_messages_sendnow_telephone').val()
			};

			if ( args.message && (args.contactlist || args.telephone) ) {
				$('.intellipush-messages-sendNow-status').html('');
				$('.intellipush-messages-sendNow-confirmed').removeClass('button-disabled');
				$('body').addClass('intellipush-thickbox');
				tb_show('','#TB_inline?inlineId=intellipush-messages-sendNow-confirmation');
			}
			return false;
		});

		$doc.on('click', '.intellipush-messages-sendNow-confirmed:not(.button-disabled)', function(){
			var $button = $(this);
			var args = {
				action: 'intellipush_sendMessage',
				message: $('#acf-field_intellipush_messages_sendnow').val(),
				contactlist: $('#acf-field_intellipush_messages_sendnow_contactlist').val(),
				telephone: $('#acf-field_intellipush_messages_sendnow_telephone').val(),
				delay: $('#acf-field_intellipush_messages_sendnow_delay').val(),
				repeat: $('#acf-field_intellipush_messages_sendnow_repeat').val()
			};
			$button.addClass('button-disabled');
			$action.find('.acf-button').addClass('button-disabled');
			$.ajax({
				type: 'POST',
				url: acf.get('ajaxurl'),
				data: args,
				dataType: 'json',
				success: function(response) {
					if(response) {
						var $status = $('.intellipush-messages-sendNow-status');
						$status.html('');
						$.each(response, function(key, value) {
							if (value && value.success) {
								if (key === 'sent-to-telephonenumber') {
									var telephones = [];
									if (value.response && !Array.isArray(value.response)) {
										telephones.push('(' + value.response.data.single_target_countrycode + ')' + value.response.data.single_target);
									} else if (value.response.length > 1) {
										$.each( value.response, function(key, value) {
											telephones.push('(' + value.data.single_target_countrycode + ')' + value.data.single_target);
										});
									}
									$status.append('<div><strong class="ip--color-green">Sent to telephone:</strong> <small>' + telephones.join(', ') + '</small></div>');
								}
								if (key === 'sent-to-contactlist') {
									$status.append('<div><strong class="ip--color-green">Sent to contactlist:</strong> <small>' + $('#acf-field_intellipush_messages_sendNow_contactlist option:selected').text() + '</small></div>');
								}
							} else {
								if (key === 'sent-to-telephonenumber') {
									$status.append('<div class="ip--color-red"><strong>Error:</strong> <small>Can not find a valid telephone number</small></div>');
								}
								if (key === 'sent-to-contactlist') {
									$status.append('<div class="ip--color-red"><strong>Error:</strong> <small>' + value.message + '</small></div>');
								}
							}
						});
					}
					$action.find('.acf-button').removeClass('button-disabled');
				}
			});
			return false;
		});
	}

	function preventSubmitOnEnter() {
		$('.intellipush_page_intellipush-tools form#post').on('keyup keypress', function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) { 
				e.preventDefault();
				return false;
			}
		});
	}

	$doc.on('click', '.ip--send-now-load-message-templates', function(){
		var $this = $(this);
		$.ajax({
			type: 'POST',
			url: acf.get('ajaxurl'),
			data: {
				action: 'intellipush_helper_getMessageTemplates'
			},
			dataType: 'json',
			success: function(response) {
				if (response && response.success) {
					if (!$this.find('select').length) {
						var $select = $('<select class="ip--display-none"></select>');
						$.each(response.data, function(k, v){
							$select.append('<option value="' + encodeURI(v.message) + '">' + v.name + '</option>');
						});
						$select.appendTo($this);
						$this.find('select').on('change', function () {
							var message = decodeURI($(this).val());
							$this.parents().find('#acf-field_intellipush_messages_sendnow').val(message);
						});
						$this.find('select').select2({dropdownCssClass: 'ip--select2-dropdown-hidden'});
					}
					$this.find('select').val('').select2('open');
				} else {
					if(!$this.next('small').length) {
						var $noTemplate = $('<small class="ip--display-inline-block ip--margin-left-5 ip--color-red ip--font-weight-normal ip--text-decoration-none">— You do not have any template yet.</small>');
						$noTemplate.insertAfter($this);
						setTimeout(function(){
							$noTemplate.remove();
						}, 3000);
					}
				}
			}
		});
	})

})(jQuery);
