<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentsController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page, Pagination $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setPage($page);
        return $this->render('admin/comments/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Cette méthode permet de modifier un commentaire
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', "Le commentaire a été modifier avec succès! ");
        }
        return $this->render("admin/comments/edit.html.twig", [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * cette méthode permet de supprimer un commentaire
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     *
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash("success", "La suppression du commentaire  est bien effectuée !");
        return $this->redirectToRoute("admin_comments_index");
    }
}
