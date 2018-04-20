/**
 * main.js v1.0.0 (build:20180419)
 * jQuery for Horoscop Wordpress plugin
 * https://wordpress.org/plugins/horoscop/
 * Copyright (c) 2018 VlÄduČ Ilie (@vladwtz)
 * https://vladilie.ro
 */
jQuery(document).ready(function($) {
	$("#horoscop-sign-list a", "#horoscop-widget").on("click", function(e) {
		e.preventDefault();
		var sign = $(this),
			form = new FormData(),
			sign_code = sign.attr("data-code");
		form.append("action", "horoscop_sign_content");
		form.append("nonce", horoscop_sign_content.nonce);
		form.append("sign", sign_code);
		$.ajax({
			url: horoscop_sign_content.ajaxurl,
			type: "POST",
			data: form,
			dataType: "json",
			processData: false,
			contentType: false,
			success: function(response, textStatus, jqXHR) {
				if( response.success ) {
					if ( undefined !== response.data.error ) {
						alert( response.data.error );
					} else {
						$("#horoscop-widget").append('<div id="horoscop-content" style="display:none;"></div><!-- /#horoscop-content -->');
						var list = $('#horoscop-sign-list'),
							content = $('#horoscop-content'),
							animation = response.data.ANIMATION,
							speed = response.data.SPEED;
						content.empty().html( response.data.HTML );
						switch ( animation ) {
							case 0: list.hide( speed, function() {
								content.show( speed );
							});
							break;
							case 1: list.fadeOut( speed, function() {
								content.fadeIn( speed );
							});
							break;
							case 2: list.slideUp( speed, function() {
								content.slideDown( speed );
							});
							break;
							default: list.slideUp( speed, function() {
								content.slideDown( speed );
							});
						}
					}
				} else {
					alert( response.data.error );
				}
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				//console.log( errorThrown );
			},
			complete: function(response) {
				var list = $('#horoscop-sign-list'),
					content = $('#horoscop-content'),
					animation = response.responseJSON.data.ANIMATION,
					speed = response.responseJSON.data.SPEED;
				content.on("click", "#horoscop-sign-title", function() {
					switch ( animation ) {
						case 0: content.hide( speed, function() {
							list.show( speed );
						});
						break;
						case 1: content.fadeOut( speed, function() {
							list.fadeIn( speed );
						});
						break;
						case 2: content.slideUp( speed, function() {
							list.slideDown( speed );
						});
						break;
						default: content.slideUp( speed, function() {
							list.slideDown( speed );
						});
					}
				});
			}
		});
		return false;
	});
});
