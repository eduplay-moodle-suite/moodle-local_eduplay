moodle-local_eduplay
====================

**moodle-local_eduplay** is the core plugin of the **EduPlay Moodle Suite**, an unofficial community project to use `EduPlay <https://eduplay.rnp.br/>`_ (RNP) videos in Moodle. It centralizes URL validation and parsing, derived URLs (canonical, embed and H5P), metadata caching and security rules, so the other plugins of the suite do not duplicate them.

Versão em português: `Português (Brasil) <../pt-br/index.html>`_.

.. warning::

   This project is unofficial. It has no affiliation with, or endorsement by, RNP, EduPlay or Moodle HQ.

.. toctree::
   :maxdepth: 2
   :caption: Contents

   installation
   configuration
   usage

Main features
-------------

* **Strict URL parser**: accepts only ``https://eduplay.rnp.br/app/video/{id}`` and rejects other hosts, routes, ports, credentials and queries.
* **Derived URLs**: canonical, official embed and H5P (``h5p-url``) URLs built from the video id.
* **Nothing temporary is stored**: only the video id or canonical URL is persisted; temporary CDN URLs never are.
* **EduPlay API client**: title search (paginated) and video metadata (title, thumbnail) from the public EduPlay API, validated, cached and switchable in the settings.
* **Interactive Video adapter**: lets authors paste the canonical link in the form of the third-party `Interactive Video <https://github.com/sokunthearithmakara/moodle-mod_interactivevideo>`_ plugin (see *Usage*).
* **Moodle 4.5 LTS and 5.3 LTS**: tested on a CI matrix with PostgreSQL and MariaDB.
