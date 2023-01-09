<?php
	
	$token = "TOKEN";
	
	$path = "https://api.telegram.org/bot".$token;
	
	$update = json_decode(file_get_contents("php://input"), TRUE);
	$chatId = $update["message"]["chat"]["id"];
	$fileId = $update["message"]["photo"][0]["file_id"];
	$message = $update["message"]["text"];
	$caption = $update["message"]["caption"];
	$destination = "@free_podslyshano";
	
	if (strpos($message, "/start") === 0) {
		
		$text = urlencode("Гамарджоба ебать! Я бот свободного подслушано. Здесь уютно и анонимно, но шок-контент и спам будут удалены. Настроен автоматический постинг в @free_podslyshano.");
		file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$text);
		
	} else {
		
		if ($fileId) { // если фото + подпись (или фото без подписи)
			
			$text = urlencode($caption);
			file_get_contents($path."/sendphoto?chat_id=".$destination."&photo=".$fileId."&caption=".$text);
			
		} else { // если просто текст
			
			$text = urlencode($message);
			file_get_contents($path."/sendmessage?chat_id=".$destination."&text=".$text);
			
		}
		
	}
	
?>
