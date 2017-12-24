<?php

$env = parse_ini_file('env.ini');

date_default_timezone_set($env['timezone']);

/**
* Class ParkingBot
*/
class ParkingBot
{
	private $ticket_price;
	private $car_list_history;
	private $car_list;
	
	function __construct($ticket_price)
	{
		$this->ticket_price = $ticket_price;
		$this->car_list_history = array();
		$this->car_list = array();
	}

	public function checkIn($registration_number) {
		$new_car = array(
			'start_time' => time(),
			'end_time' => false
		);

		$this->car_list[$registration_number] = $new_car;
	}

	public function checkOut($registration_number) {
		$car = $this->car_list[$registration_number];
		
		$car['registration_number'] = $registration_number;
		//$car['end_time'] = time();
		$car['end_time'] = strtotime('+1 day', time());
		$car['fee'] = $this->calculateFee($car);

		$this->car_list_history[] = $car;

		unset($this->car_list[$registration_number]);

		return $car['fee'];
	}

	public function getCars($from_time = false, $to_time = false) {
		$cars = array(
			'old_cars' => $this->car_list_history
		);

		// If $to_time is passed, active cars would not be relevant (assuming $to_time is not >= now)
		if (!$to_time)
		{
			$cars['active_cars'] = $this->car_list;
		}

		return $cars;
	}

	private function calculateFee($car) {
		$start_time = $car['start_time'];
		$end_time = $car['end_time'];

		$diff = ($end_time - $start_time) / 60 / 60;

		$hours = round($diff, 0, PHP_ROUND_HALF_UP);

		$total_fee = $this->ticket_price * $hours;

		return $total_fee;
	}
}