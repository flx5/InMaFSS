 <?php

# Mini-lib to allow bitwise operations on very large "numbers".
# We use FLOAT types in PHP to represent these numbers to get away from the limitations
# of 32 bit signed integers.
# PHP doesn't offer large integers capable of handling IP addresses as
# IPv4 requires at a minimum 32 bit unsigned integers.  PHP  can only do 32 bit signed.
# FLOAT types on the other hand can do anything.... except bitwise operators :)
# This mini-lib provides the ability for logical/bitwise AND and OR on floating types.
# This hasn't been tested, but this should also be capable of handling IPv6 sized addresses
#
# Paul Gregg, April 29, 2009
# Thanks go to Liyang who was enough of a pain in the ass to make me write this.
#


// Convert an arbitrary sized decimal (in float format) to and array of 16-bit integers
Function float2largearray($n) {
  $result = array();
  while ($n > 0) {
    array_push($result, ($n & 0xffff));
    list($n, $dummy) = explode('.', sprintf("%F", $n/65536.0));
    # note we don't want to use "%0.F" as it will get rounded which is bad.
  }
  return $result;
}

// Convert our largearray format back to an arbitrary sized whole number float
Function largearray2float($a) {
  $factor = 1.0;
  $result = 0.0;
  foreach ($a as $element) {
    $result += ($factor * $element);
    $factor = $factor << 16;
  }
  list($result, $dummy) = explode('.', sprintf("%F", $result));
  return $result;
}

// Perform a bitwise AND operation of $a and $b
// We only need to operate on the minimum number of elements because any extra elements
// in any array would be negated by the AND with the implied zeros in the smaller array
Function largearray_and($a, $b) {
  $indexes = min(count($a), count($b));
  $c = array();
  for ($i=0; $i<$indexes; $i++) {
    array_push($c, $a[$i] & $b[$i]);
  }
  return $c;
}

Function largearray_or($a, $b) {
  $indexes = max(count($a), count($b));
  $c = array();
  for ($i=0; $i<$indexes; $i++) {
    if (!isset($a[$i])) $a[$i] = 0;
    if (!isset($b[$i])) $b[$i] = 0;
    array_push($c, $a[$i] | $b[$i]);
  }
  return $c;
}

Function float_and($a, $b) {
  return
    largearray2float(
      largearray_and( float2largearray($a), float2largearray($b) )
    );
}
  
Function float_or($a, $b) {
  return
    largearray2float(
      largearray_or( float2largearray($a), float2largearray($b) )
    );
}
  

?>