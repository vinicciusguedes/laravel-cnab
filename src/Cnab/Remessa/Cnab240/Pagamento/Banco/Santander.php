<?php

namespace VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\Util;
use VinicciusGuedes\LaravelCnab\CalculoDV;
use VinicciusGuedes\LaravelCnab\Exception\ValidationException;
use VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\AbstractRemessa;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\Remessa as RemessaContract;

class Santander extends AbstractRemessa implements RemessaContract
{
    const OCORRENCIA_REMESSA = '01';
    const OCORRENCIA_PEDIDO_BAIXA = '02';
    const OCORRENCIA_CONCESSAO_ABATIMENTO = '04';
    const OCORRENCIA_CANC_ABATIMENTO = '05';
    const OCORRENCIA_ALT_VENCIMENTO = '06';
    const OCORRENCIA_PROTESTAR = '09';
    const OCORRENCIA_NAO_PROTESTAR = '10';
    const OCORRENCIA_SUSTAR_PROTESTO = '18';
    const OCORRENCIA_ALT_OUTROS_DADOS = '31';
    const PROTESTO_SEM = '0';
    const PROTESTO_DIAS_CORRIDOS = '1';
    const PROTESTO_DIAS_UTEIS = '2';
    const PROTESTO_PERFIL_BENEFICIARIO = '3';
    const PROTESTO_AUTOMATICO = '9';

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('codigoCliente');
        $this->addCampoObrigatorio('idremessa');
    }

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SANTANDER;

    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */
    protected $carteiras = [101, 201];

    /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;

    /**
     * Retorna o codigo do cliente.
     *
     * @return string
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     * @return Santander
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return Santander
     * @throws ValidationException
     */
    public function addBoleto(BoletoContract $boleto)
    {
        $this->boletos[] = $boleto;
        $this->segmentoJ($boleto);
        $this->segmentoJ52($boleto);
        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return Santander
     * @throws ValidationException
     */
    public function segmentoJ(BoletoContract $boleto)
    {
        $this->iniciaDetalhe();
        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '3');
        $this->add(9, 13, Util::formatCnab('9', $this->iRegistrosLote, 5));
        $this->add(14, 14, 'J');
        $this->add(15, 15, '0'); //TIPO DE MOVIMENTO
        $this->add(16, 17, '00'); //CÓDIGO DE INSTRUÇÃO PARA MOVIMENTO
        $this->add(18, 61, Util::formatCnab('X', $boleto->getCodigoBarras(), 44)); //CÓD. DE BARRAS
        $this->add(62, 91, Util::formatCnab('X', $boleto->getBeneficiario()->getNome(), 30)); // NOME DO FAVORECIDO
        $this->add(92, 99, $boleto->getDataVencimento()->format('dmY')); //DATA DO VENCIMENTO (NOMINAL)
        $this->add(100, 114, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO TÍTULO (NOMINAL)
        $this->add(115, 129, Util::formatCnab('9', $boleto->getDesconto(), 13, 2)); //DESCONTOS - VALOR DO DESCONTO + ABATIMENTO
        $this->add(130, 144, Util::formatCnab('9', $boleto->getMulta(), 13, 2)); //ACRÉSCIMOS - VALOR DA MORA + MULTA
        $this->add(145, 152, $boleto->getDataVencimento()->format('dmY')); //DATA DO PAGAMENTO
        $this->add(153, 167, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO PAGAMENTO
        $this->add(168, 182, Util::formatCnab('9', 0, 15));
        $this->add(183, 202, Util::formatCnab('X', $boleto->getNumeroDocumento(),20)); //SEU NÚMERO - Nº DOCTO ATRIBUÍDO PELA EMPRESA
        $this->add(203, 222, ''); //NOSSO NÚMERO - NÚMERO ATRIBUÍDO PELO BANCO
        $this->add(223, 224, Util::formatCnab('9', $boleto->getMoeda(), 2));
        $this->add(225, 230, '');
        $this->add(231, 240, Util::formatCnab('X', 0, 10));

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return Santander
     * @throws ValidationException
     */
    public function segmentoJ52(BoletoContract $boleto)
    {
        $this->iniciaDetalhe();
        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '3');
        $this->add(9, 13, Util::formatCnab('9', $this->iRegistrosLote, 5));
        $this->add(14, 14, 'J');
        $this->add(15, 15, '');
        $this->add(16, 17, '0'); //TIPO DE MOVIMENTO
        $this->add(18, 19, '52'); //CÓDIGO DO REGISTRO IDENTIFICAÇÃO DO REGISTRO OPCIONAL
        $this->add(20, 20, strlen(Util::onlyNumbers($boleto->getPagador()->getDocumento())) == 14 ? 2 : 1); //TIPO DE INSCRIÇÃO DO SACADO 1=CPF|2=CNPJ
        $this->add(21, 35, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getDocumento()), 15)); //NÚMERO DE INSCRIÇÃO DO SACADO
        $this->add(36, 75, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40)); //NOME DO SACADO
        $this->add(76, 76, strlen(Util::onlyNumbers($boleto->getBeneficiario()->getDocumento())) == 14 ? 2 : 1); //TIPO DE INSCRIÇÃO DO CEDENTE 1=CPF|2=CNPJ
        $this->add(77, 91, Util::formatCnab('9', Util::onlyNumbers($boleto->getBeneficiario()->getDocumento()), 15)); //NÚMERO DE INSCRIÇÃO DO CEDENTE
        $this->add(92, 131, Util::formatCnab('X', $boleto->getBeneficiario()->getNome(), 40)); //NOME DO CEDENTE
        $this->add(132, 132, '0'); //TIPO DE INSCRIÇÃO DO SACADOR AVALISTA
        $this->add(133, 147, '000000000000000'); //NÚMERO DE INSCRIÇÃO DO SACADOR AVALISTA
        $this->add(148, 187, Util::formatCnab('X', '', 40)); //NOME DO SACADOR AVALISTA
        $this->add(188, 240, Util::formatCnab('X', '', 53)); //COMPLEMENTO DE REGISTRO

        if($boleto->getSacadorAvalista()) {
            $this->add(132, 132, strlen(Util::onlyNumbers($boleto->getSacadorAvalista()->getDocumento())) == 14 ? 2 : 1);
            $this->add(133, 147, Util::formatCnab('9', Util::onlyNumbers($boleto->getSacadorAvalista()->getDocumento()), 15));
            $this->add(148, 187, Util::formatCnab('X', $boleto->getSacadorAvalista()->getNome(), 40));
        }

        return $this;
    }

    /**
     * @return Santander
     * @throws ValidationException
     */
    protected function header()
    {
        $this->iniciaHeader();

        /**
         * HEADER DE ARQUIVO
         */
        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0000');
        $this->add(8, 8, '0');
        $this->add(9, 17, '');
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getPagador()->getDocumento())) == 14 ? 2 : 1); //EMPRESA – INSCRIÇÃO - TIPO DE INSCRIÇÃO DA EMPRESA
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getPagador()->getDocumento()), 14)); //INSCRIÇÃO NÚMERO - CNPJ EMPRESA DEBITADA
        $this->add(33, 52, Util::formatCnab('9', $this->getCodigoCliente(), 20));
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5)); //AGÊNCIA - NÚMERO AGÊNCIA DEBITADA
        $this->add(58, 58, '');
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12)); //CONTA - NÚMERO DE C/C DEBITADA
        $this->add(71, 71, CalculoDV::SantanderContaCorrente($this->getAgencia(), $this->getConta()));
        $this->add(72, 72, ''); //DAC - DAC DA AGÊNCIA/CONTA DEBITADA
        $this->add(73, 102, Util::formatCnab('X', $this->getPagador()->getNome(), 30)); //NOME DA EMPRESA
        $this->add(103, 132, Util::formatCnab('X', Util::$bancos[Util::onlyNumbers($this->getCodigoBanco())], 30)); //NOME DO BANCO
        $this->add(133, 142, '');
        $this->add(143, 143, 1); //ARQUIVO-CÓDIGO - CÓDIGO 1:REMESSA/2:RETORNO
        $this->add(144, 151, $this->getDataRemessa('dmY')); //DATA DE GERAÇÃO DO ARQUIVO
        $this->add(152, 157, $this->getHoraRemessa('His')); //HORA DE GERAÇÃO DO ARQUIVO
        $this->add(158, 163, Util::formatCnab('9', $this->getIdremessa(), 6));
        $this->add(164, 166, Util::formatCnab('9', '060', 3));
        $this->add(167, 171, ''); //UNIDADE DE DENSIDADE - DENSIDADE DE GRAVAÇÃO DO ARQUIVO
        $this->add(172, 191, '');
        $this->add(192, 211, '');
        $this->add(212, 230, '');
        $this->add(231, 240, '');

        return $this;
    }

    /**
     * Retorna o codigo de transmissão.
     *
     * @return string
     * @throws ValidationException
     */
    public function getCodigoTransmissao()
    {
        return Util::formatCnab('9', $this->getAgencia(), 4)
            . Util::formatCnab('9', '0000', 4)
            . Util::formatCnab('9', $this->getCodigoCliente(), 7);
    }

    /**
     * @return Santander
     * @throws ValidationException
     */
    protected function headerLote()
    {
        $this->iniciaHeaderLote();

        /**
         * HEADER DE LOTE
         */
        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '1');
        $this->add(9, 9, 'C');
        $this->add(10, 11, '20'); //TIPO DE PAGAMENTO - TIPO DE PAGTO
        $this->add(12, 13, '30'); //FORMA DE PAGAMENTO - FORMA DE PAGAMENTO
        $this->add(14, 16, '030');
        $this->add(17, 17, '');
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getPagador()->getDocumento())) == 14 ? 2 : 1); //EMPRESA - INSCRIÇÃO TIPO INSCRIÇÃO EMPRESA DEBITADA
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getPagador()->getDocumento()), 14)); //INSCRIÇÃO NÚMERO - CNPJ EMPRESA OU CPF DEBITADO
        $this->add(33, 52, Util::formatCnab('X', $this->getCodigoCliente(), 20));
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5)); //AGÊNCIA - NÚMERO AGÊNCIA DEBITADA
        $this->add(58, 58, '');
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12)); //CONTA - NÚMERO DE C/C DEBITADA
        $this->add(71, 71, '');
        $this->add(72, 72, ''); //DAC - DAC DA AGÊNCIA/CONTA DEBITADA
        $this->add(73, 102, Util::formatCnab('X', $this->getPagador()->getNome(), 30)); //NOME DA EMPRESA - NOME DA EMPRESA DEBITADA
        $this->add(103, 142, '');
        $this->add(143, 172, Util::formatCnab('X', $this->getPagador()->getEndereco(), 30)); //ENDEREÇO DA EMPRESA - NOME DA RUA, AV, PÇA, ETC...
        $this->add(173, 177, Util::formatCnab('9', $this->getPagador()->getNumero(), 5)); //NÚMERO - NÚMERO DO LOCAL
        $this->add(178, 192, Util::formatCnab('X', $this->getPagador()->getComplemento(), 15)); //COMPLEMENTO. - CASA, APTO, SALA, ETC...
        $this->add(193, 212, Util::formatCnab('X', $this->getPagador()->getCidade(), 20)); //CIDADE - NOME DA CIDADE
        $this->add(213, 220, Util::formatCnab('9', $this->getPagador()->getCep(), 8)); //CEP
        $this->add(221, 222, Util::formatCnab('X', $this->getPagador()->getUf(), 2)); //ESTADO - SIGLA DO ESTADO
        $this->add(223, 230, '');
        $this->add(231, 240, '0');

        return $this;
    }

    /**
     * @return Santander
     * @throws ValidationException
     */
    protected function trailerLote()
    {
        $this->iniciaTrailerLote();

        $valor = array_reduce($this->boletos, function($valor, $boleto) {
            return $valor + $boleto->getValor();
        }, 0);

        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '5');
        $this->add(9, 17, '');
        $this->add(18, 23, Util::formatCnab('9', $this->getCountDetalhes() + 2, 6));
        $this->add(24, 41, Util::formatCnab('9', $valor, 16, 2));
        $this->add(42, 59, Util::formatCnab('9', 0, 18));
        $this->add(60, 65, Util::formatCnab('9', 0, 6));
        $this->add(65, 230, '');
        $this->add(231, 240, ''); //OCORRENCIA

        return $this;
    }

    /**
     * @return Santander
     * @throws ValidationException
     */
    protected function trailer()
    {
        $this->iniciaTrailer();

        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '9999');
        $this->add(8, 8, '9');
        $this->add(9, 17, '');
        $this->add(18, 23, Util::formatCnab('9', $this->getCount(), 6));
        $this->add(24, 29, Util::formatCnab('9', $this->getCount(), 6));
        $this->add(30, 240, '');

        return $this;
    }
}
