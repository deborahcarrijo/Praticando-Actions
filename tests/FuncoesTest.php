<?php

use PHPUnit\Framework\TestCase;
use Funcoes\Funcoes;

class FuncoesTest extends TestCase
{
    public function testSoma()
    {
        $obj = new Funcoes();
        $this->assertEquals(4, $obj->soma(2, 2));
    }
}
