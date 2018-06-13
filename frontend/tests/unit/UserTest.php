<?php
namespace frontend\tests;

use Yii;
use frontend\tests\fixtures\UserFixture;


class UserTest extends \Codeception\Test\Unit
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
    
    protected function _before()
    {
        Yii::$app->setComponents([
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ],
        ]);
    }

    // tests
    public function testGetNicknameOnNicknameEmpty()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        expect($user->getNickname())->equals('1');
    }
    
    public function testGetNicknameOnNicknameNotEmpty()
    {
        $user = $this->tester->grabFixture('users', 'user3');
        expect($user->getNickname())->equals('snow');
    }
    
    public function testCountPostsOnPostsExist()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        expect($user->countPosts())->equals(3);
    }
    
    public function testCountPostsOnPostsEmpty()
    {
        $user = $this->tester->grabFixture('users', 'user3');
        expect($user->countPosts())->equals(0);
    }
    
    public function testFollowUser()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        $userToFollow = $this->tester->grabFixture('users', 'user3');
        
        $user->follow($userToFollow);
        $this->tester->seeRedisKeyContains('user:1:subscriptions', 3);
        $this->tester->seeRedisKeyContains('user:3:followers', 1);
        
        $this->tester->sendCommandToRedis('del', 'user:1:subscriptions');
        $this->tester->sendCommandToRedis('del', 'user:3:followers');
    }
    
    public function testUnsubscribe()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        $userToFollow = $this->tester->grabFixture('users', 'user3');
        
        $user->follow($userToFollow);
        $user->unsubscribe($userToFollow);
        
        $this->tester->dontSeeInRedis('user:1:subscriptions', 3);
        $this->tester->dontSeeInRedis('user:3:followers', 1);
        
        $this->tester->sendCommandToRedis('del', 'user:1:subscriptions');
        $this->tester->sendCommandToRedis('del', 'user:3:followers');
    }    
}