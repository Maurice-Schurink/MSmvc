<?php
namespace blueprints;

use Exception;

interface MS_compressInterface
{
	/**
	 * @param $name   : the key to use for the magic method
	 * @param $value : the value to use for the magic method
	 *
	 * @return mixed: the interface
	 */
	public function __set($name, $value);

	/**
	 * @param $name : the key to use for the magic method
	 *
	 * @return mixed: the interface
	 */
	public function __get($name);

	/**
	 * @return mixed: we create a new archive
	 */
	public function createNewArchive();

	/**
	 * @param $fileName : the file name to add
	 *
	 * @return mixed: the directory added to the archive
	 * @throws Exception:  if we cannot create the archive file we throw an exception
	 */
	public function createNewDirectory($fileName);

	/**
	 * @param $archiveFile : the archive to open
	 *
	 * @return mixed: the archive opened
	 * @throws Exception: if we cannot write to the archive we throw an exception
	 */
	public function openArchive($archiveFile);

	/**
	 * @param      $file : the file to add to the archive
	 * @param null $name : the new name for the file to use
	 *
	 * @return mixed: the file added to the archive
	 * @throws Exception:  if we cannot open archive file we throw an exception
	 */
	public function addFile($file, $name = NULL);

	/**
	 * @param $fileName : the name for the file to use
	 * @param $content  : the content for the file
	 *
	 * @return mixed: the file added to the archive
	 * @throws Exception: if we cannot write to the archive we throw an exception
	 */
	public function addString($fileName, $content);

	/**
	 * @param      $directory : the directory to use
	 * @param null $name      : the optional name to use
	 *
	 * @return mixed: the directory added to the archive
	 * @throws Exception: if we cannot write to the archive we throw an exception
	 */
	public function addDirectory($directory, $name = NULL);

	/**
	 * @param $key : the key to use for deleting for to a file or directory from the archive
	 *
	 * @return mixed: the file deleted from the archive
	 * @throws Exception: if we cannot write to the archive we throw an exception
	 */
	public function deleteFrom($key);

	/**
	 * @return mixed: the content from the archive
	 * @throws Exception: if we cannot write to the archive we throw an exception
	 */
	public function getContent();

	/**
	 * @param null $location : the location to use relative from /uploads
	 *
	 * @return mixed: the content from the archive or an exception if it failed
	 * @throws Exception:  if we cannot open the archive file or we don't have write access to the upload directory we
	 *                     throw an exception
	 */
	public function extract($location = NULL);

	/**
	 * @param $file : the file to check
	 *
	 * @return bool: true or false if the file depending if the file is write able
	 */
	public function checkWriteAble($file);
}