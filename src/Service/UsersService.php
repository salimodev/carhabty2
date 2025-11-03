<?php


namespace App\Service;


use App\Entity\Users;
class UsersService
{

    private $em;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em )
    {
        $this->em = $em;

    }

    public function getProfile($id){
        $profile = $this->em->getRepository(Users::class)->find($id);
       return $profile;
    
    }

    function ModifierProfileSansMDW($id,$nom,$email,$telephone,$logoImg){
      
        $user = $this->em->getRepository(Users::class)->find($id);
       
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setTel1($telephone);
        $user->setPhoto($logoImg);

        
        $this->em->persist($user);
        $this->em->flush();

        return ($user);
    }

    function ModifierProfileAvecMDW($id,$nom,$email,$telephone,$password,$logoImg){
      
        $user = $this->em->getRepository(Users::class)->find($id);
       
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setTel1($telephone);
        $user->setPassword($password);
        $user->setPhoto($logoImg);

        
        $this->em->persist($user);
        $this->em->flush();

        return ($user);
    }


}