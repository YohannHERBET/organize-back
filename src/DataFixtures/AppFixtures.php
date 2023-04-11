<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Category;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * for create data in DB (DataBase)
 */
class AppFixtures extends Fixture
{

    private $connection;

    public function __construct(Connection $connection)
    {
        //recover the connection to the dataBase
        $this->connection = $connection;
    }

    /**
     * Initialize index in DB
     */
    private function truncate()
    {
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // we truncate
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('TRUNCATE TABLE project');
        $this->connection->executeQuery('TRUNCATE TABLE task');
        $this->connection->executeQuery('TRUNCATE TABLE category');
    }

    public function load(ObjectManager $manager): void
    {

        $this->truncate();

        // Users
        $userList = [];

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNickname('admin');
        $admin->setPassword('$2y$13$.PJiDK3kq2C4owW5RW6Z3ukzRc14TJZRPcMfXcCy9AyhhA9OMK3Li');
        $userList[] = $admin;
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setNickname('user');
        $user->setPassword('$2y$13$ZqCHV23K0KMWmCxntdDlmOocuxuuSOXeT7nfKy2ZbE2vFC1VS3Q..');
        $userList[] = $user;
        $manager->persist($user);

        $user = new User();
        $user->setEmail('bob@bob.com');
        $user->setRoles(['ROLE_USER']);
        $user->setNickname('bob');
        $user->setPassword('$2y$13$6nQ6yD7wZjVBSwcOtJdtw.jypODxznmW78zL9hM/aScyOnf80TPs6');
        $userList[] = $user;
        $manager->persist($user);


        //Project
        $projectList = [];
        for ($p = 0; $p < 10; $p++) {

            $project = new Project();
            $project->setTitle('Projet ' . ($p + 1));
            $project->setDescriptionProject('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
            $randomUser = $userList[mt_rand(1, (count($userList) - 1))];
            $project->setUser($randomUser);
            $projectList[] = $project;
            $manager->persist($project);
        }


        //Category
        $categoriesList = [];
        shuffle($projectList);
        for ($c = 0; $c < 20; $c++) {
            $category = new Category();
            $category->setNameCategory('Catégorie ' . ($c + 1));
            $randomProject = $projectList[mt_rand(1, (count($projectList) - 1))];
            $category->setProject($randomProject);
            $categoriesList[] = $category;
            $manager->persist($category);
        }


        //Task
        shuffle($categoriesList);
        for ($t = 0; $t < 20; $t++) {
            $task = new Task();
            $task->setName('Tache ' . ($t + 1));
            $task->setPriority(mt_rand(1, 3));
            $task->setCreatedAt(new DateTime());
            $task->setDescritpionTask('lorem ...');
            $randomCategory = $categoriesList[mt_rand(1, (count($categoriesList) - 1))];
            $task->setCategory($randomCategory);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
