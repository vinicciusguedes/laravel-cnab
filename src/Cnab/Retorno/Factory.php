<?php
namespace VinicciusGuedes\LaravelCnab\Cnab\Retorno;

use VinicciusGuedes\LaravelCnab\Contracts\Boleto\Boleto as BoletoContract;
use VinicciusGuedes\LaravelCnab\Contracts\Cnab\Retorno;
use VinicciusGuedes\LaravelCnab\Util;

class Factory
{
    /**
     * @param $file
     *
     * @return Retorno
     * @throws \Exception
     */
    public static function make($file, $type = 'C')
    {
        if (!$file_content = Util::file2array($file)) {
            throw new \Exception("Arquivo: não existe");
        }

        if (!Util::isHeaderRetorno($file_content[0])) {
            throw new \Exception("Arquivo: $file, não é um arquivo de retorno");
        }

        $instancia = self::getBancoClass($file_content, $type);
        return $instancia->processar();
    }

    /**
     * @param $file_content
     *
     * @return mixed
     * @throws \Exception
     */
    private static function getBancoClass($file_content, $type = 'C')
    {
        $banco = '';
        $namespace = '';
        if (Util::isCnab400($file_content)) {
            $banco = mb_substr($file_content[0], 76, 3);
            if($type == 'C') {
                $namespace = __NAMESPACE__ . '\\Cnab400\\Cobranca\\';
            }
            if($type == 'P') {
                $namespace = __NAMESPACE__ . '\\Cnab400\\Pagamento\\';
            }
        } elseif (Util::isCnab240($file_content)) {
            $banco = mb_substr($file_content[0], 0, 3);
            if($type == 'C') {
                $namespace = __NAMESPACE__ . '\\Cnab240\\Cobranca\\';
            }
            if($type == 'P') {
                $namespace = __NAMESPACE__ . '\\Cnab240\\Pagamento\\';
            }
        }

        $bancoClass = $namespace . Util::getBancoClass($banco);

        if (!class_exists($bancoClass)) {
            throw new \Exception("Banco não possui essa versão de CNAB");
        }

        return new $bancoClass($file_content);
    }
}
