<!-- Vaibhav Garg -->
<!-- https://github.com/21VAibhavGArg/igstorymaker -->
<?php
	error_reporting(1);
	putenv('GDFONTPATH=' . realpath('.'));

	$quote_data = ($_GET["q"]) ? $_GET["q"] : " ";

	$cwd = getcwd();
	$ts = time();
	//resources
	$mr = $cwd.'\Montserrat.ttf';
	$canvas = imagecreatefrompng('BG.png');	
	$uppr_q = imagecreatefrompng($cwd.'\upper_quote.png');
	$lwr_q = imagecreatefrompng($cwd.'\lower_quote.png');
	
	//colros
	$black = imagecolorallocate($canvas, 0, 0, 0);
	$white = imagecolorallocate($canvas, 225, 225, 225);

	$data = explode(" - ", $quote_data);

//add author
	$author = $data[1];
	$a_len = strlen($author);
	$a_size = 30;
	$a_bb = imagettfbbox(30, 0, $mr, $author); //author text bounding box
	$a_size = round($a_size * (1 - (($a_bb[2] - 290) / $a_bb[2]))); //shrink bbox to fit
	$a_size = ($a_size > 35) ? 35 : $a_size; //limiting the size of author text
	$a_bb = imagettfbbox($a_size, 0, $mr, $author);
	imagefttext($canvas, $a_size, 0, 650 - ($a_bb[2]), 150 - ($a_bb[3]), $white, $mr, $author); //650 and 150 are padding top and left

//add quote
	$quote = $data[0];
	$q_len = strlen($quote);
	$q_size = 60;
	$q_bb = imagettfbbox(60, 0, $mr, $quote); //quote text bounding box
	$q_l = $q_bb[2];
	$max_char_limit = round(sqrt((0.7 * $q_l * 60)) / 30);
	$lines = explode('|', wordwrap($quote, $max_char_limit,'|'));
	$max_x = 0;
	$nl = 0;
	//write with 30pt size
	foreach ($lines as $line) {
		$d_crd = imagettfbbox(60, 0, $mr, $line);
		$max_x = ($max_x < $d_crd[2]) ? $d_crd[2] : $max_x;
		$nl += 1;
	}
	//calc shrink text in pt
	$q_size = ($q_len < 100) ? round(0.9 * 60 * 500 / $max_x) : round(60 * 500 / $max_x);
	$q_size = $q_size > 180 ? 180 : $q_size;
	//recalc max x for shrinked size
	$max_x = 0;
	foreach ($lines as $line) {
		$d_crd = imagettfbbox($q_size, 0, $mr, $line);
		$max_x = ($max_x < $d_crd[2]) ? $d_crd[2] : $max_x;
	}
	//center align
	$y = ((1280 - (($q_size * $nl) + (0.3 * $q_size * ($nl - 1)))) / 2) + 20;
	$x=((720 - $max_x) / 2);
	//write quote
	foreach ($lines as $line) {
		imagefttext($canvas, $q_size, 0, $x, $y, $white, $mr, $line);
		$y += 1.3 * $q_size;
	}

//add quotation marks
	imagecopy($canvas, $lwr_q, $max_x + $x - 50, $y - $q_size - 20, 0, 0, 60, 38);
	$y=((1280 - (($q_size * $nl) + (0.3 * $q_size * ($nl - 1)))) / 2) + 20; //set last y
	imagecopy($canvas, $uppr_q, $x - 20, $y - $q_size - 50, 0, 0, 141, 89);
	imagepng($canvas, "story-".$ts.".png");
?>
<!DOCTYPE html>
<html>
<head>
	<title>IG Story Maker</title>
</head>
<body>
<img src=<?php echo "story-".$ts.".png"; ?>>
<?php echo "<br>source: "."story-".$ts.".png<br>"."Quote- ".$quote."<br>". "Author-" .$author.";<br>"."Q len- ".$q_len."; A len- ".$a_len."; A size- ".$a_size."; Q size- ".$q_size."; WordWrap-".$max_char_limit."; Max X- ".$max_x;?>
</body>
</html>