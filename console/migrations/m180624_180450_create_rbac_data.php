<?php

use yii\db\Migration;
use backend\models\User;

/**
 * Class m180624_180450_create_rbac_data
 */
class m180624_180450_create_rbac_data extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;    
        // Define permissions
        $viewComplaintsListPermission = $auth->createPermission('viewComplaintsList');
        $auth->add($viewComplaintsListPermission);
        $viewPostPermission = $auth->createPermission('viewPost');
        $auth->add($viewPostPermission);
        $deletePostPermission = $auth->createPermission('deletePost');
        $auth->add($deletePostPermission);
        $approvePostPermission = $auth->createPermission('approvePost');
        $auth->add($approvePostPermission);
        $viewUsersListPermission = $auth->createPermission('viewUsersList');
        $auth->add($viewUsersListPermission);
        $viewUserPermission = $auth->createPermission('viewUser');
        $auth->add($viewUserPermission);
        $deleteUserPermission = $auth->createPermission('deleteUser');
        $auth->add($deleteUserPermission);
        $updateUserPermission = $auth->createPermission('updateUser');
        $auth->add($updateUserPermission);
        // Define roles with permissions
        $moderatorRole = $auth->createRole('moderator');
        $auth->add($moderatorRole);
        $auth->addChild($moderatorRole, $viewComplaintsListPermission);
        $auth->addChild($moderatorRole, $viewPostPermission);
        $auth->addChild($moderatorRole, $deletePostPermission);
        $auth->addChild($moderatorRole, $approvePostPermission);
        $auth->addChild($moderatorRole, $viewUsersListPermission);
        $auth->addChild($moderatorRole, $viewUserPermission);
        $adminRole = $auth->createRole('admin');
        $auth->add($adminRole);
        $auth->addChild($adminRole, $moderatorRole);
        $auth->addChild($adminRole, $deleteUserPermission);
        $auth->addChild($adminRole, $updateUserPermission);
        // Create admin user
        // Расчитывается, что после создания суперпользователя пароль будет изменен (или права админа переданы другому пользователю).     
        $user = new User([
            'email' => 'admin@admin.com',
            'username' => 'Admin',
            'password_hash' => '$2y$13$P9.d7KUb8C6BHCvkdzMsrOi5U.vIAw01UmriB.34PiN50e8nTGFge', // 111111
        ]);
        $user->generateAuthKey();
        $user->save();
        // Assign admin role to 
        $auth->assign($adminRole, $user->getId());
    }
    public function safeDown()
    {
        echo "m171015_143334_create_rbac_data cannot be reverted.\n";
        return false;
    }
}
