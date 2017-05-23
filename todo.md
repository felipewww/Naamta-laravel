- STEPS

-[x] Não permitir criar o mesmo e-mail template em onsucces/rejected
-[x] Por onde será selecionado o Previous step? drag and drop ou select (como esta)? Se for por select.
-[x] Confirmação ao deletar um EMAIL USAGE no caso de edit que o e-mail ja esta no banco.
-[x] Carregar os selects de e-mail para STEPS default
-[ ] TODO - Create a message to notify user that this e-mail wasn't saved on DB, because FK already exists.

- LAYOUT
-[x] eventos e icones dos botoes de ação
-[x] alterar topos das páginas
-[x] delete do step default
-[x] sweetalert? Não, bootstrap modal
-[x] criar um safeLeave, mensagem de aviso antes de sair da página, caso tenho feito alteração em STEPS
-[x] status dos steps clonados
-[ ] tratamentos de error em ajax

-[x] Exibir mensagem em FROMS.SHOW quando o form for trashed (vindo do step)
-[x] ajustar vlaidação de form do appSettings, textarea de description é obrigatorio
-[x] botão CANCEL da edição do step da aplicação, deve voltar aos steps da app, esta indo para os steps default
-[x] choosen na seleção de form/screen do step

-[ ] Fluxo de 3 anos deve ser um novo registro do mesmo usuario e um backup geral. A aplicação (steps, forms e etc)
e a aplicação terá todos os status alterados para FALSE, definindo-a como uma NEW REGISTERED APPLICATION.

Sexta feira eu fiz o clone da aplicação completa, jogando para o mongo. Falta agora quando alterar a Approvals,
deletar o relacionamento no sql e no mongo e criar o novo. O form parece que ja estava feito isso, mas precisa testar.

Falta também exibir o ID do item (approval/form) selecionado buscando das tabelas _forms e _approvals de relacionamento.

##quando subir para producao
-[ ] Lembrar de configurar o listener de jobs (php artisan queue:listen)