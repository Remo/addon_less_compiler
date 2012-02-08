<?php defined('C5_EXECUTE') or die(_("Access Denied."));
class LessCompile extends Job {

	public function getJobName() {
		return t("Compile LESS files");
	}

	public function getJobDescription() {
		return t("Compiles LESS files into CSS files as designated");
	}

	public function run() {
		Loader::model('less_compiler','less_compiler');
		Loader::model('/models/page_theme');
		$theme = PageTheme::getSiteTheme();
		$path = $theme->getThemeDirectory();
		$frpath = $path.'/less';
		$topath = $path.'/';
		$lc = new LessCompiler();
		$files = $lc->getFlatFilesArray($frpath,$frpath);
		
		return $this->compile($files, $topath, $frpath);
	}
	
	private function compile(array $files, $topath, $frpath) {
		Loader::library('less','less_compiler');
		foreach($files as $file) {
			$path = preg_split('~/~',$file);
			$filename = array_pop($path);
			$fp = '';
			foreach($path as $cpath) {
				if (!is_dir($topath."/".$fp.$cpath))
					mkdir($topath.$fp.$cpath);
				$fp.=$cpath."/";
			}
			$tofile = $topath.$fp.substr($filename,0,strlen($filename)-5).'.css';
			try {
				$lessc = new lessc($frpath.$file);
				file_put_contents($tofile,$lessc->parse());
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}
		return t('Successfully compiled!');
	}
}
