<?php

require_once("functions.php");		// functions used for calculations

date_default_timezone_set('Europe/London');

header("Expires: ".gmdate("D, d M Y H:i:s",strtotime("1 day ago"))." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0");
header("Pragma: no-cache");

$neck=30;
$waist=60;
$hips=60;
$height=170;
$gender=0;
$fat=0;

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Calculate body fat percentage.</title>
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
	document.getElementById("neck").focus();
};
		</script>
	</head>
	<body>
<?php
if(isset($_POST["calculate"]))
{
/*
men: %Fat = 86.010*LOG(abdomen - neck) - 70.041*LOG(height) + 30.30

women: %Fat = 163.205*LOG(abdomen + hip - neck) - 97.684*LOG(height) - 78.387
*/
	if(isset($_POST["_0"]))						// neck, male/female
	{
		$neck=$_POST["_0"];
	}
	if(isset($_POST["_1"]))						// waist, male/female
	{
		$waist=$_POST["_1"];
	}
	if(isset($_POST["_2"]))						// hips, female
	{
		$hips=$_POST["_2"];
	}
	if(isset($_POST["_3"]))						// height, male/female
	{
		$height=$_POST["_3"];
	}
	if(isset($_POST["_4"]))						// gender, male/female
	{
		$gender=$_POST["_4"];
	}

	if($gender==0)							// male
	{
		$fat=(86.01*log10(max($waist-$neck,1)))-(70.041*log10(max($height,1)))+30.3;
	}
	else								// female
	{
		$fat=163.205*log10(max($hips+$waist-$neck,1))-97.684*log10(max($height,1))-78.387;
	}
}

$tab_index=1;
?>
		<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" target="_self" accept-charset="UTF-8">
			<div class="divTable">
				<div class="divTableBody">
<?php
if($fat!=0)
{
?>
					<div class="divTableRow">
						<div class="divTableHead">Body fat</div>
						<div class="divTableCell"><?php echo number_format($fat,3); ?>%</div>
					</div>
<?php
}
?>
					<div class="divTableRow">
						<div class="divTableHead">Neck</div>
						<div class="divTableCell"><input name="_0" id="neck" type="number" step="0.1" min="10" max="100" value="<?php echo $neck; ?>" tabindex="<?php echo $tab_index++; ?>" /> cm</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Waist</div>
						<div class="divTableCell"><input name="_1" id="waist" type="number" step="0.1" min="10" max="300" value="<?php echo $waist; ?>" tabindex="<?php echo $tab_index++; ?>" /> cm</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Hips (female only)</div>
						<div class="divTableCell"><input name="_2" id="hips" type="number" step="0.1" min="10" max="500" value="<?php echo $hips; ?>" tabindex="<?php echo $tab_index++; ?>" /> cm</div>
					</div>
					<div class="divTableRow">
						<div class="divTableHead">Height</div>
						<div class="divTableCell"><input name="_3" id="height" type="number" step="0.1" min="10" max="300" value="<?php echo number_format($height,1); ?>" tabindex="<?php echo $tab_index++; ?>"/> cm</div>
					</div>
					<div class="divTableHead">Gender</div>
						<div class="divTableCell">
							<select name="_4" id="gender" tabindex="<?php echo $tab_index++; ?>">
								<option value="1" <?php if($gender==1) printf("selected"); ?>>Female</option>
								<option value="0" <?php if($gender==0) printf("selected"); ?>>Male</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<input type="submit" name="calculate" id="calculate" value="Calculate" tabindex="<?php echo $tab_index++; ?>" />
			<input type="reset" tabindex="<?php echo $tab_index++; ?>" />
		</form>
	</body>
</html>
