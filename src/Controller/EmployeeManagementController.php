<?php
namespace Aolr\UserBundle\Controller;

use Aolr\UserBundle\Entity\Role;
use Aolr\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super-admin/employee-management")
 * Class EmployeeManagementController
 * @package App\Controller
 * @IsGranted("ROLE_SUPER_USER")
 */
class EmployeeManagementController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="employee_management_list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator): Response
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('u');
        $pagination = $paginator->paginate(
            $qb->where('u.isEmployee = 1'),
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('@AolrUser/employee_management/list.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/{id}/roles", name="ajax_employee_management_roles", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function roleListAction(Request $request, User $user): Response
    {
        return new Response($this->renderView('@AolrUser/employee_management/role_list.html.twig', [
            'roles' => $this->em->getRepository(Role::class)->findBy(['isActive' => true]),
            'user' => $user
        ]));
    }
}
