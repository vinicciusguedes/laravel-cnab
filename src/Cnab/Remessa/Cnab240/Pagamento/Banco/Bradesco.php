<?php
/**
 * Created by PhpStorm.
 * User: simetriatecnologia
 * Date: 15/09/16
 * Time: 14:02
 */

namespace VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\CalculoDV;
use VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\AbstractRemessa;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\Remessa as RemessaContract;
use VinicciusGuedes\LaravelCnab\Util;

class Bradesco extends AbstractRemessa implements RemessaContract
{

    const OCORRENCIA_REMESSA = '01';
    const OCORRENCIA_PEDIDO_BAIXA = '02';
    const OCORRENCIA_PROTESTO_FAMILIAR = '03';
    const OCORRENCIA_CONCESSAO_ABATIMENTO = '04';
    const OCORRENCIA_CANC_ABATIMENTO = '05';
    const OCORRENCIA_ALT_VENCIMENTO = '06';
    const OCORRENCIA_CONCESSAO_DESCONTO = '07';
    const OCORRENCIA_CANC_DESCONTO = '08';
    const OCORRENCIA_PROTESTAR = '09';
    const OCORRENCIA_CANC_PROTESTO_BAIXAR = '10';
    const OCORRENCIA_CANC_PROTESTO = '11';
    const OCORRENCIA_ALT_MORA = '12';
    const OCORRENCIA_CANC_MORA = '13';
    const OCORRENCIA_ALT_MULTA = '14';
    const OCORRENCIA_CANC_MULTA = '15';
    const OCORRENCIA_ALT_DESCONTO = '16';
    const OCORRENCIA_NAO_CONCEDER_RETORNO = '17';
    const OCORRENCIA_ALT_ABATIMENTO = '18';
    const OCORRENCIA_ALT_LIMITE_RECEBIMENTO = '19';
    const OCORRENCIA_CANC_LIMITE_RECEBIMENTO = '20';
    const OCORRENCIA_ALT_NUMERO_TITULO = '21';
    const OCORRENCIA_ALT_NUMERO_CONTROLE = '22';
    const OCORRENCIA_ALT_PAGADOR = '23';
    const OCORRENCIA_ALT_SACADOR = '24';
    const OCORRENCIA_RECUSA_PAGADOR = '30';
    const OCORRENCIA_ALT_OUTROS_DADOS = '31';
    const OCORRENCIA_ALT_RATEIO = '33';
    const OCORRENCIA_CANC_RATEIO = '34';
    const OCORRENCIA_CANC_DEBITO_AUT = '35';
    const OCORRENCIA_ALT_CARTEIRA = '40';
    const OCORRENCIA_CANC_PROTESTO_NAO_TRATADO = '41';
    const OCORRENCIA_ALT_ESPECIE = '42';
    const OCORRENCIA_TRANS_CARTEIRA = '43';
    const OCORRENCIA_ALT_CONTRATO_COBRANCA = '44';
    const OCORRENCIA_EXC_NEGATIVACAO = '45';
    const OCORRENCIA_BAIXA_SEM_PROTESTO = '46';
    const OCORRENCIA_CANC_NEGATIVACAO = '47';

    const PROTESTO_DIAS_CORRIDOS = '1';
    const PROTESTO_DIAS_UTEIS = '2';
    const PROTESTO_SEM = '3';
    const PROTESTO_FAMILIAR_DIAS_UTEIS = '4';
    const PROTESTO_FAMILIAR_DIAS_CORRIDOS = '5';
    const PROTESTO_NEGATIVACAO_SEM_PROTESTO = '8';
    const PROTESTO_AUTOMATICO = '9';

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('idremessa');
    }

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BRADESCO;


    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */

    protected $carteiras = ['04' ,'09', '28'];


    /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;

    /**
     * Retorna o codigo do cliente.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCodigoCliente()
    {
        if (empty($this->codigoCliente)) {
            $this->codigoCliente = Util::formatCnab('9', $this->getCarteiraNumero(), 4) .
                Util::formatCnab('9', $this->getAgencia(), 5) .
                Util::formatCnab('9', $this->getConta(), 7) .
                Util::formatCnab('9', $this->getContaDv() ?: CalculoDV::bradescoContaCorrente($this->getConta()), 1);
        }

        return $this->codigoCliente;
    }

    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return Bradesco
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
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
     * @return $this
     * @throws \Exception
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
        $this->add(16, 17, '14'); //CÓDIGO DE MOVIMENTO
        $this->add(18, 61, Util::formatCnab('X', $boleto->getCodigoBarrasInserido(), 44)); //CÓD. DE BARRAS
        $this->add(62, 91, Util::formatCnab('X', $boleto->getBeneficiario()->getNome(), 30)); // NOME DO FAVORECIDO
        $this->add(92, 99, $boleto->getDataVencimento()->format('dmY')); //DATA DO VENCIMENTO (NOMINAL)
        $this->add(100, 114, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO TÍTULO (NOMINAL)
        $this->add(115, 129, Util::formatCnab('9', $boleto->getDesconto(), 13, 2)); //DESCONTOS - VALOR DO DESCONTO + ABATIMENTO
        $this->add(130, 144, Util::formatCnab('9', $boleto->getMulta(), 13, 2)); //ACRÉSCIMOS - VALOR DA MORA + MULTA
        $this->add(145, 152, $boleto->getDataVencimento()->format('dmY')); //DATA DO PAGAMENTO
        $this->add(153, 167, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO PAGAMENTO
        $this->add(168, 182, Util::formatCnab('9', 0, 15));
        $this->add(183, 202, Util::formatCnab('X', $boleto->getNumeroDocumento(),20)); //SEU NÚMERO - Nº DOCTO ATRIBUÍDO PELA EMPRESA
        $this->add(203, 222, Util::formatCnab('X', $boleto->getNossoNumero(), 20)); //NOSSO NÚMERO - NÚMERO ATRIBUÍDO PELO BANCO
        $this->add(223, 224, Util::formatCnab('9', $boleto->getMoeda(), 2));
        $this->add(225, 230, '');
        $this->add(231, 240, Util::formatCnab('X', 0, 10));

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
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
        $this->add(16, 17, '00'); //TIPO DE MOVIMENTO
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
        $this->add(188, 240, Util::formatCnab('X', 0, 53)); //COMPLEMENTO DE REGISTRO

        if($boleto->getSacadorAvalista()) {
            $this->add(132, 132, strlen(Util::onlyNumbers($boleto->getSacadorAvalista()->getDocumento())) == 14 ? 2 : 1);
            $this->add(133, 147, Util::formatCnab('9', Util::onlyNumbers($boleto->getSacadorAvalista()->getDocumento()), 15));
            $this->add(148, 187, Util::formatCnab('X', $boleto->getSacadorAvalista()->getNome(), 40));
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
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
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getBeneficiario()->getDocumento())) == 14 ? 2 : 1);
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getBeneficiario()->getDocumento()), 14));
        $this->add(33, 52, Util::formatCnab('9', Util::onlyNumbers($this->getCodigoCliente()), 20));
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5));
        $this->add(58, 58, CalculoDV::bradescoAgencia($this->getAgencia()));
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12));
        $this->add(71, 71, CalculoDV::bradescoContaCorrente($this->getConta()));
        $this->add(72, 72, '');
        $this->add(73, 102, Util::formatCnab('X', $this->getBeneficiario()->getNome(), 30));
        $this->add(103, 132, Util::formatCnab('X', Util::$bancos[Util::onlyNumbers($this->getCodigoBanco())], 30));
        $this->add(133, 142, '');
        $this->add(143, 143, 1);
        $this->add(144, 151, $this->getDataRemessa('dmY'));
        $this->add(152, 157, date('His'));
        $this->add(158, 163, Util::formatCnab('9', $this->getIdremessa(), 6));
        $this->add(164, 166, '089');
        $this->add(167, 171, '01600');
        $this->add(172, 211, '');
        $this->add(212, 240, '');
        return $this;
    }
    /**
     * @return $this
     * @throws \Exception
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
        $this->add(12, 13, '00'); //FORMA DE PAGAMENTO - FORMA DE PAGAMENTO
        $this->add(14, 16, '040');
        $this->add(17, 17, '');
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getPagador()->getDocumento())) == 14 ? 2 : 1); //EMPRESA - INSCRIÇÃO TIPO INSCRIÇÃO EMPRESA DEBITADA
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getPagador()->getDocumento()), 14)); //INSCRIÇÃO NÚMERO - CNPJ EMPRESA OU CPF DEBITADO
        $this->add(33, 52, Util::formatCnab('9', Util::onlyNumbers($this->getCodigoCliente()), 20));
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5)); //AGÊNCIA - NÚMERO AGÊNCIA DEBITADA
        $this->add(58, 58, CalculoDV::bradescoAgencia($this->getAgencia()));
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12)); //CONTA - NÚMERO DE C/C DEBITADA
        $this->add(71, 71, CalculoDV::bradescoContaCorrente($this->getConta()));
        $this->add(72, 72, Util::formatCnab('9', $this->getContaDv() ?: CalculoDV::itauContaCorrente($this->getConta()), 1)); //DAC - DAC DA AGÊNCIA/CONTA DEBITADA
        $this->add(73, 102, Util::formatCnab('X', $this->getPagador()->getNome(), 30)); //NOME DA EMPRESA - NOME DA EMPRESA DEBITADA
        $this->add(103, 142, ''); //MENSAGEM
        $this->add(143, 172, Util::formatCnab('X', $this->getPagador()->getEndereco(), 30)); //ENDEREÇO DA EMPRESA - NOME DA RUA, AV, PÇA, ETC...
        $this->add(173, 177, Util::formatCnab('9', $this->getPagador()->getNumero(), 5)); //NÚMERO - NÚMERO DO LOCAL
        $this->add(178, 192, Util::formatCnab('X', $this->getPagador()->getComplemento(), 15)); //COMPLEMENTO. - CASA, APTO, SALA, ETC...
        $this->add(193, 212, Util::formatCnab('X', $this->getPagador()->getCidade(), 20)); //CIDADE - NOME DA CIDADE
        $this->add(213, 217, Util::formatCnab('9', $this->getPagador()->getCep(), 5)); //CEP
        $this->add(218, 220, ''); //CEP COMPLEMENTO
        $this->add(221, 222, Util::formatCnab('X', $this->getPagador()->getUf(), 2)); //ESTADO - SIGLA DO ESTADO
        $this->add(223, 230, '');
        $this->add(231, 240, '0');

        return $this;
    }
    /**
     * @return $this
     * @throws \Exception
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
        $this->add(60, 230, '');
        $this->add(231, 240, '0'); //OCORRENCIA

        return $this;
    }
    /**
     * @return $this
     * @throws \Exception
     */
    protected function trailer()
    {
        $this->iniciaTrailer();

        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '9999');
        $this->add(8, 8, '9');
        $this->add(9, 17, '');
        $this->add(18, 23, Util::formatCnab('9', 1, 6));
        $this->add(24, 29, Util::formatCnab('9', $this->getCount(), 6));
        $this->add(30, 35, '000000');   //Deve ser informado zeros (exclusivo para conciliação bancária)
        $this->add(36, 240, '');

        return $this;
    }
}
