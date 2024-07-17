<?php

require 'autoload.php';
$beneficiario = new VinicciusGuedes\LaravelCnab\Pessoa([
    'nome'      => 'ACME',
    'endereco'  => 'Rua um, 123',
    'cep'       => '99999-999',
    'uf'        => 'UF',
    'cidade'    => 'CIDADE',
    'documento' => '99.999.999/9999-99',
]);

$pagador = new VinicciusGuedes\LaravelCnab\Pessoa([
    'nome'      => 'Cliente',
    'endereco'  => 'Rua um, 123',
    'bairro'    => 'Bairro',
    'cep'       => '99999-999',
    'uf'        => 'UF',
    'cidade'    => 'CIDADE',
    'documento' => '999.999.999-99',
]);

$boleto = new VinicciusGuedes\LaravelCnab\Boleto\Banco\Fibra([
    'logo'                   => realpath(__DIR__ . '/../logos/') . DIRECTORY_SEPARATOR . '224.png',
    'dataVencimento'         => new Carbon\Carbon('2023-01-04'),
    'valor'                  => 10,
    'multa'                  => false,
    'juros'                  => false,
    'numero'                 => 1,
    'numeroDocumento'        => 1,
    'range'                  => 0,
    'pagador'                => $pagador,
    'beneficiario'           => $beneficiario,
    'modalidadeCarteira'     => 'D',
    'carteira'               => 112,
    'agencia'                => '0001',
    'codigoCliente'          => '12345',
    'conta'                  => '1234567',
    'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
    'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
    'aceite'                 => 'N',
    'especieDoc'             => 'DM',
]);

$remessa = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab400\Cobranca\Banco\Fibra([
    'agencia'       => '0001',
    'conta'         => '1234567',
    'contaDv'       => 9,
    'carteira'      => 112,
    'beneficiario'  => $beneficiario,
    'codigoCliente' => '12345',
]);
$remessa->addBoleto($boleto);

echo $remessa->save(__DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'fibra.txt');
