<?php
	class reddit {

		private $subreddit = array();
		private $username = false;
		private $password = false;
		private $file = false;

		public $fileType = array();
		public $expiration = false;
		public $mininumUps = 42;
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
			foreach ($subreddit as $sr) {
				if (!in_array($sr, $this->data)) {
					$data = json_decode(file_get_contents($this->prefix.$sr.$this->suffix));
					foreach ($data->data->children as $row) {
						$img_name_parts = explode('.', $row->url);
						$img_name_extension = end($img_name_parts);
						
						if (!in_array($img_name_extension, $this->fileType)) {
							continue;
						}
						$img = file_get_contents($row->url);
						$img_md5 = md5($img);
						
						$img_save_path = $dir_save.$img_md5.$img_name_extension;
						$img_temp_path = $dir_temp.$img_md5.$img_name_extension;
						
						file_put_contents($img_temp_path, $row->url);
						$row->data->file = file_get_contents($row->url);
						$this->data[$sr] = $row->data;
					}
				}
			}
		}
	}
?>