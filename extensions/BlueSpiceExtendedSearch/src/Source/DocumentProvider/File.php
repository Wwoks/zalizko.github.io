<?php

namespace BS\ExtendedSearch\Source\DocumentProvider;

class File extends DecoratorBase {

	/**
	 *
	 * @param string $sUri
	 * @param \SplFileInfo $oFile
	 * @return array
	 */
	public function getDataConfig( $sUri, $oFile ) {
		$contents = file_get_contents(
			$oFile->getPathname()
		);
		$contents = base64_encode( $contents );

		$aDC = $this->oDecoratedDP->getDataConfig( $sUri, $oFile );
		$magic = \MediaWiki\MediaWikiServices::getInstance()->getMimeAnalyzer();
		$aDC = array_merge( $aDC, [
			'basename' => $oFile->getBasename(),
			'basename_exact' => $oFile->getBasename(),
			'extension' => $oFile->getExtension(),
			'mime_type' => $magic->guessMimeType( $oFile->getPathname() ),
			'mtime' => $oFile->getMTime(),
			'ctime' => $oFile->getCTime(),
			'size' => $oFile->getSize(),
			'source_file_path' => $oFile->getPathname(),
			'the_file' => $contents
		] );

		$contents = null;
		unset( $contents );

		return $aDC;
	}
}
