<?php
	// credentials
	require_once 'config.php';

	function getInventory($steamids, $app_id, $context_id, $count) {
		// generate request URL
		$request = 'http://steamcommunity.com/inventory/'.$steamids.'/'.$app_id.'/'.$context_id.'?l=english&count='.$count;

		$result = file_get_contents($request);

		// decode and return result
		return json_decode($result, true);
	}

	function postToForum($forum_key, $cards, $trade_url) {
		$output = '(Post to: "https://steamcommunity.com/app/'.$forum_key.'/tradingforum/")<br>';
		$output .= 'I am trading the following cards: '.$cards.'<br>';
		$output .= 'If you are interested please send me a <a href="'.$trade_url.'">trade offer</a><br><br>';

		return $output;
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
			$cards .= '"'.$name.'",&nbsp;';
		}

		echo postToForum($key, $cards, $trade_url);
		$cards = '';
	}
?>