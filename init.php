<?php
require_once 'src/core.php';
$config = \Chibi\Registry::getConfig();
$fontsPath = TextHelper::absolutePath($config->main->mediaPath . DS . 'fonts');
$libPath = TextHelper::absolutePath($config->main->mediaPath . DS . 'lib');



function download($source, $destination = null)
{
	echo 'Downloading: ' . $source . '...' . PHP_EOL;
	flush();

	if ($destination !== null and file_exists($destination))
		return file_get_contents($destination);

	$content = file_get_contents($source);
	if ($destination !== null)
	{
		$dir = dirname($destination);
		if (!file_exists($dir))
			mkdir($dir, 0755, true);

		file_put_contents($destination, $content);
	}
	return $content;
}



//jQuery
download('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', $libPath . DS . 'jquery' . DS . 'jquery.min.js');

//jQuery UI
download('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js', $libPath . DS . 'jquery-ui' . DS . 'jquery-ui.min.js');
$manifest = download('http://ajax.googleapis.com/ajax/libs/jqueryui/1/MANIFEST');
$lines = explode("\n", str_replace("\r", '', $manifest));
foreach ($lines as $line)
{
	if (preg_match('/themes\/flick\/(.*?) /', $line, $matches))
	{
		$srcUrl = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/' . $matches[0];
		$dstUrl = $libPath . DS . 'jquery-ui' . DS . $matches[1];
		download($srcUrl, $dstUrl);
	}
}

//jQuery Tag-it!
download('http://raw.github.com/aehlke/tag-it/master/css/jquery.tagit.css', $libPath . DS . 'tagit' . DS . 'jquery.tagit.css');
download('http://raw.github.com/aehlke/tag-it/master/js/tag-it.min.js', $libPath . DS . 'tagit' . DS . 'jquery.tagit.js');

//Mousetrap
download('http://raw.github.com/ccampbell/mousetrap/master/mousetrap.min.js', $libPath . DS . 'mousetrap' . DS . 'mousetrap.min.js');

//fonts
download('http://googlefontdirectory.googlecode.com/hg/apache/droidsans/DroidSans.ttf', $fontsPath . DS . 'DroidSans.ttf');
download('http://googlefontdirectory.googlecode.com/hg/apache/droidsans/DroidSans-Bold.ttf', $fontsPath . DS . 'DroidSans-Bold.ttf');



require_once 'upgrade.php';
