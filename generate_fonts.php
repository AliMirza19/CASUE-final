<?php
$json = json_decode(file_get_contents('storage/fonts_map.json'), true);
$wanted = [
    'Arial', 'Arial Black', 'Calibri', 'Cambria', 'Candara', 'Comic Sans MS', 'Consolas', 'Constantia', 'Corbel',
    'Courier New', 'Ebrima', 'Franklin Gothic Medium', 'Gabriola', 'Gadugi', 'Garamond', 'Georgia', 'Impact',
    'Ink Free', 'Javanese Text', 'Leelawadee UI', 'Lucida Console', 'Lucida Sans Unicode', 'Malgun Gothic',
    'Microsoft Himalaya', 'Microsoft JhengHei', 'Microsoft New Tai Lue', 'Microsoft PhagsPa', 'Microsoft Sans Serif',
    'Microsoft Tai Le', 'Microsoft YaHei', 'Microsoft Yi Baiti', 'MingLiU-ExtB', 'Mongolian Baiti', 'MV Boli',
    'Myanmar Text', 'Nirmala UI', 'Palatino Linotype', 'Segoe Print', 'Segoe Script', 'Segoe UI',
    'Segoe UI Black', 'Segoe UI Historic', 'Segoe UI Symbol', 'Sylfaen', 'Symbol', 'Tahoma', 'Times New Roman',
    'Trebuchet MS', 'Verdana', 'Webdings', 'Wingdings'
];
$map = [];
foreach ($json as $key => $file) {
    if (!str_contains($key, '(TrueType)')) continue;
    $clean = trim(str_replace('(TrueType)', '', $key));
    foreach ($wanted as $w) {
        if (str_starts_with($clean, $w)) {
            $variant = 'regular';
            if (str_contains(strtolower($clean), 'bold') && str_contains(strtolower($clean), 'italic')) $variant = 'bold_italic';
            elseif (str_contains(strtolower($clean), 'bold')) $variant = 'bold';
            elseif (str_contains(strtolower($clean), 'italic')) $variant = 'italic';
            $map[$w][$variant] = $file;
        }
    }
}
file_put_contents('storage/mapped_fonts.json', json_encode($map, JSON_PRETTY_PRINT));
echo "Done";
