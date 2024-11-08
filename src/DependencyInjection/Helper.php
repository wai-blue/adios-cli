<?php

namespace App\DependencyInjection;

class Helper
{
  public static function findProjectRoot($cwd): ?string
  {
    $dir = $cwd;
    while ($dir !== '/') {
      if (file_exists($dir . '/composer.json')) {
        return $dir;
      }
      $dir = dirname($dir);
    }
    return null;
  }
}