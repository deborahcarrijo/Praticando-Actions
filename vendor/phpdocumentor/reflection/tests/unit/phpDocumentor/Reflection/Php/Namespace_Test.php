<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Namespace_ class.
 */
// @codingStandardsIgnoreStart
#[CoversClass(Namespace_::class)]
class Namespace_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    use MetadataContainerTestHelper;

    protected Namespace_ $fixture;

    private Fqsen $fqsen;

    private DocBlock $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fqsen    = new Fqsen('\MySpace');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Namespace_($this->fqsen, $this->docBlock);
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    public function testAddAndGetClasses(): void
    {
        $this->assertEmpty($this->fixture->getClasses());

        $class = new Fqsen('\MySpace\MyClass');
        $this->fixture->addClass($class);

        $this->assertEquals(['\MySpace\MyClass' => $class], $this->fixture->getClasses());
    }

    public function testAddAndGetConstants(): void
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Fqsen('\MySpace::MY_CONSTANT');
        $this->fixture->addConstant($constant);

        $this->assertEquals(['\MySpace::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    public function testAddAndGetFunctions(): void
    {
        $this->assertEmpty($this->fixture->getFunctions());

        $function = new Fqsen('\MySpace\MyFunction()');
        $this->fixture->addFunction($function);

        $this->assertEquals(['\MySpace\MyFunction()' => $function], $this->fixture->getFunctions());
    }

    public function testAddAndGetInterfaces(): void
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Fqsen('\MySpace\MyInterface');
        $this->fixture->addInterface($interface);

        $this->assertEquals(['\MySpace\MyInterface' => $interface], $this->fixture->getInterfaces());
    }

    public function testAddAndGetTraits(): void
    {
        $this->assertEmpty($this->fixture->getTraits());

        $trait = new Fqsen('\MySpace\MyTrait');
        $this->fixture->addTrait($trait);

        $this->assertEquals(['\MySpace\MyTrait' => $trait], $this->fixture->getTraits());
    }

    public function testGetFqsen(): void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $this->fixture->getName());
    }
}
