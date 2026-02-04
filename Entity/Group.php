<?php

namespace Plugin\ClaudeSample\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Customer;

if (!class_exists(Group::class)) {
    /**
     * @ORM\Table(name="plg_claude_sample_group")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\Entity(repositoryClass="Plugin\ClaudeSample\Repository\GroupRepository")
     */
    class Group extends AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(type="integer", options={"unsigned":true})
         *
         * @ORM\Id()
         *
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255)
         */
        private $name;

        /**
         * @var int
         *
         * @ORM\Column(type="smallint", nullable=true, options={"unsigned":true})
         */
        private $sortNo;

        /**
         * @var Collection
         *
         * @ORM\ManyToMany(targetEntity="Eccube\Entity\Customer", mappedBy="ClaudeSampleGroups")
         */
        private $Customers;

        public function __construct()
        {
            $this->Customers = new ArrayCollection();
        }

        public function __toString(): string
        {
            return $this->name;
        }

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getName(): ?string
        {
            return $this->name;
        }

        public function setName(string $name): self
        {
            $this->name = $name;

            return $this;
        }

        public function getSortNo(): ?int
        {
            return $this->sortNo;
        }

        public function setSortNo(?int $sortNo): self
        {
            $this->sortNo = $sortNo;

            return $this;
        }

        public function getCustomers(): Collection
        {
            return $this->Customers;
        }

        public function addCustomer(Customer $customer): self
        {
            if (false === $this->Customers->contains($customer)) {
                $this->Customers->add($customer);
            }

            return $this;
        }

        public function removeCustomer(Customer $customer): self
        {
            if ($this->Customers->contains($customer)) {
                $this->Customers->removeElement($customer);
            }

            return $this;
        }
    }
}
