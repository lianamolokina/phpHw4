<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Service\GradeService;
use App\Service\StudentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GradeController extends AbstractController
{
    public function __construct(
        private readonly StudentService $studentService,
        private readonly GradeService $gradeService,
    ) {}

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $newStudent = new Student();
        $studentForm = $this->createForm(StudentType::class, $newStudent);
        $studentForm->handleRequest($request);

        if ($studentForm->isSubmitted() && $studentForm->isValid()) {
            $this->studentService->create(
                $newStudent->getFirstName(),
                $newStudent->getLastName()
            );
            $this->addFlash('success', 'Студент добавлен');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('grade/index.html.twig', [
            'students' => $this->studentService->getAllWithGrades(),
            'studentForm' => $studentForm,
        ]);
    }

    #[Route('/grade/{id}', name: 'app_grade_add', methods: ['POST'])]
    public function addGrade(Student $student, Request $request): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('grade', $token)) {
            throw $this->createAccessDeniedException('Неверный CSRF-токен');
        }

        $value = (int) $request->request->get('value', 0);

        try {
            $this->gradeService->addGrade($student, $value);
            $this->addFlash('success', 'Оценка выставлена');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_home');
    }
}
