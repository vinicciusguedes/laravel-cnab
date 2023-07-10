<?php

namespace VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Pagamento\Banco;

use VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\AbstractRetorno;
use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\RetornoCnab240;
use VinicciusGuedes\LaravelCnab\Util;
use Illuminate\Support\Arr;

class Bradesco extends AbstractRetorno implements RetornoCnab240
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BRADESCO;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '00' => "Crédito ou Débito Efetivado - Este códigoindicaque o pagamento foi confirmado - Compromisso efetivamente pago/liquidado. Este código indica que o pagamento foi confirmado",
        '01' => "Insuficiência de Fundos - Débito Não Efetuado - Seu pagamento não pode ser efetivado por não possuir saldo disponível suficiente em sua conta corrente.",
        '02' => "Crédito ou Débito Cancelado pelo Pagador/Credor - Compromisso agendado foi cancelado.",
        '03' => "Débito Autorizado pela Agência – Efetuado -Pagamento foi autorizado e efetivado pela agência",
        'AA' => "Controle Inválido - Trata-se de movimento processado com Data e Hora de Gravação de outro movimento já processado pelo Sistema (Arquivo duplicado). Campos de controle do arquivo de Remessa (Banco, Lote, Registro) inválidos.",
        'AB' => "Tipo de Operação Inválido - Verificar coluna ‘9’ do header de lote. Para pagamento a fornecedor, tributos e pagamento de títulos, devera conter fixo ‘C’. Verificar as posições 223 a 224 do header de lote devera conter o parâmetro fixo '01'.",
        'AC' => "Tipo de Serviço Inválido - Código do tipo serviço diferente dos utilizados, ou tipo de serviço incompatível à forma de pagamento. Verificar serviço informado nas posições de 10 a 11 no header de lote (no layout G025)",
        'AD' => "Forma de Lançamento Inválida - Código do tipo serviço diferente dos utilizados, ou tipo de serviço incompatível à forma de pagamento. Verificar serviço informado nas posições de 10 a 11 no header de lote (no layout G025)",
        'AE' => "Tipo/Número de Inscrição Inválido Verificar a posição 18 nas linhas de header de arquivo e header de lote o campo tipo de inscrição (1- CPF, 2 - CNPJ, 3 - PIS, 9 - Outros). Das posições de 19 a 32 se foi preenchido conforme o tipo de inscrição informado.",
        'AF' => "Código de Convênio Inválido - Verificar nas linhas de header de arquivo e header de lote, da posição 33 a 52 se esta preenchida com o código de convênio conforme cadastro junto ao Banco.",
        'AG' => "Agência/Conta Corrente/DV Inválido -Verificar agência e conta de débito nas linhas de header de arquivo e header de lote o código da agência nas posições de 53 a 57 (mantendo “0” a esquerda. Ex.: 01111) dígito da agência deve ser informado na posição 58, número da conta das posições de 59 a 70 com dígito na posição 71. Para mais detalhes verificar no layout G008, G009, G010 e G011.",
        'AH' => "Nº Sequencial do Registro no Lote Inválido Verificar as posições de 9 a 13 em cada segmento do arquivo, a sequência deve ser numérica e em ordem crescente (Ex: 00001, 00002...), o sequencial deve começar sempre em 00001 em cada novo lote. Para mais detalhes verificar no layout G038.",
        'AI' => "Código de Segmento de Detalhe Inválido A sequencia deve ser numérica e ordem crescente (ex: 00001, 00002 ...) o sequencial deve começar sempre em 00001 em cada novo lote.",
        'AJ' => "Tipo de Movimento Inválido - Verificar qual o código do tipo de movimento foi informado na posição 15 em cada um dos segmentos do arquivo. Nota: '0'= Inclusão, '5'= Alteração, '9'= Exclusão. Demais códigos e detalhes verificar no layout G060. Verificar se o tópico de receita está zerado na posição de 111 a 116 na remessa para o Segmento N. Verificar se o código de barras desse tributo já não foi pago anteriormente.",
        'AK' => "Código da Câmara de Compensação do Banco Favorecido/Depositário Inválido - Preencher com o código da Câmara Centralizadora para envio. '018' para TED (STR, CIP), '700' para DOC (COMPE). Outras modalidades preencher com zeros. Nas linhas de segmento 'A' do arquivo coluna 18 a 20.",
        'AL' => "Código do Banco Favorecido Inoperante nesta data ou Depositário Inválido - Banco favorecido informado está inválido, verificar nas posições de 21 a 23 nas linhas de segmento 'A'. Campos devem ser numéricos. Para mais detalhes verificar no layout P002 ",
        'AM' => "Agência Mantenedora da Conta Corrente do Favorecido Inválida - Verificar numero da agência do favorecido, poderá estar invalido ou deslocado nas linhas de detalhe no segmento 'A', verificar nas posições de 24 a 28 sendo o digito verificador da agencia na posição 29.",
        'AN' => "Conta Corrente/DV do Favorecido Inválido - Número da conta do favorecido poderá estar inválido ou deslocado nas linhas de detalhe no segmento 'A', verificar nas posições de 30 a 41 sendo o digito verificador na posição 42. Nota: conta do favorecido também poderá estar encerrada ou bloqueada",
        'AO' => "Nome do Favorecido Não Informado - Nome do credor/favorecido está totalmente em branco e o mesmo é obrigatório para esse pagamento.",
        'AP' => "Data Lançamento Inválido Pode ser um dos seguintes motivos: O campo Data (vencimento, lançamento, emissão ou processamento) está zerado, em branco, fora do padrão (DDMMAAAA) ou não numérico; A data informada é inferior à data base (data da leitura/processamento do arquivo), ou é igual à data base para pagamento via 'Crédito Administrativo'; A solicitação para cancelamento do compromisso a ser pago está fora do prazo limite; A data de desconto informada está incorreta ou maior que a data do vencimento do título; O horário do agendamento/liberação do compromisso ultrapassou o horário limite para efetuar consulta de saldo em sua conta corrente para pagamento do compromisso. O horário de inclusão ou liberação da TED ou Títulos de Outros Bancos 250K ultrapassou o horário limite para envio. O pagamento de contas, tributos e impostos não pode ser realizado por não ser permitido seu recebimento para esta data. Deverá contatar com o emissor da fatura para nova emissão. Para qualquer uma das ocorrências o compromisso deverá ser incluído novamente, porém, com a devida regularização.",
        'AQ' => "Tipo/Quantidade da Moeda Inválido - Código do tipo de moeda diferente das utilizadas ou quantidade de moeda não numérica ou zerada.",
        'AR' => "Valor do Lançamento Inválido - No segmento 'A' do arquivo de remessa verificar nas posições de 120 a 134 (valor do Pagamento), e nas posições de 163 a 177 (valor real da efetivação do pagamento). No segmento 'J' do arquivo de remessa verificar nas posições de 153 a 167, o valor informado para pagamento. No segmento 'O' do arquivo de remessa verificar das colunas 108 a 122, o valor informado para pagamento. No segmento 'N' do arquivo de remessa verificar das colunas 96 a 110, o valor informado para pagamento. Para mais detalhes verificar no layout P010 para os segmentos A ,J e N e P004 para segmento O.",
        'AT' => "Tipo/Número de Inscrição do Favorecido Inválido Verificar no segmento 'B' do arquivo remessa: Tipo de inscrição do favorecido na posição 18 sendo: (1- CPF, 2 - CNPJ, 3 - PIS, 9 - Outros), e nas posições de 19 a 32 se o preenchimento foi realizado de acordo com o tipo informado na posição 18. Para mais detalhes verificar nos layouts G005 e G006.",
        'AU' => "Logradouro do Favorecido Não Informado - Verificar no segmento 'B' do arquivo de remessa se nas posições de 33 a 62 contém o endereço do favorecido. Para mais ",
        'AV' => "Nº do Local do Favorecido Não Informado - Verificar no segmento 'B' do arquivo de remessa nas posições de 63 a 67, se foi informado o numero do local referente ao endereço do favorecido. Para mais detalhes verificar no layout G032.",
        'AW' => "Cidade do Favorecido Não Informada - Verificar no segmento 'B' do arquivo de remessa nas posições de 98 a 117, se foi informado o nome da cidade do favorecido. Para mais detalhes verificar no layout G033.",
        'AX' => "CEP/Complemento do Favorecido Inválido - Verificar no segmento 'B' do arquivo de remessa nas posições de 118 a 123, se foi informado o CEP do favorecido e nas posições de 123 a 125 se foi informado o complemento do CEP.",
        'AY' => "Sigla do Estado do Favorecido Inválida - Verificar no segmento 'B' do arquivo de remessa nas posições de 126 a 127, se foi informado à sigla do estado/UF do favorecido. Para mais detalhes verificar no layout G036.",
        'AZ' => "Código/Nome do Banco Depositário Inválido - Código do Banco favorecido encontra-se inválido, não numérico ou zerado.",
        'BA' => "Código/Nome da Agência Depositária Não Informado - Ocorrência para títulos rastreados ou DDA.",
        'BB' => "Seu Número Inválido - Inclusão de um compromisso que já se encontra cadastrado no sistema Multipag Bradesco (compromisso em duplicidade).",
        'BC' => "Nosso Número Inválido - O Nosso Número identificado para quitação de títulos encontra-se irregular.",
        'BD' => "Inclusão Efetuada com Sucesso - Pagamento agendado com sucesso, o mesmo pode esta autorizado ou desautorizado, na base do Banco.",
        'BE' => "Alteração Efetuada com Sucesso- Autorização de pagamento que esta na base do Banco Autorizado ou Desautorizado, via arquivo.",
        'BF' => "Exclusão Efetuada com Sucesso - Pagamento excluído com êxito",
        'BG' => "Agência/Conta Impedida Legalmente - A conta de crédito informada encontra-se impedida por determinação de meios legais, que impossibilitam a efetivação do pagamento.",
        'BH' => "Empresa não pagou salário‘BI’ = Falecimento do mutuário",
        'BJ' => "Empresa não enviou remessa do mutuário",
        'BK' => "Empresa não enviou remessa no vencimento",
        'BL' => "Valor da parcela inválida",
        'BM' => "Identificação do contrato inválida",
        'BN' => "Operação de Consignação Incluída com Sucesso",
        'BO' => "Operação de Consignação Alterada com Sucesso",
        'BP' => "Operação de Consignação Excluída com Sucesso",
        'BQ' => "Operação de Consignação Liquidada com Sucesso",
        'CA' => "Código de Barras - Código do Banco Inválido - Verificar nas posições de 18 a 61 no segmento J se o código de barras está invalido, deslocado ou em branco.",
        'CB' => "Código de Barras - Código da Moeda Inválido - Verificar nas posições de 18 a 61 no segmento J se o código de barras está invalido, deslocado ou em branco. Verificar também nas posições de 18 a 20 se código do banco está correto (Ex.°: Bradesco: 237). Nota: campo deve ser apenasNUMERICO. Para mais detalhes verificar layout G063.",
        'CC' => "Código de Barras - Dígito Verificador Geral Inválido - Informar ao cliente que o código de barras no segmento 'J' informado nas posições de 18 a 61 está invalido ou deslocado. Verificar também o se o digito geral está na posição 22 do código de barras. Nota: campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout G063",
        'CD' => "Código de Barras - Valor do Título Divergente/Inválido. - Verificar nas posições de 18 a 61 no segmento J se o código de barras está invalido, deslocado ou em branco. Verificar também nas posições de 27 a 36 se o valor do código de barras está correto em relação ao valor do documento. Nota: campo deve ser apenas numérico. Para mais detalhes verificar layout G063.",
        'CE' => "Código de Barras - Campo Livre Inválido - Verificar nas posições de 18 a 61 no segmento J se o código de barras está invalido, deslocado ou em branco. Verificar também nas posições de 37 a 61 se dados estão em conformidade com o campo livre do código de barras. Nota: campo deve ser apenas numérico. Para mais detalhes verificar layout G063.",
        'CF' => "Valor do Documento Inválido - Verificar nas posições de 100 a 114 do arquivo de remessa no segmento 'J' se o valor do titulo está inválido ou deslocado. O campo deve ser apenas numérico. Para mais detalhes verificar no layout G042.",
        'CG' => "Valor do Abatimento Inválido - Verificar nas posições de 115 a 129 no segmento 'J' se o valor do abatimento está inválido ou deslocado. O campo deve ser apenas numérico. Para mais detalhes verificar no layout L002.",
        'CH' => "Valor do Desconto Inválido - Verificar nas posições de 115 a 129 no segmento 'J' se o valor do desconto/bonificação está inválido ou deslocado. O campodeve ser apenas numérico. Para mais detalhes verificar no layout L002",
        'CI' => "Valor de Mora Inválido - Verificar nas posições de 130 a 144 no segmento 'J' se o valor de mora está inválido ou deslocado. O campo deve ser apenas numérico. Para mais detalhes verificar no layout L003.",
        'CJ' => "Valor da Multa Inválido - Verificar nas posições de 130 a 144 no segmento 'J' se o valor de multa está inválido ou deslocado. O campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout L003.",
        'CK' => "Valor do IR Inválido - Verificar nas posições de 18 a 32 no segmento 'C' se o valor do IR (Imposto de Renda) está inválido ou deslocado. Nota: campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout G050.",
        'CL' => "Valor do ISS Inválido - Verificar nas posições de 33 a 47 se o valor do ISS está inválido ou deslocado. O campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout G051.",
        'CM' => "Valor do IOF Inválido - Verificar nas posições de 48 a 62 no segmento 'C' se o valor do IOF está inválido ou deslocado. O campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout G052.",
        'CN' => "Valor de Outras Deduções Inválido - Verificar nas posições de 63 a 77 no segmento 'C' se o valor de outras deduções está inválido ou deslocado. Nota: campo dever ser apenas NUMERICO. Para mais detalhes verificar no layout G053.",
        'CO' => "Valor de Outros Acréscimos Inválido Verificar nas posições de 78 a 92 no segmento 'C' se o valor de outros acréscimos está inválido ou deslocado. Nota: campo dever ser apenas NUMERICO. Para mais detalhes verificar no layout G054.",
        'CP' => "Valor do INSS Inválido - Verificar nas posições de 113 a 127 no segmento 'C' se o valor do INSS está inválido ou deslocado. Nota: campo deve ser apenas NUMERICO. Para mais detalhes verificar no layout G055.",
        'HA' => "Lote Não Aceito - Trata-se de movimento já processado (Duplicado), ou com Registro/Segmento incorreto.",
        'HB' => "Inscrição da Empresa Inválida para o Contrato - Verificar no header de arquivo e no header de lote nas posições de 19 a 32 se o numero de inscrição da empresa pertence ao numero de convenio informado nas posições de 33 a 38.",
        'HC' => "Convênio com a Empresa Inexistente/Inválido para o Contrato - Verificar se o número de convenio informado nas posições de 33 a 38 está correto, verificação deve ser realizada no header de arquivo e no header de lote. Para mais detalhes verificar no layout G007.'",
        'HD' => "Agência/Conta Corrente da Empresa Inexistente/Inválido para o Contrato - Verificar no header de arquivo e header de lote se o número de agencia e conta informada corretamente. Código da agencia fica nas posições de 53 a 57 sendo o digito verificador na posição 58 para Código da conta verificar nas posições de 59 a70 sendo o digito verificador na posição 71. Para mais detalhes verificar no layout G008, G009, G010 e G011.",
        'HE' => "Tipo de Serviço Inválido para o Contrato - Verificar no header de lote qual o tipo de serviço informado nas posições de 10 a 11. Se o tipo de serviço estiver correto emrelação ao lançamento do arquivo verificar se contrato está com o serviço disponível para utilização. Para mais detalhes verificar no layout G025.",
        'HF' => "Conta Corrente da Empresa com Saldo Insuficiente - Lançamento recusado por saldo insuficiente na conta de debito vinculada ao convênio utilizado.",
        'HG' => "Lote de Serviço Fora de Sequência Verificar se na data de transmissão, nas posições de 4 a 7 se existem arquivos com o mesmo numero de lote ou se foram enviados fora de sequencia, também verificar se o Nº Sequencial do Registro no Lote nas posições de 9 a 13 (Segmentos A, B, J, N, O) estão em sequência crescente para cada lote aberto quando for arquivo multiheader. Para mais detalhes verificar no layout G002 e G038.",
        'HH' => "Lote de Serviço Inválido - Verificar se na data de transmissão existe arquivos com o mesmo numero de lote ou se houve envio fora de sequencia (devendo iniciar em 0001). Verificar no header do lote, nas posições de 4 a 7. Para mais detalhes verificar no layout G002. ",
        'HI' => "Arquivo não aceito - Todo arquivo será rejeitado por diferentes motivos de recusa nos segmentos de detalhe.‘",
        'HJ' => "Tipo de Registro Inválido - Verificar em todas as linhas do arquivo remessa na posição ‘8’ se foi informado o código de registro correto. Ex.°: No header do arquivo na posição ‘8’ deve conter o código '0'que significa Header de Arquivo, para demais segmentos teremos '1' que significa Header de Lote, '2' que significa Registros Iniciais do Lote, '3' que significa Detalhe, '4' que significa Registros Finais do Lote, '5' => que significa Trailer de Lote, '9' que significa Trailer de Arquivo. Para mais detalhes verificar no layout G003. ",
        'HK' => "Código Remessa / Retorno Inválido - Verificar no header do arquivo na posição 143 se foi informado o código fixo '1' que significa remessa. Nota: Qualquer informaçãodiferente de '1' pode gerar recusa. Para mais detalhes verificar no layout G015. ",
        'HL' => "Versão de layout inválida - Verificar nas posições de 14 a 16 no header de lote se a versão do layout está correta em relação ao tipo de pagamento inserido no lote Nota: Esse parâmetro é utilizado para que possamos saber que tipo de estrutura de pagamento deve ser lida no arquivo Ex.°: Se a Versão do layout informada for '040' sabemos que a estrutura que está no arquivo remessa deve ser para 'Títulos de Cobrança' para demais lançamentos existem as seguintes versões: PAGFOR '045', PAGAMENTO DE TITULOS '040' e TRIBUTOS '012', Verificar também no header de arquivo nas posições de 164 à 166 se está informado fixo '089'.Para mais detalhes verificar no layout G019 e G030.‘HM’ = Mutuário não identificado",
        'HM' => "Mutuário não identificado",
        'HN' => "Tipo do benefício não permite empréstimo",
        'HO' => "Benefício cessado/suspenso",
        'HP' => "Benefício possui representante legal",
        'HQ' => "Benefício é do tipo PA (Pensão alimentícia)",
        'HR' => "Quantidade de contratos permitida excedida",
        'HS' => "Benefício não pertence ao Banco informado",
        'HT' => "Início do desconto informado já ultrapassado",
        'HU' => "Número da parcela inválida",
        'HV' => "Quantidade de parcela inválida",
        'HW' => "Margem consignável excedida para o mutuário dentro do prazo do contrato",
        'HX' => "Empréstimo já cadastrado",
        'HY' => "Empréstimo inexistente",
        'HZ' => "Empréstimo já encerrado",
        'H1' => "Arquivo sem trailer Verificar no arquivo de remessa se falta a ultima linha do registro trailer do arquivo registro tipo '9'. Nota: O tipo de registro é informado em todas as linhas do arquivo na posição 8. Para mais detalhes verificar no layout G003.",
        'H2' => "Mutuário sem crédito na competência",
        'H3' => "Não descontado – outros motivos",
        'H4' => "Retorno de Crédito não pago Estorno de pagamento quando os dados do favorecido esta incorreto",
        'H5' => "Cancelamento de empréstimo retroativo",
        'H6' => "Outros Motivos de Glosa",
        'H7' => "Margem consignável excedida para o mutuário acima do prazo do contrato",
        'H8' => "Mutuário desligado do empregador",
        'H9' => "Mutuário afastado por licença",
        'IA' => "Primeiro nome do mutuário diferente do primeiro nome do movimento do censo ou diferente da base de Titular do Benefício",
        'TA' => "Lote Não Aceito - Totais do Lote com Diferença Verificar no trailer de lote nas posições de 18 a 23 se o somatório de registros informados está preenchido e se está correto em relação ao total de linhas do lote. Verificar no trailer de lote nas posições de 24 a 41 se o total do valor do lote está correto em relação ao valor total dos pagamentos. No trailer do arquivo nas posições de 18 a 23, verificar se a quantidade de lotes está correta ou nas posições de 24 a 29 se quantidade de registros informados está correta em relação ao total de linhas no arquivo remessa. Para mais detalhes verificar no layout G057, P007, G049 e G056.",
        'YA' => "Título Não Encontrado - O título de Cobrança não foi localizado na CIP para pagamento.",
        'YB' => "Identificador Registro Opcional Inválido Verificar no segmento 'J-52' do arquivo de remessa nas posições de 18 a 19 se foi informado o código fixo '52'. No segmento 'N' do arquivo de remessa quando for DARF sem código de barras, verificar nas posições de 143 a 159 se foi informado o 'número de referência', pois se trata de um campo numérico obrigatório para 'DARF sem código de barras'. Para mais detalhes verificar no layout G067 e N009.",
        'YC' => "Código Padrão Inválido Ocorrência especifica para o tipo de serviço alegação de sacado.",
        'YD' => "Código de Ocorrência Inválido Ocorrência especifica para o tipo de serviço alegação de sacado.",
        'YE' => "Complemento de Ocorrência Inválido Ocorrência especifica para o tipo de serviço alegação de sacado.",
        'YF' => "Alegação já Informada - Ocorrência especifica para o tipo de serviço alegação de sacado. Observação: As ocorrências iniciadas com 'ZA' tem caráter informativo para o cliente ",
        'ZA' => "Agência/Conta do Favorecido Substituída Beneficiário é correntista do banco favorecido, mas seu numero de agencia e conta foram alterados. Nota: Pode ter mudado sua conta de agencia ou os dados podem ter sofrido algum tipo de atualização/alteração.",
        'ZB' => "Divergência entre o primeiro e último nome do beneficiário versus primeiro e último nome na Receita Federal. Verificar o cadastro do beneficiário junto a receita federal para identificar se existe alguma divergência de informações.",
        'ZC' => "Confirmação de Antecipação de Valor",
        'ZD' => "Antecipação Parcial de Valor",
        'ZE' => "Título bloqueado na base - Titulo Bloqueado ou não encontrado na Base da CIP.",
        'ZF' => "Sistema em contingência – título valor maior que referência",
        'ZG' => "Sistema em contingência – título vencido",
        'ZH' => "Sistema em contingência – título indexado",
        'ZI' => "Beneficiário divergente - Dados do Beneficiário divergente do constante na CIP.",
        'ZJ' => "Limite de pagamentos parciais excedidos",
        'ZK' => "Boleto já liquidado - Título de cobrança já liquidado na base da CIP.",
        '5A' => "Agendado sob lista de debito 'Pagamento agendado que faz parte de uma lista com um número para autorização.'",
        '5B' => "Pagamento não autoriza sob lista de debito 'Pagamento da lista não foi autorizado'",
        '5C' => "Lista com mais de uma modalidade 'Lista de pagamento não permite mais de uma modalidade'",
        '5D' => "Lista com mais de uma data de pagamento 'Lista de pagamento não permite mais de uma data de pagamento'",
        '5E' => "Número de lista duplicado 'Número da lista enviado pelo cliente já foi utilizado'",
        '5F' => "Lista de debito vencida e não autorizada 'Pagamentos que pertence a uma determinada lista estão vencidos e não foram autorizados'",
        '5I' => "Ordem de Pagamento emitida 'Pagamento realizado ao favorecido nesta data'",
        '5J' => "Ordem de pagamento com data limite vencida '‘5M' - Número de lista de debito invalida'",
        '5T' => "Pagamento realizado em contrato na condição de TESTE",
    ];

    /**
     * Array com as possiveis descricoes de baixa e liquidacao.
     *
     * @var array
     */
    private $baixa_liquidacao = [

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
            ->setAgencia($this->rem(53, 57, $header))
            ->setAgenciaDv($this->rem(58, 58, $header))
            ->setConta($this->rem(59, 70, $header))
            ->setContaDv($this->rem(71, 71, $header))
//            ->setContaDv($this->rem(72, 72, $header))
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
            ->setTipoInscricao($this->rem(18, 18, $headerLote))
            ->setNumeroInscricao($this->rem(19, 32, $headerLote))
            ->setAgencia($this->rem(53, 57, $headerLote))
            ->setAgenciaDv($this->rem(58, 58, $headerLote))
            ->setConta($this->rem(59, 70, $headerLote))
            ->setContaDv($this->rem(71, 71, $headerLote))
//            ->setContaDv($this->rem(73, 73, $headerLote))
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
            $d->setOcorrencia($this->rem(16, 17, $detalhe))
                ->setOcorrenciaDescricao(Arr::get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
                ->setNossoNumero($this->rem(203, 222, $detalhe))
                //->setCarteira($this->rem(58, 58, $detalhe)) // Não Tem
                //->setNumeroDocumento($this->rem(59, 73, $detalhe)) Não Localizado
                ->setDataVencimento($this->rem(92, 99, $detalhe))
                ->setValor(Util::nFloat($this->rem(100, 114, $detalhe)/100, 2, false))
                ->setNumeroControle($this->rem(183, 202, $detalhe)); //Referencia do Sacado;
                /*->setPagador([
                    'nome' => $this->rem(149, 188, $detalhe),
                    'documento' => $this->rem(134, 148, $detalhe),
                ])*/
                //->setValorTarifa(Util::nFloat($this->rem(199, 213, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $msgAdicional = str_split(sprintf('%08s', $this->rem(231, 240, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('23')) { //Valiação referente aos campos 16 e 17 Do J
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('00','09','50','60')) {
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
            } elseif ($d->hasOcorrencia('05','06','10','11','17','19','51','61')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('25','27','33','40','52','53','54')) {
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
            //->setQtdTitulosCobrancaSimples((int) $this->rem(24, 29, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(24, 41, $trailer)/100, 2, false));
            //->setQtdTitulosCobrancaVinculada((int) $this->rem(47, 52, $trailer))
            //->setValorTotalTitulosCobrancaVinculada(Util::nFloat($this->rem(53, 69, $trailer)/100, 2, false))
            //->setQtdTitulosCobrancaCaucionada((int) $this->rem(70, 75, $trailer))
            //->setValorTotalTitulosCobrancaCaucionada(Util::nFloat($this->rem(76, 92, $trailer)/100, 2, false))
            //->setQtdTitulosCobrancaDescontada((int) $this->rem(93, 98, $trailer))
            //->setValorTotalTitulosCobrancaDescontada(Util::nFloat($this->rem(99, 115, $trailer)/100, 2, false));

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
