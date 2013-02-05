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
		
		$this->autoRender = false;
		$this->layout = 'empty';

		if (! empty($baseUrl)) {
			$symLinks['/' . $baseUrl] = WWW_ROOT;
		}

		foreach ($files as &$file) {
			if (empty($file)) {
				continue;
			}

			$plugin = false;
			// TODO: Get plugin handling to work
			/*
			list($first, $second) = pluginSplit($file);
			if (CakePlugin::loaded($first) === true) {
				$file = $second;
				$plugin = $first;
			}*/

			$pluginPath = (! empty($plugin) ? '../plugins/' . $plugin . '/' . WEBROOT_DIR . '/' : '');
			$file = $pluginPath . $type . '/' . $file . '.' . $type;
			$newFiles[] = $file;

            /*
			if (! empty($plugin) && ! isset($plugins[$plugin])) {
				$plugins[$plugin] = true;
				$symLinks['/' . $baseUrl . '/' . Inflector::underscore($plugin)] = APP . 'plugin/' . $plugin . '/' . WEBROOT_DIR . '/';
			}*/
		}

		$_GET['f'] = implode(',', $newFiles);
		$_GET['symlinks'] = $symLinks;

		App::import('Vendor', 'Minify.minify/index');
	}

}
?>