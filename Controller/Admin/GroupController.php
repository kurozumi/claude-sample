<?php

namespace Plugin\ClaudeSample\Controller\Admin;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Util\CacheUtil;
use Plugin\ClaudeSample\Entity\Group;
use Plugin\ClaudeSample\Form\Type\Admin\GroupType;
use Plugin\ClaudeSample\Repository\GroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/%eccube_admin_route%/claude_sample")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class GroupController extends AbstractController
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/group", name="claude_sample_admin_group")
     */
    public function index(): Response
    {
        $groups = $this->groupRepository->getQueryBuilderBySearchData()
            ->getQuery()
            ->getResult();

        return $this->render(
            '@ClaudeSample/admin/group_index.twig',
            [
                'groups' => $groups,
            ]
        );
    }

    /**
     * @Route("/group/new", name="claude_sample_admin_group_new")
     */
    public function new(Request $request, CacheUtil $cacheUtil): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($group);
            $this->entityManager->flush();
            $this->addSuccess('admin.common.save_complete', 'admin');

            $cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('claude_sample_admin_group_edit', ['id' => $group->getId()]);
        }

        return $this->render(
            '@ClaudeSample/admin/group_edit.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/group/{id}/edit", requirements={"id" = "\d+"}, name="claude_sample_admin_group_edit")
     */
    public function edit(Request $request, Group $group, CacheUtil $cacheUtil): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($group);
            $this->entityManager->flush();
            $this->addSuccess('admin.common.save_complete', 'admin');

            $cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('claude_sample_admin_group_edit', ['id' => $group->getId()]);
        }

        return $this->render(
            '@ClaudeSample/admin/group_edit.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/group/{id}/delete", requirements={"id" = "\d+"}, name="claude_sample_admin_group_delete", methods={"DELETE"})
     */
    public function delete(Group $group): RedirectResponse
    {
        $this->isTokenValid();

        try {
            $this->entityManager->remove($group);
            $this->entityManager->flush();

            $this->addSuccess('admin.common.delete_complete', 'admin');
        } catch (ForeignKeyConstraintViolationException $exception) {
            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $group->getName()]);
            $this->addError($message, 'admin');
        } catch (\Exception $exception) {
            $message = trans('admin.common.delete_error');
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('claude_sample_admin_group');
    }

    /**
     * @Route("/group/sort", name="claude_sample_admin_group_sort", methods={"PUT"})
     */
    public function sort(Request $request, CacheUtil $cacheUtil): JsonResponse
    {
        if (false === $request->isXmlHttpRequest()) {
            return $this->json(['message' => trans('admin.common.move_error')], 500);
        }

        if (false === $this->isTokenValid()) {
            return $this->json(['message' => trans('admin.common.move_error')], 500);
        }

        parse_str($request->get('groups'), $data);

        $sortNo = [];
        foreach ($data['group'] as $id) {
            $group = $this->groupRepository->find($id);
            if ($group) {
                $sortNo[] = $group->getSortNo();
            }
        }

        sort($sortNo);

        foreach ($data['group'] as $pos => $id) {
            $group = $this->groupRepository->find($id);
            $group->setSortNo($sortNo[$pos]);
            $this->entityManager->persist($group);
        }
        $this->entityManager->flush();

        $cacheUtil->clearDoctrineCache();

        return $this->json($data['group']);
    }
}
