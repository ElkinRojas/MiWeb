<?php
	/**
	* Connection Class
	*/

	class Connection {
		private $host = "localhost";
		private $username = "root";
		private $password = "";
		private $database = "pina_burger";
		protected $link;

		public function connect() {
			$this->link = new mysqli( $this->host, $this->username, $this->password, $this->database );
		    $this->link->query( "SET NAMES 'utf8'; " );
		    //$this->link->query( "SET lc_time_names = 'es_CO'; ");
			
			if ($this->link->connect_errno) {
			    echo "Fail to connect to MySQL: " . $this->link->connect_error;
			}
			
		    return $this->link;
		}

		public function close() {
			$this->link->close();
		}

		public function set_host($host) {
			$this->host = $host;
		}

		public function set_username($username) {
			$this->username = $username;
		}

		public function set_password($password) {
			$this->password = $password;
		}

		public function set_database($database) {
			$this->database = $database;
		}
	}
?>