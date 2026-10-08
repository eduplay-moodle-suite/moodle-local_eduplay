Uso
===

O ``local_eduplay`` é uma biblioteca para outros plugins; nesta versão não há interface para o usuário final.

.. code-block:: php

   use local_eduplay\local\url_parser;

   $ref = url_parser::parse_reference('https://eduplay.rnp.br/app/video/353479');
   if ($ref !== null) {
       $ref->canonical_url(); // https://eduplay.rnp.br/app/video/353479
       $ref->embed_url();     // https://eduplay.rnp.br/app/video/embed/353479
       $ref->h5p_url();       // https://eduplay.rnp.br/api/v1/videos/353479/h5p-url
   }

``parse_reference()`` retorna ``null`` para qualquer coisa que não seja uma URL canônica de vídeo do EduPlay. Persista apenas o id do vídeo ou a URL canônica: o endpoint ``h5p-url`` pode redirecionar para uma URL temporária do CDN, que nunca deve ser gravada.
