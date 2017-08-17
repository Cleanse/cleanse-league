<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Request;
use Session;
use Validator;
use Illuminate\Support\MessageBag;
use Cms\Classes\ComponentBase;
use Cleanse\League\Classes\FormHelper;
use Cleanse\League\Models\Season;

use Cleanse\League\Classes\Scheduler;

class ManagerSeasons extends ComponentBase
{
    public $season;

    private $postData = [];
    private $post;

    private $validationRules;
    private $validationMessages;

    private $errorAutofocus;

    public $result;

    public function componentDetails()
    {
        return [
            'name'        => 'PvPaissa League Manager Log',
            'description' => 'PvPaissa League Manager log of actions.'
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $this->season = Season::orderBy('id', 'desc')->first();

        return [
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Season Name',
                'validation' => [['validation_type' => 'required', 'validation_error' => 'Required.']],
                'placeholder' => '2017 Fall Season',
                'default' => $this->season->name ?? ''
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->page['flashSuccess'] = Session::get('flashSuccess');
    }

    public function onUpdate()
    {
        $mode = post('mode', 'create');
        $this->page['mode'] = $mode;
    }

    public function onFormSend()
    {
        /**
         * Validation
         */
        $this->setFieldsValidationRules();

        $this->post = Input::all();

        // Validate
        $validator = Validator::make($this->post, $this->validationRules, $this->validationMessages);
        $validator->valid();
        $this->validationMessages = $validator->messages();
        $this->setPostData($validator->messages());

        if ($validator->invalid()) {
            $errors = [];

            // Form main error msg
            $errors[] = 'There was an error sending your data!';

            Flash::error(implode(PHP_EOL, $errors));

        } else {
            Flash::success('Championship was updated.');

            Session::flash('flashSuccess', true);

            $championship = Championship::find($this->championship->id);

            // Store data in DB
            $championship->storeFormData($this->postData);

            // Redirect to prevent repeated sending of form
            // Clear data after success AJAX send
            if (!Request::ajax()) {
                return Redirect::refresh();
            } else {
                $this->post = [];
                $this->postData = [];
                $this->page['flashSuccess'] = true;
            }
        }
    }

    public function getFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onFormSend';
        $attributes['data-request-validate'] = NULL;
        $attributes['data-request-update'] = "'" . $this->alias . "::cleanse-message':'#cleanse-league-form-message','" . $this->alias . "::cleanse-form':'#cleanse-league-form'";
        $attributes['data-request-confirm'] = 'Is the information correct?';

        return $attributes;
    }

    /**
     * Generate field HTML code
     * @param array
     * @return string
     */
    public function getFieldHtmlCode(array $fieldSettings)
    {
        if (empty($fieldSettings['name']) && empty($fieldSettings['type'])) {
            return NULL;
        }

        $fieldType = FormHelper::getFieldTypes($fieldSettings['type']);
        $fieldRequired = $this->isFieldRequired($fieldSettings);

        $output = [];

        $wrapperCss = 'form-group';

        // Add wrapper error class if there are any
        if (!empty($this->postData[$fieldSettings['name']]['error'])) {
            $wrapperCss .= ' has-error';
        }

        $output[] = '<div class="' . $wrapperCss . '">';

        // Label
        if (!empty($fieldSettings['label'])) {
            $output[] = '<label class="control-label ' . ($fieldRequired ? 'required' : '') . '" for="' . $fieldSettings['name'] . '">' . $fieldSettings['label'] . '</label>';
        }

        // Add help-block if there are errors
        if (!empty($this->postData[$fieldSettings['name']]['error'])) {
            $output[] = '<small class="help-block">' . $this->postData[$fieldSettings['name']]['error'] . "</small>";
        }

        //Value
        if (!empty($this->postData[$fieldSettings['name']]['value']) && empty($fieldType['html_close'])) {
            $value = $this->postData[$fieldSettings['name']]['value'];
        } elseif (!empty($fieldSettings['default'])) {
            $value = $fieldSettings['default'];
        } else {
            $value = '';
        }

        // Field attributes
        $attributes = [
            'id' => $fieldSettings['name'],
            'name' => $fieldSettings['name'],
            'class' => 'form-control',
            'value' => $value,
            'placeholder' => (!empty($fieldSettings['placeholder']) ? $fieldSettings['placeholder'] : '')
        ];

        // Autofocus only when no error
        if (!empty($fieldSettings['autofocus']) && !Flash::error()) {
            $attributes['autofocus'] = NULL;
        }

        // Add custom attributes from field settings
        if (!empty($fieldType['attributes'])) {
            $attributes = array_merge($attributes, $fieldType['attributes']);
        }

        // Add error class if there are any and autofocus field
        if (!empty($this->postData[$fieldSettings['name']]['error'])) {
            $attributes['class'] = $attributes['class'] . ' error';

            if (empty($this->errorAutofocus)) {
                $attributes['autofocus'] = NULL;
                $this->errorAutofocus = true;
            }

        }

        if ($fieldRequired) {
            $attributes['required'] = NULL;
        }


        $output[] = '<' . $fieldType['html_open'] . ' ' . $this->formatAttributes($attributes) . '>';

        // For pair tags insert value between
        if (!empty($fieldSettings['default']) && !empty($fieldType['html_close'])) {
            $output[] = $fieldSettings['default'];
        }

        if (!empty($this->postData[$fieldSettings['name']]['value']) && !empty($fieldType['html_close'])) {
            $output[] = $this->postData[$fieldSettings['name']]['value'];
        }

        if (!empty($fieldType['html_close'])) {
            $output[] = '</' . $fieldType['html_close'] . '>';
        }

        $output[] = "</div>";

        return (implode('', $output));
    }

    /**
     * Search for required validation type
     * @param array
     * @return bool
     */
    private function isFieldRequired($fieldSettings)
    {

        if (empty($fieldSettings['validation'])) {
            return false;
        }

        foreach ($fieldSettings['validation'] as $rule) {
            if (!empty($rule['validation_type']) && $rule['validation_type'] == 'required') {
                return true;
            }
        }

        return false;

    }

    /**
     * Format attributes array
     * @param array
     * @return array
     */
    private function formatAttributes(array $attributes)
    {

        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return implode(' ', $output);

    }

    /**
     * Generate Submit Button HTML code
     * @return string
     */
    public function getSubmitButtonHtmlCode()
    {
        if (!count($this->fields())) {
            return 'Please add some form fields.';
        }

        $output = [];

        $output[] = '<div id="submit-wrapper" class="form-group">';

        $output[] = '<button data-attach-loading class="oc-loader btn btn-primary">';

        $output[] = 'Create Season';

        $output[] = '</button>';

        $output[] = "</div>";

        return (implode('', $output));
    }

    /**
     * Generate validation rules and messages
     */
    private function setFieldsValidationRules()
    {

        $fieldsDefinition = $this->fields();

        $validationRules = [];
        $validationMessages = [];

        foreach ($fieldsDefinition as $field) {

            if (!empty($field['validation'])) {
                $rules = [];

                foreach ($field['validation'] as $rule) {
                    $rules[] = $rule['validation_type'];

                    if (!empty($rule['validation_error'])) {
                        $validationMessages[($field['name'] . '.' . $rule['validation_type'])] = $rule['validation_error'];
                    }
                }
                $validationRules[$field['name']] = implode('|', $rules);
            }

        }

        $this->validationRules = $validationRules;
        $this->validationMessages = $validationMessages;

    }

    /**
     * Generate post data with errors
     * @param \Illuminate\Support\MessageBag
     */
    private function setPostData(MessageBag $validatorMessages)
    {
        foreach (Input::all() as $key => $value) {
            $this->postData[$key] = [
                'value' => e(Input::get($key)),
                'error' => $validatorMessages->first($key),
            ];
        }
    }

    public function onSchedule()
    {
        $schedule = new Scheduler();

        $this->page['result'] = $schedule->buildSchedule();
    }
}
