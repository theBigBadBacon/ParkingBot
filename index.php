<?php
	/**
	* TODO:
	*
	* Do some mobile first styling
	* CSS variables
	* Separate CSS file into components
	* Filter on already present cars with JS (especially bad in demo mode since demo cars times are random)
	* Cleanup getcars
	* Clear get params on in-date-input-clear
	* Prevent downloading possibility of ini file
	*/
	include 'ParkingBot.php';

	$env = parse_ini_file('env.ini');

	$ticket_price = $env['ticket_price_zone_a'];

	$parkingBot = new ParkingBot($ticket_price);

	// Create 8 demo cars
	for ($i = 0; $i < 8; $i++) { 
		$regnr = 'DEM00' . $i;

		// Set parking started to a random day between 10 and 90 days ago
		$start_time = strtotime('-' . rand(10,90) . ' days', time());
		
		// Set parking ended day to 1 to 9 days after parking started
		$end_time = $start_time + (rand(1, 9) * 60 * 60 * 24) + (rand(25, 5000) * 60);

		$parkingBot->checkIn($regnr, $start_time);
		$parkingBot->checkOut($regnr, $end_time);
	}

	$filter_from = filter_input(INPUT_GET, 'filter_from');
	$filter_to = filter_input(INPUT_GET, 'filter_to');

	$filter_from = $filter_from ?: false;
	$filter_to = $filter_to ?: false;

	$total_fee = 0;

	$car_lists = $parkingBot->getCars($filter_from, $filter_to);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Adeprimo Parking Bot</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="main">
		<h1 class="heading">Adeprimo - ParkingBot</h1>
		<form action="/" method="get" class="form">
			From: <input type="date" name="filter_from" class="form-field" value="<?= $filter_from; ?>">
			To: <input type="date" name="filter_to" class="form-field" value="<?= $filter_to; ?>">
			<input type="submit" name="submit" class="form-submit" value="Filter">
		</form>
		<button class="button" onclick="window.location = '/';">Clear filter</button>
		<table class="list">
			<tr class="list-row">
				<td class="list-row-item">License plate</td>
				<td class="list-row-item">Check-in time</td>
				<td class="list-row-item">Check-out time</td>
				<td class="list-row-item" align="right">Parking fee in <?= $env['ticket_price_zone_a_currency'] ?></td>
			</tr>
			<?php foreach ($car_lists as $name => $cars): ?>
				<?php foreach ($cars as $registration_number => $car): ?>
					<?php $total_fee += $car['fee']; ?>
					<tr class="list-row">
						<td class="list-row-item"><?= $car['registration_number'] ?: $registration_number; ?></td>
						<td class="list-row-item"><?= date('Y-m-d H:i', $car['start_time']); ?></td>
						<td class="list-row-item"><?= $car['end_time'] ? date('Y-m-d H:i', $car['end_time']) : 'Still parked'; ?></td>
						<td class="list-row-item" align="right"><?= $car['fee']; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
			<tr class="list-row">
				<td class="list-row-item" colspan="3"><?= count($car_lists['old_cars']) + count($car_lists['active_cars']); ?> cars in total</td>
				<td class="list-row-item" align="right">Total amount: <?= $total_fee; ?></td>
			</tr>
		</table>
	</div>
</body>
</html>