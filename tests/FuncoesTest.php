<?php

use PHPUnit\Framework\TestCase;
use Funcoes\FuncoesMatematica;

class FuncoesTest extends TestCase
{
    public function testSoma()
    {
        $obj = new FuncoesMatematica;
        $this->assertEquals(4, $obj->soma(2, 2));
    }
}
