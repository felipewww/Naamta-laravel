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
            'name'      => 'FirstForm',
            'status'    => 1
        ]);

        $json = '[{"config":{"_id":"0","title":"New Page","tabId":""},"fields":[{"_id":"","type":"header","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[]},"ordenate":1,"isRequired":false,"label":"Header","help":"","class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"error":false,"ordenate":2,"isRequired":false,"label":"Text Field ","help":"Help Text Goes Here","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"email-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"error":false,"ordenate":3,"isRequired":false,"label":"E-mail Field ","help":"Help Text Goes Here","value":"","checked":false,"class":""},"comments":[]}]}]';
        $json = json_decode($json);

        $formController->_saveContainers($json, $form->id);
       /* DB::table('form_templates')->insert(
            array
            (
                [
                    'id'        => 1,
                    'name'     => 'Form Template One',
                    'status'    => 1,
                    'created_at' => Carbon::now(-3),
                    'updated_at' => Carbon::now(-3),
                ],
                [
                    'id'        => 2,
                    'name'     => 'Form Template Two',
                    'status'    => 1,
                    'created_at' => Carbon::now(-2),
                    'updated_at' => Carbon::now(-2),
                ],
                [
                    'id'        => 3,
                    'name'     => 'Form Template Three',
                    'status'    => 1,
                    'created_at' => Carbon::now(3),
                    'updated_at' => Carbon::now(3),
                ],
            )
        );*/
    }
}
