<?php
// https://www.php.net/manual/en/language.exceptions.php
function exceptions_error_handler($severity, $message, $filename, $lineno) {
   throw new ErrorException($message, 0, $severity, $filename, $lineno);
}
set_error_handler('exceptions_error_handler');

error_reporting(E_ERROR | E_PARSE);

$response = array();

$f = (function() use (&$response) {

	$lockfilePath = "/home/[REDACTED]/public_html/counting/lockfile"; 
	$publicPath = "/home/[REDACTED]/counting_public/";
	$countPath = "/home/[REDACTED]/public_html/counting/counts/";

	if (!isset($_POST) || !isset($_POST["user"]) || !isset($_POST["confKey"])) {
		$response[] = 4;
		$response[] = "InvalidCall";
		return;
	}

	$userName = $_POST["user"];
	$confKey = $_POST["confKey"];
	try {
		// open lockfile
		while (true) {
			$lockFile = fopen($lockfilePath, "w");
			if ($lockFile !== false)
				break;
			sleep(1);
		}	

		// lock acquired, do work now
		// verify confirmation key
		if (!ctype_alnum($confKey)) {
			$response[] = 1;
			$response[] = "ConfKeyUnsafe";
			return;
		}
		$ownId = fileowner($publicPath . $confKey);
		$ownInfo = posix_getpwuid($ownId);
		$ownName = $ownInfo["name"];

		if ($ownName !== $userName) {
			$response[] = 2;
			$response[] = "ConfKeyInvalid";
			return;
		}
		// confkey verified
		// get current count
		$countFiles = glob($countPath . "*");
		// sort
		usort($countFiles, function($a, $b) { $partsA = explode('.', basename($a)); $partsB = explode('.', basename($b)); return $partsA[0] - $partsB[0]; });
		// get last user who counted
		$lastCountFile = $countFiles[count($countFiles) - 1];
		$lastCountFile = basename($lastCountFile);
		$lastCountFileParts = explode('.', $lastCountFile);
		$newCountNumber = $lastCountFileParts[0] + 1;
		$lastCountUser = $lastCountFileParts[1];
		// check if already counted
		if ($lastCountUser == $userName) {
			$response[] = 3;
			$response[] = "AlreadyCounted";
			return;
		}
		// touch the count file
		touch($countPath . $newCountNumber . "." . $userName);
		
		// return info
		$response[] = 0;
		$response[] = $newCountNumber;
		$response[] = $lastCountUser;
			
	
	} catch (Exception $ex) {
		$response[] = 999;
		$response[] = "UnknownInternal";
	}
	fclose($lockFile);

});

$f();

echo(
	implode(",", $response)
);

?>
