<?php
/*
 *  $Id: Blob.php,v 1.5 2004/03/20 04:16:50 hlellelid Exp $
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
 * A class for handling binary LOBs.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.5 $
 * @package   creole.util
 */
class Blob extends Lob {            
    
    /**
     * Dump the contents of the file using fpassthru().
     *
     * @return void
     * @throws Exception if no file or contents.
     */
    function dump()
    {
        if (!$this->data) {            
            // hmmm .. must be a file that needs to read in
            if ($this->inFile) {
                $fp = @fopen($this->inFile, "rb");
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