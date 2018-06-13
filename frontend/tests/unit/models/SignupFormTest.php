<?php
namespace frontend\tests\models;
use frontend\modules\user\models\SignupForm;
use frontend\tests\fixtures\UserFixture;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;
    
    public function _fixtures()
    {
        return [
            'users' => UserFixture::className()
        ];
    }

    // tests
    public function testTrimUsername()
    {
        $model = new SignupForm;
        $model->username = '  Some Username   ';
        $model->email = 'email@mail.com';
        $model->password = '111111';
        
        $model->signup();
        
        expect($model->username)->equals('Some Username');
    }
    
    public function testRequiredUsername()
    {
        $model = new SignupForm;
        $model->username = '';
        $model->email = 'email@mail.com';
        $model->password = '111111';
        
        $model->signup();
        
        expect($model->getFirstError('username'))->equals('Username cannot be blank.');
    }
    
    public function testMinLengthUsername()
    {
        $model = new SignupForm;
        $model->username = 'q';
        $model->email = 'email@mail.com';
        $model->password = '111111';
        
        $model->signup();
        
        expect($model->getFirstError('username'))->equals('Username should contain at least 2 characters.');
    }
    
    public function testUniqueEmail()
    {
        $model = new SignupForm;
        $model->username = 'User Name';
        $model->email = '1@got.com';
        $model->password = '111111';
        
        $model->signup();
        
        expect($model->getFirstError('email'))->equals('This email address has already been taken.');
    }
    
    public function testRequiredPassword()
    {
        $model = new SignupForm;
        $model->username = 'User Name';
        $model->email = 'email@mail.com';
        $model->password = '';
        
        $model->signup();
        
        expect($model->getFirstError('password'))->equals('Password cannot be blank.');
    }
    
}