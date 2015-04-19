<?php
	// ** More subreddits to check out:	** //
	// ** iWallpaper					** //
	// ** NSFW_Wallpapers				** //
	// ** Offensive_Wallpapers			** //

	# Settings:
	include('class.reddit.php');
	$reddit = new reddit(array('WQHD_Wallpaper', 'wallpapers'), 'RedditWallpaper', 'thisis4w3some');
	$reddit->fileType = array('jpg', 'png');
	$reddit->expiration = '1 year'; // php strtotime used
	$reddit->saveDir = 'wallpaper';
	$reddit->author = false;
	$reddit->mininumUps = 20;
	$reddit->minimumWidth = 1920;
	$reddit->minimumHeight = 1080;
	$reddit->maximumWidth = 2560;
	$reddit->maximumHeight = 1440;

	$reddit->scanSubreddits();
	foreach ($reddit->data as $subreddits) {
		foreach ($subreddits as $row) {
			$reddit->saveWallpaper($reddit->validate($row));
		}
	}
	$reddit->deleteExpiredWallpaper();
