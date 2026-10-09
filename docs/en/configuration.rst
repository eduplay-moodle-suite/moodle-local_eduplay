Configuration
=============

Go to *Site administration* > *Plugins* > *Local plugins* > *EduPlay*.

.. list-table::
   :header-rows: 1

   * - Setting
     - Description
   * - Metadata cache lifetime
     - How long EduPlay video metadata stays in the ``local_eduplay/metadata`` application cache (default: 1 hour).

Query the EduPlay service
-------------------------

*Site administration* > *Plugins* > *Local plugins* > *EduPlay* > **Query the EduPlay service** (enabled by default).

When enabled, the server queries the public EduPlay API to search videos by title and to read the title and thumbnail of a video. When disabled, this plugin makes **no request** to EduPlay and only pasted video links work.

The API used (``/api/v1/search`` and ``/api/v1/videos/{id}``) is public but **not documented by RNP**, so it may change without notice. Confirm it with RNP before relying on it in production.

Capabilities
------------

* ``local/eduplay:manage``: manage plugin settings (managers by default).

Privacy
-------

The plugin stores no personal data. When remote lookups are enabled, the **text typed in the video search** is sent to the EduPlay service (``eduplay.rnp.br``) from the server; no user identifier is sent. This is declared in the Moodle Privacy API (external location).
