<?php

namespace Plugin\ClaudeSample\Form\Type\Admin;

use Plugin\ClaudeSample\Entity\Group;
use Plugin\ClaudeSample\Repository\GroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupType extends AbstractType
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'claude_sample.admin.group.name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Group $data */
            $data = $event->getData();

            if ($form->isValid() && null === $data->getId()) {
                $maxSortNo = $this->groupRepository->getMaxSortNo();
                $data->setSortNo($maxSortNo + 1);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
