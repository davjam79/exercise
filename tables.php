<?php
// create tables for conversion formula

$weight_array=array();
for($temp=100;$temp<=1600;$temp++)						// to 160kg
{
	$weight_array[$temp]=$temp/10;
}

$height_array=array();								// to 8' 0"
for($temp=96;$temp<=384;$temp++)
{
	$height_array[$temp]=to_height($temp);
}

function to_height($height)
{
	$metres=$height/4*0.0254;
	$feet=intval($height/48);
	$height=$height%48;
	$inch=intval($height/4);
	$height=$height%4;
	switch($height)
	{
		case 0:
			$fraction="";
			break;
		case 1:
			$fraction="1/4";
			break;
		case 2:
			$fraction="1/2";
			break;
		case 3:
			$fraction="3/4";
			break;
	}
	return(array( "feet" => $feet,	"inches" => $inch,	"fraction" => $fraction,	"metres" => $metres));
}
?>
