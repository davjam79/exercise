<?php

function	miles_to_metres($distance)
{
	return($distance*1609.344);
}

function	time_to_sec($time)
{
	$seconds=0;
	$minutes=0;
	$hours=0;

	$temp=array_reverse(explode(":",$time));

	if(isset($temp[0]))
	{
		$seconds=$temp[0];
		if(isset($temp[1]))
		{
			$minutes=$temp[1];
			if(isset($temp[2]))
			{
				$hours=$temp[2];
			}
		}
	}

	$total=$seconds+60*$minutes+3600*$hours;

	if($total<=0)
	{
		return(FALSE);
	}
	return($total);
}

function	speed_ms($distance,$seconds)
{
	$metres=miles_to_metres($distance);
	return($metres/$seconds);
}

function	bmr($weight,$height,$age,$which,$gender=0,$fat=0)
{
	$temp=array("weight"=>$weight,"height"=>$height,"age"=>$age,"which"=>$which,"gender"=>$gender,"fat"=>$fat);
	$bmr=0;
	switch($which)
	{
		case 2:
/*
	Both	BMR = 370 + 21.6*Weight*(1-Fat)
*/
			$bmr=370+(21.6*$weight*(1-$fat/100));
			break;

		case 1:									// Harris-Benedict BMR equations
/*
	Men	BMR = 13.397*Weight + 4.799*Height - 5.677*Age + 88.362
	Women	BMR = 9.247*Weight + 3.098*Height - 4.330*Age + 447.593
*/
			if($gender==0)
			{
				$bmr=(13.397*$weight)+(4.799*($height*100))-(5.677*$age)+88.362;
			}
			else
			{
				$bmr=(9.247*$weight)+(3.098*($height*100))-(4.330*$age)+447.593;
			}
			break;

		case 0:
		default:								// Mifflin - St Jeor Equation
/*
	Men	BMR = 10*Weight + 6.25*Height - 5*Age +5
	Women	BMR = 10*Weight + 6.25*Height - 5*Age -161
*/
			if($gender==0)
			{
				$bmr=(10*$weight)+(6.25*($height*100))-(5*$age)+5;
			}
			else
			{
				$bmr=(10*$weight)+(6.25*($height*100))-(5*$age)-161;
			}
			break;
	}
	$temp["bmr"]=$bmr;
/*
?>
<pre>
<?php
print_r($temp);
?>
</pre>
<?php
*/
	return($bmr);
}

// time -> 00:00:00
// height -> height in inches x4
// weight -> weight in kg x10
function	calories($distance,$time,$height,$weight,$age,$fat,$clothed,$gender=0)
{
	$fitbit=0;				// calories according to Fitbit
	$garmin=0;				// calories according to Garmin

	$bmr=array();

	$height=$height/100;			// to metres
//	$weight=$weight/10;			// to kg
//	$clothed=$clothed/10;			// to kg
	$seconds=time_to_sec($time);
	if($seconds===FALSE)
	{
		return(FALSE);
	}

	$velocity=speed_ms($distance,$seconds);
	$mph=$velocity*3600/1609.344;
	$pace=60/$mph;

	$bmr[1]=bmr($weight,$height,$age,0,$gender,$fat)/1440;
	$bmr[2]=bmr($weight,$height,$age,1,$gender,$fat)/1440;
	$bmr[3]=bmr($weight,$height,$age,2,$gender,$fat)/1440;

	$average=(0.035*$clothed)+(($velocity*$velocity/$height)*0.029*$clothed);	// per minute

//	$garmin=($bmr+(0.035*$weight)+(($velocity*$velocity/$height)*0.029*$weight))*($seconds/60);	// per minute

	$total=$average*($seconds/60);
	$fitbit=$total*(213/180);
	$garmin=$total*(522/422);

	$bmr_1=($bmr[1]+$average)*($seconds/60);
	$bmr_2=($bmr[2]+$average)*($seconds/60);
	$bmr_3=($bmr[3]+$average)*($seconds/60);

	return(
		array(
			"average"	=>	$average,
			"speed"		=>	$velocity,
			"duration"	=>	$seconds,
			"mph"		=>	$mph,
			"pace"		=>	$pace,
			"bmr1"		=>	$bmr[1],
			"bmr2"		=>	$bmr[2],
			"bmr3"		=>	$bmr[3],
			"total"		=>	$total,
			"fitbit"	=>	$fitbit,
			"garmin"	=>	$garmin,
			"bmr_1"		=>	$bmr_1,
			"bmr_2"		=>	$bmr_2,
			"bmr_3"		=>	$bmr_3,
		)
	);
}

function	time_from_seconds($seconds)
{
	$temptz=date_default_timezone_get();
	date_default_timezone_set("UTC");
	$time=date("H:i:s",$seconds);
	date_default_timezone_set($temptz);
	return($time);
}

?>
