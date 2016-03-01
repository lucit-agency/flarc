<?php

/**
 * @todo Remove this after https://secure.phabricator.com/D13426.
 * @todo Submit this upstream after T27678.
 */
final class ArcanistDirnameFileXHPASTLinterRule
  extends ArcanistXHPASTLinterRule {

  const ID = 1009;

  public function getLintName() {
    return pht('`%s` Usage', 'dirname(__FILE__)');
  }

  public function getLintSeverity() {
    return ArcanistLintSeverity::SEVERITY_WARNING;
  }

  public function process(XHPASTNode $root) {
    if (!$this->version || version_compare($this->version, '5.3.0', '<')) {
      return;
    }

    $function_calls = $this->getFunctionCalls($root, array('dirname'));

    foreach ($function_calls as $function_call) {
      $args = $function_call->getChildOfType(1, 'n_CALL_PARAMETER_LIST');

      if (count($args->getChildren()) != 1) {
        continue;
      }

      $arg = $args->getChildByIndex(0);

      if ($arg->getTypeName() == 'n_MAGIC_SCALAR' &&
          strtoupper($arg->getSemanticString()) == '__FILE__') {
        $this->raiseLintAtNode(
          $function_call,
          pht(
            'Use `%s` instead of `%s`.',
            '__DIR__',
            'dirname(__FILE__)'),
          '__DIR__');
      }
    }
  }

}
