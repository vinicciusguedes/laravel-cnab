Itaú
====

This bank has the following mandatory fields:

:agencia: Account keeping agency. (size: 4)
:conta: Account number. (size: 5)

.. code-block:: php

    // for 400 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab400\Cobranca\Banco\Itau;

    // Or, for 240 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Cobranca\Banco\Itau;

    $send->setBeneficiario($beneficiario)
        ->setCarteira(109)
        ->setAgencia(1111)
        ->setConta(22222);

Or, Simply:

.. code-block:: php

    $sendArray = [
        'beneficiario' => $beneficiario,
        'carteira' => 109,
        'agencia' => 1111,
        'conta' => 22222,
    ];

    // for 400 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab400\Cobranca\Banco\Itau($sendArray);

    // Or, for 240 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Cobranca\Banco\Itau($sendArray);

.. ATTENTION::
    To generate the file see the :ref:`send` session.