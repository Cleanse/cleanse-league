<?php namespace Cleanse\League\Classes;

class FormHelper
{
    /**
     * Generate form fields types list
     * @return array
     */
    public function getTypeOptions($value, $formData)
    {

        $fieldTypes = $this->getFieldTypes();

        $types = [];

        if (!$fieldTypes) {
            return [];
        }

        foreach ($fieldTypes as $key => $value) {
            $types[$key] = 'janvince.smallcontactform::lang.settings.form_field_types.' . $key;
        }

        return $types;

    }

    /**
     * Generate form fields types list
     * @return array
     */
    public function getValidationTypeOptions($value, $formData)
    {

        return [
            'required' => 'janvince.smallcontactform::lang.settings.form_field_validation.required',
            'email' => 'janvince.smallcontactform::lang.settings.form_field_validation.email',
            'numeric' => 'janvince.smallcontactform::lang.settings.form_field_validation.numeric',
        ];
    }

    /**
     * HTML field types mapping array
     * @param bool
     * @return array
     */
    public static function getFieldTypes($type = false)
    {
        $types = [
            'text' => [
                'html_open' => 'input',
                'attributes' => [
                    'type' => 'text',
                ],
                'html_close' => NULL,
            ],

            'email' => [
                'html_open' => 'input',
                'attributes' => [
                    'type' => 'email',
                ],
                'html_close' => NULL,
            ],

            'textarea' => [
                'html_open' => 'textarea',
                'attributes' => [
                    'rows' => 5,
                ],
                'html_close' => 'textarea',
            ]
        ];

        if ($type) {
            if (!empty($types[$type])) {
                return $types[$type];
            }
        }

        return $types;
    }
}
