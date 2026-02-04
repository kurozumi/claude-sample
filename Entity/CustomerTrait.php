<?php

namespace Plugin\ClaudeSample\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait CustomerTrait
{
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Plugin\ClaudeSample\Entity\Group", inversedBy="Customers", cascade={"persist"})
     *
     * @ORM\JoinTable(name="plg_claude_sample_customer_group",
     *     joinColumns={@ORM\JoinColumn(name="customer_id", referencedColumnName="id")}
     * )
     *
     * @ORM\OrderBy({"sortNo" = "ASC"})
     */
    private $ClaudeSampleGroups;

    public function getClaudeSampleGroups(): Collection
    {
        if (null === $this->ClaudeSampleGroups) {
            $this->ClaudeSampleGroups = new ArrayCollection();
        }

        return $this->ClaudeSampleGroups;
    }

    public function addClaudeSampleGroup(Group $group): self
    {
        if (null === $this->ClaudeSampleGroups) {
            $this->ClaudeSampleGroups = new ArrayCollection();
        }

        if (false === $this->ClaudeSampleGroups->contains($group)) {
            $this->ClaudeSampleGroups->add($group);
        }

        return $this;
    }

    public function removeClaudeSampleGroup(Group $group): self
    {
        if (null === $this->ClaudeSampleGroups) {
            $this->ClaudeSampleGroups = new ArrayCollection();
        }

        if ($this->ClaudeSampleGroups->contains($group)) {
            $this->ClaudeSampleGroups->removeElement($group);
        }

        return $this;
    }

    public function hasClaudeSampleGroups(): bool
    {
        if (null === $this->ClaudeSampleGroups) {
            $this->ClaudeSampleGroups = new ArrayCollection();
        }

        return $this->ClaudeSampleGroups->count() > 0;
    }
}
