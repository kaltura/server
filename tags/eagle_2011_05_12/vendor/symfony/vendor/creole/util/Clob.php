<?php
/*
 *  $Id: Clob.php,v 1.6 2004/07/27 23:15:13 hlellelid Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

require_once 'creole/util/Lob.php';

/**
 * A class for handling character (ASCII) LOBs.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.6 $
 * @package   creole.util
 */
class Clob extends Lob {

    /**
     * Read LOB data from file.
     * @param string $file Filename may also be specified here (if not specified using setInputFile()).
     * @return void
     * @throws Exception - if no file specified or error on read.
     * @see setInputFile()
     */
    public function readFromFile($file = null)
    {
        if ($file !== null) {
            $this->setInputFile($file);
        }
        if (!$this->inFile) {
            throw Exception('No file specified for read.');
        }
        $data = null;
        $file = fopen($this->inFile, "rt");
        while (!feof($file)) $data .= fgets($file, 4096);
        fclose($file);
        if ($data === false) {
            throw new Exception('Unable to read from file: '.$this->inFile);
        }
        $this->setContents($data);
    }


    /**
     * Write LOB data to file.
     * @param string $file Filename may also be specified here (if not set using setOutputFile()).
     * @throws Exception - if no file specified, no contents to write, or error on write.
     * @see setOutputFile()
     */
    public function writeToFile($file = null)
    {
        if ($file !== null) {
            $this->setOutputFile($file);
        }
        if (!$this->outFile) {
            throw new Exception('No file specified for write');
        }
        if ($this->data === null) {
            throw new Exception('No data to write to file');
        }
        $file = fopen($this->inFile, "wt");
        if (fputs($file, $this->data) === false)
            throw new Exception('Unable to write to file: '.$this->outFile);
        fclose($file);
    }

    /**
     * Dump the contents of the file using fpassthru().
     *
     * @return void
     * @throws Exception if no file or contents.
     */
    function dump()
    {
        if (!$this->data) {

            // is there a file name set?
            if ($this->inFile) {
                $fp = @fopen($this->inFile, "r");
                if (!$fp) {
                    throw new Exception('Unable to open file: '.$this->inFile);
                }
                fpassthru($fp);
                @fclose($fp);
            } else {
                throw new Exception('No data to dump');
            }

        } else {
            echo $this->data;
        }

    }



}