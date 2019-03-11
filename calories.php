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
//$min_cal=2400;
$target=0;

$now=86400-time()%86400;                        // time to midnight
$clock=date("H:i:s",time());

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Calculate time walking needed to reach <?php echo $min_cal; ?> calories.</title>
		<meta content="">
		<style>
/* DivTable.com */
.divTable		{display: table;	width: 100%;}
.divTableRow		{display: table-row;}
.divTableCell		{border: 1px solid #999999;	display: table-cell;	padding: 3px 10px;}
.divTableHead		{border: 1px solid #999999;	display: table-cell;	padding: 3px 10px;}
.divTableHeading	{background-color: #EEE;	display: table-header-group;	font-weight: bold;}
.divTableFoot		{background-color: #EEE;	display: table-footer-group;	font-weight: bold;}
.divTableBody		{display: table-row-group;}
		</style>
		<script>
window.onload = function()
{
	document.getElementById("calories").focus();
};
		</script>
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
			if(($temp>=60)&&($temp<=275))				// between 0.6m and 2.75m feet tall
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
			if(($temp>=10)&&($temp<=160))				// between 10 and 160kg
			{
				$weight=$temp;
			}
		}
	}
	if(isset($_POST["reference_calories"]))					// used for the reference exercise
	{
		$temp=$_POST["reference_calories"];
		if(is_numeric($temp))
		{
			if(($temp>=1)&&($temp<=1600))				// between 1 and 1600
			{
				$reference_calories=$temp;
			}
		}
	}
	if(isset($_POST["calories"]))						// calories used so far
	{
		$temp=$_POST["calories"];
		if(is_numeric($temp))
		{
			if($temp>=0)						// at least 0
			{
				$calories=$temp;
			}
		}
	}
	if(isset($_POST["target"]))						// minimum calorie target
	{
		$temp=$_POST["target"];
		if(is_numeric($temp))
		{
			if($temp>=0)						// at least 0
			{
				$target=$temp;
			}
		}
	}
	if(isset($_POST["intake"]))						// calories consumed so far
	{
		$temp=$_POST["intake"];
		if(is_numeric($temp))
		{
			if($temp>=0)						// any positive number
			{
				$intake=$temp;
			}
		}
	}
	if(isset($_POST["time"]))						// time taken for reference calorie count 
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
		$time["seconds"]=$time['secs']+60*$time['mins']+3600*$time['hour'];
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

//	$height/=4;
//	$weight/=10;

	$h1=$height/100;							// cm -> m
	$w1=$weight;

	$bmr=array();

	$bmr[0]=bmr($w1,$h1,$age,0,$gender,$fat);
	$bmr[1]=bmr($w1,$h1,$age,1,$gender,$fat);
	$bmr[2]=bmr($w1,$h1,$age,2,$gender,$fat);
	
	$time_string=sprintf("%02s:%02s:%02s",$time['hour'],$time['mins'],$time['secs']);

	$target=max($target,$bmr[0]);

	$min[0]=max($intake,$target);
	$min[1]=max(intval($bmr[0]),$intake)+350;
	$min[2]=max(intval($bmr[0]),$intake)+500;
	$min[3]=max(intval($bmr[0]),$intake)+750;

	if($min[0]==$intake)
	{
		$which="intake";
	}
	elseif($min[0]==$min_cal)
	{
		$which="target";
	}
	else
	{
		$which="BMR";
	}

	$to_burn=array();

	if($time['seconds']>0)
	{
		$ref_rate=$reference_calories/$time['seconds'];
	}
	else
	{
		$ref_rate=0.00027777777777;
	}

	$temp=intval((86400-$now)*$bmr[0]/86400);
	$calories=max($temp,$calories);		// calories can't be lower than the time based proportion of BMR.
	$active=$calories-$temp;

	$temp=$min[0]-$now*$bmr[0]/86400-$calories;		// how many calories left to burn to hit target
	$to_burn["time_1_350"]=max($min[1]-$now*$bmr[0]/86400-$calories,0)/$ref_rate;
	$to_burn["time_1_500"]=max($min[2]-$now*$bmr[0]/86400-$calories,0)/$ref_rate;
	$to_burn["time_1_750"]=max($min[3]-$now*$bmr[0]/86400-$calories,0)/$ref_rate;
	$to_burn["time_1b"]=max($intake-$now*$bmr[0]/86400-$calories,0)/$ref_rate;
	$to_burn["bmr1"]=$bmr[0];
	if($temp<=0)
	{
		$to_burn["left_1"]=0;
		$to_burn["rate_1"]=0;
		$to_burn["time_1"]=0;
//		$to_burn["time_1_350"]=0;
//		$to_burn["time_1_500"]=0;
//		$to_burn["time_1_750"]=0;
//		$to_burn["time_1b"]=0;
	}
	else
	{
		$to_burn["left_1"]=$temp;
		$to_burn["time_1"]=$temp/$ref_rate;
		$to_burn["rate_1"]=$temp/($now/60);
	}

	$temp=$min[0]-$now*$bmr[1]/86400-$calories;		// how many calories left to burn to hit target
	$to_burn["time_2_350"]=max($min[1]-$now*$bmr[1]/86400-$calories,0)/$ref_rate;
	$to_burn["time_2_500"]=max($min[2]-$now*$bmr[1]/86400-$calories,0)/$ref_rate;
	$to_burn["time_2_750"]=max($min[3]-$now*$bmr[1]/86400-$calories,0)/$ref_rate;
	$to_burn["time_2b"]=max($intake-$now*$bmr[1]/86400-$calories,0)/$ref_rate;
	$to_burn["bmr2"]=$bmr[1];
	if($temp<=0)
	{
		$to_burn["left_2"]=0;
		$to_burn["rate_2"]=0;
		$to_burn["time_2"]=0;
//		$to_burn["time_2_350"]=0;
//		$to_burn["time_2_500"]=0;
//		$to_burn["time_2_750"]=0;
//		$to_burn["time_2b"]=0;
	}
	else
	{
		$to_burn["left_2"]=$temp;
		$to_burn["time_2"]=$temp/$ref_rate;
		$to_burn["rate_2"]=$temp/($now/60);
	}
	
	$temp=$min[0]-$now*$bmr[2]/86400-$calories;		// how many calories left to burn to hit target
	$to_burn["time_3_350"]=max($min[1]-$now*$bmr[2]/86400-$calories,0)/$ref_rate;
	$to_burn["time_3_500"]=max($min[2]-$now*$bmr[2]/86400-$calories,0)/$ref_rate;
	$to_burn["time_3_750"]=max($min[3]-$now*$bmr[2]/86400-$calories,0)/$ref_rate;
	$to_burn["time_3b"]=max($intake-$now*$bmr[2]/86400-$calories,0)/$ref_rate;
	$to_burn["bmr3"]=$bmr[2];

	if($temp<=0)
	{
		$to_burn["left_3"]=0;
		$to_burn["rate_3"]=0;
		$to_burn["time_3"]=0;
//		$to_burn["time_3_350"]=0;
//		$to_burn["time_3_500"]=0;
//		$to_burn["time_3_750"]=0;
//		$to_burn["time_3b"]=0;
	}
	else
	{
		$to_burn["left_3"]=$temp;
		$to_burn["time_3"]=$temp/$ref_rate;
		$to_burn["rate_3"]=$temp/($now/60);
	}
	
?>
		<div class="divTable">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHeading"></div>
					<div class="divTableHead">BMR 1</div>
					<div class="divTableHead">BMR 2</div>
					<div class="divTableHead">BMR 3</div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">BMR</div>
					<div class="divTableCell"><?php echo number_format($to_burn["bmr1"],0); ?> (<?php echo number_format($to_burn["bmr1"]/24,1); ?>/h)</div>
					<div class="divTableCell"><?php echo number_format($to_burn["bmr2"],0); ?> (<?php echo number_format($to_burn["bmr2"]/24,1); ?>/h)</div>
					<div class="divTableCell"><?php echo number_format($to_burn["bmr3"],0); ?> (<?php echo number_format($to_burn["bmr3"]/24,1); ?>/h)</div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Calories left to burn (BMR 1)</div>
					<div class="divTableCell"><?php echo number_format($to_burn['left_1'],0); ?></div>
					<div class="divTableCell"><?php echo number_format($to_burn['left_2'],0); ?></div>
					<div class="divTableCell"><?php echo number_format($to_burn['left_3'],0); ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Cals/min</div>
					<div class="divTableCell"><?php echo number_format($to_burn["rate_1"],2); ?></div>
					<div class="divTableCell"><?php echo number_format($to_burn["rate_2"],2); ?></div>
					<div class="divTableCell"><?php echo number_format($to_burn["rate_3"],2); ?></div>
				</div>
				<div class="divTableRow">
					<div class="divTableHead">Estimated exercise time for <?php echo number_format($intake,0); ?> calories</div>
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_1b"]); ?></div>
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_2b"]); ?></div>
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_3b"]); ?></div>
				</div>
<?php
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
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_1".$key]); ?></div>
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_2".$key]); ?></div>
					<div class="divTableCell"><?php echo time_from_seconds($to_burn["time_3".$key]); ?></div>
				</div>
<?php
}
?>
				<div class="divTableRow">
					<div class="divTableHead">Estimated minimum calories burned by midnight</div>
<?php
// Calories burnt at BMR rate for rest the of the day
//	$temp=$bmr[0]*$now/86400+$calories;
	$temp=$bmr[0]+$active;
?>
					<div class="divTableCell"><?php echo number_format($temp,0); ?></div>
<?php
// Calories burnt at BMR rate for rest the of the day
//	$temp=$bmr[1]*$now/86400+$calories;
	$temp=$bmr[1]+$active;
?>
					<div class="divTableCell"><?php echo number_format($temp,0); ?></div>
<?php
// Calories burnt at BMR rate for rest the of the day
//	$temp=$bmr[2]*$now/86400+$calories;
	$temp=$bmr[2]+$active;
?>
					<div class="divTableCell"><?php echo number_format($temp,0); ?></div>
				</div>
			</div>
		</div>
		<hr />
		<div class="divTable">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHead">Reference burn rate (Cal/min)</div>
					<div class="divTableHead"><?php echo number_format(($ref_rate>0.00027777777777?$ref_rate:0)*60,2); ?></div>
				</div>
			</div>
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableHead">Estimated activity burn</div>
					<div class="divTableHead"><?php echo number_format($active,0); ?> calories</div>
				</div>
			</div>
		</div>
		<hr />
<?php
}

//printf("%s<br />\n%s<br />\n",$height,$weight);

$tab_index=1;

?>
		<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" target="_self" accept-charset="UTF-8">
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableHead">Present calories</div>
						<div class="divTableCell"><input name="calories" id="calories" type="number" step="1" min="0" max="10000" value="<?php echo $calories; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Present food intake (Cal)</div>
						<div class="divTableCell"><input name="intake" id="intake" type="number" step="1" min="0" max="20000" value="<?php echo $intake; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Reference calories</div>
						<div class="divTableCell"><input name="reference_calories" id="reference_calories" type="number" step="1" min="0" max="10000" value="<?php echo $reference_calories; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Time</div>
						<div class="divTableCell"><input name="time" id="time" type="time" step="1" min="0" value="<?php echo $time_string; ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableHead">Gender</div>
						<div class="divTableCell">
							<select name="gender" id="gender" tabindex="<?php echo $tab_index++; ?>">
								<option value="1" <?php if($gender==1) printf("selected"); ?>>Female</option>
								<option value="0" <?php if($gender==0) printf("selected"); ?>>Male</option>
							</select>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Height (cm)</div>
						<div class="divTableCell"><input name="height" id="height" type="number" step="0.1" min="0" max="1000" value="<?php echo number_format($height,1); ?>" tabindex="<?php echo $tab_index++; ?>"/></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Weight (kg)</div>
						<div class="divTableCell"><input name="weight" id="weight" type="number" step="0.1" min="0" max="1000" value="<?php echo number_format($weight,1); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Bodyfat %</div>
						<div class="divTableCell"><input name="fat" id="fat" type="number" step="0.01" min="0" max="100" value="<?php echo number_format($fat,2); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Minimum calorie target</div>
						<div class="divTableCell"><input name="target" id="target" type="number" step="1" min="<?php echo number_format($bmr[0],0); ?>" max="10000" value="<?php echo number_format($target,0); ?>" tabindex="<?php echo $tab_index++; ?>" /></div>
					</div>
				</div>
			</div>
			<input name="now" type="hidden" value="<?php echo $now; ?>" />
			<input type="submit" name="calculate" id="calculate" value="Calculate" tabindex="<?php echo $tab_index++; ?>" />
			<input type="reset" tabindex="<?php echo $tab_index++; ?>" />
		</form>
	<script type="text/javascript">
		var newTitle = "Calculate time walking needed to reach <?php echo $min[0]; ?> calories as of <?php echo $clock; ?>.";
		document.title = newTitle;
	</script>
	</body>
</html>
