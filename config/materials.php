<?php

$families = ['citrus', 'floral', 'herbal', 'woody', 'resinous', 'spicy', 'green', 'fruity', 'balsamic', 'animalic', 'gourmand', 'leathery', 'smoky', 'marine'];
$functions = ['fixative', 'modifier'];
$safety = ['phototoxic', 'toxic', 'irritant', 'allergenic', 'sensitizer'];
$effects = ['calming', 'focus', 'uplifting', 'grounding', 'sedative', 'aphrodisiac', 'stimulant'];

sort($families);
sort($functions);
sort($safety);
sort($effects);

return [
    'families' => $families,
    'functions' => $functions,
    'safety' => $safety,
    'effects' => $effects,
];
