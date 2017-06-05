<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FormTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formController     = new \App\Http\Controllers\FormsController();
        $formModel          = new \App\Models\FormTemplate();

        $form = $formModel->create([
            'name'      => 'DefaultForm_RegistrationFirstForm',
            'status'    => 0
        ]);

        $json = '[{"config":{"_id":"0","title":"New Page","tabId":"1"},"fields":[{"_id":"321","type":"header","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[]},"ordenate":1,"isRequired":false,"label":"Header","help":"","class":""},"comments":[]},{"_id":"32","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"error":false,"ordenate":2,"isRequired":false,"label":"Text Field ","help":"Help Text Goes Here","value":"","checked":false,"class":""},"comments":[]},{"_id":"3","type":"email-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"error":false,"ordenate":3,"isRequired":false,"label":"E-mail Field ","help":"Help Text Goes Here","value":"","checked":false,"class":""},"comments":[]}]}]';
        $json = json_decode($json);

        $formController->_saveContainers($json, $form->id);

        $defaultcontroller = new \App\Http\Controllers\Controller();
        $_id = $defaultcontroller->_storeFormToMongo($form);

        $form->delete();
    }
}
