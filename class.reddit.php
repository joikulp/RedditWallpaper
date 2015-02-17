<?php
	class reddit {

		private $subreddit = array();
		private $username = false;
		private $password = false;
		private $img = false;

		public $fileType = array();
		public $expiration = false;
		public $mininumUps = false;
		public $saveDir = false;
		public $tempDir = false;
		public $data = array();

		private $prefix = 'https://www.reddit.com/r/';
		private $suffix = '/new.json?sort=new';

		public function __construct ($subreddit = false, $username = false, $password = false)
		{
			$this->setSubreddit($subreddit);
			$this->username = $username;
			$this->password = $password;
		}

		public function setSubreddit ($subreddit)
		{
			if (is_array($subreddit)) {
				foreach ($subreddit as $sr) {
					$this->subreddit[] = $sr;
				}
			} else if ($subreddit) {
				$this->subreddit[] = $subreddit;
			}
		}

		public function scanSubreddits ()
		{
			foreach ($this->subreddit as $sr) {
				if (!in_array($sr, $this->data)) {
					$data = json_decode(file_get_contents($this->prefix.$sr.$this->suffix));
					foreach ($data->data->children as $row) {
						$this->data[$sr][] = $row->data;
					}
				}
			}
		}

		public function validate ($row)
		{
			// Correct extension
			if ($this->fileType) {
				$img_name_parts = explode('.', $row->url);
				$img_name_extension = end($img_name_parts);
				if (!in_array($img_name_extension, $this->fileType)) {
					return false;
				}
			}

			// Minimum ups requirement met
			if ($this->mininumUps) {
				if ($row->ups < $this->mininumUps) {
					return false;
				}
			}

			// File does not exist already
			$img = file_get_contents($row->url);
			$img_md5 = md5($img);
			$save_path = $this->saveDir .'/'. $img_md5 .'.'. $img_name_extension;
			$temp_path = $this->tempDir .'/'. $img_md5 .'.'. $img_name_extension;
			if (file_exists($save_path)) {
				return false;
			} else {
				$this->img = $img_md5 .'.'. $img_name_extension;
				file_put_contents($temp_path, $img);
			}

			return true;
		}

		public function saveWallpaper ($keep)
		{
			if ($this->img) {
				if ($keep) {
					rename($this->tempDir .'/'. $this->img, $this->saveDir .'/'. $this->img);
				} else {
					unlink($this->tempDir .'/'. $this->img);
				}
				$this->img = false;
			} else {
				return false;
			}
			return true;
		}

		public function deleteExpiredWallpaper ()
		{
			if ($this->expiration) {
				foreach (scandir($this->saveDir) as $file) {
					if (strtotime('-'. $this->expiration) > filemtime($this->saveDir .'/'. $file)) {
						unlink($this->saveDir .'/'. $file);
					}
				}
			} else {
				return false;
			}
			return true;
		}
	}
?>