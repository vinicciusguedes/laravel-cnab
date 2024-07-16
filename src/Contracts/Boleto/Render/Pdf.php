<?php

namespace VinicciusGuedes\LaravelCnab\Contracts\Boleto\Render;

interface Pdf
{
    public function gerarBoleto($dest, $save_path);

    public function setLocalizacaoPix($localizacao);
}
