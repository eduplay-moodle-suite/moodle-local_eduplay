Installation
============

Requirements
------------

* Moodle 4.5 LTS or 5.3 LTS.
* PHP supported by your Moodle version.

From a ZIP file
---------------

1. Download the plugin ZIP (the top-level folder must be named ``eduplay``).
2. In Moodle, go to *Site administration* > *Plugins* > *Install plugins* and upload the ZIP.
3. Follow the on-screen upgrade steps.

From Git
--------

.. code-block:: bash

   git clone https://github.com/eduplay-moodle-suite/moodle-local_eduplay.git local/eduplay

Then open *Site administration* > *Notifications* to finish the installation. On Moodle 5.1 and later, the target directory is ``public/local/eduplay``.
