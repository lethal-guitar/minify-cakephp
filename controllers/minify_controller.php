<?php
/**
 * Minify Controller
 *
 * Classe responsável pela compressão de arquivos javascript e css.
 *
 * @package		app.Controller
 */
class MinifyController extends MinifyAppController {

	public $name = 'Minify';
	public $uses = array();

	/**
	 * Take care of any minifying requests.
	 * The import is not defined outside the class to avoid errors if the class is read from the console.
	 *
	 * @return void
	 */
	public function index($type) {
		$files = array_unique(explode(',', $_GET['f']));
		$plugins = array();
		$symLinks = array();
		$newFiles = array();

		// Prevent "view not found" errors
		// in those cases where Minify library
		// does not answer the request
		$this->autoRender = false;
		$this->layout = 'empty';

		$allPlugins = App::objects('plugin');

		if (! empty($this->base)) {
			$symLinks['/' . $this->base] = WWW_ROOT;
		}

		foreach ($files as &$file) {
			if (empty($file)) {
				continue;
			}

			$plugin = false;
			list($first, $second) = pluginSplit($file);
			if (in_array($first, $allPlugins)) {
				$file = $second;
				$plugin = $first;
			}

			$pluginPath = (! empty($plugin) ? '../plugins/' . $plugin . '/' . WEBROOT_DIR . '/' : '');
			$file = $pluginPath . $type . '/' . $file . '.' . $type;
			$newFiles[] = $file;

			if (! empty($plugin) && ! isset($plugins[$plugin])) {
				$plugins[$plugin] = true;
				$symLinks['/' . $this->base . '/' . Inflector::underscore($plugin)] = APP . 'plugin/' . $plugin . '/' . WEBROOT_DIR . '/';
			}
		}

		$_GET['f'] = implode(',', $newFiles);
		$_GET['symlinks'] = $symLinks;

		App::import('Vendor', 'Minify.minify/index');
	}

}
?>