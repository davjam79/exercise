<?php

require_once("tables.php");		// load arrays to generate pop-ups
require_once("functions.php");		// functions used for calculations

date_default_timezone_set('Europe/London');

header("Expires: ".gmdate("D, d M Y H:i:s",strtotime("1 day ago"))." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0");
header("Pragma: no-cache");

$height=268;									// 5'7"
$weight=820;									// 82.0kg
$clothed=$weight+50;								// 87.0kg
$age=51;
$fat=20;
$time=array(
	"hours"	=>	0,
	"mins"	=>	0,
	"secs"	=>	0
	);
$distance=0;									// 0 miles

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Calculate calories burnt during a walk.</title>
		<meta content="">
		<style>
/* DivTable.com */
.divTable		{display: table;	width: 100%;}
.divTableRow		{display: table-row;}
.divTableHeading	{background-color: #EEE;	display: table-header-group;}
.divTableCell		{border: 1px solid #999999;	display: table-cell;	padding: 3px 10px;}
.divTableHead		{border: 1px solid #999999;	display: table-cell;	padding: 3px 10px;}
.divTableHeading	{background-color: #EEE;	display: table-header-group;	font-weight: bold;}
.divTableFoot		{background-color: #EEE;	display: table-footer-group;	font-weight: bold;}
.divTableBody		{display: table-row-group;}
		</style>
	</head>
	<body>
<?php
if(isset($_POST["calculate"]))
{
/*
* /
?>
<pre>
<?php
print_r($_POST);
?>
</pre>
<?php
/ *
*/
	if(isset($_POST["height"]))
	{
		$temp=$_POST["height"];
		if(is_numeric($temp))
		{
			if(($temp>=96)&&($temp<=384))				// between 2 and 8 feet tall
			{
				$height=$temp;
			}
		}
	}
//printf("<p>Height: %s</p>\n",$height);
	if(isset($_POST["weight"]))
	{
		$temp=$_POST["weight"];
		if(is_numeric($temp))
		{
			if(($temp>=100)&&($temp<=1600))				// between 10 and 160kg
			{
				$weight=$temp;
			}
		}
	}
//printf("<p>Naked weight: %s</p>\n",$weight);
	if(isset($_POST["weight2"]))
	{
		$temp=$_POST["weight2"];
		if(is_numeric($temp))
		{
			if(($temp>=100)&&($temp<=1600))				// between 10 and 160kg
			{
				$clothed=$temp;
			}
		}
	}
//printf("<p>Clothed weight: %s</p>\n",$clothed);
	if(isset($_POST["distance"]))
	{
		$temp=$_POST["distance"];
		if(is_numeric($temp))
		{
			if((($temp*100)>=10)&&(($temp*100)<=10000))		// between 0.1 and 100 miles
			{
				$distance=$temp;
			}
		}
	}
//printf("<p>Distance: %s</p>\n",$distance);
	if(isset($_POST["fat"]))
	{
		$temp=$_POST["fat"];
		if(is_numeric($temp))
		{
			if((intval($temp*100)>=100)&&(intval($temp*100)<=7500))	// between 1% and 75%
			{
				$fat=$temp;
			}
		}
	}
//printf("<p>Bodyfat: %s</p>\n",$fat);
	if(isset($_POST["time"]))
	{
		$temp=$_POST["time"];
		$temp=explode(":",$temp);
		if(isset($temp[0])&&(is_numeric($temp[0])))
		{
			$time["hour"]=$temp[0];
			if(isset($temp[1])&&(is_numeric($temp[1])))
			{
				$time["mins"]=$temp[1];
				if(isset($temp[2])&&(is_numeric($temp[2])))
				{
					$time["secs"]=$temp[2];
				}
			}
		}
	}
	$time_string=sprintf("%02s:%02s:%02s",$time['hour'],$time['mins'],$time['secs']);
//printf("<p>Time: %s</p>\n",$time_string);


	$results=calories($distance,$time_string,$height,$weight,$age,$fat,$clothed);

	if($results===FALSE)							// error in calculations
	{
?>
		<p>Can't calculate calories. Bad time or distance.</p>
<?php
	}
	else
	{
?>
		<p></p>
		<div class="divTable">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHeading"></div>
					<div class="divTableHead">Formula</div>
					<div class="divTableHead">Fitbit</div>
					<div class="divTableHead">Garmin</div>
					<div class="divTableHead"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Time</div>
					<div class="divTableCell"><?php echo $time_string; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Distance</div>
					<div class="divTableCell"><?php echo $distance; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Speed m/s</div>
					<div class="divTableCell"><?php echo $results["speed"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Speed mph</div>
					<div class="divTableCell"><?php echo $results["mph"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Cals/min</div>
					<div class="divTableCell"><?php echo $results["average"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">BMR/min</div>
					<div class="divTableCell"><?php echo $results["bmr1"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"><?php echo $results["bmr_1"]; ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">BMR/min (v2)</div>
					<div class="divTableCell"><?php echo $results["bmr2"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"><?php echo $results["bmr_2"]; ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">BMR/min (v3)</div>
					<div class="divTableCell"><?php echo $results["bmr3"]; ?></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"></div>
					<div class="divTableCell"><?php echo $results["bmr_3"]; ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Total calories</div>
					<div class="divTableCell"><?php echo intval($results["total"]); ?></div>
					<div class="divTableCell"><?php echo $results["fitbit"]; ?></div>
					<div class="divTableCell"><?php echo $results["garmin"]; ?></div>
					<div class="divTableCell"></div>
				</div>
			</div>
		</div>
		<hr />
<?php
	}
}

//printf("%s<br />\n%s<br />\n",$height,$weight);

?>
		<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" target="_self" accept-charset="UTF-8">
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableHead">Distance</div>
						<div class="divTableCell"><input name="distance" type="number" step="0.01" min="0" max="100" value="<?php echo $distance; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Time</div>
						<div class="divTableCell"><input name="time" type="time" step="1" min="0" value="<?php echo $time_string; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Height</div>
						<div class="divTableCell">
							<select name="height">
<?php
foreach($height_array as $key => $value)
{
?>
								<option value="<?php echo $key; ?>" <?php if($key==$height) echo "selected"; ?> >
<?php
	printf("%s' %s %s\"",$value['feet'],$value['inches'],$value['fraction']);
?>
								</option>
<?php
}
?>
								
							</select>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Naked weight</div>
						<div class="divTableCell">
							<select name="weight">
<?php
foreach($weight_array as $key => $value)
{
?>
								<option value="<?php echo $key; ?>" <?php if($key==$weight) echo "selected"; ?> >
<?php
	printf("%s kg",$value);
?>
								</option>
<?php
}
?>
								
							</select>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Clothed weight</div>
						<div class="divTableCell">
							<select name="weight2">
<?php
foreach($weight_array as $key => $value)
{
?>
								<option value="<?php echo $key; ?>" <?php if($key==$clothed) echo "selected"; ?> >
<?php
	printf("%s kg",$value);
?>
								</option>
<?php
}
?>
								
							</select>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Bodyfat %</div>
						<div class="divTableCell"><input name="fat" type="number" step="0.01" min="0" max="100" value="<?php echo $fat; ?>" /></div>
					</div>
				</div>
			</div>
			<input type="submit" name="calculate" value="Calculate" />
			<input type="reset" />
		</form>	
	</body>
</html>