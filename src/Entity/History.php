<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // /**
    //  * @var Collection<int, User>
    //  */
    // #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'history', orphanRemoval: true)]
    // private Collection $userId;

    #[ORM\Column]
    private ?\DateTime $login_date = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    #[ORM\Column(length: 255)]
    private ?string $device = null;

    #[ORM\Column(length: 255)]
    private ?string $os = null;

    #[ORM\Column(length: 255)]
    private ?string $browser = null;

    // public function __construct()
    // {
    //     $this->userId = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    // /**
    //  * @return Collection<int, User>
    //  */
    // public function getUserId(): Collection
    // {
    //     return $this->userId;
    // }

    // public function addUserId(User $userId): static
    // {
    //     if (!$this->userId->contains($userId)) {
    //         $this->userId->add($userId);
    //         $userId->setHistory($this);
    //     }

    //     return $this;
    // }

    // public function removeUserId(User $userId): static
    // {
    //     if ($this->userId->removeElement($userId)) {
    //         // set the owning side to null (unless already changed)
    //         if ($userId->getHistory() === $this) {
    //             $userId->setHistory(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getLoginDate(): ?\DateTime
    {
        return $this->login_date;
    }

    public function setLoginDate(\DateTime $login_date): static
    {
        $this->login_date = $login_date;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }
}
