<?php

namespace Ockcyp\Bundle\TodoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Ockcyp\Bundle\TodoBundle\Entity\Todo;
use Doctrine\ORM\Query;
use DateTime;

class TodoController extends Controller
{
    public function showAction($id)
    {
        $todoRepo = $this->getDoctrine()
            ->getRepository('OckcypTodoBundle:Todo');

        $result = $todoRepo->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);

        if (!$result) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'Invalid todo ID: ' . $id
            ), 404);
        }

        return new JsonResponse($result);
    }

    public function listAction()
    {
        $todoRepo = $this->getDoctrine()
            ->getRepository('OckcypTodoBundle:Todo');

        $result = $todoRepo->createQueryBuilder('t')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($result);
    }

    public function createAction(Request $request)
    {
        $description = $request->request->get('desc', null);
        if (!$description) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'Description cannot be empty.'
            ), 400);
        }

        $todo = new Todo;
        $now = new DateTime;
        $todo->setDescription($description)
            ->setDateAdded($now);

        $em = $this->getDoctrine()
            ->getManager();

        $em->persist($todo);
        $em->flush();

        return new RedirectResponse(
            $this->generateUrl(
                'ockcyp_todo_show',
                array('id' => $todo->getId())
            ),
            201
        );
    }

    public function updateAction($id, Request $request)
    {
        $todoRepo = $this->getDoctrine()
            ->getRepository('OckcypTodoBundle:Todo');

        $todo = $todoRepo->find($id);
        if (!$todo) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'Invalid todo ID: ' . $id
            ), 404);
        }

        $description = $request->request->get('desc', null);
        if (!$description) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'Description cannot be empty.'
            ), 400);
        }

        $todo->setDescription($description)
            ->setDateModified(new DateTime);

        $em = $this->getDoctrine()
            ->getManager();

        try {
            $em->persist($todo);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'A problem occurred while deleting todo ID: ' . $id
            ), 500);
        }

        return new JsonResponse(array(
            'success' => true
        ));
    }

    public function deleteAction($id)
    {
        $todoRepo = $this->getDoctrine()
            ->getRepository('OckcypTodoBundle:Todo');

        $todo = $todoRepo->find($id);
        if (!$todo) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'Invalid todo ID: ' . $id
            ), 404);
        }

        $em = $this->getDoctrine()
            ->getManager();

        try {
            $em->remove($todo);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'error' => 'A problem occurred while deleting todo ID: ' . $id
            ), 500);
        }

        return new JsonResponse(array(
            'success' => true
        ));
    }
}
