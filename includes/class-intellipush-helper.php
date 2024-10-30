<?php
/**
 * Intellupush helper functions
 * @since      1.0.0
 */

use Intellipush\Intellipush;
use Intellipush\User;
use Intellipush\Contact;
use Intellipush\Contactlist;
use Intellipush\Notification\Sms;
use Intellipush\Notification\Notifications;
use Intellipush\Contact\Filter;
use Intellipush\Url;
use Intellipush\Notification\Batch;
use Intellipush\Contact\BatchImport;
use Intellipush\Statistics;


/* ---------- Start : Define varibles ---------- */
$isAuth = IntellipushHelper_isAuth();
$INTELLIPUSH_TRANSIENT_EXPIRATION_SHORT 		= $isAuth ? 1 * MINUTE_IN_SECONDS : 1 * DAY_IN_SECONDS;
$INTELLIPUSH_TRANSIENT_EXPIRATION_LONG 			= $isAuth ? 5 * MINUTE_IN_SECONDS : 5 * DAY_IN_SECONDS;
/* ---------- End : Define varibles ---------- */


function IntellipushHelper_init() {
	$intellipush = get_transient('intellipush_helper_init');
	if ($intellipush === false) {
		$intellipush = new Intellipush(get_option('intellipush_settings_api_id', '0000000'), get_option('intellipush_settings_api_secret_key', '00000000000000000000000000000000'));
		set_transient('intellipush_helper_init', $intellipush, $INTELLIPUSH_TRANSIENT_EXPIRATION_LONG);
	}
	return $intellipush;
}

function IntellipushHelper_getUserInfo() {
	$userInfo = get_transient('intellipush_helper_userInfo');
	if ( $userInfo === false ) {
		$userInfo = IntellipushHelper_init()->read(new User());
		set_transient('intellipush_helper_userInfo', $userInfo, $INTELLIPUSH_TRANSIENT_EXPIRATION_SHORT);
	}
	return $userInfo;
}

function IntellipushHelper_getStatistics() {
	$statistics = get_transient('intellipush_helper_statistics');
	if ( $statistics === false ) {
		$statistics = IntellipushHelper_init()->read(new Statistics ());
		set_transient('intellipush_helper_statistics', $statistics, $INTELLIPUSH_TRANSIENT_EXPIRATION_LONG);
	}
	return $statistics;
}

function IntellipushHelper_getNotifications() {
	$notifications = new Notifications();
	$allNotifications = get_transient('intellipush_helper_notifications');
	if ( $allNotifications === false ) {
		$notifications->items(10000)->page(1);
		$allNotifications = IntellipushHelper_init()->read($notifications);
		get_transient('intellipush_helper_notifications', $allNotifications, $INTELLIPUSH_TRANSIENT_EXPIRATION_SHORT);
	}
	return $allNotifications;
}

function IntellipushHelper_createContactlist($contactlistName) {
	$response = null;
	if ($contactlistName) {
		$contactlist = new Contactlist();
		$contactlist->name($contactlistName);
		$response = IntellipushHelper_init()->create($contactlist);
		if ($response->success) {
			IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
			IntellipushHelper_clearCache( 'intellipush_helper_contactlist' );
		}
	}
	return $response;
}

function IntellipushHelper_addContact($name, $country, $phoneNumber, $email, $company = null, $zipCode = null, $sex = null) {
	$response = null;
	if ($name && $country && $phoneNumber) {
		$country = IntellipushHelper_getCountryInfoByContryName($country);
		if ($country) {
			$contact = new Contact();
			$contact->name($name)->countrycode($country['phoneCode'])->phone($phoneNumber)->email($email)->company($company)->sex($sex)->country($country['name'])->zipcode($zipCode);
			$response = IntellipushHelper_init()->create($contact);
			if ($response->success) {
				IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
				IntellipushHelper_clearCache( 'intellipush_helper_contacts' );
			}
		}
	}
	return $response;
}

function IntellipushHelper_addToContactlist($contactId, $contactlistId) {
	$response = null;
	if ($contactId && $contactlistId) {
		$contactlist = new Contactlist();
		$contactlist->id($contactlistId)->contactId($contactId);
		$response = IntellipushHelper_init()->create($contactlist);
		if ($response->success) {
			IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
			IntellipushHelper_clearCache( 'intellipush_helper_contacts' );
		}
	}
	return $response;
}

function IntellipushHelper_addToContactlistBatch($contacts, $contactlistId) {
	$response = null;
	if ($contacts && $contactlistId) {
		$batch = new BatchImport();
		foreach ($contacts as $key => $value) {
			$country = IntellipushHelper_getCountryInfoByContryName($value['country']);
			if($country) {
				$contact = new Contact();
				$contact->name($value['name'])->countrycode($country['phoneCode'])->phone($value['phoneNumber'])->email($value['email'])->company($value['company'])->country($country['name'])->zipcode($value['zipCode']);
				$batch->add($contact);
			}
		}
		$batch->importToContactlistId($contactlistId);
		$response = IntellipushHelper_init()->create($batch);
		if ($response->success) {
			IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
			IntellipushHelper_clearCache( 'intellipush_helper_contacts' );
		}
	}
	return $response;
}

function IntellipushHelper_deleteFromContactlist($contactlistId, $country, $phoneNumber) {
	$response = null;
	if ($contactlistId && $country && $phoneNumber) {
		$country = IntellipushHelper_getCountryInfoByContryName($country);
		if ($country) {
			$contact = new Contact();
			$contact->countrycode($country['phoneCode'])->phone($phoneNumber);
			$contact = IntellipushHelper_init()->read($contact);
			if ($contact->success && $contact->id) {
				foreach ($contact->id as $id) {
					$contactlist = new Contactlist();
					$contactlist->id($contactlistId)->contactId($id);
					$response[] = IntellipushHelper_init()->delete($contactlist);
				}
				IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
				IntellipushHelper_clearCache( 'intellipush_helper_contactlist' );
			}
		}
	}
	return $response;
}

function IntellipushHelper_sendMessage($message, $receivers, $delay = null, $repeat = null) {
	$response = null;
	if ($message && $receivers) {
		$sms = new Sms();
		$when = new DateTime(current_time('Y-m-d H:i:s'));
		$sms->message($message);
		if(is_array($receivers)) {
			$sms->receivers($receivers);
		} else if (is_numeric($receivers)) {
			$sms->contactlist($receivers);
		} else {
			return $response;
		}
		if ($delay) {
			$when->add(new DateInterval('PT' . $delay . 'M'));
			$sms->when($when);
		}
		if ($repeat && $repeat !== 'never') {
			if(!$delay) {
				$when->add(new DateInterval('PT1M'));
			}
			$sms->repeat($repeat)->when($when);
		}
		$response = IntellipushHelper_init()->create($sms);
		if ($response->success) {
			IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
			IntellipushHelper_clearCache( 'intellipush_helper_notifications' );
		}
	}
	return $response;
}

function IntellipushHelper_deleteScheduledMessage($message, $country, $phoneNumber, $similarThan = 100) {
	$response = null;
	if ($message && $country && $phoneNumber) {
		$country = IntellipushHelper_getCountryInfoByContryName($country);
		if ($country) {
			$allNotifications = IntellipushHelper_getNotifications();
			if ($allNotifications->success && $allNotifications->id) {
				foreach ($allNotifications->response->data as $key => $value) {
					similar_text ($value->text_message, $message, $similar);
					if ($similar >= $similarThan && $value->single_target_countrycode === $country['phoneCode'] && $value->single_target = $phoneNumber) {
						$sms = new Sms();
						$sms->id($value->id);
						$response = IntellipushHelper_init()->delete($sms);
						if ($response->success) {
							IntellipushHelper_clearCache( 'intellipush_helper_statistics' );
							IntellipushHelper_clearCache( 'intellipush_helper_notifications' );
						}
					}
				}
			}
		}
	}
	return $response;
}

function IntellipushHelper_getContactlist() {
	$contactlist = get_transient('intellipush_helper_contactlist');
	if ( $contactlist === false ) {
		$contactlist = IntellipushHelper_init()->getContactlists(100000, 1);
		$contactlist = $contactlist->success ? $contactlist->response->data : null;
		set_transient('intellipush_helper_contactlist', $contactlist, $INTELLIPUSH_TRANSIENT_EXPIRATION_SHORT);
	}
	return $contactlist;
}

function IntellipushHelper_getContacts($limit = 50, $page = 1, $order = 'name', $sort = 'ASC', $cache = true) {
	$transient_name = 'intellipush_helper_contacts_' . $limit . '_' . $page . '_' . $order . '_' . $sort;
	$contacts = get_transient($transient_name);
	if ( $contacts === false || !$cache) {
		$contact = new Contact();
		$contact->items($limit)->page($page)->order($order)->sort($sort);
		$contacts = IntellipushHelper_init()->read($contact);
		$contacts = $contacts->success ? $contacts->response->data : null;
		set_transient($transient_name, $contacts, $INTELLIPUSH_TRANSIENT_EXPIRATION_LONG);
	}
	return $contacts;
}

function IntellipushHelper_getMessageTemplates() {
	$messageTemplates = get_field('intellipush_messages_templates', 'option');
	if ( $messageTemplates ) {
		$messageTemplates = array(
			'data' => $messageTemplates,
			'success' => 1
		);
	} else {
		$messageTemplates = null;
	}
	return $messageTemplates;
}

function intellipush_helper_ajax_getMessageTemplates() {
	$response = IntellipushHelper_getMessageTemplates();
	echo json_encode($response);
	wp_die();
}
add_action( 'wp_ajax_intellipush_helper_getMessageTemplates', 'intellipush_helper_ajax_getMessageTemplates', 100 );


function IntellipushHelper_getShortUrls($limit = 50, $page = 1, $cache = true) {
	$transient_name = 'intellipush_helper_shortUrls_' . $limit . '_' . $page;
	$shortUrls = get_transient($transient_name);
	if ( $shortUrls === false || !$cache ) {
		$shortUrls = new Url();
		$shortUrls->items($limit);
		$shortUrls->page($page);
		$shortUrls = IntellipushHelper_init()->read($shortUrls);
		$shortUrls = $shortUrls->success ? $shortUrls->response->data : null;
		set_transient( $transient_name, $shortUrls, $INTELLIPUSH_TRANSIENT_EXPIRATION_LONG);
	}
	return $shortUrls;
}

function IntellipushHelper_createShortUrl($link) {
	$response = null;
	if ( $link ) {
		$url = new Url();
		$url->longUrl($link);
		$response = IntellipushHelper_init()->create($url);
	}
	return $response;
}

function intellipush_helper_ajax_createShortUrl() {
	$response = null;
	if(isset($_POST['url'])) {
		$response = IntellipushHelper_createShortUrl($_POST['url']);
	}
	echo json_encode($response);
	wp_die();
}
add_action( 'wp_ajax_intellipush_helper_createShortUrl', 'intellipush_helper_ajax_createShortUrl', 100 );

function intellipush_helper_ajax_sendMessage() {
	$response = null;
	$message = isset($_POST['message']) ? $_POST['message'] : null;
	$contactlist =  isset($_POST['contactlist']) ? $_POST['contactlist'] : null;
	$telephone =  isset($_POST['telephone']) ? $_POST['telephone'] : null;
	if( $message && ($contactlist||$telephone) ) {
		$delay = isset($_POST['delay']) ? $_POST['delay'] : null;
		$repeat = isset($_POST['repeat']) ? $_POST['repeat'] : null;
		if ($contactlist) {
			$response['sent-to-contactlist'] = IntellipushHelper_sendMessage($message, $contactlist, $delay, $repeat);
		}
		if($telephone) {
			$receivers = explode(',', $telephone);
			$receivers = array_map('trim', $receivers);
			foreach ($receivers as $key => $value) {
				$isValid = IntellipushHelper_convertPhoneNumberToArray($value);
				if ($isValid) {
					$receivers[$key] = $isValid;
				} else {
					unset($receivers[$key]);
				}
			}
			$response['sent-to-telephonenumber'] = IntellipushHelper_sendMessage($message, $receivers, $delay, $repeat);;
		}
	}
	echo json_encode($response);
	wp_die();
}
add_action( 'wp_ajax_intellipush_sendMessage', 'intellipush_helper_ajax_sendMessage', 100 );


function IntellipushHelper_isAuth() {
	return IntellipushHelper_getUserInfo()->success;
}

function IntellipushHelper_getCredits() {
	return IntellipushHelper_getUserInfo()->response->data->centicredits / 100;
}

function IntellipushHelper_isApiSetup() {
	return !!get_option('intellipush_settings_api_id') && !!get_option('intellipush_settings_api_secret_key');
}

function IntellipushHelper_clearCache( $name = '' ) {
	if( !$name ) {
		delete_transient( 'intellipush_helper_init' );
		delete_transient( 'intellipush_helper_userInfo' );
		delete_transient( 'intellipush_helper_statistics' );
		delete_transient( 'intellipush_helper_notifications' );
		delete_transient( 'intellipush_helper_contactlist' );
		delete_transient( 'intellipush_helper_contacts' );

		delete_transient( 'intellipush_helper_contacts_10_1_time_added_DESC' );
		delete_transient( 'intellipush_helper_shortUrls_10_1' );
	} else {
		delete_transient( $name );
	}
}

function IntellipushHelper_param_clearCache() {
	if (isset($_GET['intellipush-clear-cache'])) {
		IntellipushHelper_clearCache();
		wp_redirect(preg_replace('/&?intellipush-clear-cache/', '', IntellipushHelper_getCurrentUrl()));
		exit;
	}
}
add_action( 'admin_init', 'IntellipushHelper_param_clearCache' );

function IntellipushHelper_randString() {
	return md5(uniqid(mt_rand(), true));
}

function IntellipushHelper_getCurrentUrl() {
	return set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
}

function IntellipushHelper_getPluginUrl() {
	return plugin_dir_url(dirname(__FILE__));
}

function IntellipushHelper_addParamToUrl($url, $key, $value = null) {
	$query = parse_url($url, PHP_URL_QUERY);
	if ($query) {
		parse_str($query, $queryParams);
		$queryParams[$key] = $value;
		$url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
	} else {
		$url .= '?' . urlencode($key) . '=' . urlencode($value);
	}
	return $url;
}

function IntellipushHelper_getCountryInfoByContryName( $countryCode ) {
	$countryArray = array(
		'BR'=>array('name'=>'Brazil','phoneCode'=>'0055','countryCode'=>'BR','countryCode3'=>'BRA'),
		'DK'=>array('name'=>'Denmark','phoneCode'=>'0045','countryCode'=>'DK','countryCode3'=>'DNK'),
		'NO'=>array('name'=>'Norway','phoneCode'=>'0047','countryCode'=>'NO','countryCode3'=>'NOR'),
		'SE'=>array('name'=>'Sweden','phoneCode'=>'0046','countryCode'=>'SE','countryCode3'=>'SWE')
	);
	$result = $countryArray[$countryCode];
	return $result ? $result : null;
}

function IntellipushHelper_convertPhoneNumberToArray( $phone ) {
	$result = null;
	if ( $phone && is_numeric($phone) ) {
		$tmp = substr($phone, 0, 4);
		if ( in_array($tmp, array('0045', '0046', '0047', '0055')) ) {
			$result = array($tmp, substr($phone, 4));
		}
	}
	return $result;
}

function IntellipushHelper_getRecommendedPhoneCode() {
	$result = '0047';
	if (IntellipushHelper_isWooCommerceActivated()) {
		$recommend = IntellipushHelper_getCountryInfoByContryName(WC_Countries::get_base_country())['phoneCode'];
		$result = $recommend ? $recommend : $result;
	}
	return $result;
}

function IntellipushHelper_getMergeCartCode() {
	$result = array();
	if (IntellipushHelper_isWooCommerceActivated()) {
		global $woocommerce;
		$items = $woocommerce->cart->get_cart();
		foreach($items as $k => $v) { 
			$result[] = array(
				$v['product_id'],
				$v['quantity'],
				$v['variation_id'],
				$v['variation']
			);
		};
	}
	return !empty($result) ? base64_encode(json_encode($result)) : false;
}

function IntellipushHelper_getHomeUrl() {
	$url = 'https://www.intellipush.com/en';
	$locale = get_locale();
	if ($locale === 'nb_NO') {
		$url = 'https://www.intellipush.com';
	}
	return $url;
}

function IntellipushHelper_getServerIP() {
	$ip = get_transient('intellipush_helper_serverIP');
	if ( $ip === false ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://ipecho.net/plain');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$ip = curl_exec($ch);
		curl_close ($ch);
		set_transient('intellipush_helper_serverIP', $ip, 60 * MINUTE_IN_SECONDS);
	}
	return $ip;
}

function IntellipushHelper_getConnectUrl() {
	$ctoken = IntellipushHelper_randString();
	$callbackUrl = urlencode(IntellipushHelper_addParamToUrl(menu_page_url('intellipush-settings', false), 'ctoken', $ctoken));
	$url = IntellipushHelper_getHomeUrl().'/wpconnect?sourceip='.IntellipushHelper_getServerIP().'&ctoken='.$ctoken.'&callbackurl='.$callbackUrl;
	return $url;
}

function IntellipushHelper_isWooCommerceActivated() {
	return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}
