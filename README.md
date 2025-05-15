# Dynamic Form Builder with Laravel Filament

This application allows you to create dynamic forms with various input types through a user-friendly admin interface built with Laravel Filament.

## Features

- Create and manage form configurations through the Filament admin panel
- Support for multiple input types:
  - Text Input
  - Text Area
  - Number Input
  - Dropdown Select
  - Multi-Select
  - Checkbox
  - Radio Buttons
  - Date Picker
  - Date & Time Picker
  - File Upload
- Configure validation rules for each field
- Preview and test forms
- Handle form submissions with validation

## Getting Started

1. Access the admin panel at `/admin`
2. Navigate to the "Form Builder" section
3. Create a new form configuration:
   - Provide basic form information (name, title, description)
   - Add form fields with different input types
   - Configure each field with validation rules and options
4. Save the form configuration
5. Preview the form using the "Preview" action in the form list

## How to Use

### Creating a Form

1. In the admin panel, go to "Form Builder" and click "New Form Configuration"
2. Fill in the basic form information:
   - Name: A unique identifier for the form (no spaces)
   - Title: The display title for the form
   - Description: Optional description of the form's purpose
3. Add form fields by clicking "Add Field" in the "Form Fields" section
4. For each field, configure:
   - Name: Field identifier (no spaces)
   - Label: Display label for the field
   - Type: Select the input type
   - Help Text: Optional help text for the field
   - Required: Toggle whether the field is required
5. Depending on the field type, additional configuration options will appear:
   - For select/multiselect/radio: Add options with label and value pairs
   - For number inputs: Set min, max, and step values
   - For text/textarea: Set min length, max length, and placeholder
6. Save the form configuration

### Previewing a Form

1. In the form list, click the "Preview" action for the form you want to view
2. This will open the form in a new tab where you can test it
3. Fill in the form and submit it to test validation and submission handling

### Embedding Forms in Your Application

To embed a form in your application, you can:

1. Link directly to the form URL: `/forms/{formId}`
2. Use the FormController to render the form in your own views
3. Customize the form styling by modifying the preview.blade.php template

## Technical Implementation

- Form configurations are stored in the database with JSON fields for flexibility
- The FormController handles form rendering and submission
- Dynamic validation rules are generated based on the form configuration
- Tailwind CSS is used for styling the form preview

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
