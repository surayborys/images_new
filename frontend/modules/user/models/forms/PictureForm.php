<?php

namespace frontend\modules\user\models\forms;

use yii\base\Model;

/**
 * This is the model for the uploaded picture
 * @property $picture
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class PictureForm extends Model
{

    public $picture;

    public function rules()
    {
        return [
            [['picture'], 'file',
                'extensions' => ['jpg'],
                'checkExtensionByMimeType' => true
            ],
        ];
    }

}
