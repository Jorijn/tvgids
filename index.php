<!DOCTYPE HTML>
<html>
	<head>
		<title>TVGids ala Jorijn</title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<style>
			html, body {
				margin: 0;
				padding: 0;
				overflow-x: scroll;
			}

			* {
				font-family: helvetica;
				font-size: 12px;
			}

			span.time {
				font-weight: bold;
			}

			.channels {
				position: absolute;
				left: 0;
				width: 200px;
				z-index: 5;
				border-right: 1px solid #ccc;
				opacity: 0.9;
			}

			.channels .channel, .timetable .nothing, .now {
				background: #f6f8f9; /* Old browsers */
				background: -moz-linear-gradient(top,  #f6f8f9 0%, #e5ebee 50%, #d7dee3 51%, #f5f7f9 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6f8f9), color-stop(50%,#e5ebee), color-stop(51%,#d7dee3), color-stop(100%,#f5f7f9)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  #f6f8f9 0%,#e5ebee 50%,#d7dee3 51%,#f5f7f9 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  #f6f8f9 0%,#e5ebee 50%,#d7dee3 51%,#f5f7f9 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  #f6f8f9 0%,#e5ebee 50%,#d7dee3 51%,#f5f7f9 100%); /* IE10+ */
				background: linear-gradient(to bottom,  #f6f8f9 0%,#e5ebee 50%,#d7dee3 51%,#f5f7f9 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f6f8f9', endColorstr='#f5f7f9',GradientType=0 ); /* IE6-9 */
			}

			.programs {
				position: absolute;
				left: 200px;
			}

			.programs .marker {
				height: 100%;
				border-right: 1px solid #ff0000;
				position: absolute;
				top: 0;
			}

			.channel, .timetable .item {
				width: 100%;
				border-top: 1px solid #ccc;
				line-height: 50px;
				white-space: nowrap;
				height: 50px;
				background: #ffffff; /* Old browsers */
				background: -moz-linear-gradient(top,  #ffffff 0%, #f3f3f3 50%, #ededed 51%, #ffffff 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(50%,#f3f3f3), color-stop(51%,#ededed), color-stop(100%,#ffffff)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  #ffffff 0%,#f3f3f3 50%,#ededed 51%,#ffffff 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  #ffffff 0%,#f3f3f3 50%,#ededed 51%,#ffffff 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  #ffffff 0%,#f3f3f3 50%,#ededed 51%,#ffffff 100%); /* IE10+ */
				background: linear-gradient(to bottom,  #ffffff 0%,#f3f3f3 50%,#ededed 51%,#ffffff 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */

				display: block;
				clear: both;
			}

			.channel * {
				white-space: nowrap;
				text-indent: 10px;
			}

			.index {
				font-weight: bold;
				width: 200px;
				display: inline-block;
			}

			.program {
				display: inline-block;
				height: 50px;
				overflow: hidden;
				border-left: 1px solid #ccc;
				position: relative;
				left: -1px;
			}

			.timetable {
				height: 30px;
				line-height: 30px;
				/* border-bottom: 1px solid #ccc; */
			}

			.timetable .nothing, .timetable .item {
				height: 30px;
			}

			.timetable .nothing {
				display: inline-block;
				width: 200px;
				border-right: 1px solid #ccc;
				height: 30px;
			}

			.timetable .item {
				display: inline-block;
				width: 60px;
				line-height: 30px;
				height: 30px;
				border-top: none;
			}


		</style>

		<script>
			window.onscroll = function(event)
			{
				document.getElementById('channels').style.left = window.pageXOffset + 'px';
			}

			function getWidth() {
			    if (self.innerWidth) {
			       return self.innerWidth;
			    }
			    else if (document.documentElement && document.documentElement.clientHeight){
			        return document.documentElement.clientWidth;
			    }
			    else if (document.body) {
			        return document.body.clientWidth;
			    }
			    return 0;
			}
		</script>
	</head>
	<body>
<?php

function tv_get_contents($url)
{
	static $memcached;

	if (class_exists('Memcache'))
	{
		$memcached = new Memcache;
		$memcached->connect('127.0.0.1', 11211);

		$key = 'tvgids_'.md5($url);
		if (($result = $memcached->get($key)) !== false)
		{
			return $result;
		}
		else
		{
			$result = file_get_contents($url);
			$memcached->set($key, $result, MEMCACHE_COMPRESSED, strtotime(date('Y-m-d', strtotime('+ 1 day')))); // set till midnight
			return $result;
		}
	}
	else
	{
		return file_get_contents($url);
	}
}

$channels = json_decode(tv_get_contents('http://www.tvgids.nl/json/lists/channels.php'));

echo '<div class="channels" id="channels">';
echo '<div class="timetable">';
echo '<div class="nothing"></div>';

echo '</div>';
$baseline = time();
$endtime = time();
foreach ($channels as $channel)
{
	echo '<div class="channel">';
	echo '<div class="index">'.$channel->name.'</div>';
	echo '</div>';
}
echo '</div>';

echo '<div class="programs">';
foreach ($channels as $channel)
{
	/*
	object(stdClass)[1]
	  public 'id' => string '1' (length=1)
	  public 'name' => string 'Nederland 1' (length=11)
	  public 'name_short' => string 'Ned 1' (length=5)
	 */
	$programs = json_decode(tv_get_contents('http://www.tvgids.nl/json/lists/programs.php?channels='.$channel->id.'&day=0'));
	$programs = $programs->{$channel->id};

	/*
	0 =>
	        object(stdClass)[178]
	          public 'db_id' => string '14061209' (length=8)
	          public 'titel' => string 'Oog in oog' (length=10)
	          public 'genre' => string 'Amusement' (length=9)
	          public 'soort' => string '' (length=0)
	          public 'kijkwijzer' => string '' (length=0)
	          public 'artikel_id' => null
	          public 'artikel_titel' => null
	          public 'datum_start' => string '2013-03-03 23:30:00' (length=19)
	          public 'datum_end' => string '2013-03-04 00:05:00' (length=19)
	 */

	usort($programs, function($a, $b) {
		$a = strtotime($a->datum_start);
		$b = strtotime($b->datum_start);

		if ($a == $b) return 0;

		return $a < $b ? -1 : 1;
	});

	$data[$channel->id] = $programs;
	foreach ($programs as $program)
	{
		if (strtotime($program->datum_start) < $baseline)
		{
			$baseline = strtotime($program->datum_start);
		}
		if (strtotime($program->datum_end) > $endtime)
		{
			$endtime = strtotime($program->datum_end);
		}
	}
}
echo '<div class="timetable">';
for ($x = $baseline, $end = $endtime; $x <= $end; $x += 600)
{
	echo '<div class="item">'.date('H:i', $x).'</div>';
}
echo '</div>';
foreach ($data as $channel_id => $programs)
{
	echo '<div class="channel">';
	$first = true;
	foreach ($programs as $program)
	{
		$start_ts = strtotime($program->datum_start);
		$end_ts   = strtotime($program->datum_end);
		$diff     = ($end_ts - $start_ts);
		$width    = ceil($diff / 10);
		$prefix   = 0;

		if ($first)
		{
			$first  = false;
			$prefix = ceil(($start_ts - $baseline) / 10);
		}

		// titel & genre, datum_start & datum_end
		echo '<div class="'.(strtotime($program->datum_start) < time() && strtotime($program->datum_end) > time() ? 'now ' : '').'program" style="width: '.$width.'px; margin-left: '.$prefix.'px" title="'.htmlspecialchars($program->titel).'">';
		echo '<span class="time">'.date('H:i', strtotime($program->datum_start)).'</span> ';
		echo '<span class="name">'.htmlspecialchars($program->titel).'</span>';
		echo '</div>';
	}
	echo '</div>';
}

$marker_position = ceil((time() - $baseline) / 10);
echo '<div class="marker" style="left: '.$marker_position.'px"></div>';

?></div>

<script>
	var current_offset_time = <?php echo $marker_position ?>;
	window.onload = function(event)
	{
		var width = getWidth(),
			half  = Math.ceil(width / 2);

		window.scrollTo(current_offset_time - (half - 200), 0);
	}
</script>

</body></html>
