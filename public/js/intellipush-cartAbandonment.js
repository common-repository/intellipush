/*
	For custom including, put this function to functions.php in your theme :

	<script type="text/javascript" src="<?php echo plugins_url();?>/intellipush/public/js/intellipush-cartAbandonment.js"></script>
	<script type='text/javascript'>
		var intellipush_public_config = <?php echo json_encode(array('ajax_url'=>admin_url( 'admin-ajax.php' )));?>;
	</script>

*/

(function( $ ) {
	'use strict';
	$(window).on('load',function(){
		if(typeof Cookies === 'undefined') {!function(e){var n=!1;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var o=window.Cookies,t=window.Cookies=e();t.noConflict=function(){return window.Cookies=o,t}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,r,i){var c;if("undefined"!=typeof document){if(arguments.length>1){if("number"==typeof(i=e({path:"/"},t.defaults,i)).expires){var a=new Date;a.setMilliseconds(a.getMilliseconds()+864e5*i.expires),i.expires=a}i.expires=i.expires?i.expires.toUTCString():"";try{c=JSON.stringify(r),/^[\{\[]/.test(c)&&(r=c)}catch(e){}r=o.write?o.write(r,n):encodeURIComponent(String(r)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=(n=(n=encodeURIComponent(String(n))).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent)).replace(/[\(\)]/g,escape);var s="";for(var f in i)i[f]&&(s+="; "+f,!0!==i[f]&&(s+="="+i[f]));return document.cookie=n+"="+r+s}n||(c={});for(var p=document.cookie?document.cookie.split("; "):[],d=/(%[0-9A-Z]{2})+/g,u=0;u<p.length;u++){var l=p[u].split("="),C=l.slice(1).join("=");this.json||'"'!==C.charAt(0)||(C=C.slice(1,-1));try{var g=l[0].replace(d,decodeURIComponent);if(C=o.read?o.read(C,g):o(C,g)||C.replace(d,decodeURIComponent),this.json)try{C=JSON.parse(C)}catch(e){}if(n===g){c=C;break}n||(c[g]=C)}catch(e){}}return c}}return t.set=t,t.get=function(e){return t.call(t,e)},t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n(function(){})});}
		var $doc = $(document);
		var isSent = Cookies.get('intellipush_cartAbandonment');
		var userInfo = {};
		var getUserInfo = function() {
			userInfo.phone = $('[name="billing_phone"]').val();
			userInfo.country = $('[name="billing_country"]').val();

			userInfo.name = $('[name="billing_first_name"]').val();
			userInfo.name = $('[name="billing_last_name"]').val() ? userInfo.name + ' ' + $('[name="billing_last_name"]').val() : userInfo.name;

			userInfo.email = $('[name="billing_email"]').val();
			userInfo.company = $('[name="billing_company"]').val();
			userInfo.zip = $('[name="billing_postcode"]').val();
		}

		var sendUserInfo = function() {
			if (isSent) {return}
			getUserInfo();
			if (userInfo.phone && userInfo.country && intellipush_public_config) {
				var args = userInfo;
				args.action = 'intellipush_cartAbandonment';
				args.merge_cart_code = intellipush_public_config.merge_cart_code;
				Cookies.set('intellipush_cartAbandonment', '1', { expires: 1 });
				$.ajax({
					type: 'POST',
					url: intellipush_public_config.ajax_url,
					data: args,
					dataType: 'json',
					success: function(response) {
						isSent = Cookies.get('intellipush_cartAbandonment');
					}
				});
			}
		}

		var debounce = function(n,t,u){var e;return function(){var a=this,i=arguments,o=u&&!e;clearTimeout(e),e=setTimeout(function(){e=null,u||n.apply(a,i)},t),o&&n.apply(a,i)}};

		$doc.on('change','[name="billing_phone"]', debounce(function() {
			$(window).off('beforeunload.IntellipushCartAbandonment');
			sendUserInfo();
		}, 5000));

		$doc.on('click','[name="woocommerce_checkout_place_order"]',function(){
			$(window).off('beforeunload.IntellipushCartAbandonment');
		});

		$(window).on('beforeunload.IntellipushCartAbandonment', function(){
			sendUserInfo();
		});
	});

})( jQuery );
