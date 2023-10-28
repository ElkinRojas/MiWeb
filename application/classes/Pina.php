<?php
    class Pina {
        public function get_votes( $con, $data ) {
			// $data = $this->protect_sql( $data );

			$sql = "SELECT v.id, v.vote, v.name_restaurante, v.ip FROM votes v";

            $sql = preg_replace('/\s\s+/', ' ', $sql);

			$result = $con->query($sql);

			if ( $result->num_rows > 0 ) {
				return $result->fetch_all(MYSQLI_ASSOC);
			}

            return array();
		}

        public function set_vote( $con, $data ) {
			// $data = $this->protect_sql( $data );

			$sql = "INSERT INTO votes ( vote, name_restaurante, ip ) VALUES ( '".$data['vote']."', '".$data['name_restaurante']."', '".$data['ip']."' ) ";

			$result = $con->query($sql);

			if (!$result) {
				return false;
			}

			return true;
		}

        public function get_count_votes( $con, $data ) {
			// $data = $this->protect_sql( $data );

			$sql = "SELECT vote, name_restaurante, COUNT(*) mi_contador FROM votes WHERE vote = '".$data['number']."' GROUP BY vote;";

			$result = $con->query($sql);

			if ( $result->num_rows > 0 ) {
				return $result->fetch_all(MYSQLI_ASSOC);
			}

			return array();
		}

		public function get_info_verification( $con, $data ) {
			//$data = $this->protect_sql( $data );
			
            $sql = "SELECT * FROM verification_code v WHERE v.phone = '".$data['phone']."' AND v.status = '".$data['status']."'";

			$result = $con->query($sql);

			if ( $result->num_rows > 0 ) {
				return $result->fetch_assoc();
			}

			return false;
		}

        public function set_verification( $con, $data ) {
			// $data = $this->protect_sql( $data );

			$sql = "INSERT INTO verification_code ( phone, code, status ) VALUES ( '".$data['phone']."', '".$data['code']."', '".$data['status']."' ) ";

			$result = $con->query($sql);

			if (!$result) {
				return false;
			}

			return true;
		}

		public function update_verification( $con, $data ) {
            //$data = $this->protect_sql( $data );

			$sql = "UPDATE verification_code SET status = '".$data['status']."' WHERE phone = '".$data['phone']."' ";

			$result = $con->query($sql);

			return $result;
		}

		public function delete_verification( $con, $phone) {
			//$id = $this->protect_sql( $id );

			$sql = "DELETE FROM verification_code WHERE phone = '{$phone}' ";
			
			$result = $con->query($sql);

			return $result;
		}
    }
?>