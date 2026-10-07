<?php

namespace App\Service;

use App\Entity\Grade;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

class GradeService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function addGrade(Student $student, int $value): Grade
    {
        if ($value < Grade::MIN_VALUE || $value > Grade::MAX_VALUE) {
            throw new \InvalidArgumentException(sprintf(
                'Оценка должна быть в диапазоне от %d до %d.',
                Grade::MIN_VALUE,
                Grade::MAX_VALUE
            ));
        }

        $grade = (new Grade())
            ->setValue($value)
            ->setStudent($student);

        $this->em->persist($grade);
        $this->em->flush();

        return $grade;
    }
}
