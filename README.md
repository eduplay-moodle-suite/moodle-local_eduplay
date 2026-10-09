# moodle-local_eduplay

[English](#english) · [Português (Brasil)](#português-brasil)

Documentation / Documentação: <https://eduplay-moodle-suite.github.io/moodle-local_eduplay/>

## English

Core plugin of the [EduPlay Moodle Suite](https://github.com/eduplay-moodle-suite): URL parser, derived URLs (canonical, embed, H5P), metadata cache and security rules for [EduPlay](https://eduplay.rnp.br/) videos. Also lets authors paste the canonical link in the form of the third-party [Interactive Video](https://github.com/sokunthearithmakara/moodle-mod_interactivevideo) plugin (converted to the `h5p-url` endpoint). Supports Moodle 4.5 LTS and 5.3 LTS.

> Unofficial project: no affiliation with RNP, EduPlay or Moodle HQ.

Install: copy this repository to `local/eduplay` (`public/local/eduplay` on Moodle 5.1+) and visit *Site administration > Notifications*.

## Português (Brasil)

Plugin núcleo da [EduPlay Moodle Suite](https://github.com/eduplay-moodle-suite): parser de URL, URLs derivadas (canônica, embed, H5P), cache de metadados e regras de segurança para vídeos do [EduPlay](https://eduplay.rnp.br/). Também permite colar o link canônico no formulário do plugin de terceiros [Interactive Video](https://github.com/sokunthearithmakara/moodle-mod_interactivevideo) (convertido para o endpoint `h5p-url`). Suporta Moodle 4.5 LTS e 5.3 LTS.

> Projeto não oficial: sem afiliação com a RNP, o EduPlay ou o Moodle HQ.

Instalação: copie este repositório para `local/eduplay` (`public/local/eduplay` no Moodle 5.1+) e acesse *Administração do site > Notificações*.

## Development / Desenvolvimento

JavaScript sources are in `amd/src`; rebuild `amd/build` with `npx grunt amd` in a Moodle checkout (`--root=local/eduplay`).

## License / Licença

GNU GPL v3 or later. See [LICENSE](LICENSE).
