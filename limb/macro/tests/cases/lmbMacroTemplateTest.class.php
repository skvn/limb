<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPHPViewTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    macro
 */
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbMacroTemplateTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/view');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view/compiled');
  }

  function testRenderTemplateVar()
  {
    $view = $this->_createView('Hello, <?$name = "Jack"?><?=@$name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testRenderLocalVar()
  {
    $view = $this->_createView('Hello, <?$name = "Jack"?><?=$name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Jack');
  }

  function testEchoVarSyntaxSugar()
  {
    $view = $this->_createView('Hello, <?$name = "Jack"?>{$name}');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Jack');
  }

  function testEchoFunctionSyntaxSugar()
  {
    $rnd = mt_rand();
    $view = $this->_createView("Hello, <?function f_$rnd(){return 'Jack';}?>{f_$rnd()}");
    $this->assertEqual($view->render(), 'Hello, Jack');
  }

  function _createView($tpl)
  {
    $file = $this->_createTemplate($tpl);
    $view = new lmbMacroTemplate($file, LIMB_VAR_DIR . '/view/compiled');
    return $view;
  }

  function _createTemplate($tpl)
  {
    $file = LIMB_VAR_DIR . '/view/tpl/' . mt_rand() . '.phtml';
    file_put_contents($file, $tpl);
    return $file;
  }
}

?>