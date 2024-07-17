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

$boleto = new VinicciusGuedes\LaravelCnab\Boleto\Banco\Bancoob([
    'logo'                   => realpath(__DIR__ . '/../logos/') . DIRECTORY_SEPARATOR . '756.png',
    'dataVencimento'         => new Carbon\Carbon(),
    'valor'                  => 100,
    'multa'                  => false,
    'juros'                  => false,
    'numero'                 => 1,
    'numeroDocumento'        => 1,
    'pagador'                => $pagador,
    'beneficiario'           => $beneficiario,
    'carteira'               => 1,
    'agencia'                => 1111,
    'convenio'               => 123123,
    'conta'                  => 22222,
    'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
    'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
    'aceite'                 => 'S',
    'especieDoc'             => 'DM',
]);

$pdf = new VinicciusGuedes\LaravelCnab\Boleto\Render\Pdf();
$pdf->addBoleto($boleto);
$pdf->SetProtection('123123');
$pdf->gerarBoleto($pdf::OUTPUT_SAVE, __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'bancoob.pdf');