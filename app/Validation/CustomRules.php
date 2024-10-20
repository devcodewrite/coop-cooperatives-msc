<?php

namespace App\Validation;

use Config\Database;

class CustomRules
{
    /**
     * Generic validation rule to check if a value exists in the table under specific conditions using request fields.
     *
     * @param string|int $value The value being validated (can be string or int).
     * @param string $params The table, field, and request fields for conditions in the format 'table.field,condition_field1,request_field1,condition_field2,request_field2,...'.
     * @param array  $data The full data array being validated.
     *
     * @return bool True if the value exists with the specified conditions, false otherwise.
     */
    public function exists_for_where($value, string $params, array $data): bool
    {
        // Split the parameters passed to the rule
        $params = explode(',', $params);

        if (count($params) < 3 || count($params) % 2 != 1) {
            return false; // Invalid rule parameters
        }

        // Extract table and field
        $table = $params[0];      // Table name
        $field = $params[1];      // Field to match the value against

        // Connect to the database
        $db = Database::connect();
        $builder = $db->table($table)->where($field, $value);

        // Loop through the condition field/request field pairs
        for ($i = 2; $i < count($params)-1; $i += 2) {
            $conditionField = $params[$i];              // Database field name for the condition
            $requestField = $params[$i + 1];            // Request field name to pull the value from

            // Fetch the condition value from the request data
            if (isset($data[$requestField])) {
                $conditionValue = $data[$requestField];
                $builder->where($conditionField, $conditionValue);  // Add WHERE condition
            } else {
                return false; // If request field value is missing, return false
            }
        }

        // Execute the query and return true if a matching record is found
        return $builder->get()->getNumRows() > 0;
    }
}
