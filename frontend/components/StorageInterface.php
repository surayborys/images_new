<?php

namespace frontend\components;

use yii\web\UploadedFile;

/**
 * File Storage Interface
 * 
 * @author Borys Suray <surayborys@gmail.com>
 */
interface StorageInterface {
    
    public function saveUploadedFile(UploadedFile $file);
    
    public function getFile(string $filename);
}
