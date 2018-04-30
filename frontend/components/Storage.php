<?php


namespace frontend\components;

use yii\base\Component;
use frontend\components\StorageInterface;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use Yii;

/**
 * File Storage Class
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class Storage extends Component implements StorageInterface {
    
    public $fileName;
    /**
     * save given UploadedFile instance to a disk
     * @param UploadedFile $file
     * @return string|null
     */
    public function saveUploadedFile(UploadedFile $file)
    {
        $path = $this->preparePath($file);
        
        if($path && $file->saveAs($path)) {
            return $this->fileName;
        }
        
    }
    
    /**
     * get  file uri from the given build-from-hash filename
     * @param string $filename
     * @return string
     */
    public function getFile(string $filename){
        return Yii::$app->params['storageUri'] . $filename;
    }
    
    /**
     * get path to save the UploadedFile $file to a file storage
     * @param UploadedFile $file
     * @return string|null
     */
    protected function preparePath(UploadedFile $file)
    {
        $this->fileName = $this->getFileName($file);
        
        $path = $this->getStoragePath() . $this->fileName;
        
        $normalizedPath = FileHelper::normalizePath($path);
        
        if(FileHelper::createDirectory(dirname($normalizedPath))) {
            return $path;
        }
    }

    /**
     * compose the path like 30/33/e22c7e49b5fc6ad795c652b224a2f6d33b26.jpg for the given Uploaded File instance
     * @param UploadedFile $file
     * @return string|null
     */
    protected function getFileName(UploadedFile $file)
    {
        //$file->tempName  /tmp/phpEt7ejl
        $hash = sha1_file($file->tempName);
        //add slashes
        $addFirstSlash = substr_replace($hash, '/', 2, 0); //get 30/33e22c7e49b5fc6ad795c.....
        $addSecondSlash = substr_replace($addFirstSlash, '/', 5, 0); //get 30/33/e22c7e49b5fc6ad795c....
        
        //get name like 30/33/e22c7e49b5fc6ad795c652b224a2f6d33b26.jpg
        return $addSecondSlash . '.' .$file->extension;
        
    }
    
    /**
     * get path for the file storage
     * @return string
     */
    protected function getStoragePath(){
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }
    
    /**
     * delete file from the file storage
     * @param string|null $filename
     * @return boolean
     */
    public function deleteFile($filename){
        
        if($filename == null){
            return true;
        }
        $file = $this->getStoragePath() . $filename;
        
        if(file_exists($file)){
            return unlink($file);
        }
        return true;
    }
}
