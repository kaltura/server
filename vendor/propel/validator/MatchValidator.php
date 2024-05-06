<?php
/*
 *  $Id: MatchValidator.php 1262 2009-10-26 20:54:39Z francois $
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
 * <http://propel.phpdb.org>.
 */

/**
 * A validator for regular expressions.
 *
 * This validator will return true, when the passed value *matches* the
 * regular expression.
 *
 * ## This class replaces the former class MaskValidator ##
 *
 * If you do want to test if the value does *not* match an expression,
 * you can use the MatchValidator class instead.
 *
 * Below is an example usage for your Propel xml schema file.
 *
 * <code>
 *   <column name="email" type="VARCHAR" size="128" required="true" />
 *   <validator column="username">
 *     <!-- allow strings that match the email adress pattern -->
 *     <rule
 *       name="match"
 *       value="/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+$/"
 *       message="Please enter a valid email address." />
 *   </validator>
 * </code>
 *
 * @author     Michael Aichler <aichler@mediacluster.de>
 * @author     Hans Lellelid <hans@xmpl.org>
 * @version    $Revision: 1262 $
 * @package    propel.validator
 */
class MatchValidator implements BasicValidator
{
	/**
	 * Prepares the regular expression entered in the XML
	 * for use with preg_match().
	 * @param      string $exp
	 * @return     string Prepared regular expession.
	 */
	private function prepareRegexp($exp)
	{
		// remove surrounding '/' marks so that they don't get escaped in next step
		if ($exp[0] !== '/' || $exp[strlen($exp)-1] !== '/' ) {
			$exp = '/' . $exp . '/';
		}

		// if they did not escape / chars; we do that for them
		$exp = preg_replace('/([^\\\])\/([^$])/', '$1\/$2', $exp);

		return $exp;
	}

	/**
	 * Whether the passed string matches regular expression.
	 */
	public function isValid (ValidatorMap $map, $str)
	{
		return (preg_match($this->prepareRegexp($map->getValue()), $str) != 0);
	}
}
