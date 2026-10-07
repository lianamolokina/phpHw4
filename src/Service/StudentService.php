<?php

namespace App\Service;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

class StudentService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly StudentRepository $studentRepository,
    ) {}

    /** @return Student[] */
    public function getAllWithGrades(): array
    {
        return $this->studentRepository->findAllWithGrades();
    }

    public function create(string $firstName, string $lastName): Student
    {
        $student = (new Student())
            ->setFirstName($firstName)
            ->setLastName($lastName);

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }
}
