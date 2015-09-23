<?php
/*
The MIT License (MIT)

Copyright (c) 2014 James

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */

namespace AlibabaSDK\Base;

/**
 * Adds a compatibility class for older versions of PHP.
 *
 * This abuses the fact that objects are coerced to being strings
 * in CURL when passed as a post field.  This also will only work with
 * safe upload off - but seeing as it's for PHP <5.5 that shouldn't be
 * an issue.
 * 
 * @author imnotjames
 * @link https://github.com/imnotjames/curlfile-compat
 */
class CURLFileCompat{
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $mime;
	/**
	 * @var string
	 */
	public $postname;
	/**
	 * Create CurlFile object
	 *
	 * @param string $name File name
	 * @param string $mimetype Mime type, optional
	 * @param string $postfilename Post filename, defaults to actual filename
	 */
	public function __construct($name, $mime = '', $postname = '') {
		$this->name = $name;
		$this->mime = $mime;
		$this->postname = $postname;
	}
	/**
	 * Get file name from which the data will be read
	 *
	 * @return string
	 */
	public function getFilename() {
		return $this->name;
	}
	/**
	 * Get mime type
	 *
	 * @param string $mime
	 */
	public function setMimeType($mime) {
		$this->mime = $mime;
	}
	/**
	 * Set mime type
	 *
	 * @return string
	 */
	public function getMimeType() {
		return $this->mime;
	}
	/**
	 * Set file name which will be sent in the post
	 *
	 * @param string $postname
	 */
	public function setPostFilename($postname) {
		$this->postname = $postname;
	}
	/**
	 * Get file name which will be sent in the post
	 *
	 * @return string
	 */
	public function getPostFilename() {
		return $this->postname;
	}
	
	public function __toString() {
		$output = '@' . $this->name;
		if (!empty($this->postname)) {
			$output .= ';filename=' . $this->postname;
		}
		if (!empty($this->mime)) {
			$output .= ';type=' . $this->mime;
		}
		return $output;
	}
	
}
