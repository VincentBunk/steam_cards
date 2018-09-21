<?php
	// include configuration file
	require_once 'config.php';

	function getInventory($steamids, $app_id, $context_id, $count) {
		// generate request URL
		$request = 'http://steamcommunity.com/inventory/'.$steamids.'/'.$app_id.'/'.$context_id.'?l=english&count='.$count;

		$result = file_get_contents($request);

		// decode and return result
		return json_decode($result, true);
	}

	function postToForum($forum_key, $cards, $trade_url) {
		// login to steam account (set in config)
		$session_id = '';

		// generate post content
		$content_hl = '[H] '.$cards.' [W] cards to complete my sets. ';
		$content = 'If you are interested please send me a trade offer --> '.$trade_url;

		// configure POST request to steam forums
		$url = 'https://steamcommunity.com/';
		$subforum_id = 'Trading_18446744073709551615'; // post to trading subforum

		$data = array('sessionid' => $session_id, 'appid' => $forum_key, 'topic' => $content_hl, 'text' => $content_hl.$content, 'subforum' => $subforum_id);

		$options = array(
			'http' => array(
				'header' => "Content-type: application/x-www-form-urlencoded",
				'method' => 'POST',
				'content' => http_build_query($data)
			)
		);

		// send POST request
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		// check for errors during POST request
		if ($result === false) {
			$result = 'Error while submitting to trade forum';
		}

		return $result;
	}

	// query for inventory
	$data = getInventory($steamids, $app_id, $context_id, '5000');

	// holds cards seperated into one set per game
	$card_sets = array();

	// sorting and pushing cards into 'card_sets' array
	foreach ($data['descriptions'] as $key) {
		if (strpos($key['type'], 'Trading Card') !== false) {
			$card_game_id = str_replace('app_', '', $key['tags'][1]['internal_name']);

			if (array_key_exists($card_game_id, $card_sets == false)) {
				$card_sets[$card_game_id][] = $card_game_id;
			}		
			$card_sets[$card_game_id][] = $key['name'];
		}
	}

	// trigger post to form
	foreach ($card_sets as $key => $value) {
		foreach ($value as $name) {
			$cards .= '"'.$name.'", ';
		}

		echo postToForum($key, $cards, $trade_url);

		// reset variable
		$cards = '';
	}
?>