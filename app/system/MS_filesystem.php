<?php
/**
 * Copyright (c) 2017.
 * MSmvc
 * An open source application development framework for PHP
 * Copyright (c) 2017 Maurice Schurink, All Rights Reserved.
 * NOTICE:  All information contained herein is, and remains the property of  Maurice Schurink. The intellectual and
 * technical concepts contained herein are proprietary to Maurice Schurink and may be covered by U.S. and Foreign
 * Patents, patents in process, and are protected by trade secret or copyright law. Dissemination of this information
 * or reproduction of this material is strictly forbidden unless prior written permission is obtained from  Maurice
 * Schurink.  Access to the source code contained herein is hereby forbidden to anyone except current  Maurice Schurink
 * employees, managers or contractors who have executed Confidentiality and Non-disclosure agreements explicitly
 * covering such access.
 * The copyright notice above does not evidence any actual or intended publication or disclosure  of  this source code,
 * which includes information that is confidential and/or proprietary, and is a trade secret, of  Maurice Schurink. ANY
 * REPRODUCTION, MODIFICATION, DISTRIBUTION, PUBLIC  PERFORMANCE, OR PUBLIC DISPLAY OF OR THROUGH USE  OF THIS  SOURCE
 * CODE  WITHOUT  THE EXPRESS WRITTEN CONSENT OF Maurice Schurink IS STRICTLY PROHIBITED, AND IN VIOLATION OF
 * APPLICABLE LAWS AND INTERNATIONAL TREATIES.  THE RECEIPT OR POSSESSION OF  THIS SOURCE CODE AND/OR RELATED
 * INFORMATION DOES NOT CONVEY OR IMPLY ANY RIGHTS TO REPRODUCE, DISCLOSE OR DISTRIBUTE ITS CONTENTS, OR TO
 * MANUFACTURE, USE, OR SELL ANYTHING THAT IT  MAY DESCRIBE, IN WHOLE OR IN PART.
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * Except as contained in this notice, the name of MSmvc. shall not be used in advertising or otherwise to promote the
 * sale, use or other dealings in this Software without prior written authorization from Maurice Schurink.
 */

namespace App\system;

/**
 * Class MS_pipeline
 * @package system\pipelines
 */
class MS_filesystem implements \SeekableIterator, \RecursiveIterator {

    /**
     * this prepend the view path
     */
    const USE_VIEW_PATH = 1;

    /**
     * this prepend the view path
     */
    const USE_LAYOUT_PATH = 2;

    /**
     * @var MS_optionals
     */
    private $options;

    /**
     * @var string
     */
    private $path;
    /**
     * array filled with segments based on the path
     * @var array
     */
    private $segments;

    /**
     * array with all the fileobjects
     * @var array
     */
    private $collection;

    /**
     * if subdirectories will be included true / false
     * @var bool
     */
    private $includeSubDirectories;

	/**
	 * array filled with data keys are variables and values data
	 * @var array
	 */
    private $localData;

	/**
	 * @var array
	 */
    private $fileContents;

    /**
     * MS_filesystem constructor.
     *
     * @param null|string $path
     */
    function __construct($path) {
        $this->options = new MS_optionals(func_get_args(), $path, TRUE);
        $this->setPath($path);

        if (is_file($this->getPath())) {
            $this->collection[] = new \SplFileInfo($this->getPath());
        } else {
            $glob = glob($this->getPath()."*");
            foreach ($glob as $file) {
                $this->collection[] = new \SplFileInfo($file);
            }
        }
    }

    /**
     * this method will set the segments for the directory
     *
     * @param string $path
     */
    public function setSegments(string $path) {
        $segments = array_filter(explode(DIRECTORY_SEPARATOR, $path));
        $segments["last"] = end($segments);
        $segments["first"] = reset($segments);
        $segments["full"] = $path;
        $this->segments = $segments;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path) {
        if ($this->options->checkExists(self::USE_VIEW_PATH)) {
            $path = "app/resources/views/$path";
        } elseif ($this->options->checkExists(self::USE_LAYOUT_PATH)) {
            $path = "app/resources/views/layouts/$path";
        }
        if(!is_file($path)){
            $path = rtrim($path,ord(DIRECTORY_SEPARATOR)).DIRECTORY_SEPARATOR;
        }
        $this->path = $this->cleanPath($path);
        $this->setSegments($this->path);
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }


    /**
     * @param $path
     *
     * @return mixed
     */
    public function cleanPath($path) {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

	/**
	 * @return array
	 */
	public function getFileContents(): array {
		return $this->fileContents;
	}

	/**
	 * @param mixed $fileContents
	 */
	public function addFileContents($fileContents) {
		$this->fileContents[] = $fileContents;
	}


	/**
     * todo:fix it
     * @param            $file
	 *
     */
    private function executeAndReturnFileContent(\SplFileInfo $file) {
        if (is_array($this->getLocalData())) {
            extract($this->getLocalData(), EXTR_SKIP);
        }
        ob_start();
        include $file->getPathname();
        $this->addFileContents(ob_get_clean());
    }

    /**
     * Determine if a file exists.
     * @return bool
     */
    public function exists() {
        return file_exists($this->path);
    }

    /**
     * @return string
     */
    public function getFileContent() {
        return file_get_contents($this->current());
    }

    /**
     * the callback foreach file that is found
     *
     * @param                $callbackMethod
     * @param null           $callbackObject
     */
    private function fileAction($callbackMethod, $callbackObject = NULL) {
        if ($callbackObject !== NULL) {
            while ($this->valid()) {
                $callbackObject->$callbackMethod($this->current());
                $this->next();
            }
        } else {
            while ($this->valid()) {
                $callbackMethod($this->current());
                $this->next();
            }
        }

    }


    /**
     * @param \SplFileInfo $target
     */
    private function includeTarget(\SplFileInfo $target) {
        include $target->getPathname();
    }

    public function include () {
        $this->fileAction("includeTarget", $this);
    }

	public function executeAndReturn () {
		$this->fileAction("executeAndReturnFileContent", $this);
		return $this->getFileContents();
	}

	/**
	 * @return mixed
	 */
	public function getLocalData() {
		return $this->localData;
	}

	/**
	 * @param mixed $localData
	 */
	public function setLocalData($localData) {
		$this->localData = $localData;
	}

    /**
     * @return bool
     */
    public function isIncludeSubDirectories() {
        return $this->includeSubDirectories;
    }

    /**
     * @param bool $includeSubDirectories
     */
    public function setIncludeSubDirectories(bool $includeSubDirectories = TRUE) {
        $this->includeSubDirectories = $includeSubDirectories;
    }

    /**
     * todo: fix it without a iterator
     *
     * @param $regex
     */
    public function regexFilter($regex) {
        /**
         * @var $item \SplFileInfo
         */
        foreach ($this->collection as $key => $item)
        {
            if (!preg_match($regex,$item->getFilename())){
                unset($this->collection[$key]);
            }
        }
    }

    /**
     * @param $extensions
     *
     * @internal param bool $only : if you want to only return the files that have this extension or all the files except these
     */
    public function filterExtensions($extensions) {
        if (is_array($extensions)) {
            $extensions = implode(array_map("ltrim", $extensions, "\x2E"), "|");
            $this->regexFilter("/^.*\.($extensions)$/i");
        } else {
            $extensions = ltrim($extensions, "\x2E");
            $this->regexFilter("/^.*\.($extensions)$/i");
        }
    }

    /**
     * Return the current element
     * @link  http://php.net/manual/en/iterator.current.php
     * @return \SplFileObject Can return any type.
     * @since 5.0.0
     */
    public function current() {
       return current($this->collection);
    }

    /**
     * Move forward to next element
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        next($this->collection);
    }

    /**
     * Return the key of the current element
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return key($this->collection);
    }

    /**
     * Checks if current position is valid
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        $key = $this->key();
        return isset($this->collection[$key]) ? TRUE : FALSE;
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        reset($this->collection);
    }

    /**
     * Seeks to a position
     * @link  http://php.net/manual/en/seekableiterator.seek.php
     *
     * @param int $position <p>
     *                      The position to seek to.
     *                      </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function seek($position) {
        if (!isset($this->collection[$position])) {
            throw new \OutOfBoundsException("invalid seek position ($position)");
        } else {
            $this->position = $position;
        }
    }

    /**
     * Returns if an iterator can be created for the current entry.
     * @link  http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren() {
        return $this->current()->isDir() ? TRUE : FALSE;
    }

    /**
     * Returns an iterator for the current entry.
     * @link  http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren() {
        return new MS_filesystem($this->current()->getPathname());
    }
}