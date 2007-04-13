<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    fs
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/fs/src/lmbFsTools.class.php');
lmbToolkit :: merge(new lmbFsTools());

?>