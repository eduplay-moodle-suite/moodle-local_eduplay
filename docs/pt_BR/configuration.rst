Configuração
============

Acesse *Administração do site* > *Plugins* > *Plugins locais* > *EduPlay*.

.. list-table::
   :header-rows: 1

   * - Configuração
     - Descrição
   * - Tempo de vida do cache de metadados
     - Por quanto tempo os metadados dos vídeos EduPlay ficam no cache de aplicação ``local_eduplay/metadata`` (padrão: 1 hora).

Consultar o serviço EduPlay
---------------------------

*Administração do site* > *Plugins* > *Plugins locais* > *EduPlay* > **Consultar o serviço EduPlay** (habilitado por padrão).

Habilitado, o servidor consulta a API pública do EduPlay para buscar vídeos por título e ler título e miniatura de um vídeo. Desabilitado, este plugin **não faz nenhuma requisição** ao EduPlay e só funcionam links de vídeo colados.

A API usada (``/api/v1/search`` e ``/api/v1/videos/{id}``) é pública, mas **não é documentada pela RNP**, então pode mudar sem aviso. Confirme com a RNP antes de depender dela em produção.

Capacidades
-----------

* ``local/eduplay:manage``: gerenciar as configurações do plugin (gerentes, por padrão).

Privacidade
-----------

O plugin não armazena dados pessoais. Com as consultas remotas habilitadas, o **texto digitado na busca de vídeos** é enviado pelo servidor ao serviço EduPlay (``eduplay.rnp.br``); nenhum identificador do usuário é enviado. Isso está declarado na Privacy API do Moodle (local externo).
