<?php

/**
 *  $Id: OCI8Connection.php,v 1.18 2005/10/17 19:03:51 dlawson_mi Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES ( INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION ) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * ( INCLUDING NEGLIGENCE OR OTHERWISE ) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */
 
require_once 'creole/Connection.php';
require_once 'creole/common/ConnectionCommon.php';
include_once 'creole/drivers/oracle/OCI8ResultSet.php';

/**
 * Oracle implementation of Connection.
 * 
 * @author    David Giffin <david@giffin.org>
 * @author    Hans Lellelid <hans@xmpl.org>
 * @author    Stig Bakken <ssb@fast.no> 
 * @author    Lukas Smith
 * @version   $Revision: 1.18 $
 * @package   creole.drivers.oracle
 */ 
class OCI8Connection extends ConnectionCommon implements Connection
{        
    protected $lastStmt			= null;    

    /**
     * Auto commit mode for oci_execute
     * @var int
     */
    protected $execMode			= OCI_COMMIT_ON_SUCCESS;

    /**
     * Connect to a database and log in as the specified user.
     *
     * @param array $dsn The data source hash.
     * @param int $flags Any connection flags.
     * @access public
     * @throws SQLException
     * @return void
     */
    function connect( $dsninfo, $flags = 0 )
    {
        if ( !extension_loaded( 'oci8' ) )
		{
            throw new SQLException( 'oci8 extension not loaded' );
        }

        $this->dsn				= $dsninfo;
        $this->flags			= $flags;
        
        $persistent				=
			( $flags & Creole::PERSISTENT === Creole::PERSISTENT );
        
        $user					= $dsninfo[ 'username' ];
        $pw						= $dsninfo[ 'password' ];
        $hostspec				= $dsninfo[ 'hostspec' ];
        $port       = $dsninfo[ 'port' ];
        $db					= $dsninfo[ 'database' ];

        $connect_function		= ( $persistent )
									? 'oci_pconnect'
									: 'oci_connect';
		$encoding = !empty($dsninfo['encoding']) ? $dsninfo['encoding'] : null;

		@ini_set( 'track_errors', true );
		
		if ( $hostspec && $port )
		{
		  $hostspec .= ':' . $port;
		}

        if ( $db && $hostspec && $user && $pw )
	{
			$conn				= @$connect_function( $user, $pw, "//$hostspec/$db", $encoding);
	}
        elseif ( $hostspec && $user && $pw )
		{
			$conn				= @$connect_function( $user, $pw, $hostspec, $encoding );
        }
		
		elseif ( $user || $pw )
		{
			$conn				= @$connect_function( $user, $pw, null, $encoding );
        }
		
		else
		{
			$conn				= false;
        }

        @ini_restore( 'track_errors' );
        
        if ( $conn == false )
		{
            $error				= oci_error();
            $error				= ( is_array( $error ) )
									? $error[ 'message' ]
									: null;

            throw new SQLException( 'connect failed', $error );
        }

        $this->dblink			= $conn;

        //connected ok, need to set a few environment settings
        //please note, if this is changed, the function setTimestamp and setDate in OCI8PreparedStatement.php
        //must be changed to match
        $sql = "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'";
        $this->executeQuery($sql);
    }


    /**
     * @see Connection::disconnect()
     */
    function close()
    {
        $ret = @oci_close( $this->dblink );
        $this->dblink = null;
        return $ret;
    }        
    
    /**
     * @see Connection::executeQuery()
     */
    function executeQuery( $sql, $fetchmode = null )
    {
        $this->lastQuery		= $sql;

        // $result = @oci_parse( $this->dblink, $sql );
        $result					= oci_parse( $this->dblink, $sql );

        if ( ! $result )
		{
            throw new SQLException( 'Unable to prepare query'
				, $this->nativeError()
				, $sql
			);
        }

        $success				= oci_execute( $result, $this->execMode );

        if ( ! $success )
		{
            throw new SQLException( 'Unable to execute query'
				, $this->nativeError( $result )
				, $sql
			);
        }
        
        return new OCI8ResultSet( $this, $result, $fetchmode );
    }

    
    /**
     * @see Connection::simpleUpdate()
     */
    
    function executeUpdate( $sql )
    {    
        $this->lastQuery		= $sql;

        $statement				= oci_parse( $this->dblink, $sql );
		
        if ( ! $statement )
		{
            throw new SQLException( 'Unable to prepare update'
				, $this->nativeError()
				, $sql
			);
        }
                
        $success				= oci_execute( $statement, $this->execMode );

        if ( ! $success )
		{
            throw new SQLException( 'Unable to execute update'
				, $this->nativeError( $statement )
				, $sql
			);
        }

        $this->lastStmt			= $statement;

        return oci_num_rows( $statement );
    }

    /**
     * Start a database transaction.
     * @throws SQLException
     * @return void
     */
    protected function beginTrans()
    {
        $this->execMode			= OCI_DEFAULT;
    }
        
    /**
     * Commit the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function commitTrans()
    {
        $result					= oci_commit( $this->dblink );

        if ( ! $result )
		{
            throw new SQLException( 'Unable to commit transaction'
				, $this->nativeError()
			);
        }

        $this->execMode			= OCI_COMMIT_ON_SUCCESS;
    }

    
    /**
     * Roll back ( undo ) the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function rollbackTrans()
    {
        $result					= oci_rollback( $this->dblink );

        if ( ! $result )
		{
            throw new SQLException( 'Unable to rollback transaction'
				, $this->nativeError()
			);
        }

        $this->execMode			= OCI_COMMIT_ON_SUCCESS;
    }

    
    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     *
     * @return int Number of rows affected by the last query.
     * @todo -cOCI8Connection Figure out whether getUpdateCount() should throw exception on error or just return 0.
     */
    function getUpdateCount()
    {
        if ( ! $this->lastStmt )
		{
            return 0;
        }

        $result					= oci_num_rows( $this->lastStmt );

        if ( $result === false )
		{
            throw new SQLException( 'Update count failed'
				, $this->nativeError( $this->lastStmt )
			);
        }

        return $result;
    }


   /**
    * Build Oracle-style query with limit or offset.
    * If the original SQL is in variable: query then the requlting
    * SQL looks like this:
    * <pre>
    * SELECT B.* FROM ( 
    *          SELECT A.*, rownum as TORQUE$ROWNUM FROM ( 
    *                  query
    *           ) A
    *      ) B WHERE B.TORQUE$ROWNUM > offset AND B.TORQUE$ROWNUM
    *     <= offset + limit
    * </pre>
    *
    * @param string &$sql the query
    * @param int $offset
    * @param int $limit
    * @return void ( $sql parameter is currently manipulated directly )
    */
   public function applyLimit( &$sql, $offset, $limit )
   {
        $sql					=
			'SELECT B.* FROM (  '
			.  'SELECT A.*, rownum AS CREOLE$ROWNUM FROM (  '
			. $sql
			. '  ) A '
			.  ' ) B WHERE ';

        if ( $offset > 0 )
		{
            $sql				.= ' B.CREOLE$ROWNUM > ' . $offset;            

            if ( $limit > 0 )
			{
                $sql			.= ' AND B.CREOLE$ROWNUM <= '
									. ( $offset + $limit );
            }
        }

		else
		{
			$sql				.= ' B.CREOLE$ROWNUM <= ' . $limit;
		}
   } 

    /**
     * Get the native Oracle Error Message as a string.
     *
     * @param string $msg The Internal Error Message
     * @param mixed $errno The Oracle Error resource
     */
    public function nativeError( $result = null )
	{
		if ( $result !== null )
		{
			$error				= oci_error( $result );
		}
	
		else
		{
			$error				= oci_error( $this->dblink );
		}         

		return $error[ 'code' ] . ': ' . $error[ 'message' ];
	}
    
    
    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        require_once 'creole/drivers/oracle/metadata/OCI8DatabaseInfo.php';

        return new OCI8DatabaseInfo( $this );
    }
    
    /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        require_once 'creole/drivers/oracle/OCI8IdGenerator.php';

        return new OCI8IdGenerator( $this );
    }
    
    /**
     * Oracle supports native prepared statements, but the oci_parse call
     * is actually called by the OCI8PreparedStatement class because
     * some additional SQL processing may be necessary ( e.g. to apply limit ).
     * @see OCI8PreparedStatement::executeQuery()
     * @see OCI8PreparedStatement::executeUpdate()
     * @see Connection::prepareStatement()
     */
    public function prepareStatement( $sql ) 
    {
        require_once 'creole/drivers/oracle/OCI8PreparedStatement.php';

        return new OCI8PreparedStatement( $this, $sql );
    }
    
    /**
     * @see Connection::prepareCall()
     */
    public function prepareCall( $sql )
	{
        throw new SQLException( 'Oracle driver does not yet support stored procedures using CallableStatement.' );
    }
    
    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        require_once 'creole/drivers/oracle/OCI8Statement.php';

        return new OCI8Statement( $this );
    }
}
