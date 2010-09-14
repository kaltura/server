<?php
/*
 *  $Id: SQLException.php,v 1.10 2004/03/20 04:16:49 hlellelid Exp $
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

/**
 * A class for handling database-related errors.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.10 $
 * @package   creole
 */
class SQLException extends Exception {
    
    /** Information that provides additional information for context of Exception (e.g. SQL statement or DSN). */
    protected $userInfo;
    
    /** Native RDBMS error string */
    protected $nativeError;
    
    /**
     * Constructs a SQLException.
     * @param string $msg Error message
     * @param string $native Native DB error message.
     * @param string $userinfo More info, e.g. the SQL statement or the connection string that caused the error.
     */
    public function __construct($msg, $native = null, $userinfo = null)
    {
        parent::__construct($msg);
        if ($native !== null) {
            $this->setNativeError($native);
        }
        if ($userinfo !== null) {
            $this->setUserInfo($userinfo);
        }
    }
    
    /**
     * Sets additional user / debug information for this error.
     *  
     * @param array $info
     * @return void
     */ 
    public function setUserInfo($info)
    {
        $this->userInfo = $info;
        $this->message .= " [User Info: " .$this->userInfo . "]";
    }
    
    /**
     * Returns the additional / debug information for this error. 
     * 
     * @return array hash of user info properties.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }
    
    /**
     * Sets driver native error message.
     *  
     * @param string $info
     * @return void
     */ 
    public function setNativeError($msg)
    {
        $this->nativeError = $msg;
        $this->message .= " [Native Error: " .$this->nativeError . "]";
    }
    
    /**
     * Gets driver native error message.
     * 
     * @return string
     */
    public function getNativeError()
    {
        return $this->nativeError;
    }        
    
    /**
     * @deprecated This method only exists right now for easier compatibility w/ PHPUnit!
     */
    public function toString()
    {
        return $this->getMessage();
    }
}