.. _return:

Retorno
=======

There are 2 types of ``Retornos``: 240 positions and 400 positions.

**Options available by bank:**

=========================  ====  ====
Banco                      240   400
=========================  ====  ====
Bancoob                    yes   yes
Banrisul                   yes*  yes*
Banco do Brasil            yes   yes*
Banco do Nordeste          no    no
Bradesco                   yes   yes*
Caixa Econônica Federal    yes   yes*
HSBC                       no    yes
Itaú                       yes   yes*
Santander                  yes   yes
Sicredi                    yes*  yes*
=========================  ====  ====

.. note::
    *** requires homologation**

All banks have the same reading process.
The constructor accepts as argument:

.. code-block:: php

    // File path
    $argument = '/path/to/retorno.ret';

    // Or, String content
    $argument = '0RETORNOCONTENTHERE...\n1RETORNOCONTENTHERE...';

    // Or, Array content
    $argument = [
        '0RETORNOCONTENTHERE...',
        '1RETORNOCONTENTHERE...'
    ];


Factory
-------

.. code-block:: php

    // The Factory will guess what the return if it is 240 or 400 and which bank and already return the instantiated object
    $return = \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Factory::make($argument);

    // To process the file
    $return->processar();

    // You can know the type of bank after instantiate, using the methods respectively:
    $return->getTipo();
    $return->getCodigoBanco();


Bancoob
-------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Bancoob($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Bancoob($argument)

    // To process the file
    $return->processar();

Banrisul
--------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Banrisul($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Banrisul($argument)

     // To process the file
    $return->processar();

Banco do Brasil
---------------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Bb($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Bb($argument)

     // To process the file
    $return->processar();

Banco do Nordeste
-----------------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Bnb($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Bnb($argument)

     // To process the file
    $return->processar();

Bradesco
--------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Bradesco($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Bradesco($argument)

     // To process the file
    $return->processar();

Caixa Econônica Federal
-----------------------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Caixa($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Caixa($argument)

     // To process the file
    $return->processar();

HSBC
----

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Hsbc($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Hsbc($argument)

     // To process the file
    $return->processar();

Itaú
----

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Itau($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Itau($argument)

     // To process the file
    $return->processar();

Santander
---------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Santander($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Santander($argument)

     // To process the file
    $return->processar();

Sicredi
-------

.. code-block:: php

    // 400 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab400\Cobranca\Banco\Sicredi($argument)

    // 240 positions
    $return = new \VinicciusGuedes\LaravelCnab\Cnab\Retorno\Cnab240\Cobranca\Banco\Sicredi($argument)

     // To process the file
    $return->processar();


Dealing with the return
-----------------------

In return of 400 positions the object of the bank has the following methods:

.. code-block:: php

    // This will return a iterable object, with all returns
    $return->getDetalhes();
    // This will return a object with information
    $return->getHeader();
    // This will return a object with totals information
    $return->getTrailer();

     // To iterate do:
    foreach($return->getDetalhes() as $object) {
        var_dump($object->toArray());
    }

In return of 240 positions the object of the bank has the following methods:

.. code-block:: php

    // This will return a iterable object, with all returns
    $return->getDetalhes();
    // This will return a object with information
    $return->getHeader();
    // This will return a object with information by lote
    $return->getHeaderLote();
    // This will return a object with totals information
    $return->getTrailer();
    // This will return a object with totals information by lote
    $return->getTrailerLote();

    // To iterate do:
    foreach($return->getDetalhes() as $object) {
        var_dump($object->toArray());
    }


The return object implements ``SeekableIterator``, so you can do a foreach on the object that will iterate for each return:

.. code-block:: php

    foreach($return as $object) {
        var_dump($object->toArray());
    }

.. seealso::

   `API return docs <https://vinicciusguedes.github.io/laravel-cnab/namespace-VinicciusGuedes.LaravelCnab.Cnab.Retorno.html>`_
      Documentation for return objects.

   `Examples <https://github.com/vinicciusguedes/laravel-cnab/tree/master/exemplos>`_
      Examples of use
