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

-[x] não permitir editar app "application/{id}/edit" quando a aplicação ja estiver liberada para o usuário
-[x] enviar e-mail para o cliente quando a aplicação estiver liberada para preenchimento (e se for recusada?)
-[ ] Front (Templates de e-mail) Register, Aprovação/reprovação do App, redefinir senha, Steps approve/reprove
-[x] qualquer um esta podendo submitar FORM do step. nao permitir. (ver isso em workflowController em SAVES methdos... ta facil) LEO fez
-[ ] Verificar como esta a VIEw de APPROVALS

crontab = schtasks no windows

processo 3 anos

-[x] mudar app para wt_emailconfirm
-[x] mudar steps para '1' (que ja esta configurado, pronto para preencher)
-[x] alterar created_at para a data atual
-[x] ====== verificar se o processo de email funciona corretamente


-[x] exibir o arquivo uploaded no form para o staff ver (filesystem, retrieve files1)
-[x] Exibir informações corretas na dashboard do admin1
-[x] tentar fazer o fluxo de DENY application
-[ ] configurar mongo na digital
-[ ] step recorrente
-[ ] Shortcodes, criar e configurar
-[x] Templates de email
-[ ] templates de tela de "before validate email"
-[ ] Dropzone, verificar

##Step recorrente
- Os steps recorrentes serão aplicados para todos os clientes ou pode ter algum cliente que não participa de determinada recorrencia?
- Existe um tempo maximo que o cliente é obrigado a preencher? Por exemplo, passarma 4 meses.. ele tem 15 dias para preencher.
- Se existir um tempo obrgatorio. O que acontece se não preencher? Bloqueia a aplicação?
- E se algum documento for enviado e estiver com erros. O que acontece? Bloqueia a aplicação?
- O que realmente será solicitado em:
 -> File upload: apenas um arquivo?
 -> Validation: ???
 -> Files from employee roster: Apenas file upload?

##Quando subir para producao
-[ ] Lembrar de configurar o listener de jobs (php artisan queue:listen)
-[ ] configurar o CRONTAB para App\Console\Commands\FlowThreeYears, schedule taking, * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

setup anacron
sudo nano /etc/anacrontab
@daily          0       naamta.daily    /var/www/naamta-backend/ php artisan app:dailyActions

//view of continuous forms when application isn't accredited yet
    //It's possible will be removed because on documentation says: "Enable continuos when applicant BECOMES ACCREDITED"...
    Route::get('/application/{id}/continuousCompliances', 'ApplicationsController@continuousComplianceNotAccredited');

##Sexta - 02/06
-[x] Verificar se no foreach homes.applications[:180] $stepwithForms se esta pegando o grupo de formulario do step.

##Continuous Compliance
-[ ] Dúvidas: Todos os forms serão um padrão para todos os clientes? Se não for, vai demorar mais, porque tem que fazer o
cadastro de forms > clientes > por tempo..
-[ ] Se for automático (padrão para todos), quando criar um form novo, atribuir para todos os clientes ou apenas para novos?
-[ ] Se for atribuir para todos. por exemplo, um form recorrente de 4 em 4 meses. Conta a partir da data do registro do form ou da aprovação final da aplicação?