<?php

require 'autoload.php';
$retorno = VinicciusGuedes\LaravelCnab\Cnab\Retorno\Factory::make(__DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'btg.ret');
$retorno->processar();

echo $retorno->getBancoNome();
dd($retorno->getDetalhes());
