<?php

namespace Plugin\ClaudeSample\Form\Extension;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Eccube\Form\Type\Admin\CustomerType;
use Plugin\ClaudeSample\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerGroupExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ClaudeSampleGroups', EntityType::class, [
                'label' => 'claude_sample.admin.customer.groups',
                'class' => Group::class,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'eccube_form_options' => [
                    'auto_render' => true,
                ],
                'choice_label' => function (Group $group) {
                    return $group->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.sortNo', Criteria::ASC);
                },
            ]);
    }

    public static function getExtendedTypes(): iterable
    {
        yield CustomerType::class;
    }
}
