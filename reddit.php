<?php
	# More subreddits to check out:
		// For mobile phones: iWallpaper
		// 18+: NSFW_Wallpapers

	# Settings:
	include('class.reddit.php');
	$reddit = new reddit(array('WQHD_Wallpaper', 'wallpapers'), 'RedditWallpaper', 'thisis4w3some');
	$reddit->fileType = array('jpg', 'png');
	$reddit->expiration = false;
	$reddit->saveDir = 'wallpaper';
	$reddit->tempDir = 'wallpaper_temp';
	$reddit->mininumUps = 42;

	foreach ($reddit->data as $data) {
		echo "Scanning" . PHP_EOL;
		
		$img = file_get_contents($data->url);
		$img_md5 = md5($img);

		$img_save_path = $dir_save.$img_md5.$img_name_extension;
		$img_temp_path = $dir_temp.$img_md5.$img_name_extension;
		if (!file_exists($img_save_path)) {
			echo "Downloading $img_md5$img_name_extension" . PHP_EOL;
			file_put_contents($img_temp_path, $img);
			copy($img_temp_path, $img_save_path);
			unlink($img_temp_path);
		} else {
			echo "Exists $img_md5$img_name_extension" . PHP_EOL;
		}
	}

	// if ($expiration_date) {
	// 	foreach (scandir($dir_save) as $file) {
	// 		if (strtotime('-'. $expiration_date) > filemtime($dir_save.$file)) {
	// 			unlink($dir_save.$file);
	// 			echo 'Delete file: '. $file . ' Created: ' . date('Y-m-d H:i:s', filemtime($dir_save.$file)) . PHP_EOL;
	// 		}
	// 	}
	// }
?>