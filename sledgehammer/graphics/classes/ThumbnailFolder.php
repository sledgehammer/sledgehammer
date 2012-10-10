<?php
/**
 * ThumbnailFolder
 */
namespace Sledgehammer;
/**
 * Een Virtual folder die aan de hand van de mapnaam de afmetingen van de thumbnail bepaald.
 * De Url /160x120/MyImage.jpg zal van de afbeelding MyImage.jpg een thumbnail maken van 160px breed en 120px hoog
 *
 * @package Graphics
 */
class ThumbnailFolder extends VirtualFolder {

	protected $imagesFolder;
	public $targetFolder;

	function __construct($imagesFolder, $targetFolder = null) {
		parent::__construct();
		$this->imagesFolder = $imagesFolder;
		if ($targetFolder == null) {
			$targetFolder = TMP_DIR.'ThumbnailFolder/'.basename($imagesFolder).'_'.substr(md5($imagesFolder), 8, 16).'/';
		}
		$this->targetFolder = $targetFolder;
	}

	function dynamicFilename($filename) {
		if ($this->isImage($filename)) {
			return new FileDocument($this->imagesFolder.$filename);
		}
		return $this->onFileNotFound();
	}

	function dynamicFoldername($folder, $filename = null) {
		if (!preg_match('/^[0-9]+x[0-9]+$/', $folder)) { // Zijn er geen afmetingen meegegeven?
			return $this->onFolderNotFound();
		}
		if (!$filename) { // Komt de afbeelding uit een subfolder($recursive)?
			$path = URL::extract_path();
			$subfolders = array_slice($path['folders'], $this->depth + 1);
			$filename = implode('/', $subfolders).'/'.$path['filename'];
		}
		$source = $this->imagesFolder.$filename;
		if (!file_exists($source)) {
			return new HttpError(404, array('warning' => 'Image "'.$filename.'" not found in "'.$this->imagesFolder.'"'));
		}
		$target = $this->targetFolder.$folder.'/'.$filename;
		if (!file_exists($target) || filemtime($source) > filemtime($target)) {
			$dimensions = explode('x', $folder);
			mkdirs(dirname($target));
			$image = new Image($source);
			$image->saveThumbnail($target, $dimensions[0], $dimensions[1]);
		}
		return new FileDocument($target);
	}

	protected function isImage($filename) {
		return substr(mimetype($filename, true), 0, 6) == 'image/';
	}

}

?>
