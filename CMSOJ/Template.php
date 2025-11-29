<?php

namespace CMSOJ;

class Template
{

	static $blocks = array();
	static $cache_path = 'cache/';
	static $cache_enabled = FALSE;

	static function view($file, $data = array())
	{
		self::$blocks = [];  // Reset blocks every request 
		$cached_file = self::cache($file);
		extract($data, EXTR_SKIP);
		require $cached_file;
	}
	static function resolvePath($file)
	{
		// If path is already absolute, return it
		if (str_starts_with($file, '/') || preg_match('/^[A-Z]:/i', $file)) {
			return $file;
		}

		// Build absolute path from project root
		return dirname(__DIR__) . '/' . $file;
	}


	static function cache($file)
	{
		if (!file_exists(self::$cache_path)) {
			mkdir(self::$cache_path, 0744);
		}
		$cached_file = self::$cache_path . str_replace(array('/', '.html'), array('_', ''), $file . '.php');
		if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
			$code = self::includeFiles(self::resolvePath($file));

			$code = self::compileCode($code);
			file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
		}
		return $cached_file;
	}

	static function clearCache()
	{
		foreach (glob(self::$cache_path . '*') as $file) {
			unlink($file);
		}
	}

	static function compileCode($code)
	{
		$code = self::compilePartials($code);
		$code = self::compileComponents($code);
		$code = self::compileBlock($code);
		$code = self::compileYield($code);
		$code = self::compileEscapedEchos($code);
		$code = self::compileEchos($code);
		$code = self::stripLeftoverBlockTags($code);
		$code = self::compilePHP($code);
		return $code;
	}

	static function includeFiles($file)
	{
		$code = file_get_contents(self::resolvePath($file));

		preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			$code = str_replace($value[0], self::includeFiles($value[2]), $code);
		}
		$code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
		return $code;
	}

	static function compilePHP($code)
	{
		return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
	}

	static function compileEchos($code)
	{
		return preg_replace(
			'~\{{\s*(.+?)\s*\}}~is',
			'<?php echo \CMSOJ\Template::asset($1) ?>',
			$code
		);
	}

	static function compileEscapedEchos($code)
	{
		return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
	}

	static function compileBlock($code)
	{
		// Match: {% block name %} ... {% endblock %}
		if (!preg_match_all('/\{%[\s]*block\s+([a-zA-Z0-9_]+)[\s]*%}(.*?){%[\s]*endblock[\s]*%}/is', $code, $matches, PREG_SET_ORDER)) {
			return $code;
		}

		foreach ($matches as $value) {
			$blockName = trim($value[1]);
			$blockContent = $value[2];

			if (!array_key_exists($blockName, self::$blocks)) {
				self::$blocks[$blockName] = '';
			}

			if (strpos($blockContent, '@parent') === false) {
				// no @parent, override completely
				self::$blocks[$blockName] = $blockContent;
			} else {
				// merge with parent
				self::$blocks[$blockName] = str_replace('@parent', self::$blocks[$blockName], $blockContent);
			}

			// remove the whole block from template
			$code = str_replace($value[0], '', $code);
		}

		return $code;
	}


	static function compileYield($code)
	{
		foreach (self::$blocks as $block => $value) {
			$code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
		}
		$code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
		return $code;
	}

	static function stripLeftoverBlockTags($code)
	{
		// Remove any stray block or endblock tags that weren't matched
		$code = preg_replace('/\{%[\s]*block\s+[a-zA-Z0-9_]+[\s]*%}/i', '', $code);
		$code = preg_replace('/\{%[\s]*endblock[\s]*%}/i', '', $code);
		return $code;
	}

	static function compilePartials($code)
	{
		// {% partial 'file' %}
		return preg_replace_callback('/\{%[\s]*partial[\s]+\'(.+?)\'[\s]*%}/i', function ($matches) {
			$file = 'CMSOJ/Views/partials/' . $matches[1] . '.html';
			return self::includeFiles(self::resolvePath($file));
		}, $code);
	}

	static function compileComponents($code)
	{
		return preg_replace_callback('/\{%[\s]*component[\s]+\'(.+?)\'\s*,\s*(\{.+?\})\s*%}/is', function ($matches) {

			$component = 'CMSOJ/Views/components/' . $matches[1] . '.html';
			$props = json_decode(str_replace("'", '"', $matches[2]), true);

			ob_start();
			extract($props, EXTR_SKIP);
			include self::resolvePath($component);
			return ob_get_clean();
		}, $code);
	}


	static function asset($path)
	{
		$full = dirname(__DIR__) . '/public' . $path;
		if (file_exists($full)) {
			return $path . '?v=' . filemtime($full);
		}
		return $path;
	}
}
