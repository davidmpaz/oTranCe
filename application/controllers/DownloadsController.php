<?php
/**
 * Controller to manage downloads.
 */
class DownloadsController extends Zend_Controller_Action
{
    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->archives = $this->_getAvailableArchives();
    }

    /**
     * Download a language pack.
     *
     * @return void
     */
    public function downloadAction()
    {
        $filename = $this->_request->getParam('file');
        if (preg_match('/^[A-Z0-9_-]+\z/i', $filename)) {
            // someone manipulated the filename. Die silently.
            die();
        }
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->_response->setHeader(
            'Content-Type',
            $this->_getArchiveContentType($filename)
        );
        $this->_response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        readfile(DOWNLOAD_PATH . DS . $filename);
    }

    /**
     * Retrives the content type of an archive.
     *
     * @param string $filename
     *
     * @return string
     */
    private function _getArchiveContentType($filename)
    {
        if (substr($filename, -4) == '.zip') {
            return 'application/zip';
        }

        if (substr($filename, -7) == '.tar.gz') {
            return 'application/x-compressed-tar';
        }

        if (substr($filename, -8) == '.tar.bz2') {
            return 'application/x-bzip-compressed-tar';
        }

        return 'application/octet-stream';
    }

    /**
     * Creates a list with available archives.
     *
     * @return array
     */
    private function _getAvailableArchives()
    {
        $files = glob(DOWNLOAD_PATH . DS . '{*.zip,*.tar.gz,*.tar.bz2}', GLOB_BRACE | GLOB_NOSORT);
        if (is_array($files)) {
            rsort($files);
        }
        $archives = array();
        if (empty($files)) {
            return $archives;
        }
        foreach ($files as $file) {
            $filename = str_replace(DOWNLOAD_PATH . DS, '', $file);
            $fileStats = stat($file);
            $archives[$filename] = array(
                'creationTime' => $fileStats['ctime'],
                'fileSize' => filesize($file),
            );
        }

        return $archives;
    }

}