<?php

namespace Aolr\UserBundle\Controller;

use Aolr\FeedBundle\Service\FeedManager;
use Aolr\UserBundle\Entity\Role;
use Aolr\UserBundle\Entity\User;
use App\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super-admin/role-management")
 * Class UserRoleManagementController
 * @package App\Controller
 */
class UserRoleManagementController extends AbstractController
{
    /**
     * @Route("/{id}/save-user-role", name="_ajax_save_user_role", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $em
     * @param FeedManager $feedManager
     *
     * @return JsonResponse
     * @throws ORMException
     */
    public function saveUserRoles(Request $request, User $user, EntityManagerInterface $em, FeedManager $feedManager): JsonResponse
    {
        $roles = $request->get('roles', []);
        $user->cleanRoles();

        foreach ($roles as $role) {
            $user->addRole($em->getReference(Role::class, $role));
        }
        $em->flush();

        $feedManager->save(Event::EVENT_CHANGE_ROLE, [
            'un' => $user->getName(), 'em' => $user->getEmail(), 'roles' => json_encode($roles)
        ]);
        return $this->json([
            'user_id' => $user->getId(),
            'roles' => $user->getRoles()
        ]);
    }
}
