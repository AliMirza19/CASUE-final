<?php

// Mocking the behavior of callAi and the extraction logic in SmartTickerService
function testExtraction($resData) {
    if (is_array($resData)) {
        // Flatten if nested under 'insights' or other common keys
        if (isset($resData['insights']) && is_array($resData['insights'])) {
            return array_values($resData['insights']);
        }
        
        // Fallback: if it's already a flat array
        if (array_is_list($resData)) {
            return $resData;
        }
        
        // Fallback: take the first array found in the object
        foreach ($resData as $value) {
            if (is_array($value)) return array_values($value);
        }
    }
    return ["Fallback string"];
}

$testCases = [
    "Nested under insights" => [
        'insights' => ["Insight A", "Insight B"]
    ],
    "Nested under other key" => [
        'data' => ["Point 1", "Point 2"]
    ],
    "Flat array" => [
        "Direct 1", "Direct 2"
    ],
    "Completely weird" => [
        'status' => 'ok',
        'content' => ['X', 'Y']
    ]
];

foreach ($testCases as $name => $case) {
    echo "Testing: $name\n";
    print_r(testExtraction($case));
    echo "-------------------\n";
}
