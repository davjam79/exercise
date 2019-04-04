<?php

require_once("functions.php");		// functions used for calculations

date_default_timezone_set('Europe/London');

header("Expires: ".gmdate("D, d M Y H:i:s",strtotime("1 day ago"))." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0");
header("Pragma: no-cache");

$height=170;									// 5'7"
$weight=82;									// 82.0kg
$age=51;
$fat=20;
$time=array(
	"hours"		=>	0,
	"mins"		=>	0,
	"secs"		=>	0,
	"seconds"	=>	0,
	);
$intake=0;
$target=0;

$now=time()-strtotime("midnight");						// time since midnight
$remaining=86400-$now;								// time to midnight
$clock=date("H:i:s",time());

function	cals_left($min,$now,$bmr,$calories)
{
	return(max($min-$now*$bmr/86400-$calories,0));
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Calculate time walking needed to reach calorie targets.</title>
		<meta content="">
		<link rel="stylesheet" href="exercise.css">
		<script>
window.onload = function()
{
	document.getElementById("calories").focus();
};
		</script>
	</head>
	<body>
<?php
if(isset($_POST["calculate"])||isset($_POST["ref_cal"]))
{
/*
* /
?>
<pre>
<?php
print_r($_POST);
printf("%s\n",$now);
printf("%s\n",$remaining);
?>
</pre>
<?php
/ *
*/
	if(isset($_POST["ref_cal"]))
	{
// time taken for reference calorie count
		if(isset($_POST["_0"]))
		{
			$temp=$_POST["_0"];
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
			$time["seconds"]=$time['secs']+60*$time['mins']+3600*$time['hour'];
		}
		else
		{
			$time['secs']=1;
			$time['seconds']=1;
		}
		if(isset($_POST["_1"]))
		{
			$calories=$_POST["_1"];
		}
		if(isset($_POST["_2"]))
		{
			$intake=$_POST["_2"];
		}
		if(isset($_POST["_3"]))
		{
			$ref_cal=$_POST["_3"];
		}
		if(isset($_POST["_4"]))
		{
			$gender=$_POST["_4"];
		}
		if(isset($_POST["_5"]))
		{
			$height=$_POST["_5"];
		}
		if(isset($_POST["_6"]))
		{
			$weight=$_POST["_6"];
		}
		if(isset($_POST["_7"]))
		{
			$fat=$_POST["_7"];
		}
		if(isset($_POST["_8"]))
		{
			$target=$_POST["_8"];
		}
// used for the reference exercise
		if(isset($_POST["_9"]))
		{
			$temp=$_POST["_9"];
			if(is_numeric($temp))
			{
// between 1 and 1600
				if(($temp>=1)&&($temp<=1600))
				{
					$pre_burn=$temp;
				}
			}
		}
		else
		{
			$pre_burn=1;
		}
		$ref_cal=intval($pre_burn/$time["seconds"]*60000)/1000;
		$ref_cal2=$ref_cal;
	}
	else
	{

// time taken for reference calorie count
		if(isset($_POST["_0"]))
		{
			$temp=$_POST["_0"];
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
			$time["seconds"]=$time['secs']+60*$time['mins']+3600*$time['hour'];
		}
		else
		{
			$time['secs']=1;
			$time['seconds']=1;
		}

// used for the reference exercise
		if(isset($_POST["_9"]))
		{
			$temp=$_POST["_9"];
			if(is_numeric($temp))
			{
// between 1 and 1600
				if(($temp>=1)&&($temp<=1600))
				{
					$pre_burn=$temp;
				}
			}
		}
		else
		{
			$pre_burn=1;
		}


		if(isset($_POST["height"]))
		{
			$temp=$_POST["height"];
			if(is_numeric($temp))
			{
// between 0.6m and 2.75m tall
				if(($temp>=60)&&($temp<=275))
				{
					$height=$temp;
				}
			}
		}


		if(isset($_POST["weight"]))
		{
			$temp=$_POST["weight"];
			if(is_numeric($temp))
			{
// between 10 and 160kg
				if(($temp>=10)&&($temp<=160))
				{
					$weight=$temp;
				}
			}
		}

// used for the reference exercise
		if(isset($_POST["reference_calories"]))
		{
			$temp=$_POST["reference_calories"];
			if(is_numeric($temp))
			{
// at least 1
				if($temp>=1)
				{
					$ref_cal=$temp;
				}
			}
		}

// calories used so far
		if(isset($_POST["calories"]))
		{
			$temp=$_POST["calories"];
			if(is_numeric($temp))
			{
// at least 0
				if($temp>=0)
				{
					$calories=$temp;
				}
			}
		}


		if(isset($_POST["target"]))					// minimum calorie target
		{
			$temp=$_POST["target"];
			if(is_numeric($temp))
			{
				if($temp>=0)					// at least 0
				{
					$target=$temp;
				}
			}
		}


		if(isset($_POST["intake"]))					// calories consumed so far
		{
			$temp=$_POST["intake"];
			if(is_numeric($temp))
			{
				if($temp>=0)					// any positive number
				{
					$intake=$temp;
				}
			}
		}


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


		if(isset($_POST["gender"]))
		{
			$temp=$_POST["gender"];
			if(is_numeric($temp))
			{
				if(($temp==1)||($temp==0))				// female == 1 , male == 0
				{
					$gender=$temp;
				}
			}
		}


		if(isset($_POST["_b"]))
		{
			$temp=$_POST["_b"];
			if(is_numeric($temp))
			{
				$ref_cal2=$temp;
			}
			else
			{
				$ref_cal2=intval($pre_burn/$time["seconds"]*60000)/1000;
			}
		}
	}

	$time_string=sprintf("%02s:%02s:%02s",$time['hour'],$time['mins'],$time['secs']);

// cm -> m
	$h1=$height/100;
	$w1=$weight;

	$bmr=array();

	$bmr[0]=bmr($w1,$h1,$age,0,$gender,$fat);
	$bmr[1]=bmr($w1,$h1,$age,1,$gender,$fat);
	$bmr[2]=bmr($w1,$h1,$age,2,$gender,$fat);

	$target=max($target,$bmr[0]);

	$min[0]=max($intake,$target);
	$min[1]=max(intval($bmr[0]),$intake)+350;
	$min[2]=max(intval($bmr[0]),$intake)+500;
	$min[3]=max(intval($bmr[0]),$intake)+750;
	$min[4]=min(max($intake,$bmr[0]),$target);

	if($min[1]==($intake+350))
	{
		$which="intake";
	}
	elseif($min[1]==($target+350))
	{
		$which="target";
	}
	else
	{
		$which="BMR";
	}

	$results_array=array();

	$to_burn=array();

	$ref_rate=$ref_cal/60;

	$temp=intval($now*$bmr[0]/86400);

// calories can't be lower than the time based proportion of BMR.
	$calories=max($temp,$calories);

// Estimated calories burnt during activities
	$active=$calories-$temp;

// how many calories left to burn to hit target
	$temp=cals_left($min[0],$remaining,$bmr[0],$calories);
	$to_burn["time_1_350"]=cals_left($min[1],$remaining,$bmr[0],$calories)/$ref_rate;
	$to_burn["time_1_500"]=cals_left($min[2],$remaining,$bmr[0],$calories)/$ref_rate;
	$to_burn["time_1_750"]=cals_left($min[3],$remaining,$bmr[0],$calories)/$ref_rate;
	$to_burn["time_1b"]=cals_left($intake,$remaining,$bmr[0],$calories)/$ref_rate;
	$to_burn["bmr1"]=$bmr[0];
	$to_burn["left_1"]=$temp;
	$to_burn["time_1"]=$temp/$ref_rate;
	$to_burn["rate_1"]=$temp/($now/60);

// how many calories left to burn to hit target
	$temp=cals_left($min[0],$remaining,$bmr[1],$calories);
	$to_burn["time_2_350"]=cals_left($min[1],$remaining,$bmr[1],$calories)/$ref_rate;
	$to_burn["time_2_500"]=cals_left($min[2],$remaining,$bmr[1],$calories)/$ref_rate;
	$to_burn["time_2_750"]=cals_left($min[3],$remaining,$bmr[1],$calories)/$ref_rate;
	$to_burn["time_2b"]=cals_left($intake,$remaining,$bmr[1],$calories)/$ref_rate;
	$to_burn["bmr2"]=$bmr[1];
	$to_burn["left_2"]=$temp;
	$to_burn["time_2"]=$temp/$ref_rate;
	$to_burn["rate_2"]=$temp/($now/60);

// how many calories left to burn to hit target
	$temp=cals_left($min[0],$remaining,$bmr[2],$calories);
	$to_burn["time_3_350"]=cals_left($min[1],$remaining,$bmr[2],$calories)/$ref_rate;
	$to_burn["time_3_500"]=cals_left($min[2],$remaining,$bmr[2],$calories)/$ref_rate;
	$to_burn["time_3_750"]=cals_left($min[3],$remaining,$bmr[2],$calories)/$ref_rate;
	$to_burn["time_3b"]=cals_left($intake,$remaining,$bmr[2],$calories)/$ref_rate;
	$to_burn["bmr3"]=$bmr[2];
	$to_burn["left_3"]=$temp;
	$to_burn["time_3"]=$temp/$ref_rate;
	$to_burn["rate_3"]=$temp/($now/60);

// how many calories left to burn to hit target
	$temp=cals_left($min[4],$remaining,$bmr[0],$calories);
	$to_burn["left_4"]=$temp;
	$to_burn["time_4"]=$temp/$ref_rate;
	$to_burn["rate_4"]=$temp/($now/60);
?>
		<div class="divTable">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHeading"></div>
					<div class="divTableHeada">BMR 1</div>
					<div class="divTableHeada">BMR 2</div>
					<div class="divTableHeada">BMR 3</div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">BMR</div>
					<div class="divTableCella"><?php echo number_format($to_burn["bmr1"],0); ?> (<?php echo number_format($to_burn["bmr1"]/24,1); ?>/h)</div>
					<div class="divTableCella"><?php echo number_format($to_burn["bmr2"],0); ?> (<?php echo number_format($to_burn["bmr2"]/24,1); ?>/h)</div>
					<div class="divTableCella"><?php echo number_format($to_burn["bmr3"],0); ?> (<?php echo number_format($to_burn["bmr3"]/24,1); ?>/h)</div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Calories left to burn (BMR 1)</div>
					<div class="divTableCella"><?php echo number_format($to_burn['left_1'],0); ?></div>
					<div class="divTableCella"><?php echo number_format($to_burn['left_2'],0); ?></div>
					<div class="divTableCella"><?php echo number_format($to_burn['left_3'],0); ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Cals/min</div>
					<div class="divTableCella"><?php echo number_format($to_burn["rate_1"],2); ?></div>
					<div class="divTableCella"><?php echo number_format($to_burn["rate_2"],2); ?></div>
					<div class="divTableCella"><?php echo number_format($to_burn["rate_3"],2); ?></div>
				</div>
<?php
if($intake>$bmr[0])
{
?>
				<div class="divTableRow">
					<div class="divTableHead">Estimated exercise time for <?php echo number_format($intake,0); ?> calories</div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_1b"]); ?></div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_2b"]); ?></div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_3b"]); ?></div>
				</div>
<?php
}
$temp=0;
foreach(array("","_350","_500","_750") as $key)
{
?>
				<div class="divTableRow">
					<div class="divTableHead">Estimated exercise time for <?php echo number_format($min[$temp++],0); ?> calories<?php
	if($key!="")
	{
		printf(" (%s + %s)",$which,str_replace("_","",$key));
	}
?></div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_1".$key]); ?></div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_2".$key]); ?></div>
					<div class="divTableCella"><?php echo time_from_seconds($to_burn["time_3".$key]); ?></div>
				</div>
<?php
}
?>
				<div class="divTableRow">
					<div class="divTableHead">Estimated minimum calories burned by midnight</div>
<?php
// Calories burnt at BMR rate for rest the of the day
	$temp=$bmr[0]+$active;
?>
					<div class="divTableCella"><?php echo number_format($temp,0); ?></div>
<?php
// Calories burnt at BMR rate for rest the of the day
	$temp=$bmr[1]+$active;
?>
					<div class="divTableCella"><?php echo number_format($temp,0); ?></div>
<?php
// Calories burnt at BMR rate for rest the of the day
	$temp=$bmr[2]+$active;
?>
					<div class="divTableCella"><?php echo number_format($temp,0); ?></div>
				</div>
			</div>
		</div>
		<hr />
		<div class="divTable">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHead">Reference burn rate (Cal/min)</div>
					<div class="divTableHeada"><?php echo number_format($ref_cal,3); ?></div>
				</div>
			</div>
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHead">Estimated activity burn</div>
					<div class="divTableHeada"><?php echo number_format($active,0); ?> calories</div>
				</div>
			</div>
		</div>
		<hr />
<?php
}

$tab_index=1;

?>
		<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" target="_self" accept-charset="UTF-8">
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableHead">Present calories</div>
						<div class="divTableCella"><input name="calories" id="calories" type="number" step="1" min="0" max="10000" value="<?php echo $calories; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Present food intake (Cal)</div>
						<div class="divTableCella"><input name="intake" id="intake" type="number" step="1" min="0" max="20000" value="<?php echo $intake; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Reference calories</div>
						<div class="divTableCella"><input name="reference_calories" id="reference_calories" type="number" step="0.001" min="0" max="15" value="<?php echo $ref_cal; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Gender</div>
						<div class="divTableCella">
							<select name="gender" id="gender" tabindex="<?php echo $tab_index++; ?>">
								<option value="1" <?php if($gender==1) printf("selected"); ?>>Female</option>
								<option value="0" <?php if($gender==0) printf("selected"); ?>>Male</option>
							</select>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Height (cm)</div>
						<div class="divTableCella"><input name="height" id="height" type="number" step="0.1" min="0" max="1000" value="<?php echo number_format($height,1); ?>" tabindex="<?php echo $tab_index++; ?>"/></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Weight (kg)</div>
						<div class="divTableCella"><input name="weight" id="weight" type="number" step="0.1" min="0" max="1000" value="<?php echo number_format($weight,1); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Bodyfat %</div>
						<div class="divTableCella"><input name="fat" id="fat" type="number" step="0.01" min="0" max="100" value="<?php echo number_format($fat,2); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Minimum calorie target</div>
						<div class="divTableCella"><input name="target" id="target" type="number" step="1" min="<?php echo intval($bmr[0]); ?>" max="10000" value="<?php echo intval($target); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
				</div>
			</div>
			<input name="_0" type="hidden" value="<?php echo $time_string; ?>" />
			<input name="_9" type="hidden" value="<?php echo $pre_burn; ?>" />
			<input name="_b" type="hidden" value="<?php echo $ref_cal2; ?>" />
			<input name="now" type="hidden" value="<?php echo $remaining; ?>" />
			<input type="submit" name="calculate" id="calculate" value="Calculate" tabindex="<?php echo $tab_index++; ?>" />
			<input type="reset" tabindex="<?php echo $tab_index++; ?>" />
		</form>
		<hr />
		<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" target="_self" accept-charset="UTF-8">
			<input name="_1" type="hidden" value="<?php echo $calories; ?>" />
			<input name="_2" type="hidden" value="<?php echo $intake; ?>" />
			<input name="_3" type="hidden" value="<?php echo $ref_cal; ?>" />
			<input name="_4" type="hidden" value="<?php echo $gender; ?>" />
			<input name="_5" type="hidden" value="<?php echo $height; ?>" />
			<input name="_6" type="hidden" value="<?php echo $weight; ?>" />
			<input name="_7" type="hidden" value="<?php echo $fat; ?>" />
			<input name="_8" type="hidden" value="<?php echo $target; ?>" />
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableHead">Reference calories burnt</div>
						<div class="divTableCella"><input name="_9" id="_9" type="number" step="1" min="0" max="10000" value="<?php echo $pre_burn; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Time</div>
						<div class="divTableCella"><input name="_0" id="_0" type="time" step="1" min="0" value="<?php echo $time_string; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Calories per minute</div>
						<div class="divTableCella"><input name="_a" type="number" value="<?php echo $ref_cal2; ?>" disabled/></div>
					</div>
				</div>
			</div>
			<input name="_b" type="hidden" value="<?php echo $ref_cal2; ?>">
			<input name="now" type="hidden" value="<?php echo $remaining; ?>" />
			<input type="submit" name="ref_cal" id="ref_cal" value="Calculate" tabindex="<?php echo $tab_index++; ?>" />
			<input type="reset" tabindex="<?php echo $tab_index++; ?>" />
		</form>
	<script type="text/javascript">
		var newTitle = "Calculate time walking needed to reach <?php echo $min[0]; ?> calories as of <?php echo $clock; ?>.";
		document.title = newTitle;
	</script>
	</body>
</html>
