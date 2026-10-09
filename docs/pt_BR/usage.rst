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

Buscar vídeos e ler metadados
-----------------------------

.. code-block:: php

   use local_eduplay\local\api_client;

   if (api_client::is_enabled()) {
       $client = new api_client();
       $result = $client->search('Documentário Eduplay 20 anos', 1); // 10 resultados por página.
       foreach ($result->videos as $video) {
           $video->name;                         // Título.
           $video->thumbnail;                    // Miniatura HTTPS em eduplay.rnp.br, ou null.
           $video->reference()->canonical_url(); // URL canônica montada a partir do id numérico.
       }
       $result->page; $result->lastpage;         // Paginação.
       $info = $client->get_video(353479);       // Null se não existir ou não for público.
   }

Só são devolvidos vídeos públicos e ativos que não exigem autenticação. Falhas lançam ``moodle_exception``. As respostas têm cache e a configuração ``Consultar o serviço EduPlay`` desliga tudo.

Adaptador para o editor do H5P
------------------------------

O editor de conteúdo do H5P (por exemplo *Banco de conteúdo* > *Adicionar* > *Interactive Video*) aceita **qualquer** URL como arquivo de vídeo sem verificá-la. Um link canônico do EduPlay seria salvo como um vídeo que nunca toca ("Video format not supported"). Apenas nas páginas do editor, o ``local_eduplay`` converte o link digitado ou colado no campo de **URL do vídeo**:

1. Em *Add a video* > *Enter video URL*, cole ``https://eduplay.rnp.br/app/video/353479``.
2. Clique em **Insert**: o editor grava ``https://eduplay.rnp.br/api/v1/videos/353479/h5p-url``, que redireciona para o MP4.

A forma de embed também é convertida; campos de imagem e outros links não são alterados. O editor mostra o tipo do vídeo como "unknown" porque a URL não tem extensão; ele toca normalmente.

Adaptador para o Interactive Video
----------------------------------

O plugin de terceiros `Interactive Video <https://github.com/sokunthearithmakara/moodle-mod_interactivevideo>`_ (``mod_interactivevideo``, testado com a 2.2) só aceita URLs de mídia e responde "The URL provided is invalid." a um link canônico do EduPlay. Quando esse plugin está instalado, o ``local_eduplay`` carrega um pequeno script apenas no formulário de adicionar/editar dele:

1. Em *Video URL*, cole ``https://eduplay.rnp.br/app/video/353479``.
2. O campo passa a ``https://eduplay.rnp.br/api/v1/videos/353479/h5p-url`` (que redireciona para o MP4) e a prévia carrega.

A forma de embed (``/app/video/embed/{id}``) também é convertida. Outros links não são alterados, então YouTube, Vimeo, arquivos MP4 e os demais continuam funcionando. Mantenha a fonte **Video link** (``videolink``) habilitada nas configurações do Interactive Video.

O que o plugin grava é a ``h5p-url``, nunca o endereço temporário do CDN. Nada é instalado nem alterado no plugin de terceiros, e o ``local_eduplay`` não faz requisições.

Limitações
~~~~~~~~~~

* **CSV / importação em lote.** A importação em lote do Interactive Video valida a coluna ``videourl`` no servidor, então o adaptador (JavaScript) não atua ali. Informe no arquivo a URL de mídia: ``https://eduplay.rnp.br/api/v1/videos/{id}/h5p-url``.
* **Flexbook.** O modal de vídeo dentro do conteúdo do ``mod_flexbook`` (campo ``#video-url``) é outra tela, de outro plugin. Não é coberto nem foi testado; cole a ``h5p-url`` também ali.
* Apenas o formulário de adicionar/editar da atividade Interactive Video é adaptado.
