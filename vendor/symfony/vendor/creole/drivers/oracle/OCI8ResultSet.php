<?php
/*
 *  $Id: OCI8ResultSet.php,v 1.13 2006/01/17 19:44:40 hlellelid Exp $
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
 
require_once 'creole/ResultSet.php';
require_once 'creole/common/ResultSetCommon.php';

/**
 * Oracle (OCI8) implementation of ResultSet class.
 *
 * @author    David Giffin <david@giffin.org>
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.13 $
 * @package   creole.drivers.oracle
 */
class OCI8ResultSet extends ResultSetCommon implements ResultSet
{
    /**
     * @see ResultSet::seek()
     */ 
    function seek($rownum)
    {
        if ( $rownum < $this->cursorPos )
		{
            // this will effectively disable previous(), first() and some calls to relative() or absolute()
            throw new SQLException( 'Oracle ResultSet is FORWARD-ONLY' );
        }
        
        // Oracle has no seek function imulate it here
        while ( $this->cursorPos < $rownum )
		{
            $this->next();
        }

        $this->cursorPos		= $rownum;

        return true;
    }
    
    /**
     * @see ResultSet::next()
     */ 
    function next()
    {   
		// no specific result position available

		// Returns an array, which corresponds to the next result row or FALSE
		// in case of error or there is no more rows in the result.
        $this->fields			= oci_fetch_array( $this->result
									, $this->fetchmode
										+ OCI_RETURN_NULLS
										+ OCI_RETURN_LOBS
								);

		if ( ! $this->fields )
		{
			// grab error via array
			$error				= oci_error( $this->result );

			if ( ! $error )
			{
				// end of recordset
				$this->afterLast();

				return false;
			}

			else
			{
				throw new SQLException( 'Error fetching result'
					, $error[ 'code' ] . ': ' . $error[ 'message' ]
				);
			}
		}

		// Oracle returns all field names in uppercase and associative indices
		// in the result array will be uppercased too.
        if ($this->fetchmode === ResultSet::FETCHMODE_ASSOC && $this->lowerAssocCase)
		{
			$this->fields = array_change_key_case($this->fields, CASE_LOWER);
        }
        
        // Advance cursor position
        $this->cursorPos++;

        return true;
    }

    /**
     * @see ResultSet::getRecordCount()
     */
    function getRecordCount()
    {
        $rows					= oci_num_rows( $this->result );

        if ( $rows === false )
		{
            throw new SQLException( 'Error fetching num rows'
				, $this->conn->nativeError( $this->result )
			);
        }

        return ( int ) $rows;
    }

    /**
     * @see ResultSet::close()
     */ 
    function close()
    {
		$this->fields			= array();
        @oci_free_statement( $this->result );
    }
}
