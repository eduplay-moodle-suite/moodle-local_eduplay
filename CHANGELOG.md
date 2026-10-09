# Changelog

## 0.3.0 - 2026-10-09

- API client for the public (undocumented) EduPlay API: paginated title search and video metadata, with host fixed, short timeouts, no redirects, strict response validation, filter (public, active, no authentication) and cache.
- New setting "Query the EduPlay service" to turn all remote requests off; Privacy API now declares the search text sent to EduPlay.

## 0.2.0 - 2026-10-09

- Interactive Video adapter: on the add/edit form of the third-party mod_interactivevideo, the canonical (or embed) EduPlay link is converted to the h5p-url endpoint before the plugin validates the field.
- Hook callback and AMD module, loaded only on that form; CI now runs grunt.
## 0.1.0 - 2026-10-08

- Plugin skeleton (Moodle 4.5 LTS and 5.3 LTS).
- Canonical URL parser and canonical/embed/H5P URL builders, with unit tests.
- CI (moodle-plugin-ci) and bilingual (en/pt_BR) Sphinx documentation.
