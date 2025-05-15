<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a preview of the form based on its configuration.
     *
     * @param  \App\Models\FormConfiguration  $formConfiguration
     * @return \Illuminate\View\View
     */
    public function preview(\App\Models\FormConfiguration $formConfiguration)
    {
        if (!$formConfiguration->is_active) {
            abort(404, 'This form is not active');
        }

        return view('forms.preview', [
            'form' => $formConfiguration,
        ]);
    }

    /**
     * Handle the form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormConfiguration  $formConfiguration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request, \App\Models\FormConfiguration $formConfiguration)
    {
        if (!$formConfiguration->is_active) {
            abort(404, 'This form is not active');
        }

        // Validate the form data based on the configuration
        $validationRules = [];
        $validationMessages = [];

        foreach ($formConfiguration->fields as $field) {
            if (!empty($field['name'])) {
                // Basic required validation
                if (!empty($field['required']) && $field['required']) {
                    $validationRules[$field['name']] = 'required';
                } else {
                    $validationRules[$field['name']] = 'nullable';
                }

                // Additional validation based on field type
                switch ($field['type']) {
                    case 'number':
                        $validationRules[$field['name']] .= '|numeric';

                        if (isset($field['min'])) {
                            $validationRules[$field['name']] .= '|min:' . $field['min'];
                        }

                        if (isset($field['max'])) {
                            $validationRules[$field['name']] .= '|max:' . $field['max'];
                        }
                        break;

                    case 'text':
                    case 'textarea':
                        if (isset($field['min_length'])) {
                            $validationRules[$field['name']] .= '|min:' . $field['min_length'];
                        }

                        if (isset($field['max_length'])) {
                            $validationRules[$field['name']] .= '|max:' . $field['max_length'];
                        }
                        break;

                    case 'date':
                        $validationRules[$field['name']] .= '|date';
                        break;

                    case 'datetime':
                        $validationRules[$field['name']] .= '|date_format:Y-m-d H:i:s';
                        break;

                    case 'select':
                        if (isset($field['options']) && is_array($field['options'])) {
                            $options = array_column($field['options'], 'value');
                            $validationRules[$field['name']] .= '|in:' . implode(',', $options);
                        }
                        break;

                    case 'multiselect':
                        $validationRules[$field['name']] .= '|array';

                        if (isset($field['options']) && is_array($field['options'])) {
                            $options = array_column($field['options'], 'value');
                            $validationRules[$field['name'] . '.*'] = 'in:' . implode(',', $options);
                        }
                        break;

                    case 'file':
                        $validationRules[$field['name']] .= '|file';
                        break;
                }

                // Custom validation messages
                if (!empty($field['label'])) {
                    $validationMessages[$field['name'] . '.required'] = 'The ' . $field['label'] . ' field is required.';
                }
            }
        }

        $validatedData = $request->validate($validationRules, $validationMessages);

        // Store the form submission in the database
        FormSubmission::create([
            'form_configuration_id' => $formConfiguration->id,
            'data' => $validatedData,
        ]);

        return redirect()->back()->with('success', 'Form submitted successfully!');
    }
}
