<?php

	$app_secret = 'adf710d8999496363b8d0348a5a467db';

	// Validate request is from Facebook and parse contents for use.
	$request = parse_signed_request($_POST['signed_request'], $app_secret);'
	// Get request type.
	$request_type = $_POST['method'];

	// Setup response.
	$response = '';

	if ($request == null) {
	// handle an unauthenticated request here
	}

	if ($request_type == 'payments_get_item_price') {
	  // Retrieving the user's info
	  $user_currency = $request['payment']['user_currency'];
	  $user_country = $request['user']['country'];

	  // Here we verify the product by passing back the URL of the OG product
	  $item['product'] = $request['payment']['product'];

	  // This is the quantity passed from the JS call to render the pay dialog.
	  // This parameter is optional and defaults to 1.
	  $quantity = $request['payment']['quantity'];

	  // Based on the user's currency and country, we set the price.
	  // We use fixed values here for testing.
	  switch($user_currency) {
		case 'EUR':
			$item['amount'] = 2.99;
			$item['currency'] = 'EUR';
			break;
		case 'GBP':
			$item['amount'] = 2.49;
			$item['currency'] = 'GBP';
			break;
		case 'BRL':
			$item['amount'] = 6.99;
			$item['currency'] = 'BRL';
			break;
		// Here we default to USD. If a user's preferred currency is different than one
		// that you specify, we will convert from the developer provided currency
		// and amount to a new amount in the user's currency. You can choose whatever
		// default currency works best for you.
		default:
			$item['amount'] = 3.99;
			$item['currency'] = 'USD';
			break;
	  }

	  // Optionally, you may also choose to override the quantity_min / quantity_max values
	  // which were passed in when invoking the Pay Dialog.
	  $item['quantity_min'] = 1;
	  $item['quantity_max'] = 100;

	  // Optionally, it's also possible to override the OG product object's title, 
	  // plural_title and description.
	  $item['title'] = 'Override Title';
	  $item['plural_title'] = 'Override Title Plural';
	  $item['description'] = 'Override Description';

	  // Finally we add the item information to the response 'content'
	  $response['content'] = $item;
	}

	// Return the identical method
	$response['method'] = $request_type;

	// Send data back
	echo json_encode($response);

	// You can find the following functions and more details
	// on https://developers.facebook.com/docs/authentication/canvas
	function parse_signed_request($signed_request, $secret) {
	  list($encoded_sig, $payload) = explode('.', $signed_request, 2);

	  // Decode the data
	  $sig = base64_url_decode($encoded_sig);
	  $data = json_decode(base64_url_decode($payload), true);

	  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		error_log('Unknown algorithm. Expected HMAC-SHA256');
		return null;
	  }

	  // check signature
	  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	  if ($sig !== $expected_sig) {
		error_log('Bad Signed JSON signature!');
		return null;
	  }
	  return $data;
	}

	function base64_url_decode($input) {
	  return base64_decode(strtr($input, '-_', '+/'));
	}
?>