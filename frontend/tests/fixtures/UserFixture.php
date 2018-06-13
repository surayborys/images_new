<?php

namespace frontend\tests\fixtures;

use yii\test\ActiveFixture;

/**
 * User Fixture
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class UserFixture extends ActiveFixture{
    
    public $modelClass = 'frontend\models\User';
    public $depends = ['frontend\tests\fixtures\PostFixture'];
}
