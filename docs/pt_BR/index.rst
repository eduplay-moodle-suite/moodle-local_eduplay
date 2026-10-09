moodle-local_eduplay
====================

O **moodle-local_eduplay** é o plugin núcleo da **EduPlay Moodle Suite**, uma iniciativa comunitária e não oficial para usar vídeos do `EduPlay <https://eduplay.rnp.br/>`_ (RNP) no Moodle. Ele centraliza a validação e a análise de URLs, as URLs derivadas (canônica, embed e H5P), o cache de metadados e as regras de segurança, evitando duplicação nos demais plugins da suíte.

English version: `English <../en/index.html>`_.

.. warning::

   Este projeto não é oficial. Não possui afiliação, endosso ou representação da RNP, do EduPlay ou do Moodle HQ.

.. toctree::
   :maxdepth: 2
   :caption: Conteúdo

   installation
   configuration
   usage

Principais recursos
-------------------

* **Parser de URL estrito**: aceita somente ``https://eduplay.rnp.br/app/video/{id}`` e rejeita outros hosts, rotas, portas, credenciais e queries.
* **URLs derivadas**: URLs canônica, de embed oficial e H5P (``h5p-url``) montadas a partir do id do vídeo.
* **Nada temporário é gravado**: persiste-se apenas o id ou a URL canônica; URLs temporárias do CDN nunca.
* **Adaptador para Interactive Video**: permite colar o link canônico no formulário do plugin de terceiros `Interactive Video <https://github.com/sokunthearithmakara/moodle-mod_interactivevideo>`_ (veja *Uso*).
* **Moodle 4.5 LTS e 5.3 LTS**: testado em matriz de CI com PostgreSQL e MariaDB.
