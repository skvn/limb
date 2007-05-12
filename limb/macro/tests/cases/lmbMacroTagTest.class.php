<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMacroTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    macro
 */

lmb_require('limb/macro/src/lmbMacroNode.class.php'); 
lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroSourceLocation.class.php');
lmb_require('limb/macro/src/lmbMacroCodeWriter.class.php'); 
 
class MacroTagClass1CompilerTest extends lmbMacroTag{}
class MacroTagClass2CompilerTest extends lmbMacroTag{}

Mock::generate('lmbMacroNode', 'MockMacroNode');
Mock::generate('lmbMacroCodeWriter', 'MockMacroCodeWriter');

class lmbMacroTagTest extends UnitTestCase
{
  protected $node;
  protected $tag_info;
  protected $source_location;

  function setUp()
  {
    $this->tag_info = new lmbMacroTagInfo('MacroTag', 'whatever');
    $this->source_location = new lmbMacroSourceLocation('my_file', 10);
    $this->node = $this->_createNode();
  }

  protected function _createNode()
  {
    return new lmbMacroTag($this->source_location, 'my_tag', $this->tag_info);
  }
  
  function testGetIdAttribute()
  {
    $this->node->setId('Test');
    $this->assertEqual($this->node->getId(), 'Test');
  }

  function testGetIdGenerated()
  {
    $id = $this->node->getId();
    $this->assertEqual($this->node->getId(), $id);
  }

  function testFindChild()
  {
    $mock = new MockMacroNode();
    $mock->setReturnValue('getId', 'Test');
    $this->node->addChild($mock);
    $this->assertIsA($this->node->findChild('Test'), 'MockMacroNode');
  }

  function testFindChildNotFound()
  {
    $this->assertFalse($this->node->findChild('Test'));
  }

  function testFindChildByClass()
  {
    $mock = new MockMacroNode();
    $this->node->addChild($mock);
    $this->assertIsA($this->node->findChildByClass('MockMacroNode'), 'MockMacroNode');
  }

  function testFindChildByClassNotFound()
  {
    $this->assertFalse($this->node->findChildByClass('Booo'));
  }

  function testFindParentByChilld()
  {
    $parent = new lmbMacroNode;
    $parent->addChild($this->node);
    $this->assertIsA($this->node->findParentByClass('lmbMacroNode'), 'lmbMacroNode');
  }

  function testFindParentByClassNotFound()
  {
    $this->assertFalse($this->node->findParentByClass('Test'));
  }

  function testRemoveChild()
  {
    $mock = new MockMacroNode();
    $mock->setReturnValue('getId', 'Test');
    $this->node->addChild($mock);
    $this->assertIsA($this->node->removeChild('Test'), 'MockMacroNode');
  }

  function testGetChildren()
  {
    $mock = new MockMacroNode();
    $this->node->addChild($mock);
    $children = $this->node->getChildren();
    $this->assertReference($mock, $children[0]);
  }

  function testPrepare()
  {
    $child = new MockMacroNode();
    $this->node->addChild($child);
    $child->expectCallCount('prepare', 1);
    $this->node->prepare();
  }

  function testGenerateConstructor()
  {
    $code_writer = new MockMacroCodeWriter();
    $child = new MockMacroNode();
    $child->expectCallCount('generateConstructor', 1);
    $this->node->addChild($child);
    $this->node->generateConstructor($code_writer);
  }

  function testGenerateContents()
  {
    $code_writer = new MockMacroCodeWriter();
    $child = new MockMacroNode();
    $child->expectCallCount('generate', 1);
    $this->node->addChild($child);
    $this->node->generateContents($code_writer);
  }

  function testGenerate()
  {
    $code_writer = new MockMacroCodeWriter();
    $child = new MockMacroNode();
    $child->expectCallCount('generate', 1);
    $this->node->addChild($child);
    $this->node->generate($code_writer);
  }

  function testCheckIdsOk()
  {
    $root = new lmbMacroNode;
    $child1 = new lmbMacroNode;
    $child1->setId('id1');

    $child2 = new lmbMacroNode;
    $child2->setId('id2');

    $root->addChild($child1);
    $root->addChild($child2);

    $root->checkChildrenIds();
  }

  function testDuplicateIdsError()
  {
    $root = new lmbMacroNode;
    $child1 = new lmbMacroNode(new lmbMacroSourceLocation('my_file', 10));
    $child1->setId('my_tag');
    $root->addChild($child1);

    $child2 = new lmbMacroNode(new lmbMacroSourceLocation('my_file2', 15));
    $child2->setId('my_tag');
    $root->addChild($child2);

    try
    {
      $root->checkChildrenIds();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Duplicate "id" attribute/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file2');
      $this->assertEqual($params['line'], 15);
      $this->assertEqual($params['duplicate_node_file'], 'my_file');
      $this->assertEqual($params['duplicate_node_line'], 10);
    }
  }

  function testDuplicateIdIsLegalInDifferentBranches()
  {
    $root = new lmbMacroNode;

    $Branch = new lmbMacroNode;
    $root->addChild($Branch);

    $child1 = new lmbMacroNode;
    $child1->setId('my_tag');
    $Branch->addChild($child1);

    $child2 = new MockMacroNode();
    $child2->setId('my_tag');
    $root->addChild($child2);

    $root->checkChildrenIds();
  }  
  
  function testGetIdByDefault()
  {
    $this->assertNotNull($this->node->getId());
  }  

  function testGetId()
  {
    $this->node->setId('TestId');
    $this->assertEqual($this->node->getId(), 'TestId');
  }
  
  function testGetAttributeUnset()
  {
    $this->assertNull($this->node->get('foo'));
  }  

  function testGetAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->assertEqual($this->node->get('foo'), 'bar');
    $this->assertEqual($this->node->get('FOO'), 'bar');
  }

  function testHasAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->node->set('tricky', NULL);
    $this->assertTrue($this->node->has('foo'));
    $this->assertTrue($this->node->has('tricky'));
    $this->assertFalse($this->node->has('missing'));
    $this->assertTrue($this->node->has('FOO'));
    $this->assertTrue($this->node->has('TRICKY'));
    $this->assertFalse($this->node->has('MISSING'));
  }

  function testRemoveAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->node->set('untouched', 'value');
    $this->assertTrue($this->node->has('foo'));
    $this->node->remove('FOO');
    $this->assertFalse($this->node->has('foo'));
  }

  function testBooleanAttribute()
  {
    //true cases
    $this->node->set('B', 'True');
    $this->assertTrue($this->node->getBool('B'));

    $this->node->set('C', 'Something');
    $this->assertTrue($this->node->getBool('C'));

    //false cases
    $this->node->set('A', NULL);
    $this->assertFalse($this->node->getBool('A'));
    
    $this->node->set('D', 'False');
    $this->assertFalse($this->node->getBool('D'));

    $this->assertFalse($this->node->getBool('E'));

    $this->node->set('F', 'n');
    $this->assertFalse($this->node->getBool('F'));

    $this->node->set('G', 'No');
    $this->assertFalse($this->node->getBool('G'));

    $this->node->set('H', 'none');
    $this->assertFalse($this->node->getBool('H'));

    $this->node->set('I', '0');
    $this->assertFalse($this->node->getBool('I'));
  }

  function testPreparseAndCheckForRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));
    $this->node->set('bar', null);
    $this->node->preParse();
  }

  function testPreparseAndCheckForMissedRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));

    try
    {
      $this->node->preParse();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('attribute'), 'bar');
    }
  }

  function testRestrictSelfNesting()
  {
    $tag_info = new lmbMacroTagInfo('CompilerTag', 'whatever');
    $tag_info->setRestrictSelfNesting(true);

    $node = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 13), 'whatever', $tag_info);

    $parent = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 10), 'whatEver', $tag_info);
    $node->setParent($parent);

    try
    {
      $node->preParse();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Tag cannot be nested within the same tag/', $e->getMessage());
      $this->assertEqual($e->getParam('same_tag_file'), 'my_file');
      $this->assertEqual($e->getParam('same_tag_line'), 10);
    }
  }

  function testCheckParentTagClassOk()
  {
    $this->tag_info->setParentClass('MacroTagClass1CompilerTest');

    $parent = new MacroTagClass1CompilerTest(null, null, null);
    $this->node->setParent($parent);

    $this->node->preParse();
  }

  function testCheckParentTagClassException()
  {
    $this->tag_info->setParentClass('MacroTagClass1CompilerTest');

    $parent = new MacroTagClass2CompilerTest(null, null, null);
    $this->node->setParent($parent);

    try
    {
      $this->node->preParse();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Tag must be enclosed by a proper parent tag/', $e->getMessage());
      $this->assertEqual($e->getParam('required_parent_tag_class'), 'MacroTagClass1CompilerTest');
      $this->assertEqual($e->getParam('file'), $this->source_location->getFile());
      $this->assertEqual($e->getParam('line'), $this->source_location->getLine());
    }
  }  
}
?>