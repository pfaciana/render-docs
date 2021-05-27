<?php


class Urls
{
	static protected $docSlug = 'manual';
	static protected $prefix = NULL;
	static protected $sectionId = NULL;

	static public function getPrefix ()
	{
		if (!isset(static::$prefix)) {
			static::$prefix = str_replace(trim($_SERVER['DOCUMENT_ROOT'], "\\/"), '', dirname($_SERVER['SCRIPT_FILENAME']));
		}

		return static::$prefix;
	}

	static public function getRel ($path)
	{
		if (is_array($path)) {
			$path = implode('/', array_filter($path));
		}

		if (!empty($path)) {
			$path = '/' . trim($path, "\\/");
		}

		return static::getPrefix() . $path;
	}

	static public function getHomeUrl ()
	{
		return static::getRel('');
	}

	static public function getDocsUrl ()
	{
		$args = func_get_args();

		!empty($args[0]) && ($args[0] = str_replace("\\", '-', $args[0]));

		array_unshift($args, static::getSlug());

		return static::getRel($args);
	}

	static public function getDocUrl ()
	{
		$args = func_get_args();

		!empty($args[0]) && ($args[0] = str_replace("\\", '-', $args[0]));

		array_unshift($args, static::getSlug(), static::getSectionId());

		return static::getRel($args);
	}

	static public function getSlug ()
	{
		return static::$docSlug;
	}

	static public function getSectionId ()
	{
		if (!isset(static::$sectionId)) {
			static::$sectionId = isset($GLOBALS['rd']) ? $GLOBALS['rd']->getSectionId() : NULL;
		}

		return static::$sectionId;
	}

	static public function getEditorUrl ($path, $line = NULL)
	{
		if (!defined('EDITOR_PREFIX') || !EDITOR_PREFIX) {
			return FALSE;
		}

		$url = EDITOR_PREFIX . str_replace('\\', '/', $path);

		if (!empty($line)) {
			$url .= ":{$line}";
		}

		return $url;
	}
}