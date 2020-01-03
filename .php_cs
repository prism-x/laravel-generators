<?php

$finder = Symfony\Component\Finder\Finder::create()
  ->in(__DIR__)
  ->exclude(['bootstrap', 'storage', 'vendor'])
  ->name('*.php')
  ->name('_ide_helper')
  ->notName('*.blade.php')
  ->ignoreDotFiles(true)
  ->ignoreVCS(true);


return PhpCsFixer\Config::create()
  ->setUsingCache(false)
  ->setRules([
    '@PSR2' => true,
    'ordered_imports' => ['sort_algorithm' => "length"],
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'no_extra_blank_lines' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'],
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'strict_comparison' => false,
    'no_superfluous_phpdoc_tags' => true,
    'not_operator_with_successor_space' => true,
  ])
  ->setFinder($finder);
