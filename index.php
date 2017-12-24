<?php
	/**
	* TODO:
	*
	* Calculate fee when leaving
	* Do some mobile first styling
	* Add date select filter
	*/
	include 'ParkingBot.php';

	$env = parse_ini_file('env.ini');

	$ticket_price = $env['ticket_price_zone_a'];

	$parkingBot = new ParkingBot($ticket_price);

	// Three cars get a ticket
	$parkingBot->checkIn('ABC123');
	$parkingBot->checkIn('DEF234');
	$parkingBot->checkIn('GHT784');

	// Two cars leave the parking lot
	$parkingBot->checkOut('DEF234');
	$parkingBot->checkOut('GHT784');

	$filter_from = filter_input(INPUT_GET, 'filter_from');
	$filter_to = filter_input(INPUT_GET, 'filter_to');

	$car_lists = $parkingBot->getCars();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Adeprimo Parking Bot</title>

</head>
<body>
	<h1>Adeprimo - ParkingBot</h1>
	<h4>Filter table</h4>
	<form action="/" method="get">
		From: <input type="date" name="filter">
		To: <input type="date" name="filter">
		<input type="submit" name="submit" value="Filter">
	</form>
	<table class="list">
		<tr class="list-row">
			<td class="list-row-item">License plate</td>
			<td class="list-row-item">Check-in time</td>
			<td class="list-row-item">Check-out time</td>
			<td class="list-row-item">Parking fee in <?= $env['ticket_price_zone_a_currency'] ?></td>
		</tr>
		<?php foreach ($car_lists as $name => $cars): ?>
			<?php foreach ($cars as $registration_number => $car): ?>
				<tr class="list-row">
					<td class="list-row-item"><?= $car['registration_number'] ?: $registration_number; ?></td>
					<td class="list-row-item"><?= date('Y-m-d H:i', $car['start_time']); ?></td>
					<td class="list-row-item"><?= $car['end_time'] ? date('Y-m-d H:i', $car['end_time']) : 'Still parked'; ?></td>
					<td class="list-row-item"><?= $car['fee']; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>
</body>
</html>