<?php

namespace VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\AbstractRetorno;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\RetornoCnab240;
use VinicciusGuedes\LaravelCnab\Util;
use Illuminate\Support\Arr;

class Santander extends AbstractRetorno implements RetornoCnab240
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SANTANDER;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '00' => 'Crédito ou Débito Efetivado',
        '01' => 'Insuficiência de Fundos - Débito Não Efetuado',
        '02' => 'Crédito ou Débito Cancelado pelo Pagador/Credor',
        '03' => 'Débito Autorizado pela Agência - Efetuado',
        'AA' => 'Controle Inválido',
        'AB' => 'Tipo de Operação Inválido',
        'AC' => 'Tipo de Serviço Inválido',
        'AD' => 'Forma de Lançamento Inválida',
        'AE' => 'Tipo/Número de Inscrição Inválido (gerado na crítica ou para informar rejeição)*',
        'AF' => 'Código de Convênio Inválido',
        'AG' => 'Agência/Conta Corrente/DV Inválido',
        'AH' => 'Número Sequencial do Registro no Lote Inválido',
        'AI' => 'Código de Segmento de Detalhe Inválido',
        'AJ' => 'Tipo de Movimento Inválido',
        'AK' => 'Código da Câmara de Compensação do Banco do Favorecido/Depositário Inválido',
        'AL' => 'Código do Banco do Favorecido, Instituição de Pagamento ou Depositário Inválido',
        'AM' => 'Agência Mantenedora da Conta Corrente do Favorecido Inválida',
        'AN' => 'Conta Corrente/DV /Conta de Pagamento do Favorecido Inválido',
        'AO' => 'Nome do Favorecido não Informado',
        'AP' => 'Data Lançamento Inválida/Vencimento Inválido/Data de Pagamento não permitida.',
        'AQ' => 'Tipo/Quantidade da Moeda Inválido',
        'AR' => 'Valor do Lançamento Inválido/Divergente/Zerado',
        'AS' => 'Aviso ao Favorecido - Identificação Inválida',
        'AT' => 'Tipo/Número de Inscrição do Favorecido/Contribuinte Inválido',
        'AU' => 'Logradouro do Favorecido não Informado',
        'AV' => 'Número do Local do Favorecido não Informado',
        'AW' => 'Cidade do Favorecido não Informada',
        'AX' => 'CEP/Complemento do Favorecido Inválido',
        'AY' => 'Sigla do Estado do Favorecido Inválido',
        'AZ' => 'Código/Nome do Banco Depositário Inválido',
        'A1' => 'Sequencial De Arq. Diverge Do Esperado',
        'A2' => 'Finalidade Do Doc/Ted Invalida',
        'A3' => 'Banco/Tipo Registro/Cod Rem-Ret Invalido',
        'A4' => 'Registro Do Arquivo Remessa Não Identificado',
        'A5' => 'Registro Header Não Encontrado',
        'A6' => 'Registro Trailler Não Encontrado',
        'A7' => 'Remessa Sem Registros Detalhe',
        'A8' => 'Arquivo Com Mais De Um Registro Header',
        'A9' => 'Arquivo Com Mais De Um Registro Trailler',
        'BB' => 'Número do Documento Inválido(Seu Número)',
        'BC' => 'Nosso Número Invalido',
        'BD' => 'Inclusão Efetuada com Sucesso',
        'BE' => 'Alteração Efetuada com Sucesso',
        'BF' => 'Exclusão Efetuada com Sucesso',
        'BG' => 'Agência/Conta Impedida Legalmente',
        'B1' => 'Bloqueado Pendente de Autorização',
        'B3' => 'Bloqueado pelo cliente',
        'B4' => 'Bloqueado pela captura de título da cobrança',
        'B8' => 'Bloqueado pela Validação de Tributos',
        'CA' => 'Código de barras - Código do Banco Inválido',
        'CB' => 'Código de barras - Código da Moeda Inválido',
        'CC' => 'Código de barras - Dígito Verificador Geral Inválido',
        'CD' => 'Código de barras - Valor do Título Inválido',
        'CE' => 'Código de barras - Campo Livre Inválido',
        'CF' => 'Valor do Documento/Principal/menor que o mínimo Inválido',
        'CH' => 'Valor do Desconto Inválido',
        'CI' => 'Valor de Mora Inválido',
        'CJ' => 'Valor da Multa Inválido',
        'CK' => 'Valor do IR Inválido',
        'CL' => 'Valor do ISS Inválido',
        'CG' => 'Valor do Abatimento inválido',
        'CM' => 'Valor do IOF Inválido',
        'CN' => 'Valor de Outras Deduções Inválido',
        'CO' => 'Valor de Outros Acréscimos Inválido',
        'HA' => 'Lote Não Aceito',
        'HB' => 'Inscrição da Empresa Inválida para o Contrato',
        'HC' => 'Convênio com a Empresa Inexistente/Inválido para o Contrato',
        'HD' => 'Agência/Conta Corrente da Empresa Inexistente/Inválida para o Contrato',
        'HE' => 'Tipo de Serviço Inválido para o Contrato',
        'HF' => 'Conta Corrente da Empresa com Saldo Insuficiente',
        'HG' => 'Lote de Serviço fora de Sequência',
        'HH' => 'Lote de Serviço Inválido',
        'HI' => 'Arquivo não aceito',
        'HJ' => 'Tipo de Registro Inválido',
        'HL' => 'Versão de Layout Inválida',
        'HU' => 'Data / hora de Envio Inválida',
        'IA' => 'Pagamento exclusive em Cartório.',
        'IJ' => 'Competência ou Período de Referência ou Número da Parcela invalido',
        'IL' => 'Código Pagamento / Receita não numérico ou com zeros',
        'IM' => 'Município Invalido',
        'IN' => 'Número Declaração Invalido',
        'IO' => 'Número Etiqueta invalido',
        'IP' => 'Número Notificação invalido',
        'IQ' => 'Inscrição Estadual invalida',
        'IR' => 'Dívida Ativa Invalida',
        'IS' => 'Valor Honorários ou Outros Acréscimos invalido',
        'IT' => 'Período Apuração invalido',
        'IU' => 'Valor ou Percentual da Receita invalido',
        'IV' => 'Número referência invalida',
        'PA' => 'Pix não efetivado',
        'PB' => 'Transação interrompida devido a erro no PSP do Recebedor',
        'PC' => 'Número da conta transacional encerrada no PSP do Recebedor',
        'PD' => 'Tipo incorreto para a conta transacional especificada',
        'PE' => 'Tipo de transação não é suportado/autorizado na conta transacional especificada',
        'PF' => 'CPF/CNPJ do usuário recebedor não é consistente com o titular da conta transacional especificada',
        'PG' => 'CPF/CNPJ do usuário recebedor incorreto',
        'PH' => 'Ordem rejeitada pelo PSP do Recebedor',
        'PI' => 'ISPB do PSP do Pagador inválido ou inexistente',
        'PJ' => 'Chave não cadastrada no DICT',
        'PK' => 'Qr Code Invalido/Vencido',
        'PL' => 'Forma De Iniciação Invalida',
        'PM' => 'Chave inválida ou inválida para o Favorecido',
        'PN' => 'Chave De Pagamento Não Informada',
        'SC' => 'Validação parcial',
        'TA' => 'Lote não Aceito - Totais do Lote com Diferença',
        'W1' => 'Sequencial De Arq. Diverge Do Esperado',
        'WW' => 'Duplicidade De Sequencial De Arquivo',
        'XB' => 'Número de Inscrição do Contribuinte Inválido',
        'XC' => 'Código do Pagamento ou Competência ou Número de Inscrição Inválido',
        'XF' => 'Código do Pagamento, Competência não Numérico ou igual a zeros',
        'YA' => 'Título não Encontrado',
        'YB' => 'Identificação Registro Opcional Inválido',
        'YC' => 'Código Padrão Inválido',
        'YD' => 'Código de Ocorrência Inválido',
        'YE' => 'Complemento de Ocorrência Inválido',
        'YF' => 'Alegação já Informada',
        'ZA' => 'Transferência Devolvida',
        'ZB' => 'Transferência mesma titularidade não permitida',
        'ZC' => 'Código pagamento Tributo inválido',
        'ZD' => 'Competência Inválida',
        'ZE' => 'Título Bloqueado na base',
        'ZF' => 'Sistema em Contingência – Título com valor maior que referência',
        'ZG' => 'Sistema em Contingência – Título vencido (pagamento de cobrança) / Banco destino não Recebe DOC/Pix (pagamentos/transferências)',
        //'ZG' => 'Banco destino não Recebe DOC/Pix (pagamentos/transferências)',
        'ZH' => 'Sistema em contingência - Título indexado',
        'ZI' => 'Beneficiário divergente',
        'ZJ' => 'Limite de pagamentos parciais excedido',
        'ZK' => 'Título já liquidado',
        'ZT' => 'Valor “outras entidades” inválido',
        'ZU' => 'Sistema Origem Inválido',
        'ZW' => 'Banco Destino não recebe DOC',
        'ZX' => 'Banco Destino inoperante para DOC',
        'ZY' => 'Código do Histórico de Crédito Invalido',
        'ZV' => 'Autorização iniciada no Internet Banking',
        'Z0' => 'Conta com bloqueio*',
        'Z1' => 'Conta fechada. É necessário ativar a conta*',
        'Z2' => 'Conta com movimento controlado*',
        'Z3' => 'Conta cancelada*',
        'Z4' => 'Registro inconsistente (Título)*',
        'Z5' => 'Apresentação indevida (Título)*',
        'Z6' => 'Dados do destinatário inválidos*',
        'Z7' => 'Agência ou conta destinatária do crédito inválida*',
        'Z8' => 'Divergência na titularidade*',
        'Z9' => 'Conta destinatária do crédito encerrada*',
        '99' => 'Bloqueado Outros Motivos',
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
            ->setCodigoCedente($this->rem(33, 52, $header))
            ->setAgencia($this->rem(53, 57, $header))
            ->setAgenciaDv($this->rem(58, 58, $header))
            ->setConta($this->rem(59, 70, $header))
            ->setContaDv($this->rem(71, 71, $header))
            ->setNomeEmpresa($this->rem(73, 102, $header))
            ->setNomeBanco($this->rem(103, 132, $header))
            ->setCodigoRemessaRetorno($this->rem(143, 143, $header))
            ->setData($this->rem(144, 151, $header))
            ->setNumeroSequencialArquivo($this->rem(158, 163, $header))
            ->setVersaoLayoutArquivo($this->rem(164, 166, $header));

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
            ->setNumeroInscricao($this->rem(19, 32, $headerLote))
            ->setCodigoCedente($this->rem(33, 52, $headerLote))
            ->setAgencia($this->rem(53, 57, $headerLote))
            ->setAgenciaDv($this->rem(58, 58, $headerLote))
            ->setConta($this->rem(59, 70, $headerLote))
            ->setContaDv($this->rem(71, 71, $headerLote))
            ->setNomeEmpresa($this->rem(73, 102, $headerLote));
            //->setNumeroRetorno($this->rem(184, 191, $headerLote))
            //->setDataGravacao($this->rem(192, 199, $headerLote));

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

        if ($this->getSegmentType($detalhe) == 'J' && $this->getSegmentAndRegisterType($detalhe) !== 'J52') {
            $d->setOcorrencia($this->rem(16, 17, $detalhe))
                ->setOcorrenciaDescricao(Arr::get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
                ->setNossoNumero($this->rem(203, 222, $detalhe))
                //->setCarteira($this->rem(54, 54, $detalhe))
                //->setNumeroDocumento($this->rem(55, 69, $detalhe))
                ->setDataVencimento($this->rem(92, 99, $detalhe))
                ->setValor(Util::nFloat($this->rem(153, 167, $detalhe)/100, 2, false))
                ->setNumeroControle($this->rem(183, 202, $detalhe));
                /*->setPagador([
                    'nome' => $this->rem(144, 183, $detalhe),
                    'documento' => $this->rem(129, 143, $detalhe),
                ])*/
                //->setValorTarifa(Util::nFloat($this->rem(194, 208, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $msgAdicional = str_split(sprintf('%08s', $this->rem(231, 240, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('00','14')) { //Valiação referente aos campos 16 e 17 Do J
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('09')) {
                $this->totais['entradas']++;
                if(array_search('a4', array_map('strtolower', $msgAdicional)) !== false) {
                    $d->getPagador()->setDda(true);
                }
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
            } elseif ($d->hasOcorrencia('99')) {
                $this->totais['baixados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('98')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('10','11')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('33')) {
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

        if ($this->getSegmentType($detalhe) == 'Y') {
            $d->setCheques([
                '1' => $this->rem(20, 53, $detalhe),
                '2' => $this->rem(44, 87, $detalhe),
                '3' => $this->rem(88, 121, $detalhe),
                '4' => $this->rem(122, 155, $detalhe),
                '5' => $this->rem(156, 189, $detalhe),
                '6' => $this->rem(190, 223, $detalhe),
            ]);
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
            //->setQtdTitulosCobrancaSimples((int) $this->rem(24, 29, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(24, 41, $trailer)/100, 2, false));
            //->setQtdTitulosCobrancaVinculada((int) $this->rem(47, 52, $trailer))
            //->setValorTotalTitulosCobrancaVinculada(Util::nFloat($this->rem(53, 69, $trailer)/100, 2, false))
            //->setQtdTitulosCobrancaCaucionada((int) $this->rem(70, 75, $trailer))
            //->setValorTotalTitulosCobrancaCaucionada(Util::nFloat($this->rem(76, 92, $trailer)/100, 2, false))
            //->setQtdTitulosCobrancaDescontada((int) $this->rem(93, 98, $trailer))
            //->setValorTotalTitulosCobrancaDescontada(Util::nFloat($this->rem(99, 115, $trailer)/100, 2, false))
            //->setNumeroAvisoLancamento($this->rem(116, 123, $trailer));

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
