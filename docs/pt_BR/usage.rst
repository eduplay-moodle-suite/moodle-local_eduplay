Uso
===

O O ``local_eduplay`` é, em essência, uma biblioteca para outros plugins. O único recurso para o usuário final é o adaptador do Interactive Video, descrito no fim desta página.

.. code-block:: php

   use local_eduplay\local\url_parser;

   $ref = url_parser::parse_reference('https://eduplay.rnp.br/app/video/353479');
   if ($ref !== null) {
       $ref->canonical_url(); // https://eduplay.rnp.br/app/video/353479
       $ref->embed_url();     // https://eduplay.rnp.br/app/video/embed/353479
       $ref->h5p_url();       // https://eduplay.rnp.br/api/v1/videos/353479/h5p-url
   }

``parse_reference()`` retorna ``null`` para qualquer coisa que não seja uma URL canônica de vídeo do EduPlay. Persista o id do vídeo ou a URL canônica (ou, quando um plugin de terceiros exige URL de mídia, a ``h5p-url``): o endpoint ``h5p-url`` redireciona para uma URL temporária do CDN, que nunca deve ser gravada.

Adaptador para o Interactive Video
----------------------------------

O plugin de terceiros `Interactive Video <https://github.com/sokunthearithmakara/moodle-mod_interactivevideo>`_ (``mod_interactivevideo``, testado com a 2.2) só aceita URLs de mídia e responde "The URL provided is invalid." a um link canônico do EduPlay. Quando esse plugin está instalado, o ``local_eduplay`` carrega um pequeno script apenas no formulário de adicionar/editar dele:

1. Em *Video URL*, cole ``https://eduplay.rnp.br/app/video/353479``.
2. O campo passa a ``https://eduplay.rnp.br/api/v1/videos/353479/h5p-url`` (que redireciona para o MP4) e a prévia carrega.

A forma de embed (``/app/video/embed/{id}``) também é convertida. Outros links não são alterados, então YouTube, Vimeo, arquivos MP4 e os demais continuam funcionando. Mantenha a fonte **Video link** (``videolink``) habilitada nas configurações do Interactive Video.

O que o plugin grava é a ``h5p-url``, nunca o endereço temporário do CDN. Nada é instalado nem alterado no plugin de terceiros, e o ``local_eduplay`` não faz requisições.
