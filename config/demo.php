<?php

return [
    'credentials' => [
        'admin_password' => env('DEMO_ADMIN_PASSWORD'),
        'artist_password' => env('DEMO_ARTIST_PASSWORD'),
        'client_password' => env('DEMO_CLIENT_PASSWORD'),
    ],
    'artists' => [
        'maria-makiling-studio' => [
            'name' => 'Maria Makiling Studio',
            'email' => 'artist@likhaspace.ph',
            'country' => 'Philippines',
            'bio' => 'Digital artist exploring Filipino folklore and contemporary stories.',
            'studio' => 'Makiling Art House',
            'sales' => 28,
            'revenue' => 37250,
        ],
        'athena-art' => [
            'name' => 'Athena Art',
            'email' => 'athena@likhaspace.test',
            'country' => 'Greece',
            'bio' => 'Myth-inspired digital illustration with a contemporary point of view.',
            'studio' => 'Athena Studio',
            'sales' => 36,
            'revenue' => 65400,
        ],
        'heather-mcrae' => [
            'name' => 'Heather McRae',
            'email' => 'heather@likhaspace.test',
            'country' => 'Scotland',
            'bio' => 'Illustrator inspired by the quiet magic of Scottish folklore.',
            'studio' => 'Loch & Light Studio',
            'sales' => 19,
            'revenue' => 28750,
        ],
        'kaito-renders' => [
            'name' => 'Kaito Renders',
            'email' => 'kaito@likhaspace.test',
            'country' => 'Japan',
            'bio' => 'Digital creator bringing Japanese folklore into vivid new worlds.',
            'studio' => 'Kaito Digital Studio',
            'sales' => 42,
            'revenue' => 81600,
        ],
    ],
    'artworks' => [
        1 => ['title' => 'Maria Makiling, Guardian of the Mountain', 'artist' => 'Maria Makiling Studio', 'artist_id' => 'maria-makiling-studio', 'country' => 'Philippines', 'price' => 1250, 'image' => 'photo-1618005182384-a83a8bd57fbe', 'icon' => 'bi-flower1', 'description' => 'Inspired by the diwata who watches over Mount Makiling, this artwork celebrates the beauty and spirit of Filipino folklore.', 'status' => 'approved'],
        2 => ['title' => 'Athena and the Owl of Wisdom', 'artist' => 'Athena Art', 'artist_id' => 'athena-art', 'country' => 'Greece', 'price' => 2100, 'image' => 'photo-1568605117036-5fe5e7bab0b7', 'icon' => 'bi-stars', 'description' => 'A contemporary interpretation of Athena, goddess of wisdom, and her enduring symbol, the owl.', 'status' => 'pending'],
        3 => ['title' => 'The Kelpie of Loch Ness', 'artist' => 'Heather McRae', 'artist_id' => 'heather-mcrae', 'country' => 'Scotland', 'price' => 1850, 'image' => 'photo-1578632767115-351597cf2477', 'icon' => 'bi-water', 'description' => 'A mysterious water spirit from Scottish legend emerges from the mist of a Highland loch.', 'status' => 'pending'],
        4 => ['title' => 'The Foxfire Promise', 'artist' => 'Kaito Renders', 'artist_id' => 'kaito-renders', 'country' => 'Japan', 'price' => 1500, 'image' => 'photo-1607604276583-eef5d076aa5f', 'icon' => 'bi-moon-stars', 'description' => 'A playful kitsune spirit leaves a trail of foxfire beneath a moonlit Japanese sky.', 'status' => 'approved'],
        5 => ['title' => 'Ibong Adarna Reimagined', 'artist' => 'Maria Makiling Studio', 'artist_id' => 'maria-makiling-studio', 'country' => 'Philippines', 'price' => 3500, 'image' => 'photo-1579783902614-a3fb3927b675', 'icon' => 'bi-feather', 'description' => 'The legendary bird from the beloved Filipino epic takes flight in vivid digital color.', 'status' => 'approved'],
        6 => ['title' => 'Medusa’s Garden', 'artist' => 'Athena Art', 'artist_id' => 'athena-art', 'country' => 'Greece', 'price' => 2800, 'image' => 'photo-1544816155-12df9643f363', 'icon' => 'bi-flower2', 'description' => 'A mythic garden remembers Medusa beyond the familiar legend, honoring her strength and transformation.', 'status' => 'rejected'],
        7 => ['title' => 'Selkie Song at Dusk', 'artist' => 'Heather McRae', 'artist_id' => 'heather-mcrae', 'country' => 'Scotland', 'price' => 2300, 'image' => 'photo-1518837695005-2083093ee35b', 'icon' => 'bi-water', 'description' => 'A selkie returns to the shore in a quiet scene inspired by Scotland’s coastal folklore.', 'status' => 'approved'],
        8 => ['title' => 'Amaterasu Brings the Dawn', 'artist' => 'Kaito Renders', 'artist_id' => 'kaito-renders', 'country' => 'Japan', 'price' => 2650, 'image' => 'photo-1490730141103-6cac27aaab94', 'icon' => 'bi-brightness-high', 'description' => 'The sun goddess Amaterasu steps into the morning, bringing light back to the world.', 'status' => 'approved'],
    ],
];
