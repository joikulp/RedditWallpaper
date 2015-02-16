<?php
	# Settings:
	$dir_save = 'wallpaper/';
	$dir_temp = 'wallpaper_temp/';
	$subreddits = array('WQHD_Wallpaper', 'wallpapers');
	// For mobile phones: iWallpaper
	// 18+: NSFW_Wallpapers
	$expiration_date = '2 weeks'; // Set false if files should not expire

	# Dont touch unless you know what you're doing:
	$allowed_extensions = array('.jpg', '.png');
	$prefix = 'https://www.reddit.com/r/';
	$suffix = '/new.json?sort=new';

	foreach ($subreddits as $sr) {
		echo "Scanning: $sr" . PHP_EOL;
		$json = json_decode(file_get_contents($prefix.$sr.$suffix));
		if (!$json) {
			echo "Error: Couldn't get subreddit!" . PHP_EOL;
			continue;
		}
		foreach ($json->data->children as $row) {
			$row = $row->data;

			$img_name_parts = explode('.', $row->url);
			$img_name_extension = '.'. end($img_name_parts);

			if (!in_array($img_name_extension, $allowed_extensions)) {
				continue;
			}

			$img = file_get_contents($row->url);
			$img_md5 = md5($img);

			$img_save_path = $dir_save.$img_md5.$img_name_extension;
			$img_temp_path = $dir_temp.$img_md5.$img_name_extension;
			if (!file_exists($img_save_path)) {
				echo "Downloading: $img_md5$img_name_extension" . PHP_EOL;
				file_put_contents($img_temp_path, $img);
				copy($img_temp_path, $img_save_path);
				unlink($img_temp_path);
			} else {
				echo "Exists: $img_md5$img_name_extension" . PHP_EOL;
			}
		}
	}

	if ($expiration_date) {
		foreach (scandir($dir_save) as $file) {
			if (strtotime('-'. $expiration_date) > filemtime($dir_save.$file)) {
				unlink($dir_save.$file);
				echo 'Delete file: '. $file . ' Created: ' . date('Y-m-d H:i:s', filemtime($dir_save.$file)) . PHP_EOL;
			}
		}
	}
?>