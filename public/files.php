<?php

require_once "/srv/http/todo-list-and-file-browser/public/common.php";

$basepath = "/home/lars/";

if (isset($_POST['download'])) {
	$path = $_POST['download'];
	header('Content-Type: '.mime_content_type($basepath.$path));
	header('Content-Disposition: attachment; filename='.array_pop(explode('/', $path)));
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($basepath.$path));
	header('Accept-Ranges: bytes');
	readfile($basepath.$path);
	exit;
}

if (isset($_GET['d'])) {
	$dir = $_GET['d'];
	if (preg_match('/\.\./', $dir)) {
		exit(1);
	}
} else {
	$dir = "";
}

// make sure directory name ends in /
if( !preg_match( "/^.*\/$/", $dir ) ) {
	$dir .= "/";
}

if( isset( $_GET['f'] ) ) {
	$file = $_GET['f'];


	// output certain files as plain text
	if (preg_match('/^.*\.(tex|py|s|sh)$/', $file) || !mime_content_type($basepath.$dir.$file)) {
		header('Content-Type: text/plain');
	} else {
		header('Content-Type: '.mime_content_type($basepath.$dir.$file));
	}
	header("Content-Disposition: inline; filename=$file");
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($basepath.$dir.$file));
	header('Accept-Ranges: bytes');
	readfile($basepath.$dir.$file);
	exit;
}

$dir = preg_match('/^\//', $dir) ? $dir : "/$dir";
$display_dir = "~$dir";

writeHead($display_dir);

?>
<body>
	<div class='section'>
		<h1><?= $display_dir ?></h1>
	</div>

	<div class='section files'>
<?php
if (is_dir($basepath.$dir)) {
	$files = getFiles($basepath.$dir);
	if (count($files) == 0) {
?>
	<span class="no_content">empty</span>
<?php
	} else {
?>
		<form action='files.php' method='post'>
<?php printFiles($basepath, $dir, $files) ?>
		</form>
<?php
	}
} else {
	echo "could not open specified directory";
}
?>
	</div>

<!-- Breadcrumbs -->
	<div class='section'>
		<a href='/'>walen.me</a>
		<span class='separator'>/</span>
<?php
$path = explode('/', $dir);
array_pop($path);
array_shift($path);

if (count($path) == 0) {
	echo "		~\n";
} else {
	echo "		<a href='/files.php'>~</a>\n";
	for ($i = 0; $i < count($path) - 1; $i++) {  
		echo "		<span class='separator'>/</span>\n";
		echo "		<a href='?d=";

		for ($j = 0; $j <= $i; $j++) {
			echo $path[$j], "/";
		}
		echo "'>{$path[$i]}</a>\n";
	}
	echo "		<span class='separator'>/</span>";
	echo "		", array_pop($path);
}
?>
	</div>

</body>
</html>