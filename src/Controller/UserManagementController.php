<?php
namespace Aolr\UserBundle\Controller;

use Aolr\FeedBundle\Service\FeedManager;
use Aolr\UserBundle\Entity\User;
use App\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super-admin/user-management")
 * Class UserManagementController
 * @package App\Controller
 */
class UserManagementController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FeedManager
     */
    private $feedManager;
    public function __construct(EntityManagerInterface $em, FeedManager $feedManager)
    {
        $this->em = $em;
        $this->feedManager = $feedManager;
    }

    /**
     * @Route("/", name="user_management_list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $this->em->getRepository(User::class)->createQueryBuilder('u'),
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('@AolrUser/user_management/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/change-verified", name="ajax_user_management_change_verified", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeVerifiedAction(Request $request, User $user): JsonResponse
    {
        $status = $request->get('value', null);

        if (null === $status) {
            throw new BadRequestHttpException('Not allow status value "null"');
        }
        $user->setIsVerified((boolval($status)));
        $this->em->flush();
        $this->feedManager->save(Event::EVENT_CHANGE_STATUS, [
            'un' => $user->getName(), 'em' => $user->getEmail(), 'type' => 'verification'
        ]);
        return $this->json([]);
    }


    /**
     * @Route("/{id}/change-active", name="ajax_user_management_change_active", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeActiveAction(Request $request, User $user): JsonResponse
    {
        $active = $request->get('value', null);

        if (null === $active) {
            throw new BadRequestHttpException('Not allow active value "null"');
        }
        $user->setIsActive(boolval($active));
        $this->em->flush();
        $this->feedManager->save(Event::EVENT_CHANGE_STATUS, [
            'un' => $user->getName(), 'em' => $user->getEmail(), 'type' => 'active'
        ]);
        return $this->json([]);
    }

    /**
     * @Route("/{id}/change-employee", name="ajax_user_management_change_employee", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeEmployeeAction(Request $request, User $user): JsonResponse
    {
        $employee = $request->get('value', null);

        if (null === $employee) {
            throw new BadRequestHttpException('Not allow employee value "null"');
        }

        $user->setIsEmployee(boolval($employee));
        $this->em->flush();
        $this->feedManager->save(Event::EVENT_CHANGE_STATUS, [
            'un' => $user->getName(), 'em' => $user->getEmail(), 'type' => 'employee'
        ]);
        return $this->json([]);
    }
}
