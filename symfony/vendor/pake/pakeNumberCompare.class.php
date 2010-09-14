<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeNumberCompare.class.php 1791 2006-08-23 21:17:06Z fabien $
 */

if (class_exists('pakeNumberCompare'))
{
 return;
}

/**
 *
 * Numeric comparisons.
 *
 * sfNumberCompare compiles a simple comparison to an anonymous
 * subroutine, which you can call with a value to be tested again.

 * Now this would be very pointless, if sfNumberCompare didn't understand
 * magnitudes.

 * The target value may use magnitudes of kilobytes (C<k>, C<ki>),
 * megabytes (C<m>, C<mi>), or gigabytes (C<g>, C<gi>).  Those suffixed
 * with an C<i> use the appropriate 2**n version in accordance with the
 * IEC standard: http://physics.nist.gov/cuu/Units/binary.html
 *
 * based on perl Number::Compare module.
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @see        http://physics.nist.gov/cuu/Units/binary.html
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeNumberCompare.class.php 1791 2006-08-23 21:17:06Z fabien $
 */
class pakeNumberCompare
{
  private $test = '';

  public function __construct($test)
  {
    $this->test = $test;
  }

  public function test($number)
  {
    if (!preg_match('{^([<>]=?)?(.*?)([kmg]i?)?$}i', $this->test, $matches))
    {
      throw new pakeException(sprintf('Don\'t understand "%s" as a test.', $this->test));
    }

    $target = array_key_exists(2, $matches) ? $matches[2] : '';
    $magnitude = array_key_exists(3, $matches) ? $matches[3] : '';
    if (strtolower($magnitude) == 'k')  $target *=           1000;
    if (strtolower($magnitude) == 'ki') $target *=           1024;
    if (strtolower($magnitude) == 'm')  $target *=        1000000;
    if (strtolower($magnitude) == 'mi') $target *=      1024*1024;
    if (strtolower($magnitude) == 'g')  $target *=     1000000000;
    if (strtolower($magnitude) == 'gi') $target *= 1024*1024*1024;

    $comparison = array_key_exists(1, $matches) ? $matches[1] : '==';
    if ($comparison == '==' || $comparison == '')
    {
      return ($number == $target);
    }
    else if ($comparison == '>')
    {
      return ($number > $target);
    }
    else if ($comparison == '>=')
    {
      return ($number >= $target);
    }
    else if ($comparison == '<')
    {
      return ($number < $target);
    }
    else if ($comparison == '<=')
    {
      return ($number <= $target);
    }

    return false;
  }
}

/*
=head1 SYNOPSIS

 Number::Compare->new(">1Ki")->test(1025); # is 1025 > 1024

 my $c = Number::Compare->new(">1M");
 $c->(1_200_000);                          # slightly terser invocation

=head1 DESCRIPTION


=head1 METHODS

=head2 ->new( $test )

Returns a new object that compares the specified test.

=head2 ->test( $value )

A longhanded version of $compare->( $value ).  Predates blessed
subroutine reference implementation.

=head2 ->parse_to_perl( $test )

Returns a perl code fragment equivalent to the test.
*/
