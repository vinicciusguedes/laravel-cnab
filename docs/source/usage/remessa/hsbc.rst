HSBC
====

This bank has the following mandatory fields:

:agencia: Account keeping agency. (size: 4)
:conta: Account number. (size: 6)
:contaDv: Account number verification code. (size: 1)

.. code-block:: php

    // for 400 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab400\Cobranca\Banco\Hsbc;

    // Or, for 240 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Cobranca\Banco\Hsbc;

    $send->setBeneficiario($beneficiario)
        ->setCarteira('CSB')
        ->setAgencia(1111)
        ->setConta(222222)
        ->setContaDv(2);

Or, Simply:

.. code-block:: php

    $sendArray = [
        'beneficiario' => $beneficiario,
        'carteira' => 'CSB',
        'agencia' => 1111,
        'conta' => 222222,
        'contaDv' => 2,
    ];

    // for 400 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab400\Cobranca\Banco\Hsbc($sendArray);

    // Or, for 240 positions
    $send = new VinicciusGuedes\LaravelCnab\Cnab\Remessa\Cnab240\Cobranca\Banco\Hsbc($sendArray);

.. ATTENTION::
    To generate the file see the :ref:`send` session.