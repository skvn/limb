<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter html
 * @version $Id: html.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 * @package wact
 */
class WactHtmlFilter extends WactCompilerFilter
{
  function getValue() {
    if ($this->isConstant())
      return htmlspecialchars($this->base->getValue(), ENT_QUOTES);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('htmlspecialchars(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(', ENT_QUOTES)');
  }
}


