<?php

/**
 * @todo Submit this upstream after T27678.
 */
final class ArcanistGotoXHPASTLinterRule extends ArcanistXHPASTLinterRule {

  const ID = 1002;

  public function getLintName() {
    return pht('Use of `%s` Statement', 'goto');
  }

  public function process(XHPASTNode $root) {
    $nodes = $root->selectDescendantsOfTypes(array(
      'n_GOTO',
      'n_LABEL',
    ));

    foreach ($nodes as $node) {
      switch ($node->getTypeName()) {
        case 'n_GOTO':
          $message = pht(
            "`%s` statements should not be used as they hinder static ".
            "analysis and, in almost all cases, you should use ".
            "conditionals instead.\n\n%s",
            'goto',
            'xkcdgoto');
          break;

        case 'n_LABEL':
          $message = pht(
            'Labels (which are associated with `%s` statements) should '.
            'not be used.',
            'goto');
          break;
      }

      $this->raiseLintAtNode($node, $message);
    }
  }

}