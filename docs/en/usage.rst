Usage
=====

``local_eduplay`` is a library for other plugins; it has no end-user interface in this release.

.. code-block:: php

   use local_eduplay\local\url_parser;

   $ref = url_parser::parse_reference('https://eduplay.rnp.br/app/video/353479');
   if ($ref !== null) {
       $ref->canonical_url(); // https://eduplay.rnp.br/app/video/353479
       $ref->embed_url();     // https://eduplay.rnp.br/app/video/embed/353479
       $ref->h5p_url();       // https://eduplay.rnp.br/api/v1/videos/353479/h5p-url
   }

``parse_reference()`` returns ``null`` for anything that is not a canonical EduPlay video URL. Persist only the video id or the canonical URL: the ``h5p-url`` endpoint may redirect to a temporary CDN URL, which must never be stored.
