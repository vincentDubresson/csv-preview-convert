<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->append([
        __FILE__,
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        // @see https://mlocati.github.io/php-cs-fixer-configurator/
        '@PhpCsFixer' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => true,
        'concat_space' => ['spacing' => 'one'],
        'combine_consecutive_issets' => false,
        'combine_consecutive_unsets' => false,
        'doctrine_annotation_spaces' => ['before_array_assignments_colon' => false, 'before_array_assignments_equals' => false, 'after_array_assignments_equals' => false],
        'explicit_indirect_variable' => false,
        'explicit_string_variable' => false,
        'increment_style' => false,
        'return_assignment' => false,
        'linebreak_after_opening_tag' => true,
        'method_chaining_indentation' => false,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'native_function_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_superfluous_elseif' => false,
        'no_useless_else' => true,
        'no_whitespace_before_comma_in_array' => true,
        'ordered_imports' => true,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['var']],
        'php_unit_test_class_requires_covers' => false,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
