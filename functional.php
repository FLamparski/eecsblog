<?php

function inc(int $x) {
  return $x + 1;
}
function dec(int $x) {
  return $x - 1;
}

function gte($n) {
  return function($x) use ($n) {
    return $x >= $n;
  };
}
function lt($n) {
  return function ($x) use ($n) {
    return $x < $n;
  };
}

function foldGeneral(array $xs, callable $fn, $initial, $istart, $itest, $istep) {
  $acc = $initial;
  $i = $istart;
  while ($itest($i)) {
    $acc = $fn($acc, $xs[$i]);
    $i = $istep($i);
  }
  return $acc;
}

function foldl(array $xs, callable $fn, $initial = null) {
  return foldGeneral($xs, $fn, $initial, 0, lt(count($xs)), 'inc');
}
function foldr(array $xs, callable $fn, $initial = null) {
  return foldGeneral($xs, $fn, $initial, count($xs) - 1, gte(0), 'dec');
}

function map(array $xs, callable $f) {
  return array_map($f, $xs);
}

function compose(/* arguments */) {
  $fns = func_get_args();
  return function($x) use ($fns) {
    return foldr($fns, function($ret, $fn) {
      return $fn($ret);
    }, $x);
  };
};
