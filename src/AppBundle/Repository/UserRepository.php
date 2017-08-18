<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function isGuestKeyExists($guestKey)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM users WHERE guest_key = :guest_key";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':guest_key', $guestKey, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
