<?php 
// License: Copyright (C) 2014  Simon Attard
function get_taf($station)
{
	$fileName = "ftp://tgftp.nws.noaa.gov/data/forecasts/taf/stations/$station.TXT";
	$taf = '';
	$fileData = @file($fileName);
	if ($fileData != false)
	{
		list($i, $date) = each($fileData);
		while (list($i, $line) = each($fileData))
		{
			$taf .= ' ' . trim($line);
		}
		$taf = trim(str_replace('  ', ' ', $taf));
	} else {
		return ' ';
	} 
	return $taf;
}
function get_metar($station)
{
	$fileName = "ftp://tgftp.nws.noaa.gov/data/observations/metar/stations/$station.TXT";
	$metar2 = '';
	$metar = '';
	$fileData = @file($fileName);  // or die('Data not available');
	if ($fileData != false)
    {
		list($i, $date) = each($fileData);
		while (list($i, $line) = each($fileData))
        {
			$metar .= ' ' . trim($line);
		}
		$metar = trim(str_replace('  ', ' ', $metar));
		$metar2 = "METAR" . " " . $metar;
	}
	return $metar2;
}
function load_table() {
	global $wpdb;
	$filename = str_replace("//","//////",__DIR__ ."/upperdb.csv");
	$table_nme = $wpdb->prefix . "upper_winds";
	$qry = "LOAD DATA LOCAL INFILE '" . $filename . "'
	IGNORE INTO TABLE $table_nme 
	FIELDS TERMINATED BY ',' 
	ENCLOSED BY '\"' 
	ESCAPED BY '\"'
	LINES TERMINATED BY '\n'
	"; 
	$wpdb->query($qry);
}
function upper_table(){
	global $wpdb;
	$table_name = $wpdb->prefix . "upper_winds";
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
    $sql = "CREATE TABLE $table_name( 
	`LVL` VARCHAR(4) NOT NULL, 
	`REGION` TEXT NOT NULL, 
	`VALID` INT NOT NULL, 
	`CURRENT` TEXT NOT NULL 
	);";
    //reference to upgrade.php file
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	load_table();
}
}
function metar_taf () {
?>
<br/>
<h4 style="font-weight:bold;">METAR/TAF GENERATOR</h4>
<p>Insert up to 3 valid ICAO identifiers to get METAR and TAF Reports</p>
<form action="<?php the_permalink(); ?>" id="avwx" method="POST">
ICAO CODES <input type="text" name="icao1" id="airport" maxlength="4" size="4" onkeyup="javascript:this.value=this.value.toUpperCase()">
<input type="text" name="icao2" id="airport" maxlength="4" size="4" onkeyup="javascript:this.value=this.value.toUpperCase()">
<input type="text" name="icao3" id="airport" maxlength="4" size="4" onkeyup="javascript:this.value=this.value.toUpperCase()">
<input type="submit" name="submit" id="submitted" value="GET REPORT">
</form> 
<br/>
<?php
if (isset($_POST['submit'])) {
			foreach ($_POST as $icao) {
				if ($icao == "GET REPORT"){
					break;
				}
				else {
					if ($icao != NULL) {
					echo get_metar($icao);
					echo "<br>";
					echo "<br>";
					echo get_taf($icao);
					echo "<br>";
					echo "<br>";
					}
				else {
					continue;
				}
			}
			}
		}	
}
?>
<?php
function winds() {
?>
<br/>
<h4 style="font-weight:bold;">UPPER WINDS AND TEMPERATURE</h4>
<form action="<?php the_permalink(); ?>" id="upper" method="POST" target="_Blank">
		<table>
		<tr style="background-color: #F1F1F1;">
			<th style="font-color: #757575;">LEVEL</th>
			<th style="font-color: #757575;">REGION</th>
			<th style="font-color: #757575;">TIME</th>
			<th style="font-color: #757575;"></th>
		</tr>
		<tr>	
			<td>
		<select name="level">
		<option value="F050">FL50</option>
		<option value="F100">FL100</option>
		<option value="F180">FL180</option>
		<option value="F240">FL240</option>
		<option value="F300">FL300</option>
		<option value="F340">FL340</option>
		<option value="F390">FL390</option>
		</select>
			</td>
		</td>	
			<td>
		<select name="reg">
		<option value="NATL">NORTH ATLANTIC</option>
		<option value="EURAFR">EUROPE-AFRICA</option>
		<option value="AMERICAS">AMERICAS</option>
		<option value="AMERSAFR">AMERS-AFR</option>
		<option value="ASIA">ASIA</option>
		<option value="ASIAAUS">ASIA-AUSTRALIA</option>
		<option value="PACIFIC">PACIFIC</option>
		</select>
			</td>	
			<td>
		<select name="val">
		<option value="6">+6</option>
		<option value="12">+12</option>
		<option value="18">+18</option>
		<option value="24">+24</option>
		</select>
			<td>
		<input type="submit" value="GET WINDS" name="temps">
		</tr>
		</table>
		</form>
<?php
get_winds();
}
?>
<?php
function get_winds(){
	global $wpdb;
	$upper_table = $wpdb->prefix . "upper_winds";
	if (isset($_POST['temps'])) {
	$wqry = "SELECT CURRENT FROM $upper_table
			WHERE LVL = '".$_POST['level']."'
			AND 
			REGION = '".$_POST['reg']."'
			AND
			VALID = '".$_POST['val']."';";
	$result = $wpdb->get_results($wqry);
	$result = $result[0]->CURRENT;
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . $result . '">'; 
	}
}
function sigwx() {
?>
<script language="javascript" type="text/javascript">
  jQuery(document).ready(function($) {
    $('#250').hide(); //hide field on start
     
    $('#region').change(function() {
     
    var $index = $('#region').index(this);
     
    if($('#region').val() != 'WAFC') { //if this value is NOT selected
    $('#100').hide(); //this field is hidden
	$('#250').show();
    }
    else {
    $('#100').show();//else it is shown
    $('#250').hide();
    }
    });
    });

</script>
<h4 style="font-weight:bold;">SIGNIFICANT WEATHER</h4>
<form action="<?php the_permalink(); ?>" id="upper" method="POST" target="_Blank">
		<table>
		<tr style="background-color: #F1F1F1;">
			<th style="font-color: #757575;">REGION</th>
			<th style="font-color: #757575;">LEVEL</th>
			<th style="font-color: #757575;">TIME</th>
			<th style="font-color: #757575;"></th>
		</tr>
		<tr>	
		<td>
		<select id="region" name="region" >
		<option value="WAFC">WAFC LONDON</option>
		<option value="AMERICAS">AMERICAS</option>
		<option value="AMERSAFR">AMERICAS/AFRICA</option>
		<option value="CPAC">CENTRAL PACIFIC</option>
		<option value="NATL">NORTH ATHLANTIC</option>
		<option value="POLAR">POLAR</option>
		<option value="SPAC">SOUTH PACIFIC</option>
		<option value="NPAC">NORTH PACIFIC</option>
		</select>
		</td>
		<td>
			<div id="100" style="font-weight:bold;">FL100-FL450</div>
			<div id="250" style="font-weight:bold;">FL250-FL630</div>
		</td>
			<td>
		<select name="time">
		<option value="6">0600Z</option>
		<option value="12">1200Z</option>
		<option value="18">1800Z</option>
		<option value="24">0000Z</option>
		</select>
			<td>
		<input type="submit" value="GET SIG WX" name="chart">
		</tr>
		</table>
		</form>
<?php
get_sigwx();
}
?>
<?php
function load_sigwx() {
	global $wpdb;
	$filename = str_replace("//","//////",__DIR__ ."/s_wx.csv");
	$table_nme = $wpdb->prefix . "swx";
	$qry = "LOAD DATA LOCAL INFILE '" . $filename . "'
	IGNORE INTO TABLE $table_nme 
	FIELDS TERMINATED BY ',' 
	ENCLOSED BY '\"' 
	ESCAPED BY '\"'
	LINES TERMINATED BY '\n'
	"; 
	$wpdb->query($qry);
}
function sigwx_table(){
	global $wpdb;
	$table = $wpdb->prefix . "swx";
if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table){
    $sql = "CREATE TABLE $table( 
	`REGION` TEXT NOT NULL, 
	`LEVEL` TEXT NOT NULL, 
	`TIME` INT NOT NULL, 
	`URL` TEXT NOT NULL 
	);";
    //reference to upgrade.php file
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	load_sigwx();
	}
}
function create_tables(){
	upper_table();
	sigwx_table();	
}
function get_sigwx(){
	global $wpdb;
	$wx_table = $wpdb->prefix . "swx";
	if (isset($_POST['chart'])) {
	$qry = "SELECT URL FROM $wx_table
			WHERE REGION = '".$_POST['region']."'
			AND 
			TIME = '".$_POST['time']."';";
	$result = $wpdb->get_results($qry);
	$result = $result[0]->URL;
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . $result . '">'; 
	}
}
function aviation_wx_admin_actions(){
	add_options_page('AviationWx','AviationWx','manage_options',__FILE__,'aviationwx_admin');
}
function aviationwx_admin(){
?>
<div class="wrap">
<h2>Aviation Weather Plugin</h2>
</div>
<table class="widefat fixed">
<thead>
	<tr>
		<th style="text-align:left;">To display METAR/TAF, Signicant Weather and Upper Winds use the below shortcodes</th>
	</tr
</thead>
	<tr>
		<td><br/></td>
	</tr>
	<tr>
		<td>User is displayed 3 fields where he can enter valid ICAO identifiers</td>
		<td style="text-align:left;">[metar_taf]</td>
	</tr>
	<tr>
		<td>User can select the appropriate Significant Weather Chart</td>
		<td style="text-align:left;">[sigwx]</td>
	</tr>
	<tr>
		<td>User can select the appropriate Upper Winds and Temperature</td>
		<td style="text-align:left;">[winds]</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">NOTE:  Insert shortcodes within the TEXT area when adding a PAGE or POST</td>
	</tr>
</table>
<?php
}





