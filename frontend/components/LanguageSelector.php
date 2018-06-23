<?php

namespace frontend\components;


use yii\base\BootstrapInterface;



/**
 * Description of LanguageSelector
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class LanguageSelector implements BootstrapInterface {
    //public $supportedLanguages = ['en-US', 'ru-RU'];
    
    public function bootstrap($app)
    {
        $cookieLanguage = $app->request->cookies['lang'];
        $supportedLanguages = $app->params['supportedLanguages'];
        
        if(isset($cookieLanguage) && in_array($cookieLanguage, $supportedLanguages)) {
            $app->language = $app->request->cookies['lang'];
        }
    }
    
}
