<?php
return [
    'adminEmail'  => 'admin@example.com',
    
    //file storage params
    'maxFileSize' => 1024*1024*40, //40 megabites
    'storagePath' => '@frontend/web/uploads/',
    'storageUri'  => '/uploads/', //http://images.com/uploads/f1/d2/add5675f64efd8987cdfe520.jpg
    
    'defaultProfileImage' => '/images/default.jpg',
    
    'profilePicture' => [
        'maxWidth' => 1280,
        'maxHeight' => 1024,
    ],
    
    'postPicture' => [
        'maxWidth' => 1280,
        'maxHeight' => 1024,
    ],
];
