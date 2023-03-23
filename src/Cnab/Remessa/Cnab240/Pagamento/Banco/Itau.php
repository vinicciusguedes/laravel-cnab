<?php
/**
 * Versão CNAB 085
 */

namespace VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\CalculoDV;
use VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\AbstractRemessa;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\Remessa as RemessaContract;
use VinicciusGuedes\LaravelCnab\Util;

class Itau extends AbstractRemessa implements RemessaContract
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
    const OCORRENCIA_NAO_CONCORDA_SACADO = '38';
    const OCORRENCIA_DISPENSA_JUROS = '47';
    const OCORRENCIA_ALT_DADOS_EXTRAS = '49';
    const OCORRENCIA_ENT_NEGATIVACAO = '66';
    const OCORRENCIA_NAO_NEGATIVAR = '67';
    const OCORRENCIA_EXC_NEGATIVACAO = '68';
    const OCORRENCIA_CANC_NEGATIVACAO = '69';
    const OCORRENCIA_DESCONTAR_TITULOS_DIA = '93';

    const PROTESTO_SEM = '0';
    const PROTESTO_DIAS_CORRIDOS = '1';
    const PROTESTO_DIAS_UTEIS = '2';
    const PROTESTO_NAO_PROTESTAR = '3';
    const PROTESTO_NEGATIVAR_DIAS_CORRIDOS = '7';
    const PROTESTO_NAO_NEGATIVAR = '8';

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_ITAU;


    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */
    protected $carteiras = ['112', '115', '188', '109', '121', '175'];

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
        if($boleto->getPixQrCode()) {
            $this->segmentoJ52Pix($boleto);
        }
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

        $ipte['dv'] = Util::formatCnab('9', $this->getContaDv(), 1);
        $ipte['fator_vencimento'] = Util::fatorVencimento($boleto->getDataVencimento());
        $ipte['valor'] = Util::formatCnab('9', $boleto->getValor(), 13, 2);
        $ipte['campo_livre'] = '';
        if(!empty($boleto->getCodigoBarrasInserido())) {
            $ipte = Util::IPTE2Variveis($boleto->getCodigoBarrasInserido());
        }

        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '3');
        $this->add(9, 13, Util::formatCnab('9', $this->iRegistrosLote, 5));
        $this->add(14, 14, 'J');
        $this->add(15, 17, '000'); //TIPO DE MOVIMENTO
        $this->add(18, 20, Util::onlyNumbers($boleto->getCodigoBanco())); //BANCO FAVORECIDO - CÓD. DE BARRAS – CÓDIGO BANCO FAVORECIDO
        $this->add(21, 21, Util::formatCnab('9', $boleto->getMoeda(), 1)); //MOEDA - CÓD. DE BARRAS – CÓDIGO DA MOEDA
        $this->add(22, 22, Util::formatCnab('9', $ipte['dv'], 1)); //DV - CÓD. DE BARRAS – DÍGITO VERIF. DO CÓD. BARRAS
        $this->add(23, 26, Util::formatCnab('9', $ipte['fator_vencimento'], 4)); //VENCIMENTO - CÓD. DE BARRAS – FATOR DE VENCIMENTO
        $this->add(27, 36, Util::formatCnab('9', $ipte['valor'], 8, 2)); //VALOR - CÓD. DE BARRAS – VALOR
        $this->add(37, 61, Util::formatCnab('9', $ipte['campo_livre'], 25)); //CAMPO LIVRE CÓD. DE BARRAS - 'CAMPO LIVRE'
        $this->add(62, 91, Util::formatCnab('X', $boleto->getBeneficiario()->getNome(), 30)); // NOME DO FAVORECIDO
        $this->add(92, 99, $boleto->getDataVencimento()->format('dmY')); //DATA DO VENCIMENTO (NOMINAL)
        $this->add(100, 114, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO TÍTULO (NOMINAL)
        $this->add(115, 129, Util::formatCnab('9', $boleto->getDesconto(), 13, 2)); //DESCONTOS - VALOR DO DESCONTO + ABATIMENTO
        $this->add(130, 144, Util::formatCnab('9', $boleto->getMulta(), 13, 2)); //ACRÉSCIMOS - VALOR DA MORA + MULTA
        $this->add(145, 152, $boleto->getDataVencimento()->format('dmY')); //DATA DO PAGAMENTO
        $this->add(153, 167, Util::formatCnab('9', $boleto->getValor(), 13, 2)); //VALOR DO PAGAMENTO
        $this->add(168, 182, Util::formatCnab('9', 0, 15));
        $this->add(183, 202, Util::formatCnab('X', $boleto->getNumeroDocumento(),20)); //SEU NÚMERO - Nº DOCTO ATRIBUÍDO PELA EMPRESA
        $this->add(203, 215, Util::formatCnab('X', '', 13));
        $this->add(216, 230, Util::formatCnab('X', $boleto->getNossoNumero(), 15)); //NOSSO NÚMERO - NÚMERO ATRIBUÍDO PELO BANCO
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
        $this->add(15, 17, '000'); //TIPO DE MOVIMENTO
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
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
     */
    public function segmentoJ52Pix(BoletoContract $boleto)
    {
        $this->iniciaDetalhe();
        $this->add(1, 3, Util::onlyNumbers($this->getCodigoBanco()));
        $this->add(4, 7, '0001');
        $this->add(8, 8, '3');
        $this->add(9, 13, Util::formatCnab('9', $this->iRegistrosLote, 5));
        $this->add(14, 14, 'J');
        $this->add(15, 17, '000'); //TIPO DE MOVIMENTO
        $this->add(18, 19, '52'); //IDENTIFICAÇÃO DO REGISTRO OPCIONAL
        $this->add(20, 20, strlen(Util::onlyNumbers($boleto->getPagador()->getDocumento())) == 14 ? 2 : 1); //TIPO DE INSCRIÇÃO DO SACADO 1=CPF|2=CNPJ
        $this->add(21, 35, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getDocumento()), 15)); //NÚMERO DE INSCRIÇÃO DO SACADO
        $this->add(36, 75, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40)); //NOME DO SACADO
        $this->add(76, 76, strlen(Util::onlyNumbers($boleto->getBeneficiario()->getDocumento())) == 14 ? 2 : 1); //TIPO DE INSCRIÇÃO DO CEDENTE 1=CPF|2=CNPJ
        $this->add(77, 91, Util::formatCnab('9', Util::onlyNumbers($boleto->getBeneficiario()->getDocumento()), 15)); //NÚMERO DE INSCRIÇÃO DO CEDENTE
        $this->add(92, 131, Util::formatCnab('X', $boleto->getBeneficiario()->getNome(), 40)); //NOME DO CEDENTE
        $this->add(132, 208, Util::formatCnab('X', $boleto->getPixQrCode(), 77)); //CHAVE DE PAGAMENTO - URL / CHAVE PIX
        $this->add(209, 240, Util::formatCnab('X', $boleto->getPixTxid(), 32)); //TXID - CÓDIGO DE IDENTIFICAÇÃO DO QR-CODE

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
        $this->add(9, 14, '');
        $this->add(15, 17, '80'); //LAYOUT DE ARQUIVO - NUM DA VERSÃO DO LAYOUT DO ARQUIVO
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getPagador()->getDocumento())) == 14 ? 2 : 1); //EMPRESA – INSCRIÇÃO - TIPO DE INSCRIÇÃO DA EMPRESA
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getPagador()->getDocumento()), 14)); //INSCRIÇÃO NÚMERO - CNPJ EMPRESA DEBITADA
        $this->add(33, 52, '');
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5)); //AGÊNCIA - NÚMERO AGÊNCIA DEBITADA
        $this->add(58, 58, '');
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12)); //CONTA - NÚMERO DE C/C DEBITADA
        $this->add(71, 71, '');
        $this->add(72, 72, CalculoDV::itauContaCorrente($this->getAgencia(), $this->getConta())); //DAC - DAC DA AGÊNCIA/CONTA DEBITADA
        $this->add(73, 102, Util::formatCnab('X', $this->getPagador()->getNome(), 30)); //NOME DA EMPRESA
        $this->add(103, 132, Util::formatCnab('X', Util::$bancos[Util::onlyNumbers($this->getCodigoBanco())], 30)); //NOME DO BANCO

        $this->add(133, 142, '');
        $this->add(143, 143, 1); //ARQUIVO-CÓDIGO - CÓDIGO 1:REMESSA/2:RETORNO
        $this->add(144, 151, $this->getDataRemessa('dmY')); //DATA DE GERAÇÃO DO ARQUIVO
        $this->add(152, 157, $this->getHoraRemessa('His')); //HORA DE GERAÇÃO DO ARQUIVO
        $this->add(158, 166, Util::formatCnab('9', 0, 9));
        $this->add(167, 171, '0'); //UNIDADE DE DENSIDADE - DENSIDADE DE GRAVAÇÃO DO ARQUIVO
        $this->add(172, 240, '');

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
        $this->add(14, 16, '030');
        $this->add(17, 17, '');
        $this->add(18, 18, strlen(Util::onlyNumbers($this->getPagador()->getDocumento())) == 14 ? 2 : 1); //EMPRESA - INSCRIÇÃO TIPO INSCRIÇÃO EMPRESA DEBITADA
        $this->add(19, 32, Util::formatCnab('9', Util::onlyNumbers($this->getPagador()->getDocumento()), 14)); //INSCRIÇÃO NÚMERO - CNPJ EMPRESA OU CPF DEBITADO
        $this->add(33, 52, '');
        $this->add(53, 57, Util::formatCnab('9', $this->getAgencia(), 5)); //AGÊNCIA - NÚMERO AGÊNCIA DEBITADA
        $this->add(58, 58, '');
        $this->add(59, 70, Util::formatCnab('9', $this->getConta(), 12)); //CONTA - NÚMERO DE C/C DEBITADA
        $this->add(71, 71, '');
        $this->add(72, 72, Util::formatCnab('9', $this->getContaDv() ?: CalculoDV::itauContaCorrente($this->getConta()), 1)); //DAC - DAC DA AGÊNCIA/CONTA DEBITADA
        $this->add(73, 102, Util::formatCnab('X', $this->getPagador()->getNome(), 30)); //NOME DA EMPRESA - NOME DA EMPRESA DEBITADA
        $this->add(103, 132, ''); //FINALIDADE DO LOTE - FINALIDADE DOS PAGTOS DO LOTE
        $this->add(133, 142, ''); //HISTÓRICO DE C/C - COMPLEMENTO HISTÓRICO C/C DEBITADA
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
        $this->add(30, 240, '');

        return $this;
    }
}
