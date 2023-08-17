<?php

namespace VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\AbstractRetorno;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\RetornoCnab240;
use VinicciusGuedes\LaravelCnab\Util;
use Illuminate\Support\Arr;

class Itau extends AbstractRetorno implements RetornoCnab240
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_ITAU;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '00' => 'PAGAMENTO EFETUAD',
        'AE' => 'DATA DE PAGAMENTO ALTERAD',
        'AG' => 'NÚMERO DO LOTE INVÁLID',
        'AH' => 'NÚMERO SEQUENCIAL DO REGISTRO NO LOTE INVÁLID',
        'AI' => 'PRODUTO DEMONSTRATIVO DE PAGAMENTO NÃO CONTRATAD',
        'AJ' => 'TIPO DE MOVIMENTO INVÁLID',
        'AL' => 'CÓDIGO DO BANCO FAVORECIDO INVÁLID',
        'AM' => 'AGÊNCIA DO FAVORECIDO INVÁLID',
        'AN' => 'CONTA CORRENTE DO FAVORECIDO INVÁLID',
        'AO' => 'NOME DO FAVORECIDO INVÁLID',
        'AP' => 'DATA DE PAGAMENTO / DATA DE VALIDADE / HORA DE LANÇAMENTO / ARRECADAÇÃO / APURAÇÃO INVÁLID',
        'AQ' => 'QUANTIDADE DE REGISTROS MAIOR QUE 99999',
        'AR' => 'VALOR ARRECADADO / LANÇAMENTO INVÁLID',
        'BC' => 'NOSSO NÚMERO INVÁLID',
        'BD' => 'PAGAMENTO AGENDAD',
        'BE' => 'PAGAMENTO AGENDADO COM FORMA ALTERADA PARA O',
        'BI' => 'CNPJ / CPF DO FAVORECIDO NO SEGMENTO J-52 ou B INVÁLIDO / DOCUMENTO FAVORECIDO INVÁLIDO PI',
        'BL' => 'VALOR DA PARCELA INVÁLID',
        'CD' => 'CNPJ / CPF INFORMADO DIVERGENTE DO CADASTRAD',
        'CE' => 'PAGAMENTO CANCELAD',
        'CF' => 'VALOR DO DOCUMENTO INVÁLIDO / VALOR DIVERGENTE DO QR COD',
        'CG' => 'VALOR DO ABATIMENTO INVÁLID',
        'CH' => 'VALOR DO DESCONTO INVÁLID',
        'CI' => 'CNPJ / CPF / IDENTIFICADOR / INSCRIÇÃO ESTADUAL / INSCRIÇÃO NO CAD / ICMS INVÁLID',
        'CJ' => 'VALOR DA MULTA INVÁLID',
        'CK' => 'TIPO DE INSCRIÇÃO INVÁLID',
        'CL' => 'VALOR DO INSS INVÁLID',
        'CM' => 'VALOR DO COFINS INVÁLID',
        'CN' => 'CONTA NÃO CADASTRAD',
        'CO' => 'VALOR DE OUTRAS ENTIDADES INVÁLID',
        'CP' => 'CONFIRMAÇÃO DE OP CUMPRID',
        'CQ' => 'SOMA DAS FATURAS DIFERE DO PAGAMENT',
        'CR' => 'VALOR DO CSLL INVÁLID',
        'CS' => 'DATA DE VENCIMENTO DA FATURA INVÁLID',
        'DA' => 'NÚMERO DE DEPEND. SALÁRIO FAMILIA INVALID',
        'DB' => 'NÚMERO DE HORAS SEMANAIS INVÁLID',
        'DC' => 'SALÁRIO DE CONTRIBUIÇÃO INSS INVÁLID',
        'DD' => 'SALÁRIO DE CONTRIBUIÇÃO FGTS INVÁLID',
        'DE' => 'VALOR TOTAL DOS PROVENTOS INVÁLID',
        'DF' => 'VALOR TOTAL DOS DESCONTOS INVÁLID',
        'DG' => 'VALOR LÍQUIDO NÃO NUMÉRIC',
        'DH' => 'VALOR LIQ. INFORMADO DIFERE DO CALCULAD',
        'DI' => 'VALOR DO SALÁRIO-BASE INVÁLID',
        'DJ' => 'BASE DE CÁLCULO IRRF INVÁLID',
        'DK' => 'BASE DE CÁLCULO FGTS INVÁLID',
        'DL' => 'FORMA DE PAGAMENTO INCOMPATÍVEL COM HOLERIT',
        'DM' => 'E-MAIL DO FAVORECIDO INVÁLID',
        'DV' => 'DOC / TED DEVOLVIDO PELO BANCO FAVORECID',
        'D0' => 'FINALIDADE DO HOLERITE INVÁLID',
        'D1' => 'MÊS DE COMPETENCIA DO HOLERITE INVÁLID',
        'D2' => 'DIA DA COMPETENCIA DO HOLETITE INVÁLID',
        'D3' => 'CENTRO DE CUSTO INVÁLID',
        'D4' => 'CAMPO NUMÉRICO DA FUNCIONAL INVÁLID',
        'D5' => 'DATA INÍCIO DE FÉRIAS NÃO NUMÉRIC',
        'D6' => 'DATA INÍCIO DE FÉRIAS INCONSISTENT',
        'D7' => 'DATA FIM DE FÉRIAS NÃO NUMÉRIC',
        'D8' => 'DATA FIM DE FÉRIAS INCONSISTENT',
        'D9' => 'NÚMERO DE DEPENDENTES IR INVÁLID',
        'EM' => 'CONFIRMAÇÃO DE OP EMITID',
        'EX' => 'DEVOLUÇÃO DE OP NÃO SACADA PELO FAVORECID',
        'E0' => 'TIPO DE MOVIMENTO HOLERITE INVÁLID',
        'E1' => 'VALOR 01 DO HOLERITE / INFORME INVÁLID',
        'E2' => 'VALOR 02 DO HOLERITE / INFORME INVÁLID',
        'E3' => 'VALOR 03 DO HOLERITE / INFORME INVÁLID',
        'E4' => 'VALOR 04 DO HOLERITE / INFORME INVÁLID',
        'FC' => 'PAGAMENTO EFETUADO ATRAVÉS DE FINANCIAMENTO COMPRO',
        'FD' => 'PAGAMENTO EFETUADO ATRAVÉS DE FINANCIAMENTO DESCOMPRO',
        'HÁ' => 'ERRO NO LOT',
        'HM' => 'ERRO NO REGISTRO HEADER DE ARQUIV',
        'IB' => 'VALOR DO DOCUMENTO INVÁLID',
        'IC' => 'VALOR DO ABATIMENTO INVÁLID',
        'ID' => 'VALOR DO DESCONTO INVÁLID',
        'IE' => 'VALOR DA MORA INVÁLID',
        'IF' => 'VALOR DA MULTA INVÁLID',
        'IG' => 'VALOR DA DEDUÇÃO INVÁLID',
        'IH' => 'VALOR DO ACRÉSCIMO INVÁLID',
        'II' => 'DATA DE VENCIMENTO INVÁLIDA / QR CODE EXPIRAD',
        'IJ' => 'COMPETÊNCIA / PERÍODO REFERÊNCIA / PARCELA INVÁLID',
        'IK' => 'TRIBUTO NÃO LIQUIDÁVEL VIA SISPAG OU NÃO CONVENIADO COM ITA',
        'IL' => 'CÓDIGO DE PAGAMENTO / EMPRESA /RECEITA INVÁLID',
        'IM' => 'TIPO X FORMA NÃO COMPATÍVE',
        'IN' => 'BANCO/AGÊNCIA NÃO CADASTRADO',
        'IO' => 'DAC / VALOR / COMPETÊNCIA / IDENTIFICADOR DO LACRE INVÁLIDO / IDENTIFICAÇÃO DO QR CODE INVÁLID',
        'IP' => 'DAC DO CÓDIGO DE BARRAS INVÁLIDO / ERRO NA VALIDAÇÃO DO QR COD',
        'IQ' => 'DÍVIDA ATIVA OU NÚMERO DE ETIQUETA INVÁLID',
        'IR' => 'PAGAMENTO ALTERAD',
        'IS' => 'CONCESSIONÁRIA NÃO CONVENIADA COM ITA',
        'IT' => 'VALOR DO TRIBUTO INVÁLID',
        'IU' => 'VALOR DA RECEITA BRUTA ACUMULADA INVÁLID',
        'IV' => 'NÚMERO DO DOCUMENTO ORIGEM / REFERÊNCIA INVÁLID',
        'IX' => 'CÓDIGO DO PRODUTO INVÁLID',
        'LA' => 'DATA DE PAGAMENTO DE UM LOTE ALTERAD',
        'LC' => 'LOTE DE PAGAMENTOS CANCELAD',
        'NA' => 'PAGAMENTO CANCELADO POR FALTA DE AUTORIZAÇÃ',
        'NB' => 'IDENTIFICAÇÃO DO TRIBUTO INVÁLID',
        'NC' => 'EXERCÍCIO (ANO BASE) INVÁLID',
        'ND' => 'CÓDIGO RENAVAM NÃO ENCONTRADO/INVÁLID',
        'NE' => 'UF INVÁLID',
        'NF' => 'CÓDIGO DO MUNICÍPIO INVÁLID',
        'NG' => 'PLACA INVÁLID',
        'NH' => 'OPÇÃO/PARCELA DE PAGAMENTO INVÁLID',
        'NI' => 'TRIBUTO JÁ FOI PAGO OU ESTÁ VENCID',
        'NR' => 'OPERAÇÃO NÃO REALIZAD',
        'PD' => 'AQUISIÇÃO CONFIRMADA (EQUIVALE A OCORRÊNCIA 02 NO LAYOUT DE RISCO SACADO',
        'RJ' => 'REGISTRO REJEITAD',
        'RS' => 'PAGAMENTO DISPONÍVEL PARA ANTECIPAÇÃO NO RISCO SACADO – MODALIDADE RISCO SACADO PÓS AUTORIZAD',
        'SS' => 'PAGAMENTO CANCELADO POR INSUFICIÊNCIA DE SALDO / LIMITE DIÁRIO DE PAGTO EXCEDID',
        'TA' => 'LOTE NÃO ACEITO - TOTAIS DO LOTE COM DIFERENÇ',
        'TI' => 'TITULARIDADE INVÁLID',
        'X1' => 'FORMA INCOMPATÍVEL COM LAYOUT 01',
        'X2' => 'NÚMERO DA NOTA FISCAL INVÁLID',
        'X3' => 'IDENTIFICADOR DE NF/CNPJ INVÁLID',
        'X4' => 'FORMA 32 INVÁLIDA',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [

    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados' => 0,
            'entradas' => 0,
            'baixados' => 0,
            'protestados' => 0,
            'erros' => 0,
            'alterados' => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setCodBanco($this->rem(1, 3, $header))
            ->setLoteServico($this->rem(4, 7, $header))
            ->setTipoRegistro($this->rem(8, 8, $header))
            ->setTipoInscricao($this->rem(18, 18, $header))
            ->setNumeroInscricao($this->rem(19, 32, $header))
            ->setAgencia($this->rem(54, 57, $header))
            ->setConta($this->rem(66, 70, $header))
            ->setContaDv($this->rem(72, 72, $header))
            ->setNomeEmpresa($this->rem(73, 102, $header))
            ->setNomeBanco($this->rem(103, 132, $header))
            ->setCodigoRemessaRetorno($this->rem(143, 143, $header))
            ->setData($this->rem(144, 151, $header));
            //->setNumeroSequencialArquivo($this->rem(158, 163, $header))
            //->setVersaoLayoutArquivo($this->rem(164, 166, $header));

        return true;
    }

    /**
     * @param array $headerLote
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeaderLote(array $headerLote)
    {
        $this->getHeaderLote()
            ->setCodBanco($this->rem(1, 3, $headerLote))
            ->setNumeroLoteRetorno($this->rem(4, 7, $headerLote))
            ->setTipoRegistro($this->rem(8, 8, $headerLote))
            ->setTipoOperacao($this->rem(9, 9, $headerLote))
            ->setTipoServico($this->rem(10, 11, $headerLote))
            ->setVersaoLayoutLote($this->rem(14, 16, $headerLote))
            ->setTipoInscricao($this->rem(18, 18, $headerLote))
            ->setNumeroInscricao($this->rem(19, 32, $headerLote))
            ->setAgencia($this->rem(53, 57, $headerLote))
            ->setConta($this->rem(59, 70, $headerLote))
            ->setContaDv($this->rem(72, 72, $headerLote))
            ->setNomeEmpresa($this->rem(73, 102, $headerLote));
            //->setNumeroRetorno($this->rem(184, 191, $headerLote))
            //->setDataGravacao($this->rem(192, 199, $headerLote))
            //->setDataCredito($this->rem(200, 207, $headerLote));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();

        if ($this->getSegmentType($detalhe) == 'J') {
            $d->setOcorrencia($this->rem(15, 17, $detalhe))
                ->setOcorrenciaDescricao(Arr::get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
                ->setNossoNumero($this->rem(216, 230, $detalhe))
                //->setCarteira($this->rem(38, 40, $detalhe))
                //->setNumeroDocumento($this->rem(59, 68, $detalhe))
                ->setDataVencimento($this->rem(92, 99, $detalhe))
                ->setValor(Util::nFloat($this->rem(153, 167, $detalhe)/100, 2, false))
                ->setNumeroControle($this->rem(183, 202, $detalhe));
                /*->setPagador([
                    'nome' => $this->rem(149, 188, $detalhe),
                    'documento' => $this->rem(134, 148, $detalhe),
                ])*/
                //->setValorTarifa(Util::nFloat($this->rem(199, 213, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $msgAdicional = str_split(sprintf('%08s', $this->rem(231, 240, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('000')) { //Valiação referente aos campos 16 e 17 Do J
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('004')) {
                $this->totais['entradas']++;
                if(array_search('a4', array_map('strtolower', $msgAdicional)) !== false) {
                    $d->getPagador()->setDda(true);
                }
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
            } elseif ($d->hasOcorrencia('099')) {
                $this->totais['baixados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('098')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('512','517','519')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('998','999')) {
                $this->totais['erros']++;
                $error = Util::appendStrings(
                    Arr::get($this->ocorrencias, $msgAdicional[0], ''),
                    Arr::get($this->ocorrencias, $msgAdicional[1], ''),
                    Arr::get($this->ocorrencias, $msgAdicional[2], ''),
                    Arr::get($this->ocorrencias, $msgAdicional[3], ''),
                    Arr::get($this->ocorrencias, $msgAdicional[4], '')
                );
                $d->setError($error);
            } else {
                $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
            }

            $ocorrenciaArray[$msgAdicional[0]] = Arr::get($this->ocorrencias, $msgAdicional[0], 'Código Inválido');
            $ocorrenciaArray[$msgAdicional[1]] = Arr::get($this->ocorrencias, $msgAdicional[1], 'Código Inválido');
            $ocorrenciaArray[$msgAdicional[2]] = Arr::get($this->ocorrencias, $msgAdicional[2], 'Código Inválido');
            $ocorrenciaArray[$msgAdicional[3]] = Arr::get($this->ocorrencias, $msgAdicional[3], 'Código Inválido');
            $ocorrenciaArray[$msgAdicional[4]] = Arr::get($this->ocorrencias, $msgAdicional[4], 'Código Inválido');
            $d->setOcorrenciaArray($ocorrenciaArray);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailerLote(array $trailer)
    {
        $this->getTrailerLote()
            ->setLoteServico($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdRegistroLote((int) $this->rem(18, 23, $trailer))
            //->setQtdTitulosCobrancaSimples((int) $this->rem(24, 41, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(24, 41, $trailer)/100, 2, false));
            //->setQtdTitulosCobrancaVinculada((int) $this->rem(47, 52, $trailer))
            //->setValorTotalTitulosCobrancaVinculada(Util::nFloat($this->rem(53, 69, $trailer)/100, 2, false));

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setNumeroLote($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdLotesArquivo((int) $this->rem(18, 23, $trailer))
            ->setQtdRegistroArquivo((int) $this->rem(24, 29, $trailer));

        return true;
    }
}
