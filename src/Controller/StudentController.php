<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\StudentType;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student_list")
     */
    public function index()
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $students = $studentRepository->findAll();

        $template = 'student/index.html.twig';
        $args = [
            'students' => $students
        ];
        return $this->render($template, $args);
    }


    /**
     * @Route("/student/new", name="student_new_form", methods={"POST", "GET"})
     */
    public function new(Request $request)
    {
        // create a task and give it some dummy data for this example
        $student = new Student();

        // create a form with 'firstName' and 'surname' text fields
        $form = $this->createForm(StudentType::class, $student);

        // if was POST submission, extract data and put into '$student'
        $form->handleRequest($request);

        // if SUBMITTED & VALID - go ahead and create new object
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createAction($student);
        }

        // render the form for the user
        $template = 'student/new.html.twig';
        $argsArray = [
            'form' => $form->createView(),
        ];
        return $this->render($template, $argsArray);
    }

    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function show(Student $student)
    {
        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }

    public function createAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return $this->redirectToRoute('student_list');
    }


    /**
     * @Route("/student/delete/{id}")
     */
    public function delete(Student $student)
    {
        // entity manager
        $em = $this->getDoctrine()->getManager();

        // store ID before deleting, so can report ID later
        $id = $student->getId();

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id '.$id);
    }

    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function update(Student $student, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
    }



}
